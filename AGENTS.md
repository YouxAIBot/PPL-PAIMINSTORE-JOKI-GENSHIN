# Paiminstore Joki Genshin

Aplikasi jasa joki Genshin Impact: aplikasi Flutter untuk pengguna dan Laravel (API + panel admin) di Docker.
Metode: RUP versi sederhana. Dokumen acuan ada di docs/ (BRD, SRS, ERD, DFD, UML) dan design/PaiminStore.fig.

## Struktur dan batas kerja
- backend/  Laravel + Docker Sail (API dan panel admin)
- mobile/   Flutter (Android 8.0 ke atas)
- design/   desain Figma
- docs/     dokumen
Ubah hanya folder yang diminta. Jangan menyentuh folder tim lain.

## Aturan umum
- Jalankan semua perintah backend lewat Docker: docker compose exec laravel.test <perintah>. Jangan memasang PHP atau Composer di komputer.
- Jangan menambah paket atau dependency tanpa persetujuan tim.
- Jangan pernah membaca, mencetak, atau commit file .env, kunci Midtrans, CREDENTIAL_KEY, atau kredensial akun game.
- Ikuti istilah baku: Pengguna, Booster, Admin/Owner, Order, Paket, Antrian.
- Ikuti kontrak API di panduan Iterasi 1. Perubahan kontrak harus disepakati dulu dengan tim lain.
- Status order: pending_payment, queued, in_progress, completed, cancelled.
- Status pembayaran: pending, paid, failed, refunded.

## Backend
- Logika bisnis ada di app/Services, bukan di controller.
- Kredensial akun game dienkripsi AES-256-GCM dan tidak pernah dikirim balik lewat API.
- Webhook Midtrans wajib memverifikasi signature dan bersifat idempoten.
- Tulis feature test untuk setiap endpoint.

## Mobile
- Token disimpan di flutter_secure_storage.
- Tampilan mengikuti mockup di design/PaiminStore.fig: bersih, satu warna aksen #2F4B8F, font Inter, daftar bergaris, bukan banyak card.
- Jalankan flutter analyze dan flutter test sebelum commit.

## Git
- Push ke main hanya untuk folder tim sendiri. Commit kecil, pesan diawali nama tim (backend:, mobile:, uiux:, docs:).
- Jalankan git pull --rebase origin main sebelum push. Jangan pernah git push --force.
