<?php

namespace App\Libraries;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;
use Config\App;

class JwtHelper
{
    private string $secretKey;
    private int $expirationTime;
    private string $algorithm;

    public function __construct()
    {
        $config = new App();
        $this->secretKey = $config->jwtSecretKey;
        $this->expirationTime = $config->jwtExpirationTime;
        $this->algorithm = $config->jwtAlgorithm;
    }

    /**
     * Generate JWT token for user
     */
    public function generateToken(array $userData): string
    {
        $issuedAt = time();
        $expirationTime = $issuedAt + $this->expirationTime;

        $payload = [
            'iss' => base_url(), // Issuer
            'aud' => base_url(), // Audience
            'iat' => $issuedAt, // Issued at
            'exp' => $expirationTime, // Expiration time
            'user_id' => $userData['id'],
            'email' => $userData['email'],
            'role' => $userData['role'] ?? 'customer',
            'name' => $userData['first_name'] . ' ' . $userData['last_name']
        ];

        return JWT::encode($payload, $this->secretKey, $this->algorithm);
    }

    /**
     * Validate and decode JWT token
     */
    public function validateToken(string $token): ?array
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, $this->algorithm));
            return (array) $decoded;
        } catch (ExpiredException $e) {
            log_message('error', 'JWT Token expired: ' . $e->getMessage());
            return null;
        } catch (SignatureInvalidException $e) {
            log_message('error', 'JWT Signature invalid: ' . $e->getMessage());
            return null;
        } catch (BeforeValidException $e) {
            log_message('error', 'JWT Token not valid yet: ' . $e->getMessage());
            return null;
        } catch (\Exception $e) {
            log_message('error', 'JWT Token validation error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Extract token from Authorization header
     */
    public function extractTokenFromHeader(string $authHeader): ?string
    {
        if (strpos($authHeader, 'Bearer ') === 0) {
            return substr($authHeader, 7);
        }
        return null;
    }

    /**
     * Generate refresh token
     */
    public function generateRefreshToken(int $userId): string
    {
        $issuedAt = time();
        $expirationTime = $issuedAt + (7 * 24 * 60 * 60); // 7 days

        $payload = [
            'iss' => base_url(),
            'aud' => base_url(),
            'iat' => $issuedAt,
            'exp' => $expirationTime,
            'user_id' => $userId,
            'type' => 'refresh'
        ];

        return JWT::encode($payload, $this->secretKey, $this->algorithm);
    }

    /**
     * Validate refresh token
     */
    public function validateRefreshToken(string $token): ?array
    {
        $decoded = $this->validateToken($token);
        if ($decoded && isset($decoded['type']) && $decoded['type'] === 'refresh') {
            return $decoded;
        }
        return null;
    }
}
