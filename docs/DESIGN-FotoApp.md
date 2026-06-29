# Design System & UI Specification
## FotoApp — Photography Business Web Application

**Version:** 1.0.0
**Date:** June 2026
**Framework:** Tailwind CSS + Flowbite
**Font:** Inter (Google Fonts)

---

## 1. Design Principles

- **Clean & Modern** — Whitespace yang lega, tidak penuh elemen
- **Photography-First** — UI tidak mengalihkan perhatian dari foto
- **Intentional** — Setiap elemen punya tujuan, tidak dekoratif berlebihan
- **Accessible** — Kontras warna memenuhi WCAG AA
- **Consistent** — Komponen yang sama digunakan di seluruh aplikasi

---

## 2. Color Palette

### 2.1 Primary Colors

```css
/* Primary — Blue */
--color-primary-50:  #eff6ff;   /* bg ringan, badge bg */
--color-primary-100: #dbeafe;   /* hover state ringan */
--color-primary-400: #60a5fa;   /* icon, accent ringan */
--color-primary-500: #3b82f6;   /* border, ring */
--color-primary-600: #2563eb;   /* button utama, link */
--color-primary-700: #1d4ed8;   /* button hover */
--color-primary-800: #1e40af;   /* teks badge */
```

### 2.2 Secondary Colors

```css
/* Secondary — Sky (aksen ringan) */
--color-sky-400: #38bdf8;
--color-sky-500: #0ea5e9;
```

### 2.3 Neutral Colors

```css
/* Neutral — Slate */
--color-slate-50:  #f8fafc;   /* background page */
--color-slate-100: #f1f5f9;   /* background card alt */
--color-slate-200: #e2e8f0;   /* border, divider */
--color-slate-400: #94a3b8;   /* placeholder, icon inactive */
--color-slate-500: #64748b;   /* subtext, caption */
--color-slate-600: #475569;   /* body text secondary */
--color-slate-700: #334155;   /* body text primary */
--color-slate-900: #0f172a;   /* heading, strong text */
```

### 2.4 Semantic Colors

```css
/* Destructive */
--color-rose-400: #fb7185;    /* remove, delete icon */
--color-rose-500: #f43f5e;    /* delete button */
--color-rose-600: #e11d48;    /* delete button hover */

/* Success */
--color-green-400: #4ade80;
--color-green-500: #22c55e;   /* status submitted, delivered */
--color-green-100: #dcfce7;   /* badge bg success */

/* Warning */
--color-amber-400: #fbbf24;
--color-amber-100: #fef3c7;   /* badge bg warning */

/* Info */
--color-blue-100: #dbeafe;    /* badge bg info */
```

### 2.5 Background Gradients

```css
/* Page background — customer side */
background: linear-gradient(135deg, #ffffff 0%, #eff6ff 50%, #f8fafc 100%);

/* Page background — admin side */
background: linear-gradient(160deg, #f8fafc 0%, #eff6ff 100%);

/* Card accent gradient (subtle) */
background: linear-gradient(135deg, #eff6ff 0%, #ffffff 100%);

/* Hero/header area */
background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 60%, #3b82f6 100%);
```

---

## 3. Typography

### 3.1 Font Family

```html
<!-- Import di app.blade.php atau CSS -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
```

```css
font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
```

### 3.2 Type Scale

| Token | Size | Weight | Usage |
|-------|------|--------|-------|
| `text-3xl font-bold` | 30px / 700 | Bold | Page title utama |
| `text-2xl font-semibold` | 24px / 600 | Semibold | Section heading |
| `text-xl font-semibold` | 20px / 600 | Semibold | Card title |
| `text-lg font-medium` | 18px / 500 | Medium | Sub-section |
| `text-base` | 16px / 400 | Regular | Body text |
| `text-sm` | 14px / 400 | Regular | Caption, label |
| `text-xs` | 12px / 400 | Regular | Badge text, helper |

### 3.3 Color Usage

```css
/* Heading */
color: #0f172a;   /* slate-900 */

/* Body text */
color: #334155;   /* slate-700 */

/* Subtext / caption */
color: #64748b;   /* slate-500 */

/* Placeholder */
color: #94a3b8;   /* slate-400 */

/* Link */
color: #2563eb;   /* blue-600 */
```

---

## 4. Spacing & Layout

### 4.1 Spacing Scale (Tailwind defaults)

