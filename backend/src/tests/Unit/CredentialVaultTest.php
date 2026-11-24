<?php

use App\Services\CredentialVault;

function vaultKey(): string
{
    return 'base64:' . base64_encode(random_bytes(32));
}

it('mengenkripsi lalu mendekripsi kembali kredensial yang sama', function () {
    $vault = new CredentialVault(vaultKey());

    $payload = $vault->encrypt(['username' => 'akun_genshin', 'password' => 'rahasia 123']);

    expect($vault->decrypt($payload))->toBe([
        'username' => 'akun_genshin',
        'password' => 'rahasia 123',
    ]);
});

it('tidak menyimpan kredensial sebagai teks biasa', function () {
    $payload = (new CredentialVault(vaultKey()))->encrypt(['username' => 'akun_genshin', 'password' => 'rahasia 123']);

    expect($payload)->toStartWith('v1:')
        ->and($payload)->not->toContain('akun_genshin')
        ->and($payload)->not->toContain('rahasia');
});

it('menghasilkan ciphertext berbeda untuk data yang sama', function () {
    $vault = new CredentialVault(vaultKey());
    $credential = ['username' => 'a', 'password' => 'b'];

    expect($vault->encrypt($credential))->not->toBe($vault->encrypt($credential));
});

it('menolak data yang diubah', function () {
    $vault = new CredentialVault(vaultKey());
    $payload = $vault->encrypt(['username' => 'a', 'password' => 'b']);

    $vault->decrypt(substr($payload, 0, -4) . 'AAAA');
})->throws(RuntimeException::class);

it('menolak kunci yang salah', function () {
    $payload = (new CredentialVault(vaultKey()))->encrypt(['username' => 'a', 'password' => 'b']);

    (new CredentialVault(vaultKey()))->decrypt($payload);
})->throws(RuntimeException::class);

it('menolak kunci yang panjangnya bukan 32 byte', function () {
    new CredentialVault('base64:' . base64_encode('pendek'));
})->throws(RuntimeException::class);
