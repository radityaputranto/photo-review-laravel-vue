# Product Requirements Document (PRD)
## FotoApp — Photography Business Web Application

**Version:** 1.1.0
**Date:** June 2026
**Author:** Solo Developer
**Stack:** Laravel + Vue.js + Inertia.js
**Status:** Planning Phase

**Changelog:**
- v1.1.0 — Input folder Google Drive diubah dari Folder ID manual menjadi URL lengkap dengan ekstraksi ID otomatis. Update pada SESSION-02, SESSION-04, DRIVE-02, database schema, user flow, dan helper text form.

---

## 1. Overview

### 1.1 Product Summary

FotoApp adalah aplikasi web berbasis monolith (Laravel + Vue.js + Inertia.js) untuk keperluan bisnis fotografi. Aplikasi ini memungkinkan fotografer (admin) untuk mengelola sesi pemotretan, menampilkan hasil foto dari Google Drive kepada customer, dan mengelola dokumen bisnis seperti invoice dan surat perjanjian kerja.

### 1.2 Problem Statement

Fotografer saat ini kesulitan dalam:
- Menampilkan hasil foto ke customer secara terorganisir per sesi
- Menerima feedback foto mana yang ingin diedit dari customer
- Mengelola dokumen bisnis (invoice, kontrak) per customer
- Tracking status pilihan foto dari setiap customer

### 1.3 Goals

- Mempermudah proses seleksi foto antara fotografer dan customer
- Mengurangi komunikasi manual melalui WhatsApp/email untuk pemilihan foto
- Memusatkan semua dokumen bisnis dalam satu platform
- Memberikan pengalaman galeri foto yang profesional kepada customer

### 1.4 Non-Goals

- Tidak memiliki fitur editing foto
- Tidak memiliki fitur payment gateway
- Tidak memiliki fitur email notifikasi (direncanakan untuk versi berikutnya)
- Tidak memiliki aplikasi mobile native

---

## 2. Users & Roles

### 2.1 Admin (Fotografer)

- Hanya satu akun admin (fotografer pemilik bisnis)
- Dibuat manual langsung di database atau via seeder
- Memiliki akses penuh ke semua fitur

**Kemampuan Admin:**
- Mengelola data customer (CRUD)
- Membuat dan mengelola sesi pemotretan
- Menghubungkan folder Google Drive ke sesi dengan paste URL folder langsung dari browser
- Menetapkan batas maksimal foto yang bisa dipilih customer
- Melihat foto yang dipilih customer
- Mengunggah dan mengelola dokumen (invoice, kontrak)
- Mengunggah download link foto yang sudah diedit

### 2.2 Customer

- Akun dibuat oleh admin, tidak bisa registrasi sendiri
- Hanya bisa melihat data miliknya sendiri

**Kemampuan Customer:**
- Melihat sesi pemotretan miliknya
- Melihat galeri foto dari Google Drive (dengan watermark)
- Memilih foto yang ingin diedit (dengan batas maksimal)
- Mereview dan submit pilihan foto
- Melihat dan preview dokumen (invoice, kontrak)
- Mengakses download link foto yang sudah diedit

---

## 3. Features & Requirements

### 3.1 Authentication

| ID | Requirement | Priority |
|----|-------------|----------|
| AUTH-01 | Login dengan email dan password | High |
| AUTH-02 | Multi-role: Admin dan Customer | High |
| AUTH-03 | Session-based auth via Laravel Sanctum | High |
| AUTH-04 | Redirect ke dashboard sesuai role setelah login | High |
| AUTH-05 | Logout dari semua device | Medium |
| AUTH-06 | Tidak ada fitur registrasi publik | High |

### 3.2 Google Drive Integration

| ID | Requirement | Priority |
|----|-------------|----------|
| DRIVE-01 | Koneksi ke Google Drive via Service Account | High |
| DRIVE-02 | Mengambil daftar foto dari folder tertentu berdasarkan Folder ID yang diekstrak dari URL | High |
| DRIVE-07 | Ekstrak Folder ID secara otomatis dari URL Google Drive yang di-input admin | High |
| DRIVE-03 | Menampilkan thumbnail foto dari Google Drive | High |
| DRIVE-04 | Mengambil metadata file (nama, ID, ukuran) | High |
| DRIVE-05 | Menampilkan dokumen PDF dari Google Drive | High |
| DRIVE-06 | Cache response Google Drive API (10 menit) untuk performa | Medium |

### 3.3 Photo Gallery (Customer)