```
4px  = p-1
8px  = p-2
12px = p-3
16px = p-4
20px = p-5
24px = p-6
32px = p-8
40px = p-10
48px = p-12
64px = p-16
```

### 4.2 Layout Structure

**Admin Layout — Sidebar:**
```
┌──────────────────────────────────────────────┐
│ Sidebar (w-64, fixed)  │  Main Content        │
│                        │  (ml-64, full height)│
│  Logo                  │                      │
│  Nav Items             │  Header bar          │
│                        │  (h-16, sticky)      │
│                        │                      │
│                        │  Page Content        │
│                        │  (p-6 atau p-8)      │
│                        │                      │
│  User profile          │                      │
└──────────────────────────────────────────────┘
```

**Customer Layout — Top Navbar:**
```
┌──────────────────────────────────────────────┐
│  Navbar (h-16, sticky, bg-white/80 backdrop) │
│  Logo  |  Nav links  |  User avatar + name   │
├──────────────────────────────────────────────┤
│                                              │
│  Page Content (max-w-7xl mx-auto px-4)       │
│                                              │
└──────────────────────────────────────────────┘
```

### 4.3 Content Max Width

```css
.page-content    { max-width: 80rem; }   /* max-w-7xl — dashboard, list */
.form-content    { max-width: 42rem; }   /* max-w-2xl — create/edit form */
.review-content  { max-width: 56rem; }   /* max-w-4xl — review halaman */
```

---

## 5. Component Specifications

### 5.1 Buttons

```html
<!-- Primary Button -->
<button class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2.5 rounded-lg transition-colors duration-200 focus:ring-4 focus:ring-blue-300">
  Label
</button>

<!-- Ghost / Outline Button -->
<button class="border border-blue-600 text-blue-600 hover:bg-blue-50 font-medium px-4 py-2.5 rounded-lg transition-colors duration-200">
  Label
</button>

<!-- Danger Button -->
<button class="bg-rose-500 hover:bg-rose-600 text-white font-medium px-4 py-2.5 rounded-lg transition-colors duration-200">
  Hapus
</button>

<!-- Ghost Neutral Button -->
<button class="text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-medium px-4 py-2.5 rounded-lg transition-colors duration-200">
  Batal
</button>

<!-- Disabled State -->
<button class="bg-blue-600 text-white font-medium px-4 py-2.5 rounded-lg opacity-50 cursor-not-allowed" disabled>
  Submit
</button>

<!-- Full Width -->
<button class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition-colors duration-200">
  Sign In
</button>
```

### 5.2 Input Fields

```html
<!-- Text Input -->
<div class="mb-4">
  <label class="block text-sm font-medium text-slate-700 mb-1.5">
    Label
  </label>
  <input
    type="text"
    class="w-full px-3.5 py-2.5 rounded-lg border border-slate-200 bg-white text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm"
    placeholder="Placeholder text"
  />
  <!-- Helper text -->
  <p class="mt-1 text-xs text-slate-500">Helper text di sini</p>
  <!-- Error state -->
  <p class="mt-1 text-xs text-rose-500">Pesan error di sini</p>
</div>

<!-- Select / Dropdown -->
<select class="w-full px-3.5 py-2.5 rounded-lg border border-slate-200 bg-white text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm appearance-none">
  <option value="">Pilih opsi</option>
</select>

<!-- Input Error State -->
<input class="w-full px-3.5 py-2.5 rounded-lg border border-rose-400 bg-rose-50 focus:ring-2 focus:ring-rose-400 ..." />
```

### 5.3 Cards

```html
<!-- Standard Card -->
<div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
  <!-- content -->
</div>

<!-- Card dengan gradient accent -->
<div class="bg-gradient-to-br from-blue-50 to-white rounded-xl shadow-sm border border-blue-100 p-6">
  <!-- content -->
</div>

<!-- Stat Card -->
<div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5 flex items-center gap-4">
  <div class="p-3 bg-blue-50 rounded-lg">
    <!-- Icon -->
  </div>
  <div>
    <p class="text-2xl font-bold text-slate-900">24</p>
    <p class="text-sm text-slate-500">Total Sesi</p>
  </div>
</div>

<!-- Session Card (Customer) -->
<div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5 hover:shadow-md transition-shadow duration-200">
  <div class="flex items-start justify-between mb-3">
    <div>
      <h3 class="font-semibold text-slate-900">Wedding Andi & Budi</h3>
      <p class="text-sm text-slate-500 mt-0.5">12 Juni 2025</p>
    </div>
    <!-- Status Badge -->
    <span class="px-2.5 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded-full">Active</span>
  </div>
  <!-- Progress Bar -->
  <div class="mb-4">
    <div class="flex justify-between text-xs text-slate-500 mb-1.5">
      <span>Foto dipilih</span>
      <span>12 / 30</span>
    </div>
    <div class="w-full bg-slate-100 rounded-full h-1.5">
      <div class="bg-blue-600 h-1.5 rounded-full" style="width: 40%"></div>
    </div>
  </div>
  <button class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 rounded-lg transition-colors">
    Lihat Gallery
  </button>
</div>
```

