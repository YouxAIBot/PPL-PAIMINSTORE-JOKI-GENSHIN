<?php

namespace App\Services;

use RuntimeException;

/**
 * Enkripsi kredensial akun game pelanggan dengan AES-256-GCM.
 *
 * Kunci terpisah dari APP_KEY: CREDENTIAL_KEY (32 byte, format "base64:...").
 * Format simpanan: "v1:" . base64(iv[12] . tag[16] . ciphertext)
 * Isi plaintext: JSON {"username": "...", "password": "..."}
 */
class CredentialVault
{
    private const CIPHER = 'aes-256-gcm';
    private const IV_LENGTH = 12;
    private const TAG_LENGTH = 16;
    private const PREFIX = 'v1:';

    private string $key;

    public function __construct(?string $key = null)
    {
        $key ??= (string) config('services.credential.key');

        if (str_starts_with($key, 'base64:')) {
            $key = base64_decode(substr($key, 7), true) ?: '';
        }

        if (strlen($key) !== 32) {
            throw new RuntimeException('CREDENTIAL_KEY belum diset atau panjangnya bukan 32 byte.');
        }

        $this->key = $key;
    }

    /** @param array{username: string, password: string} $credential */
    public function encrypt(array $credential): string
    {
        $plaintext = json_encode([
            'username' => (string) ($credential['username'] ?? ''),
            'password' => (string) ($credential['password'] ?? ''),
        ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);

        $iv = random_bytes(self::IV_LENGTH);
        $tag = '';
        $ciphertext = openssl_encrypt($plaintext, self::CIPHER, $this->key, OPENSSL_RAW_DATA, $iv, $tag, '', self::TAG_LENGTH);

        if ($ciphertext === false) {
            throw new RuntimeException('Gagal mengenkripsi kredensial.');
        }

        return self::PREFIX.base64_encode($iv.$tag.$ciphertext);
    }

    /** @return array{username: string, password: string} */
    public function decrypt(string $payload): array
    {
        if (! str_starts_with($payload, self::PREFIX)) {
            throw new RuntimeException('Format kredensial tidak dikenal.');
        }

        $raw = base64_decode(substr($payload, strlen(self::PREFIX)), true);

        if ($raw === false || strlen($raw) <= self::IV_LENGTH + self::TAG_LENGTH) {
            throw new RuntimeException('Data kredensial rusak.');
        }

        $iv = substr($raw, 0, self::IV_LENGTH);
        $tag = substr($raw, self::IV_LENGTH, self::TAG_LENGTH);
        $ciphertext = substr($raw, self::IV_LENGTH + self::TAG_LENGTH);

        $plaintext = openssl_decrypt($ciphertext, self::CIPHER, $this->key, OPENSSL_RAW_DATA, $iv, $tag);

        if ($plaintext === false) {
            throw new RuntimeException('Gagal mendekripsi kredensial (kunci salah atau data diubah).');
        }

        $data = json_decode($plaintext, true, flags: JSON_THROW_ON_ERROR);

        return [
            'username' => (string) ($data['username'] ?? ''),
            'password' => (string) ($data['password'] ?? ''),
        ];
    }
}
