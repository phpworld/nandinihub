<?php

namespace App\Libraries;

use App\Models\ShippingMethodModel;
use App\Models\CouponModel;

class ShippingService
{
    protected $shippingMethodModel;
    protected $couponModel;

    public function __construct()
    {
        $this->shippingMethodModel = new ShippingMethodModel();
        $this->couponModel = new CouponModel();
    }

    /**
     * Get available shipping methods for a given order amount
     */
    public function getAvailableShippingMethods(float $orderAmount): array
    {
        return $this->shippingMethodModel->getAvailableShippingMethods($orderAmount);
    }

    /**
     * Calculate shipping cost for a specific method and order
     */
    public function calculateShippingCost(int $methodId, float $orderAmount, ?string $couponCode = null): array
    {
        $method = $this->shippingMethodModel->getShippingMethodById($methodId);
        
        if (!$method) {
            return [
                'success' => false,
                'message' => 'Invalid shipping method',
                'cost' => 0.00
            ];
        }

        // Check if order qualifies for this shipping method
        if ($orderAmount < $method['minimum_order_amount']) {
            return [
                'success' => false,
                'message' => "Minimum order amount of \${$method['minimum_order_amount']} required for this shipping method",
                'cost' => 0.00
            ];
        }

        if ($method['maximum_order_amount'] !== null && $orderAmount > $method['maximum_order_amount']) {
            return [
                'success' => false,
                'message' => "Maximum order amount of \${$method['maximum_order_amount']} exceeded for this shipping method",
                'cost' => 0.00
            ];
        }

        $shippingCost = (float) $method['cost'];

        // Check if coupon provides free shipping
        if ($couponCode) {
            $coupon = $this->couponModel->where('code', $couponCode)
                                       ->where('is_active', 1)
                                       ->first();
            
            if ($coupon && $coupon['type'] === 'free_shipping') {
                // Validate coupon usage and expiry
                if ($this->isCouponValid($coupon, $orderAmount)) {
                    $shippingCost = 0.00;
                }
            }
        }

        return [
            'success' => true,
            'message' => 'Shipping cost calculated successfully',
            'cost' => $shippingCost,
            'method' => $method
        ];
    }

    /**
     * Get the best shipping option for an order (cheapest available)
     */
    public function getBestShippingOption(float $orderAmount): ?array
    {
        return $this->shippingMethodModel->getCheapestShippingMethod($orderAmount);
    }

    /**
     * Get default shipping method
     */
    public function getDefaultShippingMethod(): ?array
    {
        return $this->shippingMethodModel->getDefaultShippingMethod();
    }

    /**
     * Check if free shipping is available for order amount
     */
    public function hasFreeShipping(float $orderAmount): bool
    {
        return $this->shippingMethodModel->hasFreeShipping($orderAmount);
    }

    /**
     * Calculate shipping for checkout (legacy compatibility)
     * This method maintains backward compatibility with existing checkout logic
     */
    public function calculateCheckoutShipping(float $orderAmount, ?int $selectedMethodId = null): array
    {
        // If no method selected, use the best available option
        if (!$selectedMethodId) {
            $method = $this->getBestShippingOption($orderAmount);
            if (!$method) {
                // Fallback to legacy logic if no shipping methods configured
                return $this->getLegacyShipping($orderAmount);
            }
            $selectedMethodId = $method['id'];
        }

        $result = $this->calculateShippingCost($selectedMethodId, $orderAmount);
        
        if (!$result['success']) {
            // Fallback to legacy logic
            return $this->getLegacyShipping($orderAmount);
        }

        return [
            'cost' => $result['cost'],
            'method_id' => $selectedMethodId,
            'method_name' => $result['method']['name'],
            'delivery_time' => $result['method']['delivery_time'],
            'is_free' => $result['cost'] == 0
        ];
    }

    /**
     * Legacy shipping calculation (for backward compatibility)
     */
    private function getLegacyShipping(float $orderAmount): array
    {
        $cost = $orderAmount >= 500 ? 0 : 50;
        
        return [
            'cost' => $cost,
            'method_id' => null,
            'method_name' => $cost == 0 ? 'Free Shipping' : 'Standard Shipping',
            'delivery_time' => $cost == 0 ? '5-7 Business Days' : '3-5 Business Days',
            'is_free' => $cost == 0
        ];
    }

    /**
     * Validate coupon for shipping
     */
    private function isCouponValid(array $coupon, float $orderAmount): bool
    {
        // Check expiry date
        if ($coupon['expiry_date'] && strtotime($coupon['expiry_date']) < time()) {
            return false;
        }

        // Check minimum order amount
        if ($coupon['minimum_order_amount'] && $orderAmount < $coupon['minimum_order_amount']) {
            return false;
        }

        // Check usage limits
        if ($coupon['usage_limit'] && $coupon['usage_count'] >= $coupon['usage_limit']) {
            return false;
        }

        return true;
    }

    /**
     * Get shipping methods formatted for frontend display
     */
    public function getShippingMethodsForCheckout(float $orderAmount): array
    {
        $methods = $this->getAvailableShippingMethods($orderAmount);
        $formattedMethods = [];

        foreach ($methods as $method) {
            $formattedMethods[] = [
                'id' => $method['id'],
                'name' => $method['name'],
                'description' => $method['description'],
                'delivery_time' => $method['delivery_time'],
                'cost' => (float) $method['cost'],
                'cost_formatted' => $method['cost'] > 0 ? '$' . number_format($method['cost'], 2) : 'Free',
                'is_free' => $method['cost'] == 0 || $method['is_free_shipping'],
                'minimum_order_amount' => (float) $method['minimum_order_amount'],
                'sort_order' => $method['sort_order']
            ];
        }

        // Sort by sort_order
        usort($formattedMethods, function($a, $b) {
            return $a['sort_order'] <=> $b['sort_order'];
        });

        return $formattedMethods;
    }

    /**
     * Validate shipping method selection
     */
    public function validateShippingMethod(int $methodId, float $orderAmount): array
    {
        $method = $this->shippingMethodModel->find($methodId);
        
        if (!$method) {
            return [
                'valid' => false,
                'message' => 'Shipping method not found'
            ];
        }

        if (!$method['is_active']) {
            return [
                'valid' => false,
                'message' => 'Shipping method is not available'
            ];
        }

        if ($orderAmount < $method['minimum_order_amount']) {
            return [
                'valid' => false,
                'message' => "Minimum order amount of \${$method['minimum_order_amount']} required"
            ];
        }

        if ($method['maximum_order_amount'] !== null && $orderAmount > $method['maximum_order_amount']) {
            return [
                'valid' => false,
                'message' => "Maximum order amount of \${$method['maximum_order_amount']} exceeded"
            ];
        }

        return [
            'valid' => true,
            'message' => 'Shipping method is valid',
            'method' => $method
        ];
    }

    /**
     * Get shipping summary for order confirmation
     */
    public function getShippingSummary(int $methodId, float $orderAmount): array
    {
        $result = $this->calculateShippingCost($methodId, $orderAmount);
        
        if (!$result['success']) {
            return [
                'method_name' => 'Unknown',
                'delivery_time' => 'Unknown',
                'cost' => 0.00,
                'cost_formatted' => 'Free'
            ];
        }

        $method = $result['method'];
        $cost = $result['cost'];

        return [
            'method_name' => $method['name'],
            'delivery_time' => $method['delivery_time'],
            'cost' => $cost,
            'cost_formatted' => $cost > 0 ? '$' . number_format($cost, 2) : 'Free',
            'description' => $method['description']
        ];
    }
}