### 5.4 Badges / Pills

```html
<!-- Status: Active -->
<span class="px-2.5 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded-full">Active</span>

<!-- Status: Submitted -->
<span class="px-2.5 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Submitted</span>

<!-- Status: Delivered -->
<span class="px-2.5 py-1 text-xs font-medium bg-purple-100 text-purple-700 rounded-full">Delivered</span>

<!-- Status: Completed -->
<span class="px-2.5 py-1 text-xs font-medium bg-slate-100 text-slate-600 rounded-full">Completed</span>

<!-- Type: Invoice -->
<span class="px-2.5 py-1 text-xs font-medium bg-rose-100 text-rose-600 rounded-full">Invoice</span>

<!-- Type: Contract -->
<span class="px-2.5 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded-full">Contract</span>

<!-- Counter pill -->
<span class="px-3 py-1 text-sm font-semibold bg-blue-600 text-white rounded-full">12 / 30</span>
```

### 5.5 Navigation Sidebar (Admin)

```html
<aside class="fixed inset-y-0 left-0 w-64 bg-white border-r border-slate-100 flex flex-col z-30">
  <!-- Logo -->
  <div class="px-6 py-5 border-b border-slate-100">
    <div class="flex items-center gap-2.5">
      <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
        <!-- Camera icon -->
      </div>
      <span class="text-lg font-bold text-slate-900">FotoApp</span>
    </div>
  </div>

  <!-- Navigation Items -->
  <nav class="flex-1 px-3 py-4 space-y-0.5">
    <!-- Active State -->
    <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-blue-50 text-blue-700 font-medium text-sm border-l-2 border-blue-600 ml-0 pl-2.5">
      <!-- Icon -->
      <span>Dashboard</span>
    </a>

    <!-- Inactive State -->
    <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-medium text-sm transition-colors">
      <!-- Icon -->
      <span>Sessions</span>
    </a>
  </nav>

  <!-- User Profile (bottom) -->
  <div class="p-4 border-t border-slate-100">
    <div class="flex items-center gap-3">
      <div class="w-9 h-9 bg-blue-600 rounded-full flex items-center justify-center text-white text-sm font-semibold">
        AD
      </div>
      <div class="flex-1 min-w-0">
        <p class="text-sm font-medium text-slate-900 truncate">Admin</p>
        <p class="text-xs text-slate-500 truncate">admin@fotoapp.com</p>
      </div>
      <!-- Logout icon -->
    </div>
  </div>
</aside>
```

### 5.6 Top Navbar (Customer)

```html
<nav class="sticky top-0 z-20 bg-white/80 backdrop-blur-md border-b border-slate-100">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between h-16">
      <!-- Logo -->
      <div class="flex items-center gap-2">
        <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
          <!-- Camera icon -->
        </div>
        <span class="text-lg font-bold text-slate-900">FotoApp</span>
      </div>

      <!-- Nav Links -->
      <div class="hidden md:flex items-center gap-1">
        <a href="#" class="px-3 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg">Dashboard</a>
        <a href="#" class="px-3 py-2 text-sm font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 rounded-lg transition-colors">Dokumen</a>
      </div>

      <!-- User -->
      <div class="flex items-center gap-3">
        <span class="text-sm text-slate-600 hidden md:block">Andi Santoso</span>
        <div class="w-9 h-9 bg-blue-600 rounded-full flex items-center justify-center text-white text-sm font-semibold">
          AS
        </div>
      </div>
    </div>
  </div>
</nav>
```

### 5.7 Photo Gallery Grid