| ID | Requirement | Priority |
|----|-------------|----------|
| GALLERY-01 | Menampilkan foto dalam grid 3-4 kolom | High |
| GALLERY-02 | Watermark teks "FotoApp" diagonal di setiap foto | High |
| GALLERY-03 | Efek hover pada foto (scale up, overlay gelap) | Medium |
| GALLERY-04 | Indikator visual foto yang sudah dipilih (border biru, checkmark) | High |
| GALLERY-05 | Counter realtime "X dari Y foto dipilih" | High |
| GALLERY-06 | Filter tab: Semua / Dipilih / Belum Dipilih | Medium |
| GALLERY-07 | Sticky bar bawah untuk submit pilihan | High |
| GALLERY-08 | Tombol submit disabled jika 0 foto dipilih | High |
| GALLERY-09 | Blokir klik kanan dan drag foto (proteksi dasar) | Medium |

### 3.4 Photo Selection

| ID | Requirement | Priority |
|----|-------------|----------|
| SELECT-01 | Customer bisa pilih/batal pilih foto dengan klik | High |
| SELECT-02 | Validasi batas maksimal foto yang bisa dipilih | High |
| SELECT-03 | Pesan error jika melebihi batas maksimal | High |
| SELECT-04 | Halaman review pilihan sebelum submit final | High |
| SELECT-05 | Modal konfirmasi sebelum submit | High |
| SELECT-06 | Setelah submit, pilihan terkunci (tidak bisa diubah) | High |
| SELECT-07 | Status "Submitted" tampil setelah customer submit | High |
| SELECT-08 | Admin bisa reset pilihan customer jika diperlukan | Medium |

### 3.5 Admin — Session Management

| ID | Requirement | Priority |
|----|-------------|----------|
| SESSION-01 | CRUD sesi pemotretan | High |
| SESSION-02 | Input: judul sesi, tanggal pemotretan, customer, URL folder Google Drive, max foto | High |
| SESSION-03 | Status sesi: Active / Completed / Delivered | High |
| SESSION-04 | Sistem otomatis ekstrak Folder ID dari URL Google Drive yang di-input admin | High |
| SESSION-04a | Preview nama folder Drive ditampilkan setelah URL berhasil divalidasi | Medium |
| SESSION-05 | Lihat detail sesi beserta status pilihan customer | High |
| SESSION-06 | Filter dan search daftar sesi | Medium |

### 3.6 Admin — Selection View

| ID | Requirement | Priority |
|----|-------------|----------|
| SELVIEW-01 | Lihat semua foto yang dipilih customer per sesi | High |
| SELVIEW-02 | Tampil foto tanpa watermark untuk admin | High |
| SELVIEW-03 | Klik foto untuk preview modal ukuran besar | Medium |
| SELVIEW-04 | Export daftar foto terpilih ke CSV | Medium |
| SELVIEW-05 | Indikasi status: Belum Submit / Sudah Submit | High |

### 3.7 Document Management

| ID | Requirement | Priority |
|----|-------------|----------|
| DOC-01 | Admin bisa tambah dokumen (invoice, kontrak) per customer | High |
| DOC-02 | Dokumen diambil dari Google Drive via File ID | High |
| DOC-03 | Tipe dokumen: Invoice / Contract / Other | High |
| DOC-04 | Customer bisa lihat daftar dokumen miliknya | High |
| DOC-05 | Preview PDF langsung di browser (embedded viewer) | High |
| DOC-06 | Admin bisa hapus dokumen | Medium |

### 3.8 Download Link (Foto Hasil Edit)

| ID | Requirement | Priority |
|----|-------------|----------|
| DOWNLOAD-01 | Admin bisa input download link per sesi | High |
| DOWNLOAD-02 | Link bisa berupa Google Drive share link atau link lainnya | High |
| DOWNLOAD-03 | Customer melihat tombol download di dashboard jika link tersedia | High |
| DOWNLOAD-04 | Status sesi berubah ke "Delivered" setelah link diisi | Medium |

### 3.9 Customer Management (Admin)

| ID | Requirement | Priority |
|----|-------------|----------|
| CUST-01 | Admin bisa tambah customer baru (nama, email, password) | High |
| CUST-02 | Admin bisa edit data customer | High |
| CUST-03 | Admin bisa nonaktifkan akun customer | Medium |
| CUST-04 | Lihat daftar semua sesi per customer | Medium |

---

## 4. Database Schema

### 4.1 Tabel: users

