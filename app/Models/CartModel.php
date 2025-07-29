<?php

namespace App\Models;

use CodeIgniter\Model;

class CartModel extends Model
{
    protected $table            = 'cart';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'session_id',
        'product_id',
        'variant_id',
        'variant_options',
        'quantity',
        'price'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'product_id' => 'required|integer',
        'quantity'   => 'required|integer|greater_than[0]',
        'price'      => 'required|decimal',
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    public function getCartItems($userId = null, $sessionId = null)
    {
        $builder = $this->select('cart.*, products.name, products.slug, products.image, products.stock_quantity, products.sale_price,
                                 product_variants.sku as variant_sku, product_variants.price as variant_price,
                                 product_variants.sale_price as variant_sale_price, product_variants.stock_quantity as variant_stock,
                                 product_variants.image as variant_image')
            ->join('products', 'products.id = cart.product_id')
            ->join('product_variants', 'product_variants.id = cart.variant_id', 'left')
            ->where('products.is_active', 1);

        if ($userId) {
            $builder->where('cart.user_id', $userId);
        } else {
            $builder->where('cart.session_id', $sessionId);
        }

        $items = $builder->orderBy('cart.created_at', 'DESC')->findAll();

        // Add variant options and calculate final prices
        $variationOptionModel = new \App\Models\ProductVariationOptionModel();

        foreach ($items as &$item) {
            if ($item['variant_id']) {
                $variantOptionModel = new ProductVariantOptionModel();
                $item['variant_options_details'] = $variantOptionModel->getOptionsByVariant($item['variant_id']);

                // Calculate final price with option modifiers
                $optionIds = array_column($item['variant_options_details'], 'variation_option_id');
                $basePrice = $item['variant_sale_price'] ?: $item['variant_price'] ?: $item['sale_price'] ?: $item['price'];

                $item['final_price'] = $variationOptionModel->getFinalPrice($optionIds, $basePrice);
                $item['price_modifier'] = $variationOptionModel->calculatePriceModifier($optionIds, $basePrice);
            } else {
                $item['variant_options_details'] = [];
                $item['final_price'] = $item['sale_price'] ?: $item['price'];
                $item['price_modifier'] = 0;
            }
        }

        return $items;
    }

    public function addToCart($data)
    {
        // Check if item already exists in cart (including variant and variant_options)
        $builder = $this->where('product_id', $data['product_id']);

        if (isset($data['variant_id'])) {
            $builder->where('variant_id', $data['variant_id']);

            // Also check variant_options for exact match
            if (isset($data['variant_options'])) {
                $builder->where('variant_options', $data['variant_options']);
            } else {
                $builder->where('variant_options IS NULL');
            }
        } else {
            $builder->where('variant_id IS NULL');
        }

        if (isset($data['user_id'])) {
            $builder->where('user_id', $data['user_id']);
        } else {
            $builder->where('session_id', $data['session_id']);
        }

        $existingItem = $builder->first();

        if ($existingItem) {
            // Update quantity
            $newQuantity = $existingItem['quantity'] + $data['quantity'];
            return $this->update($existingItem['id'], ['quantity' => $newQuantity]);
        } else {
            // Insert new item
            return $this->insert($data);
        }
    }

    public function updateCartItem($cartId, $quantity)
    {
        return $this->update($cartId, ['quantity' => $quantity]);
    }

    public function removeCartItem($cartId)
    {
        return $this->delete($cartId);
    }

    public function getCartItemsWithDetails($userId = null, $sessionId = null)
    {
        $items = $this->getCartItems($userId, $sessionId);

        // Add final_price calculation with variation option modifiers
        foreach ($items as &$item) {
            // Start with base price
            if ($item['variant_id']) {
                $basePrice = $item['variant_sale_price'] ?? $item['variant_price'] ?? $item['sale_price'] ?? $item['price'];
            } else {
                $basePrice = $item['sale_price'] ?? $item['price'];
            }

            // Calculate price modifier from variation options
            $priceModifier = 0;
            if ($item['variant_id'] && !empty($item['variant_options'])) {
                $variantOptions = json_decode($item['variant_options'], true);
                if (is_array($variantOptions)) {
                    $variationOptionModel = new \App\Models\ProductVariationOptionModel();

                    foreach ($variantOptions as $optionId) {
                        $option = $variationOptionModel->find($optionId);
                        if ($option && $option['price_modifier']) {
                            if ($option['price_type'] === 'percentage') {
                                $priceModifier += $basePrice * ($option['price_modifier'] / 100);
                            } else {
                                $priceModifier += $option['price_modifier'];
                            }
                        }
                    }
                }
            }

            // Set final price and price modifier
            $item['price_modifier'] = $priceModifier;
            $item['final_price'] = $basePrice + $priceModifier;
        }

        return $items;
    }

    public function getCartTotal($userId = null, $sessionId = null)
    {
        $items = $this->getCartItemsWithDetails($userId, $sessionId);
        $total = 0;

        foreach ($items as $item) {
            // Use final_price which includes variation option modifiers
            $price = $item['final_price'];
            $total += $price * $item['quantity'];
        }

        return $total;
    }

    public function getCartCount($userId = null, $sessionId = null)
    {
        $builder = $this->selectSum('quantity');

        if ($userId) {
            $builder->where('user_id', $userId);
        } else {
            $builder->where('session_id', $sessionId);
        }

        $result = $builder->first();
        return $result['quantity'] ?? 0;
    }

    public function clearCart($userId = null, $sessionId = null)
    {
        $builder = $this->builder();

        if ($userId) {
            $builder->where('user_id', $userId);
        } else {
            $builder->where('session_id', $sessionId);
        }

        return $builder->delete();
    }

    public function transferCartToUser($sessionId, $userId)
    {
        // Get existing user cart items
        $userCartItems = $this->where('user_id', $userId)->findAll();
        $userProductIds = array_column($userCartItems, 'product_id');

        // Get session cart items
        $sessionCartItems = $this->where('session_id', $sessionId)->findAll();

        foreach ($sessionCartItems as $sessionItem) {
            if (in_array($sessionItem['product_id'], $userProductIds)) {
                // Product already exists in user cart, update quantity
                $userItem = array_filter($userCartItems, function ($item) use ($sessionItem) {
                    return $item['product_id'] == $sessionItem['product_id'];
                });
                $userItem = reset($userItem);

                $newQuantity = $userItem['quantity'] + $sessionItem['quantity'];
                $this->update($userItem['id'], ['quantity' => $newQuantity]);
                $this->delete($sessionItem['id']);
            } else {
                // Transfer session item to user
                $this->update($sessionItem['id'], [
                    'user_id' => $userId,
                    'session_id' => null
                ]);
            }
        }
    }

    /**
     * Get user cart items (alias for getCartItems)
     */
    public function getUserCartItems($userId)
    {
        return $this->getCartItems($userId);
    }

    /**
     * Get cart summary for a user
     */
    public function getCartSummary($userId)
    {
        $cartItems = $this->getUserCartItems($userId);

        $totalItems = 0;
        $subtotal = 0;

        foreach ($cartItems as $item) {
            $totalItems += $item['quantity'];
            $subtotal += ($item['price'] * $item['quantity']);
        }

        return [
            'total_items' => $totalItems,
            'subtotal' => $subtotal,
            'total' => $subtotal, // For now, same as subtotal (no taxes/shipping)
            'currency' => 'INR'
        ];
    }
}
