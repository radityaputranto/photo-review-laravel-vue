# FotoApp — Setup & Implementation Guide
## Step-by-Step untuk Solo Developer

**Stack:** Laravel 11 + Vue.js 3 + Inertia.js + Tailwind CSS + Flowbite
**Database:** MySQL 8
**Auth:** Laravel Breeze (Inertia + Vue)

---

## Checklist Progress

- [ ] Step 1 — Persiapan Environment Lokal
- [ ] Step 2 — Setup Project Laravel + Breeze + Inertia + Vue
- [ ] Step 3 — Konfigurasi Database
- [ ] Step 4 — Migrations & Models
- [ ] Step 5 — Auth, Role & Middleware
- [ ] Step 6 — Seeder Admin & Customer Dummy
- [ ] Step 7 — Setup Google Drive API
- [ ] Step 8 — GoogleDriveService Class
- [ ] Step 9 — Setup Frontend (Tailwind + Flowbite + Heroicons)
- [ ] Step 10 — Layout Components (Admin & Customer)
- [ ] Step 11 — Struktur Folder Vue Pages
- [ ] Step 12 — Routes & Controllers
- [ ] Step 13 — Urutan Build Fitur

---

## Step 1 — Persiapan Environment Lokal

### Tools yang harus terinstall:

| Tool | Versi Minimum | Cek dengan |
|------|--------------|------------|
| PHP | 8.2+ | `php -v` |
| Composer | 2.x | `composer -V` |
| Node.js | 18+ | `node -v` |
| NPM | 9+ | `npm -v` |
| MySQL | 8.0+ | `mysql --version` |

### Rekomendasi local server:

