# Omah Tandang — Sistem Arsip Multimedia

Aplikasi PHP dan MySQL untuk mengelola kategori, foto kegiatan, dokumen, anggota, dan profil pengguna.

## Persyaratan

- PHP 8.0 atau lebih baru
- Ekstensi PHP: `mysqli` dan `fileinfo`
- MySQL/MariaDB
- Apache direkomendasikan agar proteksi `.htaccess` pada folder upload aktif

## Instalasi baru

1. Salin folder proyek ke `htdocs` (XAMPP) atau `www` (Laragon).
2. Import `omahtandang.sql`.
3. Sesuaikan database pada `build/pages/connection.php`, atau gunakan environment:
   - `DB_HOST`
   - `DB_USER`
   - `DB_PASS`
   - `DB_NAME`
   - `DB_PORT`
4. Pastikan folder `build/pages/uploads` dapat ditulis oleh server.
5. Buka proyek dari browser.

## Memperbarui database lama

1. Cadangkan database dan folder upload.
2. Jalankan `MIGRASI_V2.sql`.
3. Ganti file proyek dengan versi baru.
4. Jangan menghapus isi `build/pages/uploads`.

## Struktur penting

- `build/pages/` — halaman aplikasi
- `build/pages/partials/layout.php` — layout bersama
- `build/assets/css/app.css` — desain aplikasi
- `build/assets/js/app.js` — interaksi umum
- `build/pages/uploads/` — file arsip dan foto profil
