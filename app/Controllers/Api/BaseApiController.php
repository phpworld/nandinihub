<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class BaseApiController extends ResourceController
{
    use ResponseTrait;

    protected $format = 'json';

    /**
     * Get authenticated user from JWT token
     */
    protected function getAuthenticatedUser(): ?array
    {
        return $this->request->jwtUser ?? null;
    }

    /**
     * Get authenticated user ID
     */
    protected function getAuthenticatedUserId(): ?int
    {
        $user = $this->getAuthenticatedUser();
        return $user['user_id'] ?? null;
    }

    /**
     * Standard success response
     */
    protected function successResponse($data = null, string $message = 'Success', int $statusCode = 200): \CodeIgniter\HTTP\ResponseInterface
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $data
        ];

        return $this->respond($response, $statusCode);
    }

    /**
     * Standard error response
     */
    protected function errorResponse(string $message = 'Error', int $statusCode = 400, $errors = null): \CodeIgniter\HTTP\ResponseInterface
    {
        $response = [
            'success' => false,
            'message' => $message
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return $this->respond($response, $statusCode);
    }

    /**
     * Validation error response
     */
    protected function validationErrorResponse($errors): \CodeIgniter\HTTP\ResponseInterface
    {
        return $this->errorResponse('Validation failed', 422, $errors);
    }

    /**
     * Not found response
     */
    protected function notFoundResponse(string $message = 'Resource not found'): \CodeIgniter\HTTP\ResponseInterface
    {
        return $this->errorResponse($message, 404);
    }

    /**
     * Unauthorized response
     */
    protected function unauthorizedResponse(string $message = 'Unauthorized'): \CodeIgniter\HTTP\ResponseInterface
    {
        return $this->errorResponse($message, 401);
    }

    /**
     * Forbidden response
     */
    protected function forbiddenResponse(string $message = 'Forbidden'): \CodeIgniter\HTTP\ResponseInterface
    {
        return $this->errorResponse($message, 403);
    }

    /**
     * Paginated response
     */
    protected function paginatedResponse($data, int $page, int $perPage, int $total, string $message = 'Success'): \CodeIgniter\HTTP\ResponseInterface
    {
        $totalPages = ceil($total / $perPage);
        
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $data,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => $totalPages,
                'has_next' => $page < $totalPages,
                'has_prev' => $page > 1
            ]
        ];

        return $this->respond($response);
    }

    /**
     * Validate required fields
     */
    protected function validateRequired(array $data, array $requiredFields): array
    {
        $errors = [];
        
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $errors[$field] = "The {$field} field is required.";
            }
        }
        
        return $errors;
    }

    /**
     * Sanitize input data
     */
    protected function sanitizeInput(array $data): array
    {
        $sanitized = [];
        
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $sanitized[$key] = trim(strip_tags($value));
            } else {
                $sanitized[$key] = $value;
            }
        }
        
        return $sanitized;
    }
}