- **Windows** → [Laragon](https://laragon.org/) (bundel PHP + MySQL + local domain otomatis)
- **Mac** → [Laravel Herd](https://herd.laravel.com/) (bundel PHP + MySQL)
- **Linux** → Install manual atau pakai Docker

### Verifikasi environment:

```bash
php -v
composer -V
node -v
npm -v
mysql --version
```

> Pastikan semua versi sesuai sebelum lanjut ke step berikutnya.

---

## Step 2 — Setup Project Laravel + Breeze + Inertia + Vue

### 2.1 Buat project Laravel baru

```bash
composer create-project laravel/laravel foto-app
cd foto-app
```

### 2.2 Install Laravel Breeze dengan Inertia + Vue

```bash
composer require laravel/breeze --dev
php artisan breeze:install vue
```

> Breeze akan otomatis setup: Inertia.js, Vue 3, Vite, Tailwind CSS, halaman Login/Register, dan Auth controller.

### 2.3 Install Node dependencies

```bash
npm install
```

### 2.4 Jalankan dev server (untuk verifikasi setup awal)

```bash
# Terminal 1 — Laravel
php artisan serve

# Terminal 2 — Vite (Vue + Tailwind)
npm run dev
```

Buka `http://localhost:8000` dan pastikan halaman Laravel muncul.

---

## Step 3 — Konfigurasi Database

### 3.1 Buat database baru

```sql
-- Jalankan di MySQL client atau phpMyAdmin
CREATE DATABASE foto_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 3.2 Konfigurasi file `.env`

```env
APP_NAME=FotoApp
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=foto_app
DB_USERNAME=root
DB_PASSWORD=

# Cache (gunakan file untuk development)
CACHE_DRIVER=file
SESSION_DRIVER=file
```

### 3.3 Verifikasi koneksi database

```bash
php artisan db:show
```

---

## Step 4 — Migrations & Models

### 4.1 Modifikasi migration users (tambah kolom role & is_active)

Buka file `database/migrations/xxxx_create_users_table.php`, tambahkan kolom berikut setelah kolom `password`:

```php
$table->enum('role', ['admin', 'customer'])->default('customer');
$table->boolean('is_active')->default(true);
```

### 4.2 Buat migration photo_sessions

```bash
php artisan make:migration create_photo_sessions_table
```

Isi migration:

```php
public function up(): void
{
    Schema::create('photo_sessions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
        $table->string('title');
        $table->date('shoot_date');
        $table->string('drive_folder_url', 500);
        $table->string('drive_folder_id', 255);
        $table->integer('max_selections')->default(30);
        $table->enum('status', ['active', 'completed', 'delivered'])->default('active');
        $table->string('download_link', 500)->nullable();
        $table->timestamp('submitted_at')->nullable();
        $table->timestamps();
    });
}
```

### 4.3 Buat migration photo_selections

```bash
php artisan make:migration create_photo_selections_table
```

Isi migration:

```php
public function up(): void
{
    Schema::create('photo_selections', function (Blueprint $table) {
        $table->id();
        $table->foreignId('session_id')->constrained('photo_sessions')->onDelete('cascade');
        $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
        $table->string('drive_file_id', 255);
        $table->string('file_name', 255);
        $table->boolean('is_final')->default(false);
        $table->timestamps();

        $table->index(['session_id', 'customer_id']);
        $table->unique(['session_id', 'drive_file_id']);
    });
}
```

### 4.4 Buat migration documents

```bash
php artisan make:migration create_documents_table
```

Isi migration:

```php
public function up(): void
{
    Schema::create('documents', function (Blueprint $table) {
        $table->id();
        $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
        $table->foreignId('session_id')->nullable()->constrained('photo_sessions')->onDelete('set null');
        $table->enum('type', ['invoice', 'contract', 'other']);
        $table->string('title', 255);
        $table->string('drive_file_id', 255);
        $table->timestamps();
    });
}
```

### 4.5 Jalankan semua migration

```bash
php artisan migrate
```

### 4.6 Buat Models

```bash
php artisan make:model PhotoSession
php artisan make:model PhotoSelection
php artisan make:model Document
```

**Update `app/Models/User.php`** — tambahkan fillable dan relasi:

```php
protected $fillable = [
    'name', 'email', 'password', 'role', 'is_active',
];

public function photoSessions()
{
    return $this->hasMany(PhotoSession::class, 'customer_id');
}

public function photoSelections()
{
    return $this->hasMany(PhotoSelection::class, 'customer_id');
}

public function documents()
{
    return $this->hasMany(Document::class, 'customer_id');
}

public function isAdmin(): bool
{
    return $this->role === 'admin';
}

public function isCustomer(): bool
{
    return $this->role === 'customer';
}
```

**Update `app/Models/PhotoSession.php`:**

```php
protected $fillable = [
    'customer_id', 'title', 'shoot_date', 'drive_folder_url',
    'drive_folder_id', 'max_selections', 'status', 'download_link', 'submitted_at',
];

protected $casts = [
    'shoot_date' => 'date',
    'submitted_at' => 'datetime',
];

public function customer()
{
    return $this->belongsTo(User::class, 'customer_id');
}

public function photoSelections()
{
    return $this->hasMany(PhotoSelection::class, 'session_id');
}

public function documents()
{
    return $this->hasMany(Document::class, 'session_id');
}
```

**Update `app/Models/PhotoSelection.php`:**

```php
protected $fillable = [
    'session_id', 'customer_id', 'drive_file_id', 'file_name', 'is_final',
];

public function session()
{
    return $this->belongsTo(PhotoSession::class, 'session_id');
}

public function customer()
{
    return $this->belongsTo(User::class, 'customer_id');
}
```

**Update `app/Models/Document.php`:**

```php
protected $fillable = [
    'customer_id', 'session_id', 'type', 'title', 'drive_file_id',
];

public function customer()
{
    return $this->belongsTo(User::class, 'customer_id');
}

public function session()
{
    return $this->belongsTo(PhotoSession::class, 'session_id');
}
```

---

## Step 5 — Auth, Role & Middleware

### 5.1 Buat RoleMiddleware

```bash
php artisan make:middleware RoleMiddleware
```

Isi `app/Http/Middleware/RoleMiddleware.php`:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!$request->user() || $request->user()->role !== $role) {
            abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}
```

### 5.2 Daftarkan middleware di `bootstrap/app.php`

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role' => \App\Http\Middleware\RoleMiddleware::class,
    ]);
})
```

### 5.3 Update redirect setelah login sesuai role

Buka `app/Http/Controllers/Auth/AuthenticatedSessionController.php`, update method `store`:

```php
public function store(LoginRequest $request): RedirectResponse
{
    $request->authenticate();
    $request->session()->regenerate();

    $user = $request->user();

    if ($user->role === 'admin') {
        return redirect()->intended(route('admin.dashboard'));
    }

    return redirect()->intended(route('customer.dashboard'));
}
```

### 5.4 Buat Policy untuk PhotoSession (keamanan customer hanya akses datanya sendiri)

```bash
php artisan make:policy PhotoSessionPolicy --model=PhotoSession
```

Isi `app/Policies/PhotoSessionPolicy.php`:

```php
public function view(User $user, PhotoSession $session): bool
{
    if ($user->isAdmin()) return true;
    return $user->id === $session->customer_id;
}

