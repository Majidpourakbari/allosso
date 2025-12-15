<?php

namespace App\Services;

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Ecdsa\Sha256;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
                throw new \Exception('Apple configuration is missing. Please check your .env file. Team ID: ' . ($teamId ? 'set' : 'missing') . ', Key ID: ' . ($keyId ? 'set' : 'missing') . ', Client ID: ' . ($clientId ? 'set' : 'missing') . ', Private Key: ' . ($privateKey ? 'set' : 'missing'));
            }

            // Replace newlines if they're escaped
            $privateKey = str_replace('\\n', "\n", $privateKey);
            
            // Remove any extra whitespace
            $privateKey = trim($privateKey);
            
            // Ensure proper format
            if (!str_contains($privateKey, 'BEGIN PRIVATE KEY')) {
                $privateKey = "-----BEGIN PRIVATE KEY-----\n" . $privateKey . "\n-----END PRIVATE KEY-----";
            }

            $now = \DateTimeImmutable::createFromFormat('U', (string) time());
            
            if (!$now) {
                throw new \Exception('Failed to create DateTimeImmutable');
            }

            $configuration = Configuration::forAsymmetricSigner(
                new Sha256(),
                InMemory::plainText($privateKey),
                InMemory::empty()
            );

            $builder = $configuration->builder(ChainedFormatter::default())
                ->withHeader('kid', $keyId)
                ->issuedBy($teamId)
                ->issuedAt($now)
                ->expiresAt($now->modify('+1 hour'))
                ->withClaim('aud', 'https://appleid.apple.com')
                ->withClaim('sub', $clientId);

            $token = $builder->getToken($configuration->signer(), $configuration->signingKey());

            return $token->toString();
        } catch (\Exception $e) {
            Log::error('Apple Client Secret Generation Error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
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
        // Decode the ID token (without verification for now)
        $parts = explode('.', $idToken);
        if (count($parts) !== 3) {
            throw new \Exception('Invalid ID token format');
        }

        $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[1])), true);

        return [
            'apple_id' => $payload['sub'] ?? null,
            'email' => $payload['email'] ?? null,
            'email_verified' => $payload['email_verified'] ?? false,
            'name' => null, // Name is only provided on first authorization
        ];
    }
}