```html
<!-- Gallery Container -->
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
  <!-- Photo Card — Unselected -->
  <div
    class="relative group aspect-square overflow-hidden rounded-xl cursor-pointer bg-slate-100"
    @click="toggleSelect(photo)"
  >
    <img
      :src="getThumbnail(photo.id)"
      class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105 select-none pointer-events-none"
    />

    <!-- Watermark Overlay -->
    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
      <span
        class="text-white font-bold text-lg opacity-30 rotate-[-35deg] select-none"
        style="text-shadow: 0 1px 3px rgba(0,0,0,0.5); letter-spacing: 0.1em;"
      >
        FotoApp
      </span>
    </div>

    <!-- Hover Overlay -->
    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors duration-200"></div>
  </div>

  <!-- Photo Card — Selected -->
  <div class="relative group aspect-square overflow-hidden rounded-xl cursor-pointer ring-2 ring-blue-600 ring-offset-2">
    <img class="w-full h-full object-cover select-none pointer-events-none" />

    <!-- Watermark -->
    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
      <span class="text-white font-bold text-lg opacity-30 rotate-[-35deg] select-none" style="text-shadow: 0 1px 3px rgba(0,0,0,0.5);">FotoApp</span>
    </div>

    <!-- Selected Indicator -->
    <div class="absolute top-2 right-2 w-7 h-7 bg-blue-600 rounded-full flex items-center justify-center shadow-lg">
      <!-- Checkmark icon white -->
    </div>
  </div>
</div>

<!-- Sticky Bottom Bar -->
<div class="fixed bottom-0 left-0 right-0 bg-white/90 backdrop-blur-md border-t border-slate-200 px-4 py-3 z-20">
  <div class="max-w-7xl mx-auto flex items-center justify-between">
    <div class="flex items-center gap-3">
      <span class="px-3 py-1 bg-blue-600 text-white text-sm font-semibold rounded-full">12 / 30</span>
      <span class="text-sm text-slate-600">foto dipilih</span>
    </div>
    <div class="flex items-center gap-2">
      <button class="text-sm text-slate-500 hover:text-slate-700 px-3 py-2 rounded-lg hover:bg-slate-100 transition-colors">
        Hapus Semua
      </button>
      <button class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
        Review & Submit
      </button>
    </div>
  </div>
</div>
```

### 5.8 Modal

```html
<!-- Overlay -->
<div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
  <!-- Modal Card -->
  <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-lg font-semibold text-slate-900">Konfirmasi Submit</h3>
      <button class="text-slate-400 hover:text-slate-600 transition-colors">
        <!-- X icon -->
      </button>
    </div>

    <!-- Body -->
    <p class="text-slate-600 text-sm leading-relaxed mb-6">
      Kamu telah memilih <strong class="text-slate-900">12 foto</strong>. Setelah submit, pilihan tidak dapat diubah.
    </p>

    <!-- Actions -->
    <div class="flex gap-3">
      <button class="flex-1 border border-slate-200 text-slate-600 hover:bg-slate-50 font-medium py-2.5 rounded-lg transition-colors">
        Batal
      </button>
      <button class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 rounded-lg transition-colors">
        Ya, Submit
      </button>
    </div>
  </div>
</div>
```

### 5.9 Table (Admin)

```html
<div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
  <!-- Table Header -->
  <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
    <h3 class="font-semibold text-slate-900">Daftar Sesi</h3>
    <button class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
      + Sesi Baru
    </button>
  </div>

  <table class="w-full">
    <thead>
      <tr class="bg-slate-50 border-b border-slate-100">
        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Sesi</th>
        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Customer</th>
        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Foto Dipilih</th>
        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-slate-100">
      <tr class="hover:bg-slate-50 transition-colors">
        <td class="px-6 py-4">
          <p class="font-medium text-slate-900">Wedding Andi & Budi</p>
          <p class="text-sm text-slate-500">12 Juni 2025</p>
        </td>
        <td class="px-6 py-4 text-sm text-slate-600">Andi Santoso</td>
        <td class="px-6 py-4">
          <span class="px-2.5 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded-full">Active</span>
        </td>
        <td class="px-6 py-4 text-sm text-slate-600">12 / 30</td>
        <td class="px-6 py-4 text-right">
          <button class="text-blue-600 hover:text-blue-700 text-sm font-medium mr-3">Detail</button>
          <button class="text-slate-400 hover:text-slate-600 text-sm">Edit</button>
        </td>
      </tr>
    </tbody>
  </table>
</div>
```

### 5.10 Empty State