public function select(User $user, PhotoSession $session): bool
{
    return $user->id === $session->customer_id
        && $session->status === 'active'
        && is_null($session->submitted_at);
}
```

Daftarkan di `app/Providers/AppServiceProvider.php`:

```php
use App\Models\PhotoSession;
use App\Policies\PhotoSessionPolicy;

Gate::policy(PhotoSession::class, PhotoSessionPolicy::class);
```

---

## Step 6 — Seeder Admin & Customer Dummy

### 6.1 Buat AdminSeeder

```bash
php artisan make:seeder AdminSeeder
```

Isi `database/seeders/AdminSeeder.php`:

```php
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'      => 'Admin Fotografer',
            'email'     => 'admin@fotoapp.com',
            'password'  => Hash::make('password'),
            'role'      => 'admin',
            'is_active' => true,
        ]);

        // Customer dummy untuk testing
        User::create([
            'name'      => 'Andi Santoso',
            'email'     => 'andi@example.com',
            'password'  => Hash::make('password'),
            'role'      => 'customer',
            'is_active' => true,
        ]);
    }
}
```

### 6.2 Daftarkan di `DatabaseSeeder.php`

```php
public function run(): void
{
    $this->call([
        AdminSeeder::class,
    ]);
}
```

### 6.3 Jalankan seeder

```bash
php artisan db:seed --class=AdminSeeder
```

> Login admin: `admin@fotoapp.com` / `password`
> Login customer: `andi@example.com` / `password`

---

## Step 7 — Setup Google Drive API

### 7.1 Google Cloud Console

1. Buka [console.cloud.google.com](https://console.cloud.google.com)
2. Klik **"New Project"** → beri nama `FotoApp`
3. Di sidebar, klik **"APIs & Services"** → **"Library"**
4. Cari **"Google Drive API"** → klik **"Enable"**
5. Buka **"APIs & Services"** → **"Credentials"**
6. Klik **"Create Credentials"** → pilih **"Service Account"**
7. Isi nama service account (contoh: `fotoapp-drive`) → klik **"Create and Continue"** → **"Done"**
8. Klik service account yang baru dibuat → tab **"Keys"** → **"Add Key"** → **"JSON"**
9. File JSON akan ter-download otomatis

### 7.2 Simpan credentials di Laravel

```bash
# Salin file JSON ke folder storage Laravel
cp /path/ke/file-credentials.json storage/app/google-credentials.json
```

Tambahkan ke `.gitignore`:

```
/storage/app/google-credentials.json
```

### 7.3 Share folder Google Drive ke Service Account

1. Buka Google Drive
2. Klik kanan folder customer → **"Share"**
3. Masukkan email service account (format: `nama@project-id.iam.gserviceaccount.com`)
4. Set permission: **"Viewer"**
5. Klik **"Send"**

> Ulangi langkah ini untuk setiap folder customer baru.

### 7.4 Install Google API Client Library

```bash
composer require google/apiclient:^2.15
```

### 7.5 Tambah config di `.env`

```env
GOOGLE_APPLICATION_CREDENTIALS=storage/app/google-credentials.json
GOOGLE_DRIVE_CACHE_MINUTES=10
```

---

## Step 8 — GoogleDriveService Class

### 8.1 Buat file service

```bash
mkdir -p app/Services
```

Buat file `app/Services/GoogleDriveService.php`:

```php
<?php

namespace App\Services;

use Google\Client;
use Google\Service\Drive;
use Illuminate\Support\Facades\Cache;

