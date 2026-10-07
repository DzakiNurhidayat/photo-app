# Photo App — Personal Photo Archive & Memory

Arsip foto pribadi yang mengorganisasikan foto lewat *tagging* semantik dan membantu menemukan kembali kenangan berdasarkan waktu dan konteks. Bukan klona Google Photos — fokusnya pada pengarsipan personal, bukan AI/cloud besar.

## Fitur

- **Upload foto** dengan pembacaan metadata EXIF otomatis (`taken_at`, kamera, GPS, orientasi).
- **Batch edit** metadata & tag langsung setelah upload.
- **Galeri** dengan pencarian, filter per tag, dan sorting.
- **Detail foto** (lightbox) lengkap dengan info EXIF dan link lokasi ke peta.
- **Multi-tagging** many-to-many, dikelompokkan dalam **Tag Groups**.
- **On This Day** — foto dari tanggal yang sama di tahun-tahun sebelumnya.
- **Event** — pengelompokan foto otomatis berdasarkan kedekatan waktu.

## Tech Stack

| Lapisan   | Teknologi            |
|-----------|----------------------|
| Backend   | Laravel 13 (PHP 8.4) |
| Frontend  | Livewire 4 + Blade   |
| Database  | PostgreSQL           |
| Storage   | Local filesystem (disk `public`) |

Arsitektur: **Service Layer** (business logic) + **Repository Pattern** (akses data). Lihat `app/Services` dan `app/Repositories`.

## Menjalankan secara lokal

Prasyarat: PHP 8.4+, Composer, PostgreSQL, Node.js.

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Atur koneksi database di `.env` (`DB_*`), lalu:

```bash
php artisan migrate
php artisan storage:link
```

Jalankan dev server:

```bash
php artisan serve
npm run dev
```

Buka http://127.0.0.1:8000.

## Testing

Test memakai SQLite in-memory, tidak membutuhkan PostgreSQL yang berjalan:

```bash
php artisan test
```

## Dokumentasi

- [PLAN.md](PLAN.md) — rencana & progres pengerjaan.
- [docs/future-features.md](docs/future-features.md) — requirement fitur berikutnya (storage konfigurabel, AI wajah, crop + hapus background).
- [docs/security-review.md](docs/security-review.md) — catatan uji keamanan.