```html
<div class="flex flex-col items-center justify-center py-16 text-center">
  <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mb-4">
    <!-- Icon -->
  </div>
  <h3 class="text-lg font-semibold text-slate-900 mb-1">Belum ada data</h3>
  <p class="text-sm text-slate-500 mb-6 max-w-xs">Belum ada sesi yang dibuat. Mulai dengan membuat sesi pertama.</p>
  <button class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
    Buat Sesi Baru
  </button>
</div>
```

---

## 6. Page Specifications

### 6.1 Login Page

```
Layout: Split screen (hidden pada mobile, full card di mobile)
  Left: bg-gradient-to-br from-blue-600 to-blue-800
    - Quote / tagline fotografi
    - Ilustrasi atau pattern tipis
  Right: bg-white flex items-center justify-center p-8
    - Logo + "FotoApp"
    - Heading "Welcome back"
    - Subtext "Masuk ke akun kamu"
    - Form: email, password (show/hide)
    - Button "Sign In" (full width, blue-600)
    - Footer: "v1.0 · FotoApp"

Background: kiri solid blue gradient, kanan putih bersih
```

### 6.2 Customer Dashboard

```
Layout: Top navbar + page content (max-w-7xl, px-4, py-8)

Sections:
  1. Greeting: "Halo, [Nama] 👋" text-2xl font-bold
     Subtext: "Berikut ringkasan sesi pemotretan kamu"

  2. Stats Row (3 kolom, gap-4):
     - Sesi Aktif (blue-50 bg, blue-600 icon)
     - Foto Dipilih (blue-50 bg)
     - Dokumen Tersedia (blue-50 bg)

  3. Section "Sesi Kamu":
     - Grid 1 kolom (mobile) / 2-3 kolom (desktop)
     - Session cards dengan progress bar
     - "Lihat Gallery" button
     - Jika ada download link: tombol "Download Foto" (green-600)

  4. Section "Dokumen Terbaru":
     - List 3-4 dokumen terbaru
     - File icon, nama, tipe badge, "Preview" button
     - Link "Lihat semua dokumen →"
```

### 6.3 Gallery Page

```
Layout: Top navbar + sticky bottom bar

Top Section:
  - Back button (← Kembali)
  - Judul sesi (text-xl font-semibold)
  - Subtext "Pilih foto yang ingin diedit"
  - Counter badge pill "12 / 30"

Filter Tabs (border-b):
  - Semua | Dipilih | Belum Dipilih
  - Active tab: border-b-2 border-blue-600, text-blue-600

Photo Grid:
  - grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3
  - aspect-square per foto
  - Watermark diagonal "FotoApp" opacity-30
  - Selected: ring-2 ring-blue-600 ring-offset-2 + checkmark badge

Sticky Bottom Bar:
  - bg-white/90 backdrop-blur border-t
  - Kiri: counter, Kanan: "Hapus Semua" ghost + "Review & Submit" filled
  - Disabled jika selected = 0
```

### 6.4 Selection Review Page

```
Layout: Top navbar + max-w-4xl mx-auto py-8 px-4

Top:
  - Back button
  - Title "Review Pilihan Kamu"
  - Subtext "Kamu memilih X foto. Periksa sebelum submit."

Content (2 kolom di desktop):
  Kiri (flex-1):
    - Grid 4 kolom thumbnail kecil (rounded-lg, aspect-square)
    - Setiap foto: tombol X (rose-400) di pojok kanan atas
    - Watermark tetap tampil

  Kanan (sticky, w-64):
    - Card putih shadow-sm
    - Nama sesi
    - "X / Y foto dipilih"
    - Progress ring / bar circular
    - Button "Konfirmasi & Submit" (blue-600, full width)
    - Link "← Kembali ke Gallery"
```

### 6.5 Documents Page (Customer)

```
Layout: Top navbar + max-w-4xl mx-auto py-8 px-4

Top:
  - Title "Dokumen Kamu"
  - Subtext "Invoice dan surat perjanjian"

Filter tabs: Semua | Invoice | Kontrak

Document Cards (list, bukan table):
  - Setiap card: flex items-center gap-4 p-4 bg-white rounded-xl shadow-sm
    - Icon PDF (rose-400 untuk invoice, blue-400 untuk contract)
    - Tengah: judul dokumen, nama sesi, tanggal
    - Kanan: tipe badge + tombol "Preview"

Empty state jika tidak ada dokumen
```

### 6.6 Admin Dashboard

