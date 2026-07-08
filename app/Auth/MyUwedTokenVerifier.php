<?php

namespace App\Auth;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use RuntimeException;

class MyUwedTokenVerifier
{
    private const LEEWAY = 60;

    public function __construct(
        private readonly ?string $secret,
        private readonly ?string $issuer,
    ) {}

    /**
     * @throws RuntimeException
     */
    public function verify(string $token): MyUwedClaims
    {
        if (empty($this->secret)) {
            throw new RuntimeException('my.uwed.uz secret sozlanmagan.');
        }

        JWT::$leeway = self::LEEWAY;
        $payload = JWT::decode($token, new Key($this->secret, 'HS256'));


        if (! isset($payload->exp)) {
            throw new RuntimeException('Token "exp" claim\'isiz.');
        }

        if (! empty($this->issuer) && ($payload->iss ?? null) !== $this->issuer) {
            throw new RuntimeException('Token "iss" mos emas.');
        }

        if (! isset($payload->sub, $payload->username)) {
            throw new RuntimeException('Token majburiy claim\'larsiz.');
        }

        return new MyUwedClaims(
            staffId: (string) $payload->sub,
            fullName: (string) ($payload->fullName ?? ''),
            email: (string) $payload->username,
            permissions: array_values((array) ($payload->permissions ?? [])),
            token: $token,
        );
    }
}
