# Paiminstore Mobile (Flutter)

Aplikasi Android untuk Pengguna. Tampilan mengikuti design/PaiminStore.fig (halaman "PaiminStore Mockup", layar MK_01 sampai MK_12). Hanya ubah folder mobile/.
Kontrak API dan layar per iterasi ada di panduan Iterasi 1 (Backend dan Mobile).

## Tumpukan teknologi
- State: flutter_riverpod. Navigasi: go_router. HTTP: dio. Token: flutter_secure_storage.
- Font Inter (google_fonts). Format rupiah dan tanggal: intl. Lampiran: image_picker. Halaman Midtrans Snap: webview_flutter.
- Target Android 8.0 (API 26) ke atas. Layout fleksibel untuk lebar 360 dp, jangan ukuran tetap.
- Jangan menambah paket tanpa persetujuan tim.

## Struktur
lib/core/   theme, network, storage, router, widgets
lib/features/   auth, catalog, order, payment, profile (masing-masing data/ dan presentation/)

## Aturan kode
- Data lewat repository. Selama API belum siap pakai versi Fake dengan data dari desain. Pilih lewat --dart-define=USE_FAKE=false dan --dart-define=API_URL=http://10.0.2.2:8000/api/v1.
- Base URL API: /api/v1. Header Accept: application/json, token Bearer di setiap permintaan terautentikasi.
- Uang dikirim sebagai angka bulat rupiah. Tampilkan "Rp 175.000" di sisi aplikasi. Waktu dari API berformat UTC, ubah ke waktu lokal.
- Hitung mundur 24 jam memakai expires_at dari server, bukan jam perangkat.
- Kredensial akun game hanya dikirim saat membuat order dan tidak pernah disimpan di perangkat.
- Respons 401 menghapus token dan kembali ke Login. Galat 422 ditampilkan di bawah tiap kolom.
- Setiap layar punya empat keadaan: memuat, kosong, galat dengan tombol coba lagi, dan data.

## Tampilan
- Warna: aksen #2F4B8F, teks #0F172A, teks sekunder #64748B, latar #F8FAFC, garis #E2E8F0, merah #DC2626 (dibatalkan), kuning #F59E0A (peringatan). Sudut membulat 12.
- Daftar bergaris dengan pemisah tipis, bukan card di setiap baris. Satu tombol utama lebar penuh di bawah tiap layar.
- Label status: pending_payment = Menunggu Pembayaran, queued = Menunggu Antrian, in_progress = Dikerjakan, completed = Selesai, cancelled = Dibatalkan.
- Iterasi 1 tidak memuat: Chat, transfer manual, invoice, review, Leaderboard, opsi Tema, Mode Anonim.

## Android
- Izin INTERNET harus ada di android/app/src/main/AndroidManifest.xml. minSdk 26.
- HTTP biasa hanya untuk debug (usesCleartextTraffic di manifest debug). Produksi wajib HTTPS.
- Jangan commit build/, keystore, atau kunci apa pun.

## Sebelum commit
flutter analyze dan flutter test harus lolos.