```
Layout: Sidebar + main content (ml-64)

Content (p-8):
  1. Header: "Dashboard" + tanggal hari ini (text-slate-500)

  2. Stats Row (4 kolom):
     - Total Sesi, Sesi Aktif, Menunggu Seleksi, Total Customer
     - Each: bg-white rounded-xl p-5, icon + number + label

  3. Section "Aktivitas Terbaru" (timeline):
     - List dengan dot indicator (blue-400)
     - "[Customer] telah submit pilihan untuk [Sesi]"
     - Timestamp relative (2 jam lalu)

  4. Section "Sesi Aktif" (table ringkas):
     - Kolom: Sesi, Customer, Status, Pilihan, Aksi
     - Max 5 baris, link "Lihat semua →"
```

### 6.7 Sessions List (Admin)

```
Layout: Sidebar + main content

Content:
  Top bar:
    - Title "Sessions" (text-2xl font-bold)
    - Kanan: search input + filter status + "Sesi Baru" button

  Card list (bukan table murni):
    - Setiap card: flex, gap-4, p-5, bg-white rounded-xl shadow-sm
      Kiri: judul sesi (bold), customer, tanggal (slate-500)
      Tengah: progress bar pilihan foto
      Kanan: status badge + "Detail" button + edit icon

  Pagination (bottom center, Flowbite pagination)
```

### 6.8 Session Create/Edit (Admin)

```
Layout: Sidebar + main content (max-w-2xl mx-auto)

Top:
  - Back button
  - Title "Sesi Baru" atau "Edit Sesi"

Form (cards terpisah per grup):
  Card 1 "Informasi Sesi":
    - Judul sesi (text input)
    - Tanggal pemotretan (date picker)
    - Status (select: Active/Completed/Delivered)

  Card 2 "Customer & Paket":
    - Customer (select dropdown searchable via Flowbite)
    - Maksimal foto (number input, min 1)
    - Helper: "Sesuaikan dengan paket yang dibeli customer"

  Card 3 "Google Drive":
    - Input "Folder ID Drive"
    - Preview nama folder (auto-fetch setelah input)
    - Helper: "Share folder ke email service account terlebih dahulu"

Bottom Actions:
  - "Batal" ghost button + "Simpan Sesi" blue button (justify-end)
```

### 6.9 Selections View (Admin)

```
Layout: Sidebar + main content

Top:
  - Back button + nama sesi + nama customer
  - "X dari Y foto dipilih" counter
  - "Export CSV" ghost button (kanan)
  - Status badge (Submitted / Belum Submit)

Jika belum submit:
  - Empty state: "Menunggu customer melakukan seleksi foto"

Jika sudah submit:
  - grid-cols-3 lg:grid-cols-5 gap-3
  - Foto tanpa watermark (admin view)
  - Setiap foto: thumbnail + nama file (text-xs truncate)
  - Klik foto: modal preview ukuran besar
  - Badge "Dipilih" (blue) di setiap foto
```

### 6.10 Documents Management (Admin)

```
Layout: Sidebar + main content

Top:
  - Title "Dokumen"
  - Filter: Customer dropdown, Tipe dropdown
  - "Upload Dokumen" button (blue, kanan)

List cards (sama seperti customer documents view, tambah tombol hapus)

Upload Modal:
  - Title "Upload Dokumen"
  - Judul dokumen (text input)
  - Tipe (select: Invoice / Kontrak / Lainnya)
  - Customer (select)
  - Sesi (select, optional)
  - Drive File ID (text input)
  - Helper: "Salin File ID dari URL Google Drive"
  - "Simpan" button
```

### 6.11 Customer Management (Admin)

```
Layout: Sidebar + main content

Top:
  - Title "Customer"
  - Search input
  - "Tambah Customer" button

Table:
  - Kolom: Avatar+Nama, Email, Total Sesi, Terdaftar, Status, Aksi
  - Avatar: initials circle (blue-600 bg, white text)
  - Status: Active badge (green) / Inactive badge (slate)
  - Aksi: "Sesi" link, "Edit" icon, toggle aktif/nonaktif

Add/Edit Modal:
  - Nama lengkap
  - Email
  - Password (hanya saat tambah baru)
  - Konfirmasi password
  - "Simpan" button
```

---

## 7. Icons

Gunakan **Heroicons** (sudah terintegrasi baik dengan Tailwind):

```bash
npm install @heroicons/vue
```

```javascript
// Penggunaan di Vue
import { CameraIcon, HomeIcon, DocumentIcon } from '@heroicons/vue/24/outline'
```

