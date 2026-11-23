# Planning Iterasi Paiminstore Joki Genshin

Metode: RUP versi sederhana. Batas pengumpulan: akhir minggu 14.
Sumber: gantt chart (docs/gantt-chart), gap analysis, BRD dan SRS.
Cara pakai: ganti `[ ]` jadi `[x]` kalau sudah selesai, lalu commit dengan awalan `docs:`.

Ringkasan fase Construction:

| Iterasi | Fokus | Status |
|---|---|---|
| 1 | Autentikasi, katalog, order, pembayaran, antrian, panel admin | Sedang berjalan |
| 2 | Chat, notifikasi push, transfer manual, refund, invoice | Belum mulai |
| 3 | Leaderboard, laporan keuangan, review, log aktivitas | Belum mulai |
| Transition | Pengujian, perbaikan, UAT, deployment, finalisasi dokumen | Belum mulai |

---

## Iterasi 1: inti pemesanan dan panel admin

### Mobile (Firaas)

| Selesai | Pekerjaan | Layar | Catatan |
|---|---|---|---|
| [x] | Setup proyek Flutter, package, struktur folder, tema | - | Font Inter, warna aksen #2F4B8F |
| [x] | Repository tiruan (fake) | - | `USE_FAKE=true` |
| [x] | Daftar dan login | MK_01 | Token di secure storage |
| [x] | Beranda, daftar paket, detail paket | MK_02 sampai MK_04 | |
| [x] | Form order dan validasi | MK_05 | UID, server, kredensial, disclaimer |
| [x] | Pembayaran WebView Midtrans Snap | MK_06 | |
| [x] | Tab Pesanan, detail order, halaman tunggu antrean | MK_07, MK_09 | Polling 30 detik |
| [x] | Profil, ubah profil, ganti password | - | |
| [x] | Unit test dan widget test | - | `flutter test` |
| [x] | Repository API siap (`ApiXxxRepository`) | - | Tinggal sambung ke Backend |
| [ ] | Uji alur lengkap dengan API asli (Langkah 8) | - | Menunggu API Backend |
| [ ] | `flutter analyze` bersih dan `flutter test` lulus pada versi final | - | |

### Backend (tim Backend)

| Selesai | Pekerjaan | Pemegang | Catatan |
|---|---|---|---|
| [x] | Migrasi 10 tabel | Lead Backend | Sesuai ERD |
| [ ] | Model dan relasi Eloquent | Lead Backend | Perlu dicek di repo |
| [x] | Seeder kategori, paket, akun | Lead Backend | DemoDataSeeder perlu dibuat idempotent |
| [ ] | Auth API: register, login, logout, profil (`/auth/*`, `/me`) | Anggota A | |
| [ ] | Katalog API (`/categories`, `/packages`) | Anggota A | Harga integer |
| [x] | CredentialVault (enkripsi AES-256-GCM) | - | Sudah ada |
| [ ] | Order API (`/orders`, antrean) | Anggota B | |
| [ ] | Pembatalan otomatis order belum dibayar 24 jam | Anggota B | AB-07 |
| [ ] | Midtrans Snap (`/orders/{no}/payments/gateway`) | Anggota C | URL selesai harus berisi `/payment/finish` |
| [ ] | Webhook Midtrans (signature SHA-512, idempoten) | Anggota C | FS-02 |
| [ ] | Feature test auth, order, webhook | Masing-masing | |
| [ ] | Pasang Sanctum token dan cek status banned di API | Anggota A | |

### Panel admin (Zefhana)

| Selesai | Pekerjaan | Catatan |
|---|---|---|
| [x] | Login admin dan dashboard | Merge PR #1 |
| [x] | CRUD kategori dan paket | |
| [x] | Kelola order: status, batal, assign booster, prioritas | |
| [x] | Lihat kredensial game (terdekripsi lewat CredentialVault) | |
| [x] | Verifikasi pembayaran dan refund | |
| [x] | Kelola user: ban, unban, reset password | |
| [x] | Daftar booster | |
| [x] | Perbaikan hasil review (CredentialVault, enum banned, guard batal) | Commit 9f21d0c |
| [ ] | Test panel admin berjalan hijau di komputer anggota lain | |

### Syarat Iterasi 1 selesai

