<?php

namespace App\Models;

use CodeIgniter\Model;

class ReviewModel extends Model
{
    protected $table            = 'reviews';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'product_id',
        'user_id',
        'order_id',
        'rating',
        'title',
        'review',
        'is_verified',
        'is_approved',
        'helpful_count'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'product_id' => 'required|integer',
        'user_id' => 'required|integer',
        'rating' => 'required|integer|greater_than[0]|less_than[6]',
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    public function getProductReviews($productId, $limit = null)
    {
        $builder = $this->select('reviews.*, users.first_name, users.last_name')
            ->join('users', 'users.id = reviews.user_id')
            ->where('reviews.product_id', $productId)
            ->where('reviews.is_approved', 1)
            ->orderBy('reviews.created_at', 'DESC');

        if ($limit) {
            $builder->limit($limit);
        }

        return $builder->findAll();
    }

    public function getProductRatingStats($productId)
    {
        $stats = $this->select('
                AVG(rating) as average_rating,
                COUNT(*) as total_reviews,
                SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as five_star,
                SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as four_star,
                SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as three_star,
                SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as two_star,
                SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as one_star
            ')
            ->where('product_id', $productId)
            ->where('is_approved', 1)
            ->first();

        if ($stats['total_reviews'] > 0) {
            $stats['five_star_percent'] = ($stats['five_star'] / $stats['total_reviews']) * 100;
            $stats['four_star_percent'] = ($stats['four_star'] / $stats['total_reviews']) * 100;
            $stats['three_star_percent'] = ($stats['three_star'] / $stats['total_reviews']) * 100;
            $stats['two_star_percent'] = ($stats['two_star'] / $stats['total_reviews']) * 100;
            $stats['one_star_percent'] = ($stats['one_star'] / $stats['total_reviews']) * 100;
        }

        return $stats;
    }

    public function getUserReview($productId, $userId)
    {
        return $this->where('product_id', $productId)
            ->where('user_id', $userId)
            ->first();
    }

    public function canUserReview($productId, $userId)
    {
        // Check if user has purchased this product
        $orderItemModel = new OrderItemModel();
        $orderModel = new OrderModel();

        // Allow reviews for any purchased product (not just delivered)
        $hasPurchased = $orderItemModel->select('order_items.*')
            ->join('orders', 'orders.id = order_items.order_id')
            ->where('order_items.product_id', $productId)
            ->where('orders.user_id', $userId)
            ->whereIn('orders.status', ['delivered', 'processing', 'shipped'])
            ->first();

        // Check if user has already reviewed
        $hasReviewed = $this->getUserReview($productId, $userId);

        return $hasPurchased && !$hasReviewed;
    }

    public function canUserReviewAnyProduct($productId, $userId)
    {
        // For testing: Allow any logged-in user to review any product
        // Check if user has already reviewed
        $hasReviewed = $this->getUserReview($productId, $userId);
        return !$hasReviewed;
    }

    public function getRecentReviews($limit = 10)
    {
        return $this->select('reviews.*, users.first_name, users.last_name, products.name as product_name, products.slug as product_slug')
            ->join('users', 'users.id = reviews.user_id')
            ->join('products', 'products.id = reviews.product_id')
            ->where('reviews.is_approved', 1)
            ->orderBy('reviews.created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    public function getPendingReviews($limit = null)
    {
        $builder = $this->select('reviews.*, users.first_name, users.last_name, products.name as product_name')
            ->join('users', 'users.id = reviews.user_id')
            ->join('products', 'products.id = reviews.product_id')
            ->where('reviews.is_approved', 0)
            ->orderBy('reviews.created_at', 'DESC');

        if ($limit) {
            $builder->limit($limit);
        }

        return $builder->findAll();
    }

    public function approveReview($reviewId)
    {
        return $this->update($reviewId, ['is_approved' => 1]);
    }

    public function rejectReview($reviewId)
    {
        return $this->update($reviewId, ['is_approved' => 0]);
    }

    public function incrementHelpfulCount($reviewId)
    {
        $review = $this->find($reviewId);
        if ($review) {
            return $this->update($reviewId, ['helpful_count' => $review['helpful_count'] + 1]);
        }
        return false;
    }

    /**
     * Get average rating for a product
     */
    public function getAverageRating($productId)
    {
        $result = $this->select('AVG(rating) as average_rating')
            ->where('product_id', $productId)
            ->where('is_approved', 1)
            ->first();

        return $result ? round((float) $result['average_rating'], 1) : 0;
    }
}
