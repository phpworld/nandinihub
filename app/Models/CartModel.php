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
        $builder = $this->select('cart.*, products.name, products.slug, products.image, products.stock_quantity, products.sale_price')
            ->join('products', 'products.id = cart.product_id')
            ->where('products.is_active', 1);

        if ($userId) {
            $builder->where('cart.user_id', $userId);
        } else {
            $builder->where('cart.session_id', $sessionId);
        }

        return $builder->orderBy('cart.created_at', 'DESC')->findAll();
    }

    public function addToCart($data)
    {
        // Check if item already exists in cart
        $builder = $this->where('product_id', $data['product_id']);

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

    public function getCartTotal($userId = null, $sessionId = null)
    {
        $items = $this->getCartItems($userId, $sessionId);
        $total = 0;

        foreach ($items as $item) {
            $price = $item['sale_price'] ? $item['sale_price'] : $item['price'];
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