class GoogleDriveService
{
    protected Client $client;
    protected Drive $drive;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setAuthConfig(storage_path('app/google-credentials.json'));
        $this->client->addScope(Drive::DRIVE_READONLY);
        $this->drive = new Drive($this->client);
    }

    /**
     * Ekstrak Folder ID dari URL Google Drive atau ID murni
     */
    public function extractFolderId(string $input): string
    {
        $input = trim($input);

        // Jika bukan URL, kembalikan langsung sebagai ID
        if (!str_contains($input, 'drive.google.com')) {
            return $input;
        }

        // Format: /folders/{folderId}
        preg_match('/\/folders\/([a-zA-Z0-9_-]+)/', $input, $matches);

        return $matches[1] ?? $input;
    }

    /**
     * Ambil daftar foto dari folder Google Drive (dengan cache)
     */
    public function getPhotosFromFolder(string $folderId): array
    {
        $cacheKey = 'drive_folder_' . $folderId;
        $minutes  = config('app.google_drive_cache_minutes', 10);

        return Cache::remember($cacheKey, now()->addMinutes($minutes), function () use ($folderId) {
            $results = $this->drive->files->listFiles([
                'q'      => "'{$folderId}' in parents and mimeType contains 'image/' and trashed = false",
                'fields' => 'files(id, name, mimeType, size, createdTime)',
                'orderBy' => 'name',
            ]);

            return array_map(function ($file) {
                return [
                    'id'           => $file->getId(),
                    'name'         => $file->getName(),
                    'thumbnail'    => $this->getThumbnailUrl($file->getId()),
                    'view_url'     => "https://drive.google.com/file/d/{$file->getId()}/view",
                ];
            }, $results->getFiles());
        });
    }

    /**
     * Ambil info folder (nama) berdasarkan Folder ID
     */
    public function getFolderInfo(string $folderId): ?array
    {
        try {
            $folder = $this->drive->files->get($folderId, [
                'fields' => 'id, name',
            ]);

            return [
                'id'   => $folder->getId(),
                'name' => $folder->getName(),
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Generate URL thumbnail Google Drive
     */
    public function getThumbnailUrl(string $fileId, int $size = 400): string
    {
        return "https://drive.google.com/thumbnail?id={$fileId}&sz=w{$size}";
    }

    /**
     * Generate URL embed PDF untuk preview di browser
     */
    public function getPdfPreviewUrl(string $fileId): string
    {
        return "https://drive.google.com/file/d/{$fileId}/preview";
    }

    /**
     * Hapus cache folder tertentu (untuk refresh manual)
     */
    public function clearFolderCache(string $folderId): void
    {
        Cache::forget('drive_folder_' . $folderId);
    }
}
```

### 8.2 Daftarkan service di AppServiceProvider

Buka `app/Providers/AppServiceProvider.php`:

```php
use App\Services\GoogleDriveService;

public function register(): void
{
    $this->app->singleton(GoogleDriveService::class, function ($app) {
        return new GoogleDriveService();
    });
}
```

### 8.3 Test koneksi Google Drive

```bash
php artisan tinker
```

```php
// Di dalam tinker
$drive = app(\App\Services\GoogleDriveService::class);

// Test ekstrak folder ID
$id = $drive->extractFolderId('https://drive.google.com/drive/folders/1A2B3CONTOHID');
echo $id; // Output: 1A2B3CONTOHID

// Test get folder info (ganti dengan Folder ID asli kamu)
$info = $drive->getFolderInfo('FOLDER_ID_ASLI');
print_r($info);
```

---

## Step 9 — Setup Frontend (Tailwind + Flowbite + Heroicons)

### 9.1 Install package frontend

```bash
npm install flowbite @heroicons/vue
```

### 9.2 Update `tailwind.config.js`

```javascript
import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
        './node_modules/flowbite/**/*.js',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
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
                },
            },
        },
    },

    plugins: [
        forms,
        require('flowbite/plugin'),
    ],
};
```

### 9.3 Tambahkan Google Font Inter di `resources/views/app.blade.php`

Tambahkan di dalam `<head>` sebelum `@vite`:

```html
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
```

### 9.4 Import Flowbite di `resources/js/app.js`

```javascript
import './bootstrap';
import '../css/app.css';
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import 'flowbite';

createInertiaApp({
    title: (title) => `${title} - FotoApp`,
    resolve: (name) =>
        resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#2563eb',
    },
});
```

---

## Step 10 — Layout Components

### 10.1 Buat AdminLayout

Buat file `resources/js/Layouts/AdminLayout.vue`:

```vue
<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import {
    HomeIcon,
    CalendarIcon,
    UsersIcon,
    DocumentTextIcon,
    ArrowRightOnRectangleIcon,
    CameraIcon,
} from '@heroicons/vue/24/outline'

