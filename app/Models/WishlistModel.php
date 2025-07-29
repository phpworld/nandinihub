<?php

namespace App\Models;

use CodeIgniter\Model;

class WishlistModel extends Model
{
    protected $table = 'wishlist';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'user_id',
        'product_id'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'user_id' => 'required|integer',
        'product_id' => 'required|integer',
    ];

    protected $validationMessages = [
        'user_id' => [
            'required' => 'User ID is required',
            'integer' => 'User ID must be an integer'
        ],
        'product_id' => [
            'required' => 'Product ID is required',
            'integer' => 'Product ID must be an integer'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Get wishlist items for a user with product details
     */
    public function getWishlistItems($userId)
    {
        return $this->select('wishlist.*, products.name, products.slug, products.price, products.sale_price, products.image, products.stock_quantity, products.is_active')
                   ->join('products', 'products.id = wishlist.product_id')
                   ->where('wishlist.user_id', $userId)
                   ->where('products.is_active', 1)
                   ->orderBy('wishlist.created_at', 'DESC')
                   ->findAll();
    }

    /**
     * Add product to wishlist
     */
    public function addToWishlist($userId, $productId)
    {
        // Check if already exists
        $existing = $this->where(['user_id' => $userId, 'product_id' => $productId])->first();
        
        if ($existing) {
            return false; // Already in wishlist
        }

        return $this->insert([
            'user_id' => $userId,
            'product_id' => $productId
        ]);
    }

    /**
     * Remove product from wishlist
     */
    public function removeFromWishlist($userId, $productId)
    {
        return $this->where(['user_id' => $userId, 'product_id' => $productId])->delete();
    }

    /**
     * Check if product is in user's wishlist
     */
    public function isInWishlist($userId, $productId)
    {
        if (!$userId) {
            return false;
        }
        
        $item = $this->where(['user_id' => $userId, 'product_id' => $productId])->first();
        return !empty($item);
    }

    /**
     * Get wishlist count for a user
     */
    public function getWishlistCount($userId)
    {
        if (!$userId) {
            return 0;
        }
        
        return $this->where('user_id', $userId)->countAllResults();
    }

    /**
     * Toggle wishlist status (add if not exists, remove if exists)
     */
    public function toggleWishlist($userId, $productId)
    {
        $existing = $this->where(['user_id' => $userId, 'product_id' => $productId])->first();
        
        if ($existing) {
            // Remove from wishlist
            $this->delete($existing['id']);
            return ['action' => 'removed', 'in_wishlist' => false];
        } else {
            // Add to wishlist
            $this->insert([
                'user_id' => $userId,
                'product_id' => $productId
            ]);
            return ['action' => 'added', 'in_wishlist' => true];
        }
    }

    /**
     * Clear entire wishlist for a user
     */
    public function clearWishlist($userId)
    {
        return $this->where('user_id', $userId)->delete();
    }

    /**
     * Get wishlist items with pagination
     */
    public function getWishlistItemsPaginated($userId, $perPage = 12)
    {
        return $this->select('wishlist.*, products.name, products.slug, products.price, products.sale_price, products.image, products.stock_quantity, products.is_active')
                   ->join('products', 'products.id = wishlist.product_id')
                   ->where('wishlist.user_id', $userId)
                   ->where('products.is_active', 1)
                   ->orderBy('wishlist.created_at', 'DESC')
                   ->paginate($perPage);
    }

    /**
     * Get popular wishlist products (most wishlisted)
     */
    public function getPopularWishlistProducts($limit = 10)
    {
        return $this->select('product_id, products.name, products.slug, products.price, products.sale_price, products.image, COUNT(*) as wishlist_count')
                   ->join('products', 'products.id = wishlist.product_id')
                   ->where('products.is_active', 1)
                   ->groupBy('product_id')
                   ->orderBy('wishlist_count', 'DESC')
                   ->limit($limit)
                   ->findAll();
    }
}
