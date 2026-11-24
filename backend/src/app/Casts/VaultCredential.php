<?php

namespace App\Casts;

use App\Services\CredentialVault;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

/**
 * Cast kolom credential_encrypted: array {username, password} <-> ciphertext CredentialVault.
 *
 * Kolom bertipe binary: PostgreSQL mengembalikannya sebagai stream, SQLite sebagai string.
 */
class VaultCredential implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?array
    {
        if ($value === null) {
            return null;
        }

        if (is_resource($value)) {
            $value = stream_get_contents($value);
        }

        return app(CredentialVault::class)->decrypt((string) $value);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        if (! is_array($value) || ! isset($value['username'], $value['password'])) {
            throw new InvalidArgumentException('Kredensial harus berupa array {username, password}.');
        }

        return app(CredentialVault::class)->encrypt($value);
    }
}
