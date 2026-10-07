# Requirement Fitur Selanjutnya

Dokumen ini menyiapkan kebutuhan untuk 3 fitur besar berikutnya. Belum diimplementasikan.

---

## 1. Lokasi penyimpanan konfigurabel

### Tujuan
Foto tidak harus disimpan di dalam folder proyek (`storage/app`). Pengguna dapat menentukan folder lain (mis. `D:/Galeri`, drive eksternal, atau nantinya object storage) lewat halaman Pengaturan. Aplikasi harus bisa **membuat, mengakses, dan mengubah** file sesuai pengaturan tersebut.

### Kebutuhan fungsional
- Halaman **Pengaturan** untuk mengatur lokasi penyimpanan aktif.
- Mendukung beberapa jenis storage:
  - Local path kustom (di luar folder proyek)
  - (Lanjutan) S3 / object storage
- Saat lokasi diubah, foto baru masuk ke lokasi baru. Foto lama tetap dapat diakses (simpan `disk` + `path` per foto — skema DB saat ini sudah menyimpan ini).
- Opsi **migrasi** foto lama ke lokasi baru (job latar belakang).
- Validasi: path bisa ditulis, cukup ruang, path valid.

### Kebutuhan teknis
- Daftarkan custom disk Laravel secara dinamis dari pengaturan (runtime `config(['filesystems.disks....'])`) atau definisikan disk `photos` yang root-nya dibaca dari tabel settings.
- Tabel `settings` (key-value) atau package settings.
- Serving file: karena di luar `public/`, perlu route yang men-stream file (`Storage::download/response`) dengan otorisasi, bukan symlink publik.
- Simpan metadata `disk` pada tiap foto (sudah ada) agar `Photo::url()` tetap benar lintas disk.

### Risiko / catatan
- Keamanan path traversal pada path kustom.
- Izin filesystem OS (Windows ACL / permission).
- URL publik tidak bisa dipakai untuk disk privat — harus lewat route terproteksi.

---

## 2. AI pengelompokan foto berdasarkan wajah

### Tujuan
Mirip Google Photos: deteksi wajah di foto, kelompokkan foto berdasarkan orang yang sama.

### Pilihan teknologi (lokal/open-source, tanpa biaya cloud)
- **face_recognition** (Python, dlib) — deteksi + embedding 128-d, mudah dipakai.
- **InsightFace** (Python, ONNX) — lebih akurat & cepat, embedding 512-d, model `buffalo_l`.
- **DeepFace** (Python) — wrapper banyak model (ArcFace, Facenet).

Rekomendasi: **InsightFace** (akurasi & performa terbaik, jalan di CPU).

### Pilihan teknologi (cloud, jika mau instan)
- AWS Rekognition (face collection + search) — berbayar per gambar.
- Azure Face API.
Catatan privasi: foto personal dikirim ke pihak ketiga — kurang cocok untuk "personal archive".

### Arsitektur yang disarankan
- **Microservice Python** terpisah (FastAPI) untuk deteksi + embedding wajah. Laravel memanggil via HTTP, atau worker membaca antrian.
- Alur:
  1. Upload foto → dispatch job `DetectFaces`.
  2. Service Python kembalikan bounding box + embedding tiap wajah.
  3. Simpan ke tabel `faces` (photo_id, bbox, embedding vector).
  4. Clustering embedding (mis. DBSCAN / cosine threshold) → `person_id`.
  5. UI: halaman "Orang", klik orang → semua fotonya. Pengguna bisa memberi nama & menggabung/memisah cluster.
- Penyimpanan embedding: PostgreSQL + ekstensi **pgvector** (cocok, DB sudah Postgres) untuk pencarian similarity.

### Skema DB (draft)
- `faces`: id, photo_id, bbox (json), embedding (vector), person_id (nullable)
- `persons`: id, name (nullable), cover_face_id

### Risiko / catatan
- Butuh runtime Python + model (~hundreds MB).
- Proses berat → wajib background job + progress.
- Privasi: tegaskan semua proses lokal.

---

## 3. AI crop wajah + hapus background

### Tujuan
Fitur terpisah: dari foto tersimpan, hasilkan **gambar baru** — crop ke area wajah/subjek dan hapus background (hasil transparan PNG).

### Pilihan teknologi
- **Hapus background:**
  - **rembg** (Python, model U²-Net/ISNet) — open-source, kualitas bagus, jalan di CPU. Rekomendasi utama.
  - remove.bg API — berbayar, instan, kualitas tinggi.
- **Crop wajah/subjek:**
  - Pakai bounding box dari fitur #2 (InsightFace) untuk crop wajah.
  - Atau deteksi subjek utama (saliency) untuk crop otomatis.
- **Image processing (crop/resize/encode):**
  - PHP: `intervention/image` (GD/Imagick) untuk crop sederhana di sisi Laravel.
  - Hapus background tetap di service Python (rembg).

### Alur
1. Dari detail foto → tombol "Buat gambar baru".
2. Pilih: crop wajah (pakai deteksi) / crop manual, dan opsi hapus background.
3. Dispatch job → service Python (rembg) → kembalikan PNG transparan.
4. Simpan sebagai foto/aset baru (tandai `derived_from_photo_id`).

### Skema DB (draft)
- Tambah kolom `derived_from_photo_id` (nullable) di `photos`, atau tabel `photo_edits`.

### Risiko / catatan
- rembg butuh unduh model saat pertama jalan.
- Proses berat → background job.
- Konsistensi arsitektur: satukan service Python dengan fitur #2 (satu service AI untuk face + rembg).

---

## Kesimpulan arsitektur gabungan
Fitur #2 dan #3 sebaiknya berbagi **satu service AI Python (FastAPI)** yang menyediakan endpoint: deteksi wajah, embedding, dan hapus background. Laravel tetap jadi orchestrator lewat queue + HTTP. PostgreSQL + pgvector untuk similarity search. Semua diproses lokal demi privasi.
