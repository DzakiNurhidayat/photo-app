# Rencana Pengerjaan — Photo App

Status per 2026-10-05. Dikerjakan bertahap, satu per satu.

## Tahap pengerjaan

### 1. On This Day — [x] SELESAI
Menampilkan foto yang diambil pada tanggal yang sama di tahun-tahun sebelumnya.
- Komponen Livewire `OnThisDay` + halaman `/memories`
- Query berdasarkan `taken_at` (bulan & hari sama, tahun < tahun ini)
- Dikelompokkan per tahun, ditampilkan "X tahun lalu"
- Masuk ke sidebar navigasi

### 2. Pengelompokan Event — [x] SELESAI
Mengelompokkan foto menjadi "event" otomatis.
- Strategi awal: cluster berdasarkan kedekatan `taken_at` (gap > N jam = event baru)
- Tampilkan foto per event dengan rentang tanggal & jumlah foto
- Opsional lanjutan: pertimbangkan tag sama / lokasi berdekatan

### 3. Pengetesan semua fitur — [x] SELESAI (30 test hijau)
- Setup test pakai SQLite in-memory (tidak bergantung Postgres lokal)
- Feature test: upload, gallery filter/sort, edit, detail+EXIF, tags, tag groups, on this day, event
- Unit test: PhotoUploadService (EXIF parse, GPS convert), helper Model Photo

### 4. Refaktoring Repository Pattern — [x] SELESAI
- `PhotoRepositoryInterface` + `EloquentPhotoRepository`
- `TagRepositoryInterface` + `EloquentTagRepository`
- Binding di `AppServiceProvider`
- Pindahkan query dari Livewire/Service ke repository

### 5. Uji keamanan — [x] SELESAI (lihat docs/security-review.md)
- Validasi upload (mime, size, ekstensi) — cegah upload file berbahaya
- Mass assignment review
- Path traversal pada akses file
- XSS pada caption/filename yang ditampilkan
- Jalankan `/security-review`

### 6. Laravel Boost — [x] SELESAI
- `composer require laravel/boost --dev`
- `php artisan boost:install`
- AGENTS.md akan di-regenerate oleh Boost

### 7. Perbaiki MD proyek — [x] SELESAI
- README.md: ganti readme Laravel default jadi deskripsi proyek sebenarnya
- CLAUDE.md: hapus bootstrap guidelines yang sudah tidak relevan setelah Boost
- Dokumentasikan fitur & cara menjalankan

---

## Requirement fitur selanjutnya (disiapkan, belum dikerjakan)
Lihat [docs/future-features.md](docs/future-features.md)
1. Storage lokasi konfigurabel (di luar folder proyek)
2. AI pengelompokan foto berdasarkan wajah
3. AI crop wajah + hapus background
