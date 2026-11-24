# Backend Paiminstore Joki Genshin

Laravel 12 + Filament (panel admin) + PostgreSQL, berjalan di Docker. Kode Laravel ada di `src/`. Semua perintah dijalankan dari folder `backend/` di PowerShell.

## Servis Docker

| Servis | Fungsi | Port di laptop |
| --- | --- | --- |
| `php` | PHP-FPM, tempat semua perintah `artisan` dan `composer` dijalankan | - |
| `nginx` | Web server | 80 dan 443 (https, panel admin), 8000 (http, API untuk Mobile). Bisa diganti lewat `.env` |
| `db` | PostgreSQL 16 | 15432 (bisa diganti lewat `.env`) |

## Pertama kali

1. Salin pengaturan Docker:

```powershell
copy .env.example .env
```

2. Jalankan container (pertama kali agak lama karena `composer install`):

```powershell
docker compose up -d --build
docker compose logs -f php
```

Tunggu sampai log berhenti dan tidak ada error, lalu tekan `Ctrl+C` (container tetap jalan).

3. Isi kunci di `src/.env` (file ini tidak boleh di-commit):

```powershell
docker compose exec php php -r "echo 'base64:'.base64_encode(random_bytes(32)).PHP_EOL;"
```

Tempel hasilnya sebagai `CREDENTIAL_KEY=...` di `src/.env`. Isi juga `MIDTRANS_SERVER_KEY` dan `MIDTRANS_CLIENT_KEY` dari akun Sandbox tim. Setelah mengubah `.env`:

```powershell
docker compose exec php php artisan config:clear
```

4. Buat tabel dan data awal:

```powershell
docker compose exec php php artisan migrate:fresh --seed
```

Kalau ada port yang sudah dipakai program lain (error `port is already allocated`), ganti di `backend/.env` lalu jalankan `docker compose up -d` lagi:

```text
HTTP_PORT=8080
HTTPS_PORT=8443
API_PORT=8001
DB_PORT=15433
```

Kalau `API_PORT` diganti, jalankan Mobile dengan `--dart-define=API_URL=http://10.0.2.2:<port>/api/v1`.

## Setelah menarik perubahan (git pull)

Kalau ada paket baru di `src/composer.json`:

```powershell
docker compose exec php composer install
```

Khusus penarikan pertama yang menambahkan Laravel Sanctum, `composer.lock` perlu diperbarui sekali, lalu di-commit. Ini juga dikerjakan otomatis saat container `php` dimulai ulang (`docker compose restart php`), jadi cukup cek `git status` dan commit `src/composer.lock`. Cara manual:

```powershell
docker compose exec php composer update laravel/sanctum
```

Kalau ada migrasi baru: `docker compose exec php php artisan migrate`.

## Akses

| Apa | Alamat |
| --- | --- |
| Panel admin | https://beckend.test/admin |
| API untuk Mobile (emulator Android) | http://10.0.2.2:8000/api/v1 |
| API dari laptop | http://localhost:8000/api/v1 |
| Database | `localhost:15432`, nama `beckend`, user `postgres`, password `secret` |

Panel admin memakai alamat `beckend.test`. Tambahkan baris ini di `C:\Windows\System32\drivers\etc\hosts` (Notepad dibuka sebagai Administrator):

```text
127.0.0.1 beckend.test
```

Browser akan memperingatkan sertifikat karena dibuat sendiri. Pilih lanjutkan.

## Akun dari seeder

Semua password `password`. Ganti sebelum dipakai di luar laptop sendiri.

| Email | Peran |
| --- | --- |
| `admin@admin.com` | admin (super_admin) |
| `user@admin.com` | customer |
| `booster1@paiminstore.test`, `booster2@paiminstore.test` | booster |

Seeder katalog berisi 3 kategori dan 10 paket yang sama dengan data tiruan di aplikasi Mobile. Seeder aman dijalankan berulang.

## Endpoint API v1

Semua jawaban JSON. Bentuk data: `{"data": ...}`. Kesalahan validasi: 422 dengan `message` dan `errors`. Harga selalu integer rupiah.

| Metode | Path | Auth | Fungsi |
| --- | --- | --- | --- |
| POST | `/auth/register` | - | Daftar (name, email, password, password_confirmation) |
| POST | `/auth/login` | - | Login, dijawab `data.token` dan `data.user` |
| POST | `/auth/logout` | Bearer | Cabut token yang dipakai |
| GET | `/me` | Bearer | Profil |
| PATCH | `/me` | Bearer | Ubah name dan email |
| PUT | `/me/password` | Bearer | Ganti password (current_password, password, password_confirmation) |
| GET | `/categories` | Bearer | Kategori aktif |
| GET | `/categories/{slug}/packages` | Bearer | Paket aktif dalam kategori |
| GET | `/packages/{id}` | Bearer | Detail paket |
| POST | `/payments/midtrans/webhook` | signature | Notifikasi Midtrans |

Belum ada: `/orders`, `/orders/{no}`, `/orders/{no}/queue`, `/orders/{no}/payments/gateway`, `/orders/{no}/payment`. Pengguna berstatus `banned` ditolak di login dan di setiap permintaan (token dicabut).

Pembayaran: URL selesai Midtrans harus mengandung `/payment/finish` (gagal: `/payment/error`) karena aplikasi Mobile membaca URL itu.

## Test

```powershell
docker compose exec php php artisan test
```

## Masalah umum

| Gejala | Penyebab dan jalan keluar |
| --- | --- |
| `laravel.log could not be opened ... Permission denied` | File dibuat oleh root. Jalankan `docker compose restart php` (izin diperbaiki saat start). Untuk perintah yang menulis file, pakai `docker compose exec -u www-data php ...` |
| Port 80, 443, atau 8000 sudah dipakai | Hentikan program lain yang memakainya (misalnya IIS atau Laragon), lalu `docker compose up -d` lagi |
| `Class "Laravel\Sanctum\HasApiTokens" not found` | Jalankan `docker compose exec php composer update laravel/sanctum` |
| `Order API: CREDENTIAL_KEY belum diset` | Isi `CREDENTIAL_KEY` di `src/.env`, lalu `config:clear` |
| Mobile gagal konek | Pastikan `docker compose ps` menunjukkan `nginx` Running dan buka `http://localhost:8000/up` |