```sql
users
- id (bigint, PK)
- name (varchar 255)
- email (varchar 255, unique)
- password (varchar 255)
- role (enum: 'admin', 'customer')
- is_active (boolean, default true)
- created_at
- updated_at
```

### 4.2 Tabel: photo_sessions

```sql
photo_sessions
- id (bigint, PK)
- customer_id (FK -> users.id)
- title (varchar 255)
- shoot_date (date)
- drive_folder_url (varchar 500)        ← URL lengkap yang di-input admin
- drive_folder_id (varchar 255)         ← Diekstrak otomatis dari URL saat save
- max_selections (integer, default 30)
- status (enum: 'active', 'completed', 'delivered')
- download_link (varchar 500, nullable)
- submitted_at (timestamp, nullable)
- created_at
- updated_at
```

### 4.3 Tabel: photo_selections

```sql
photo_selections
- id (bigint, PK)
- session_id (FK -> photo_sessions.id)
- customer_id (FK -> users.id)
- drive_file_id (varchar 255)
- file_name (varchar 255)
- is_final (boolean, default false)
- created_at
- updated_at

INDEX: (session_id, customer_id)
INDEX: (session_id, drive_file_id) UNIQUE
```

### 4.4 Tabel: documents

```sql
documents
- id (bigint, PK)
- customer_id (FK -> users.id)
- session_id (FK -> photo_sessions.id, nullable)
- type (enum: 'invoice', 'contract', 'other')
- title (varchar 255)
- drive_file_id (varchar 255)
- created_at
- updated_at
```

---

## 5. Google Drive Integration Detail

### 5.1 Setup Service Account

1. Buat project di Google Cloud Console
2. Enable Google Drive API
3. Buat Service Account dan download JSON credentials
4. Simpan credentials di `storage/app/google-credentials.json`
5. Share setiap folder Google Drive ke email service account dengan akses Viewer

### 5.1a Logika Ekstraksi Folder ID dari URL

Admin cukup paste URL folder langsung dari browser Google Drive. Sistem akan mengekstrak Folder ID secara otomatis saat data disimpan.

**Format URL yang didukung:**
```
https://drive.google.com/drive/folders/1A2B3C4D5E6F7G8H9I
https://drive.google.com/drive/folders/1A2B3C4D5E6F7G8H9I?usp=sharing
https://drive.google.com/drive/u/0/folders/1A2B3C4D5E6F7G8H9I
```

**Implementasi di Laravel (`GoogleDriveService.php`):**
```php
public function extractFolderId(string $input): string
{
    // Jika input sudah berupa ID murni (bukan URL)
    if (!str_contains($input, 'drive.google.com')) {
        return trim($input);
    }

    // Ekstrak dari URL format /folders/{folderId}
    preg_match('/\/folders\/([a-zA-Z0-9_-]+)/', $input, $matches);

    return $matches[1] ?? trim($input);
}
```

**Kolom yang disimpan di database:**
- `drive_folder_url` — URL asli yang di-input admin (untuk referensi)
- `drive_folder_id` — ID yang diekstrak otomatis (digunakan untuk Drive API call)

### 5.2 Operasi yang Diperlukan

| Operasi | Endpoint Drive API |
|---------|--------------------|
| List foto dalam folder | `files.list` dengan query `'{folderId}' in parents` |
| Ambil thumbnail | `https://drive.google.com/thumbnail?id={fileId}&sz=w400` |
| Preview PDF | Embed Google Drive viewer: `https://drive.google.com/file/d/{fileId}/preview` |
| Ambil info folder | `files.get` dengan fileId |

### 5.3 Caching Strategy

- Cache list foto per folder selama 10 menit (menggunakan Laravel Cache)
- Cache key: `drive_folder_{folderId}`
- Invalidate cache ketika admin refresh manual

---

## 6. Watermark Implementation

### 6.1 Pendekatan

Watermark diterapkan di sisi **CSS/Frontend** menggunakan overlay elemen HTML di atas tag `<img>`. Ini cukup untuk proteksi dasar karena:
- Foto tidak bisa di-klik kanan save
- Screenshot masih bisa dilakukan (tradeoff yang diterima)
- Tidak perlu processing server-side (lebih efisien)

### 6.2 Spesifikasi Watermark

- Teks: "FotoApp" atau nama bisnis fotografer
- Posisi: Diagonal 45 derajat, terpusat
- Opacity: 30-40%
- Warna: Putih dengan text-shadow hitam tipis
- Ukuran font: Responsif sesuai ukuran gambar

---

## 7. User Flows

### 7.1 Flow Customer — Memilih Foto

