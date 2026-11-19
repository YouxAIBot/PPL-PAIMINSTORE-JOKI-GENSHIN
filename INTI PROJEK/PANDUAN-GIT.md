# Panduan Git untuk Tim

Panduan ini menjelaskan cara tiap tim mengirim (push) hasil kerjanya ke repo GitHub bersama. Baca sampai habis sebelum push pertama.

Repo: https://github.com/YouxAIBot/PPL-PAIMINSTORE-JOKI-GENSHIN

## Aturan utama

1. Semua tim memakai **satu repo**.
2. **Jangan kerja langsung di branch `main`.** Setiap tugas dikerjakan di branch sendiri.
3. Hasil kerja masuk ke `main` lewat **Pull Request**, bukan push langsung.
4. Setiap tim hanya mengubah **folder timnya sendiri**.

## Pembagian folder

| Tim | Folder | Isi |
| --- | --- | --- |
| Dokumentasi / Analyst | `docs/` | BRD, SRS, DFD, ERD, UML, laporan |
| UI/UX | `design/` | Wireframe, desain layar, file ekspor desain |
| Backend | `backend/` | Laravel API, database, integrasi |
| Mobile | `mobile/` | Aplikasi Flutter untuk pengguna |
| Admin Panel | `admin-panel/` | Panel web untuk Admin dan Booster |

Kalau perlu mengubah folder tim lain, kabari tim tersebut dulu.

## Persiapan awal (sekali saja)

1. Pastikan Git sudah terpasang: buka PowerShell, ketik `git --version`.
2. Terima undangan kolaborator dari GitHub (cek email).
3. Clone repo:

```powershell
cd C:\Users\NAMA\Documents
git clone https://github.com/YouxAIBot/PPL-PAIMINSTORE-JOKI-GENSHIN.git
cd PPL-PAIMINSTORE-JOKI-GENSHIN
```

4. Atur identitas:

```powershell
git config user.name "Nama Kamu"
git config user.email "email-github-kamu@gmail.com"
```

5. Saat push pertama, akan muncul jendela login GitHub di browser. Login dengan akun yang sudah diundang.

## Alur kerja harian

### 1. Ambil versi terbaru dan buat branch

```powershell
git checkout main
git pull
git checkout -b backend/api-order
```

Format nama branch: `tim/nama-tugas`. Contoh:

| Tim | Contoh nama branch |
| --- | --- |
| UI/UX | `uiux/layar-checkout` |
| Backend | `backend/api-order` |
| Mobile | `mobile/halaman-login` |
| Admin Panel | `admin/verifikasi-pembayaran` |
| Dokumentasi | `docs/revisi-srs` |

### 2. Kerjakan tugas, lalu simpan perubahan

```powershell
git add .
git commit -m "backend: tambah endpoint buat order"
```

Awali pesan commit dengan nama tim: `backend:`, `mobile:`, `uiux:`, `admin:`, `docs:`.

### 3. Ambil update terbaru sebelum push

```powershell
git pull --rebase origin main
```

Langkah ini mencegah error `rejected` karena ada perubahan orang lain.

### 4. Push branch ke GitHub

```powershell
git push -u origin backend/api-order
```

Push berikutnya di branch yang sama cukup `git push`.

### 5. Buka Pull Request

1. Buka repo di GitHub. Akan muncul tombol **Compare & pull request**, klik.
2. Isi judul dan deskripsi singkat: apa yang dikerjakan dan kode kebutuhan terkait (contoh FU-07).
3. Klik **Create pull request**.
4. Koordinator atau anggota lain me-review, lalu klik **Merge pull request**.

### 6. Sinkronkan setelah merge

```powershell
git checkout main
git pull
```

Mulai tugas berikutnya dengan membuat branch baru dari langkah 1.

## Yang tidak boleh di-push

- File `.env` dan kredensial apa pun (kunci Midtrans, token FCM, password database).
- Folder besar hasil build: `vendor/`, `node_modules/`, `build/`, `.dart_tool/`.
- File pribadi atau berukuran sangat besar.

Pastikan `.gitignore` ada di tiap folder proyek. Template bawaan Laravel dan Flutter sudah mencakup ini.

## Masalah umum

| Pesan error | Penyebab | Solusi |
| --- | --- | --- |
| `rejected ... non-fast-forward` | Ada perubahan baru di GitHub yang belum kamu ambil | `git pull --rebase origin main`, lalu push lagi |
| `not a git repository` | Terminal tidak berada di folder repo | `cd` ke folder repo dulu |
| `Permission denied` / `403` | Akun belum diundang atau salah login | Cek undangan kolaborator dan akun GitHub yang dipakai |
| `CONFLICT (content)` saat pull | Dua orang mengubah baris yang sama | Buka file yang konflik, pilih isi yang benar, hapus tanda `<<<<<<<`, `=======`, `>>>>>>>`, lalu `git add .` dan `git rebase --continue`. Kalau bingung, tanya koordinator |
| Salah commit di `main` | Lupa membuat branch | Jangan push. Hubungi koordinator untuk dirapikan |

## Ringkasan cepat

```powershell
git checkout main
git pull
git checkout -b tim/nama-tugas
# ...kerjakan tugas...
git add .
git commit -m "tim: deskripsi singkat"
git pull --rebase origin main
git push -u origin tim/nama-tugas
# lalu buka Pull Request di GitHub
```