- [ ] `docker compose up -d` dan `migrate --seed` jalan di komputer anggota lain
- [ ] Semua endpoint kontrak API jalan dan `php artisan test` lulus
- [ ] Kredensial game tersimpan terenkripsi, tidak muncul di respons, log, atau git
- [ ] Pembayaran Sandbox mengubah order dari `pending_payment` ke `queued` otomatis
- [ ] Order belum dibayar lebih dari 24 jam dibatalkan penjadwal
- [ ] Mobile menjalankan alur lengkap dengan API asli

---

## Iterasi 2: komunikasi dan pembayaran lanjutan

| Selesai | Pekerjaan | Bagian | Kode kebutuhan |
|---|---|---|---|
| [ ] | Chat real-time user dan admin (Reverb) | Backend, Mobile, Admin | Proses 5.0 |
| [ ] | Riwayat chat dan inbox admin | Backend, Admin | |
| [ ] | Tandai terbaca dan hitung unread | Backend | |
| [ ] | Notifikasi push (FCM) | Backend, Mobile | |
| [ ] | Email notifikasi pesan baru | Backend | |
| [ ] | Transfer manual: upload bukti bayar | Mobile, Backend | FU-09 |
| [ ] | Verifikasi atau tolak bukti di panel admin | Admin | FU-11 |
| [ ] | Proses refund lewat Midtrans | Backend, Admin | Proses 3.5 |
| [ ] | Invoice PDF | Backend, Mobile | Proses 2.5 |
| [ ] | Lampiran file (foto) di order dan chat, simpan di R2 | Backend | |
| [ ] | Penghapusan otomatis kredensial 7 hari setelah selesai | Backend | FS-04 |
| [ ] | Test fitur iterasi 2 | Semua | |

Syarat selesai:
- [ ] Pesan chat sampai real-time dan push masuk ke HP
- [ ] Bukti transfer manual bisa diverifikasi dari panel admin
- [ ] Refund mengubah status payment dan order dengan benar
- [ ] Invoice PDF bisa diunduh

---

## Iterasi 3: laporan dan penutup fitur

| Selesai | Pekerjaan | Bagian | Kode kebutuhan |
|---|---|---|---|
| [ ] | Review dan rating setelah order selesai | Mobile, Backend | Proses 2.6 |
| [ ] | Leaderboard (periode, anonimisasi, kurangi refund) | Mobile, Backend | Proses 6.0 |
| [ ] | Pengaturan tampil di leaderboard (`show_on_leaderboard`) | Mobile, Backend | |
| [ ] | Laporan keuangan mingguan dan grafik tren | Admin | Proses 7.0 |
| [ ] | Breakdown pendapatan per kategori | Admin | |
| [ ] | Ekspor laporan PDF dan Excel | Admin | |
| [ ] | Log aktivitas (activity_logs) tampil di panel admin | Admin | |
| [ ] | Test fitur iterasi 3 | Semua | |

Syarat selesai:
- [ ] Leaderboard tampil sesuai periode dan nama anonim jika dipilih
- [ ] Laporan mingguan cocok dengan data pembayaran
- [ ] Ekspor PDF dan Excel bisa dibuka

---

## Transition: pengujian dan penyerahan

| Selesai | Pekerjaan | Catatan |
|---|---|---|
| [ ] | Pengujian menyeluruh (fungsional, API, mobile) | Lampirkan hasil `flutter test` dan `php artisan test` |
| [ ] | Perbaikan bug hasil pengujian | |
| [ ] | UAT dengan pengguna atau owner | Catat hasil dan masukan |
| [ ] | Deployment | |
| [ ] | Finalisasi dokumen (BRD, SRS, ERD, DFD, UML sesuai kondisi akhir) | |
| [ ] | Presentasi dan demo | Siapkan skenario demo |
| [ ] | Pengumpulan | Batas akhir minggu 14 |

---

## Risiko yang perlu dipantau

| Risiko | Dampak | Penanganan |
|---|---|---|
| API Backend belum jadi, Mobile menunggu | Langkah 8 tertunda | Dorong auth dan katalog dulu agar login dan beranda bisa langsung dites |
| Kontrak API berubah sepihak | Mobile rusak | Perubahan dikabarkan dulu ke tim lain |
| Seeder tidak idempotent | Error saat `migrate --seed` ulang | Pakai `migrate:fresh --seed` atau perbaiki seeder |
| Deadline minggu 14 mepet | Iterasi 2 dan 3 tidak selesai | Tentukan fitur wajib dan opsional lebih awal |