```
Login
  → Dashboard (lihat sesi aktif)
    → Klik "View Gallery" pada sesi
      → Gallery Page (foto tampil dengan watermark)
        → Klik foto untuk memilih (max sesuai paket)
          → Review Selection Page
            → Klik "Confirm & Submit"
              → Modal konfirmasi
                → Submit → Pilihan terkunci, status "Submitted"
```

### 7.2 Flow Admin — Membuat Sesi

```
Login Admin
  → Dashboard Admin
    → Menu Sessions → "New Session"
      → Isi form: judul, customer, tanggal, URL folder Google Drive, max foto
        → Sistem validasi URL dan ekstrak Folder ID otomatis
          → Preview nama folder tampil sebagai konfirmasi
            → Save
              → Sesi tersedia di dashboard customer
```

### 7.3 Flow Admin — Melihat Pilihan Customer

```
Dashboard Admin
  → Notifikasi "Customer X telah submit pilihan"
    → Sessions List → Klik sesi
      → Tab "Photo Selections"
        → Lihat grid foto yang dipilih
          → Export CSV (opsional)
```

### 7.4 Flow Admin — Upload Dokumen

```
Menu Documents → "Upload Document"
  → Isi form: judul, tipe, customer, sesi (opsional), Drive File ID
    → Save
      → Dokumen tersedia di halaman Documents customer
```

---

## 8. Tech Stack

| Layer | Teknologi |
|-------|-----------|
| Backend | Laravel 11 |
| Frontend | Vue.js 3 (Composition API) |
| SSR Bridge | Inertia.js |
| Database | MySQL 8 |
| Styling | Tailwind CSS + Flowbite |
| Auth | Laravel Breeze (Inertia + Vue starter) |
| Drive API | google/apiclient (PHP) |
| Cache | Laravel Cache (file/redis) |
| Build Tool | Vite |
| Deployment | VPS / shared hosting dengan PHP 8.2+ |

---

## 9. Page List

| No | Halaman | Role | Deskripsi |
|----|---------|------|-----------|
| 1 | Login | Public | Form login email & password |
| 2 | Customer Dashboard | Customer | Ringkasan sesi, status, dokumen |
| 3 | Gallery | Customer | Grid foto dari Drive + watermark |
| 4 | Selection Review | Customer | Review foto sebelum submit |
| 5 | Documents | Customer | Daftar invoice & kontrak |
| 6 | Document Preview | Customer | Preview PDF di browser |
| 7 | Admin Dashboard | Admin | Ringkasan bisnis, aktivitas terkini |
| 8 | Sessions List | Admin | Daftar semua sesi |
| 9 | Session Create/Edit | Admin | Form buat/edit sesi |
| 10 | Session Detail | Admin | Detail sesi + tab seleksi & dokumen |
| 11 | Selections View | Admin | Foto yang dipilih customer |
| 12 | Documents Management | Admin | Kelola dokumen per customer |
| 13 | Customer Management | Admin | Daftar & kelola customer |

---

## 10. Non-Functional Requirements

| Aspek | Requirement |
|-------|-------------|
| Performance | Halaman gallery load < 3 detik (dengan caching Drive API) |
| Security | Semua route dilindungi middleware auth + role |
| Security | Customer hanya bisa akses data miliknya (policy) |
| Security | Blokir klik kanan pada foto di galeri |
| Compatibility | Support browser modern: Chrome, Firefox, Safari, Edge |
| Responsive | Mobile-friendly untuk customer side |
| Scalability | Bisa handle hingga 50 customer aktif bersamaan |

---

## 11. Development Phases

### Phase 1 — Foundation (Minggu 1)
- Setup project Laravel + Vue + Inertia
- Setup database & migrations
- Authentication (login, logout, role middleware)
- Google Drive service integration

### Phase 2 — Core Customer Features (Minggu 2)
- Gallery page dengan watermark
- Fitur pilih foto + validasi batas maksimal
- Selection review & submit
- Customer dashboard

### Phase 3 — Admin Features (Minggu 3)
- Admin dashboard
- Session management (CRUD)
- Selections view + export CSV
- Customer management

### Phase 4 — Document & Polish (Minggu 4)
- Document management (admin & customer)
- PDF preview
- Download link feature
- Testing, bug fix, UI polish

---

## 12. Out of Scope (v1.0)

- Email notifikasi
- Fitur chat/komentar pada foto
- Watermark server-side (image processing)
- Multi-bahasa
- Dark mode
- Aplikasi mobile
- Integrasi payment
