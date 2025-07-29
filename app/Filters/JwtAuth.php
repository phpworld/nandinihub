<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use App\Libraries\JwtHelper;

class JwtAuth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $jwtHelper = new JwtHelper();
        
        // Get Authorization header
        $authHeader = $request->getHeaderLine('Authorization');
        
        if (empty($authHeader)) {
            return $this->unauthorizedResponse('Authorization header missing');
        }

        // Extract token from header
        $token = $jwtHelper->extractTokenFromHeader($authHeader);
        
        if (!$token) {
            return $this->unauthorizedResponse('Invalid authorization format. Use: Bearer <token>');
        }

        // Validate token
        $decoded = $jwtHelper->validateToken($token);
        
        if (!$decoded) {
            return $this->unauthorizedResponse('Invalid or expired token');
        }

        // Store user data in request for use in controllers
        $request->jwtUser = $decoded;
        
        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No action needed after
    }

    private function unauthorizedResponse(string $message)
    {
        $response = service('response');
        return $response->setStatusCode(401)
                        ->setContentType('application/json')
                        ->setJSON([
                            'success' => false,
                            'message' => $message,
                            'error_code' => 'UNAUTHORIZED'
                        ]);
    }
}
