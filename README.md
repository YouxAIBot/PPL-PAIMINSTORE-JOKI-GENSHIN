README Paiminstore Joki Genshin
Nov 2, 2026 · @Youx
Dokumen ini adalah pintu masuk tim ke seluruh berkas analisis dan perancangan Paiminstore Joki Genshin. Mulai dari sini untuk tahu dokumen mana yang dipakai, istilah apa yang disepakati, dan apa yang sudah selesai.
Tentang proyek
Paiminstore Joki Genshin adalah aplikasi jasa joki (boosting) Genshin Impact. Pengguna memesan dan membayar lewat aplikasi Android. Admin dan booster mengelola order lewat panel web.
• Mata kuliah: CIE515 Pengembangan Perangkat Lunak (dosen: Qori Halimatul Hidayah).
• Metode: RUP versi sederhana (prototype tidak diperbolehkan dosen).
• Wawancara dan analisis masalah sampai minggu 6, pengumpulan akhir di minggu 14.
Teknologi dan peran
Bagian
Teknologi
Aplikasi pengguna
Flutter (Android Studio)
Admin panel
Laravel, berjalan di Docker
Database
PostgreSQL
Cache
Redis
Pembayaran
Midtrans (Snap)
Notifikasi
Firebase Cloud Messaging (FCM)
Chat real-time
Laravel Reverb
Peran
Akses
Fungsi
Pengguna
Aplikasi Android
Memesan joki, membayar, memantau antrian, chat, memberi review
Admin / Owner
Panel web
Kelola katalog, verifikasi pembayaran, tugaskan booster, balas chat, lihat laporan
Booster
Panel web
Mengerjakan order yang ditugaskan dan menyelesaikannya
Daftar dokumen dan file
BRD adalah sumber utama kebutuhan. Dokumen lain harus konsisten dengan BRD.
Dokumen online
Dokumen
Isi
BRD
Kebutuhan bisnis, kebutuhan fungsional dan nonfungsional, aturan bisnis, metode RUP
Perancangan Aplikasi
Analisis awal, ERD, DDL, sitemap, alur pengguna, asumsi
Lembar Tugas PPL
Gap analysis, lembar analisis kebutuhan (Sesi 6), model FSD (Sesi 7)
Diagram (file draw.io)
File
Isi
dfd-level0-context.drawio
DFD Level 0 (context diagram)
dfd-level1.drawio
DFD Level 1, 8 proses
dfd-level2-1-autentikasi.drawio sampai dfd-level2-8-konten.drawio
DFD Level 2, satu file per proses 1.0 sampai 8.0
erd-paiminstore.drawio
ERD 10 tabel, notasi crow's foot
usecase-paiminstore.drawio
Use case diagram
flowchart-activity-paiminstore.drawio
Diagram alir (activity diagram berlajur)
class-diagram-paiminstore.drawio
Class diagram
sequence-order-paiminstore.drawio
Sequence diagram order
sequence-pembayaran-paiminstore.drawio
Sequence diagram pembayaran
gantt-paiminstore.drawio
Jadwal pengerjaan (Gantt)
Cara membuka file draw.io
1. Buka app.diagrams.net lalu pilih File, Open from, Device. Atau pasang ekstensi draw.io di VS Code dan buka file .drawio langsung.
2. Pilih file .drawio dari folder diagram.
3. Untuk ekspor: File, Export as, lalu pilih PNG atau PDF untuk slide dan laporan.
Saran susunan folder repositori:
docs/
  README.md
  diagram/
    dfd/        (10 file DFD)
    erd/        (erd-paiminstore.drawio)
    uml/        (use case, activity, class, sequence)
    gantt/
Semua garis di DFD dan ERD sengaja dibuat siku-siku. Saat menggeser kotak di draw.io, garisnya ikut, tetapi bisa perlu dirapikan lagi.
Istilah baku
Pakai istilah ini di semua dokumen, diagram, dan kode supaya tidak berbeda antaranggota.
Istilah
Arti
Jangan dipakai
Pengguna
Orang yang memesan joki lewat aplikasi
Pelanggan, customer
Booster
Orang yang mengerjakan order
Worker, joki
Admin / Owner
Pengelola panel dan pemilik usaha
-
Order
Satu pesanan joki, bernomor order_no (juga nomor invoice)
-
Paket
Jenis joki yang dijual, ada di dalam kategori
-
Antrian
Order berstatus queued, urut menurut prioritas lalu waktu bayar
-
Objek
Nilai status
Order
pending_payment, queued, in_progress, completed, cancelled
Payment
pending, paid, failed, refunded
Metode bayar
gateway, manual
Metode dan jadwal
Proyek memakai RUP versi sederhana. Rincian fase dan alasan memilihnya ada di bagian 12 BRD, dan jadwal per minggu ada di file gantt-paiminstore.drawio.
Fase RUP
Isi pekerjaan
Inception
Wawancara, analisis masalah dan kebutuhan, BRD, gap analysis
Elaboration
Model analisis dan desain: DFD, ERD, UML, rancangan arsitektur
Construction
Pengembangan bertahap dalam 3 iterasi, prioritas Tinggi lebih dulu
Transition
Pengujian penerimaan (UAT), perbaikan, serah terima
• Minggu 5: mulai mendokumentasikan masalah yang dialami stakeholder.
• Sampai minggu 6: wawancara dan analisis masalah dan kebutuhan selesai.
• Minggu 14: batas akhir pengumpulan.
Aturan kerja dan status dokumen
Aturan ini usulan awal; tim boleh mengubahnya.
1. Kebutuhan berubah: ubah BRD lebih dulu, lalu sesuaikan diagram yang terkait.
2. Skema database berubah: perbarui DDL, ERD, dan class diagram sekaligus.
3. Pakai istilah baku di atas, dan kode kebutuhan (FU, FS, NF, AB) tidak diganti setelah dipakai.
4. Beri tahu tim sebelum mengubah dokumen yang dipakai bersama.
Dokumen
Status
Catatan
BRD
Draf lengkap
Bagian 12 memuat metode RUP
Perancangan aplikasi (ERD, DDL)
Selesai
ERD juga tersedia sebagai file draw.io
DFD Level 0 sampai 2
Selesai
Level 1 dan Level 2 proses 1.0 sudah disederhanakan
ERD draw.io
Selesai
10 tabel, 14 relasi
UML (use case, activity, class, sequence)
Selesai
Istilah aktor sudah disamakan
Gantt
Selesai
Belum dimasukkan ke BRD
Gap analysis dan Sesi 6, Sesi 7
Selesai
Gap analysis direvisi
Konfirmasi As-Is dengan owner
Selesai
Disetujui owner
Sequence diagram chat
Belum
Opsional
