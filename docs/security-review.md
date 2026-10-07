# Catatan Uji Keamanan

Review per 2026-10-05. Fokus pada permukaan yang relevan: upload, akses file, input pengguna.

## Diperbaiki
- **Upload SVG (risiko XSS).** Aturan `image` Laravel mengizinkan SVG, yang bisa memuat `<script>` dan tereksekusi saat file disajikan dari disk publik. Validasi dibatasi ke `mimes:jpeg,jpg,png,webp,gif`. ([UploadPhoto.php](../app/Livewire/UploadPhoto.php))
- **Batch edit tanpa validasi.** `caption`/`taken_at` kini divalidasi (`string|max:1000`, `date`). ([BatchEditPhotos.php](../app/Livewire/BatchEditPhotos.php))

## Aman (diverifikasi)
- **Nama file.** File disimpan dengan nama UUID yang dibuat server, bukan nama dari klien → tidak ada path traversal / overwrite. Nama asli hanya disimpan sebagai metadata.
- **Mass assignment.** Semua `update()` memakai array eksplisit (hanya `caption`/`taken_at`), tidak meneruskan input mentah.
- **XSS output.** Caption & nama file ditampilkan via `{{ }}` Blade (auto-escape).
- **CSRF.** Ditangani Livewire.
- **Sort galeri.** Kolom sort divalidasi terhadap whitelist (`taken_at`, `created_at`, `original_filename`).

## Catatan / belum ditangani (sadar risiko)
- **Tidak ada autentikasi.** Aplikasi saat ini single-user lokal. Foto di disk `public` dapat diakses siapa pun yang tahu URL-nya. Dapat diterima untuk tahap lokal, tetapi **harus** pindah ke penyajian file terproteksi saat lokasi penyimpanan dibuat konfigurabel (lihat [future-features.md](future-features.md) #1).
- **Belum ada rate limiting** pada upload. Pertimbangkan jika aplikasi dibuka ke jaringan.

## Rekomendasi lanjutan
- Jalankan `php artisan` security advisories / `composer audit` secara berkala.
- Saat menambah storage eksternal, gunakan route streaming terproteksi, bukan symlink publik.
