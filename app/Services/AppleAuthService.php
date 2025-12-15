<?php

namespace App\Services;

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Ecdsa\Sha256;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Illuminate\Support\Facades\Http;

class AppleAuthService
{
    /**
     * Generate Apple Client Secret (JWT token)
     */
    public function generateClientSecret(): string
    {
        try {
            $teamId = config('services.apple.team_id');
            $keyId = config('services.apple.key_id');
            $clientId = config('services.apple.client_id');
            $privateKey = config('services.apple.private_key');

            if (!$teamId || !$keyId || !$clientId || !$privateKey) {
                $missing = [];
                if (!$teamId) $missing[] = 'APPLE_TEAM_ID';
                if (!$keyId) $missing[] = 'APPLE_KEY_ID';
                if (!$clientId) $missing[] = 'APPLE_CLIENT_ID';
                if (!$privateKey) $missing[] = 'APPLE_PRIVATE_KEY';
                throw new \Exception('Apple configuration is missing: ' . implode(', ', $missing) . '. Please check your .env file.');
            }

            // Replace newlines if they're escaped
            $privateKey = str_replace('\\n', "\n", $privateKey);
            
            // Remove any existing headers/footers if present
            $privateKey = preg_replace('/-----BEGIN PRIVATE KEY-----/', '', $privateKey);
            $privateKey = preg_replace('/-----END PRIVATE KEY-----/', '', $privateKey);
            $privateKey = trim($privateKey);
            
            // Ensure proper format
            $privateKey = "-----BEGIN PRIVATE KEY-----\n" . $privateKey . "\n-----END PRIVATE KEY-----";

            $now = \DateTimeImmutable::createFromFormat('U', (string) time());
            
            $configuration = Configuration::forAsymmetricSigner(
                new Sha256(),
                InMemory::plainText($privateKey),
                InMemory::empty()
            );

            $token = $configuration->builder(ChainedFormatter::default())
                ->withHeader('kid', $keyId)
                ->issuedBy($teamId)
                ->issuedAt($now)
                ->expiresAt($now->modify('+1 hour'))
                ->withClaim('aud', 'https://appleid.apple.com')
                ->withClaim('sub', $clientId)
                ->getToken($configuration->signer(), $configuration->signingKey());

            return $token->toString();
        } catch (\Exception $e) {
            \Log::error('Apple generateClientSecret error', [
                'message' => $e->getMessage(),
                'has_team_id' => !empty(config('services.apple.team_id')),
                'has_key_id' => !empty(config('services.apple.key_id')),
                'has_client_id' => !empty(config('services.apple.client_id')),
                'has_private_key' => !empty(config('services.apple.private_key')),
            ]);
            throw $e;
        }
    }

    /**
     * Get authorization URL
     */
    public function getAuthorizationUrl(string $state = null): string
    {
        $clientId = config('services.apple.client_id');
        $redirectUri = config('services.apple.redirect');
        $state = $state ?? bin2hex(random_bytes(16));

        $params = [
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'response_mode' => 'form_post',
            'scope' => 'name email',
            'state' => $state,
        ];

        return 'https://appleid.apple.com/auth/authorize?' . http_build_query($params);
    }

    /**
     * Exchange authorization code for tokens
     */
    public function getTokens(string $code): array
    {
        $clientId = config('services.apple.client_id');
        $clientSecret = $this->generateClientSecret();
        $redirectUri = config('services.apple.redirect');

        $response = Http::asForm()->post('https://appleid.apple.com/auth/token', [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'code' => $code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $redirectUri,
        ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to exchange code for tokens: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Get user info from ID token
     */
    public function getUserInfo(string $idToken): array
    {
        try {
            // Decode the ID token (without verification for now)
            $parts = explode('.', $idToken);
            if (count($parts) !== 3) {
                throw new \Exception('Invalid ID token format: Expected 3 parts, got ' . count($parts));
            }

            // Decode payload with proper base64 URL decoding
            $payloadEncoded = $parts[1];
            $payloadEncoded = str_replace(['-', '_'], ['+', '/'], $payloadEncoded);
            
            // Add padding if needed
            $padding = strlen($payloadEncoded) % 4;
            if ($padding > 0) {
                $payloadEncoded .= str_repeat('=', 4 - $padding);
            }
            
            $payloadDecoded = base64_decode($payloadEncoded, true);
            if ($payloadDecoded === false) {
                throw new \Exception('Failed to decode ID token payload');
            }
            
            $payload = json_decode($payloadDecoded, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Failed to parse ID token JSON: ' . json_last_error_msg());
            }

            if (!isset($payload['sub'])) {
                throw new \Exception('Missing sub claim in ID token');
            }

            return [
                'apple_id' => $payload['sub'],
                'email' => $payload['email'] ?? null,
                'email_verified' => $payload['email_verified'] ?? false,
                'name' => null, // Name is only provided on first authorization
            ];
        } catch (\Exception $e) {
            \Log::error('Apple getUserInfo error', [
                'message' => $e->getMessage(),
                'id_token_length' => strlen($idToken),
            ]);
            throw $e;
        }
    }
}