const page = usePage()
const user = page.props.auth.user

const navItems = [
    { label: 'Dashboard', href: route('admin.dashboard'), icon: HomeIcon },
    { label: 'Sessions', href: route('admin.sessions.index'), icon: CalendarIcon },
    { label: 'Customers', href: route('admin.customers.index'), icon: UsersIcon },
    { label: 'Documents', href: route('admin.documents.index'), icon: DocumentTextIcon },
]
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50">
        <!-- Sidebar -->
        <aside class="fixed inset-y-0 left-0 w-64 bg-white border-r border-slate-100 flex flex-col z-30">
            <!-- Logo -->
            <div class="px-6 py-5 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                        <CameraIcon class="w-5 h-5 text-white" />
                    </div>
                    <span class="text-lg font-bold text-slate-900">FotoApp</span>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-3 py-4 space-y-0.5">
                <Link
                    v-for="item in navItems"
                    :key="item.href"
                    :href="item.href"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors"
                    :class="$page.url.startsWith(item.href.replace(page.props.ziggy.url, ''))
                        ? 'bg-blue-50 text-blue-700 border-l-2 border-blue-600 pl-2.5'
                        : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'"
                >
                    <component :is="item.icon" class="w-5 h-5 flex-shrink-0" />
                    {{ item.label }}
                </Link>
            </nav>

            <!-- User Profile -->
            <div class="p-4 border-t border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-blue-600 rounded-full flex items-center justify-center text-white text-sm font-semibold flex-shrink-0">
                        {{ user.name.charAt(0).toUpperCase() }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-slate-900 truncate">{{ user.name }}</p>
                        <p class="text-xs text-slate-500 truncate">{{ user.email }}</p>
                    </div>
                    <Link
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="text-slate-400 hover:text-slate-600 transition-colors"
                        title="Logout"
                    >
                        <ArrowRightOnRectangleIcon class="w-5 h-5" />
                    </Link>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="ml-64 min-h-screen">
            <div class="p-8">
                <slot />
            </div>
        </main>
    </div>
</template>
```

### 10.2 Buat CustomerLayout

Buat file `resources/js/Layouts/CustomerLayout.vue`:

```vue
<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { CameraIcon, ArrowRightOnRectangleIcon } from '@heroicons/vue/24/outline'

const page = usePage()
const user = page.props.auth.user

const navItems = [
    { label: 'Dashboard', href: route('customer.dashboard') },
    { label: 'Dokumen', href: route('customer.documents.index') },
]
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-white via-blue-50 to-slate-50">
        <!-- Navbar -->
        <nav class="sticky top-0 z-20 bg-white/80 backdrop-blur-md border-b border-slate-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <!-- Logo -->
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                            <CameraIcon class="w-5 h-5 text-white" />
                        </div>
                        <span class="text-lg font-bold text-slate-900">FotoApp</span>
                    </div>

                    <!-- Nav Links -->
                    <div class="hidden md:flex items-center gap-1">
                        <Link
                            v-for="item in navItems"
                            :key="item.href"
                            :href="item.href"
                            class="px-3 py-2 text-sm font-medium rounded-lg transition-colors"
                            :class="$page.url === item.href.replace(page.props.ziggy.url, '')
                                ? 'text-blue-600 bg-blue-50'
                                : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50'"
                        >
                            {{ item.label }}
                        </Link>
                    </div>

                    <!-- User -->
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-slate-600 hidden md:block">{{ user.name }}</span>
                        <div class="w-9 h-9 bg-blue-600 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                            {{ user.name.charAt(0).toUpperCase() }}
                        </div>
                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="text-slate-400 hover:text-slate-600 transition-colors"
                            title="Logout"
                        >
                            <ArrowRightOnRectangleIcon class="w-5 h-5" />
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <slot />
        </main>
    </div>
</template>
```

---

## Step 11 — Struktur Folder Vue Pages

Buat folder dan file kosong berikut (isi implementasi dikerjakan per fitur di Step 13):

```bash
# Buat semua folder
mkdir -p resources/js/Pages/Auth
mkdir -p resources/js/Pages/Customer
mkdir -p resources/js/Pages/Admin/Sessions
mkdir -p resources/js/Pages/Admin/Selections
mkdir -p resources/js/Pages/Admin/Documents
mkdir -p resources/js/Pages/Admin/Customers
mkdir -p resources/js/Components/Shared
mkdir -p resources/js/Components/Gallery
mkdir -p resources/js/Components/Admin

# Buat file Vue kosong
touch resources/js/Pages/Customer/Dashboard.vue
touch resources/js/Pages/Customer/Gallery.vue
touch resources/js/Pages/Customer/SelectionReview.vue
touch resources/js/Pages/Customer/Documents.vue
touch resources/js/Pages/Customer/DocumentPreview.vue

touch resources/js/Pages/Admin/Dashboard.vue
touch resources/js/Pages/Admin/Sessions/Index.vue
touch resources/js/Pages/Admin/Sessions/Create.vue
touch resources/js/Pages/Admin/Sessions/Edit.vue
touch resources/js/Pages/Admin/Sessions/Show.vue
touch resources/js/Pages/Admin/Selections/Show.vue
touch resources/js/Pages/Admin/Documents/Index.vue
touch resources/js/Pages/Admin/Customers/Index.vue

touch resources/js/Components/Shared/StatusBadge.vue
touch resources/js/Components/Shared/EmptyState.vue
touch resources/js/Components/Shared/ConfirmModal.vue
touch resources/js/Components/Gallery/PhotoCard.vue
touch resources/js/Components/Gallery/StickyBar.vue
touch resources/js/Components/Admin/StatsCard.vue
touch resources/js/Components/Admin/SessionCard.vue
```

---

## Step 12 — Routes & Controllers

### 12.1 Buat semua Controllers

```bash
# Customer Controllers
php artisan make:controller Customer/DashboardController
php artisan make:controller Customer/GalleryController
php artisan make:controller Customer/SelectionController
php artisan make:controller Customer/DocumentController

# Admin Controllers
php artisan make:controller Admin/DashboardController
php artisan make:controller Admin/SessionController --resource
php artisan make:controller Admin/SelectionController
php artisan make:controller Admin/DocumentController
php artisan make:controller Admin/CustomerController --resource
```

### 12.2 Setup Routes di `routes/web.php`

Ganti seluruh isi `routes/web.php` dengan:

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

// ─── Public ─────────────────────────────────────────────────────────────────
Route::get('/', fn() => redirect()->route('login'));

// ─── Auth (bawaan Breeze, jangan hapus) ──────────────────────────────────────
require __DIR__.'/auth.php';

// ─── Customer ────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\Customer\DashboardController::class, 'index'])
        ->name('dashboard');

    // Gallery & Selections
    Route::get('/sessions/{session}/gallery', [\App\Http\Controllers\Customer\GalleryController::class, 'show'])
        ->name('sessions.gallery');

    Route::post('/sessions/{session}/select', [\App\Http\Controllers\Customer\SelectionController::class, 'toggle'])
        ->name('sessions.select');

    Route::get('/sessions/{session}/review', [\App\Http\Controllers\Customer\SelectionController::class, 'review'])
        ->name('sessions.review');

    Route::post('/sessions/{session}/submit', [\App\Http\Controllers\Customer\SelectionController::class, 'submit'])
        ->name('sessions.submit');

    // Documents
    Route::get('/documents', [\App\Http\Controllers\Customer\DocumentController::class, 'index'])
        ->name('documents.index');

    Route::get('/documents/{document}/preview', [\App\Http\Controllers\Customer\DocumentController::class, 'preview'])
        ->name('documents.preview');
});

// ─── Admin ───────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])
        ->name('dashboard');

    // Sessions
    Route::resource('sessions', \App\Http\Controllers\Admin\SessionController::class);

    // Validate Drive URL (AJAX)
    Route::post('/sessions/validate-drive-url', [\App\Http\Controllers\Admin\SessionController::class, 'validateDriveUrl'])
        ->name('sessions.validate-drive-url');

    // Selections
    Route::get('/sessions/{session}/selections', [\App\Http\Controllers\Admin\SelectionController::class, 'show'])
        ->name('sessions.selections');

    Route::get('/sessions/{session}/selections/export', [\App\Http\Controllers\Admin\SelectionController::class, 'export'])
        ->name('sessions.selections.export');

    Route::delete('/sessions/{session}/selections/reset', [\App\Http\Controllers\Admin\SelectionController::class, 'reset'])
        ->name('sessions.selections.reset');

    // Documents
    Route::get('/documents', [\App\Http\Controllers\Admin\DocumentController::class, 'index'])
        ->name('documents.index');

    Route::post('/documents', [\App\Http\Controllers\Admin\DocumentController::class, 'store'])
        ->name('documents.store');

    Route::delete('/documents/{document}', [\App\Http\Controllers\Admin\DocumentController::class, 'destroy'])
        ->name('documents.destroy');

    // Customers
    Route::resource('customers', \App\Http\Controllers\Admin\CustomerController::class)
        ->only(['index', 'store', 'update', 'destroy']);

    Route::patch('/customers/{customer}/toggle-active', [\App\Http\Controllers\Admin\CustomerController::class, 'toggleActive'])
        ->name('customers.toggle-active');
});
```

### 12.3 Verifikasi semua routes terdaftar

```bash
php artisan route:list
```

---

## Step 13 — Urutan Build Fitur

Kerjakan fitur dalam urutan berikut. Setiap fitur bisa di-test sebelum lanjut ke berikutnya.

### Urutan Pengerjaan

| # | Fitur | File yang dikerjakan | Estimasi |
|---|-------|---------------------|----------|
| 1 | Auth + role + redirect | `AuthenticatedSessionController`, middleware | 1 hari |
| 2 | Admin: Customer Management | `CustomerController`, `Admin/Customers/Index.vue` | 1 hari |
| 3 | Admin: Session Management (CRUD) | `SessionController`, `Admin/Sessions/*.vue` | 2 hari |
| 4 | Google Drive test koneksi | `GoogleDriveService`, tinker test | 1 hari |
| 5 | Customer: Gallery + watermark | `GalleryController`, `Customer/Gallery.vue`, `PhotoCard.vue` | 2 hari |
| 6 | Customer: Pilih foto + validasi max | `SelectionController` (toggle), `StickyBar.vue` | 1 hari |
| 7 | Customer: Review & submit | `SelectionController` (review, submit), `Customer/SelectionReview.vue` | 1 hari |
| 8 | Admin: Selections view + export CSV | `SelectionController` (admin), `Admin/Selections/Show.vue` | 1 hari |
| 9 | Customer & Admin: Dashboard | `DashboardController` (keduanya), `Dashboard.vue` (keduanya) | 1 hari |
| 10 | Document management | `DocumentController` (keduanya), `Documents/*.vue` | 2 hari |
| 11 | PDF preview + download link | `DocumentPreview.vue`, update `SessionController` | 1 hari |
| 12 | UI polish + testing | Semua halaman | 2 hari |

### Cara eksekusi per fitur

Untuk setiap fitur, ikuti pola ini:

```
1. Kerjakan Controller (logika + return Inertia::render)
2. Buat/update Vue Page (terima props dari controller)
3. Test di browser
4. Lanjut fitur berikutnya
```

### Perintah yang sering dipakai saat development

```bash
# Jalankan dev server (selalu aktif saat coding)
php artisan serve
npm run dev

# Clear cache saat ada masalah
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Lihat semua routes
php artisan route:list

# Masuk tinker untuk test query/service
php artisan tinker

# Reset database dan seed ulang
php artisan migrate:fresh --seed
```

---

## Catatan Penting

### Keamanan
- Jangan commit file `google-credentials.json` ke Git
- Selalu gunakan `$request->user()->id` untuk filter data customer, jangan percaya input dari URL
- Gunakan Laravel Policy untuk validasi akses per resource

### Performa
- Google Drive API di-cache 10 menit per folder (sudah ada di `GoogleDriveService`)
- Untuk gallery dengan banyak foto, pertimbangkan lazy loading dengan IntersectionObserver di Vue

### Tips Development
- Test dengan akun admin dan customer secara bergantian menggunakan dua browser berbeda atau mode incognito
- Gunakan `dd()` atau `Log::info()` untuk debug data dari Google Drive API
- Install [Vue DevTools](https://devtools.vuejs.org/) di browser untuk debug props Inertia