| Konteks | Icon |
|---------|------|
| Dashboard | `HomeIcon` |
| Sessions | `CalendarIcon` |
| Customers | `UsersIcon` |
| Documents | `DocumentTextIcon` |
| Gallery | `PhotoIcon` |
| Logout | `ArrowRightOnRectangleIcon` |
| Edit | `PencilSquareIcon` |
| Delete | `TrashIcon` |
| Download | `ArrowDownTrayIcon` |
| Check/Selected | `CheckIcon` |
| Close/Remove | `XMarkIcon` |
| Upload | `CloudArrowUpIcon` |
| Camera/App | `CameraIcon` |

---

## 8. Animations & Transitions

```css
/* Transisi standar untuk semua elemen interaktif */
transition-colors duration-200
transition-all duration-200
transition-shadow duration-200

/* Photo card hover */
group-hover:scale-105 transition-transform duration-300

/* Modal masuk */
/* Gunakan Vue <Transition> dengan class: */
.modal-enter-active { transition: all 0.2s ease-out; }
.modal-enter-from   { opacity: 0; transform: scale(0.95); }

/* Overlay */
.overlay-enter-active { transition: opacity 0.2s ease; }
.overlay-enter-from   { opacity: 0; }
```

---

## 9. Responsive Breakpoints

Menggunakan Tailwind default breakpoints:

| Breakpoint | Width | Keterangan |
|------------|-------|------------|
| `sm` | 640px | Mobile landscape |
| `md` | 768px | Tablet |
| `lg` | 1024px | Desktop kecil |
| `xl` | 1280px | Desktop |

**Mobile-first approach:**
- Customer side: prioritas mobile (gallery harus enak di HP)
- Admin side: prioritas desktop (sidebar tersembunyi di mobile, toggle dengan hamburger)

---

## 10. Tailwind Config Additions

```javascript
// tailwind.config.js
module.exports = {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif'],
      },
      colors: {
        primary: {
          50:  '#eff6ff',
          100: '#dbeafe',
          400: '#60a5fa',
          500: '#3b82f6',
          600: '#2563eb',
          700: '#1d4ed8',
          800: '#1e40af',
        }
      },
      backgroundImage: {
        'gradient-page-customer': 'linear-gradient(135deg, #ffffff 0%, #eff6ff 50%, #f8fafc 100%)',
        'gradient-page-admin':    'linear-gradient(160deg, #f8fafc 0%, #eff6ff 100%)',
      }
    },
  },
  plugins: [
    require('flowbite/plugin'),
  ],
}
```

---

## 11. Flowbite Components yang Digunakan

| Komponen | Halaman |
|----------|---------|
| `Datepicker` | Session Create/Edit (tanggal pemotretan) |
| `Select` / `Dropdown` | Semua form select |
| `Modal` | Konfirmasi submit, upload dokumen, preview foto |
| `Pagination` | Sessions list, customer list |
| `Tooltip` | Tombol aksi di tabel |
| `Spinner` | Loading state fetch Drive API |
| `Alert` | Error / success feedback |

```javascript
// Import Flowbite di app.js
import 'flowbite';
```

---

## 12. File Structure (Vue Components)

```
resources/js/
├── app.js
├── Pages/
│   ├── Auth/
│   │   └── Login.vue
│   ├── Customer/
│   │   ├── Dashboard.vue
│   │   ├── Gallery.vue
│   │   ├── SelectionReview.vue
│   │   ├── Documents.vue
│   │   └── DocumentPreview.vue
│   └── Admin/
│       ├── Dashboard.vue
│       ├── Sessions/
│       │   ├── Index.vue
│       │   ├── Create.vue
│       │   ├── Edit.vue
│       │   └── Show.vue
│       ├── Selections/
│       │   └── Show.vue
│       ├── Documents/
│       │   └── Index.vue
│       └── Customers/
│           └── Index.vue
├── Layouts/
│   ├── AdminLayout.vue        ← Sidebar layout
│   └── CustomerLayout.vue     ← Navbar layout
└── Components/
    ├── Shared/
    │   ├── StatusBadge.vue
    │   ├── EmptyState.vue
    │   └── ConfirmModal.vue
    ├── Gallery/
    │   ├── PhotoCard.vue
    │   └── StickyBar.vue
    └── Admin/
        ├── StatsCard.vue
        └── SessionCard.vue
```
