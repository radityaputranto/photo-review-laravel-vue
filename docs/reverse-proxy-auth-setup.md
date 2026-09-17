# Dokumentasi Setup & Penyesuaian Reverse Proxy, HTTPS, dan Autentikasi

Dokumen ini mencatat seluruh konfigurasi, penyesuaian kode, dan penyelesaian masalah terkait akses aplikasi **FotoApp / Photo-App** melalui Docker, jaringan lokal (LAN), Tailscale VPN, dan Reverse Proxy publik (Cloudflare Tunnel).

---

## 1. Ringkasan Masalah & Analisis Akar Masalah (Root Cause)

### A. Isu Akses Melalui HTTPS / Cloudflare Tunnel
- **Gejala**: Ketika mengakses form login via `https://photo-app.raditya.biz.id/login` dan menekan tombol *Sign In*, form tidak merespons atau langsung memunculkan pesan error:  
  `"Kolom ini wajib diisi."` di bawah input password, serta nilai password terhapus.
- **Akar Masalah**:
  1. **Mixed Active Content**: Halaman diakses melalui HTTPS, namun router Ziggy/Laravel di-generate dengan skema HTTP (`http://photo-app.raditya.biz.id/login`) karena header proxy `X-Forwarded-Proto` belum ditangani dan skema HTTPS belum dipaksakan. Browser modern secara otomatis memblokir request POST/AJAX yang mengarah ke HTTP dari halaman HTTPS (*Blocked Mixed Active Content*).
  2. **False Trigger Validasi Vuelidate di Frontend**: Saat request `form.post(...)` dibatalkan/gagal, callback `onFinish` menjalankan `form.reset('password')`. Karena form sebelumnya sudah ditandai *dirty* melalui `v$.value.$validate()`, nilai password yang menjadi string kosong (`""`) langsung dianggap tidak valid oleh Vuelidate, sehingga memicu pesan: `"Kolom ini wajib diisi."`.

### B. Isu Peran Super Admin (`super_admin`)
- **Gejala**: Pengguna dengan akun `superadmin@fotoapp.com` diarahkan ke halaman dashboard customer bukan dashboard admin.
- **Akar Masalah**: Pengecekan peran di `AuthenticatedSessionController.php`, `routes/web.php`, dan `bootstrap/app.php` hanya memeriksa `in_array($user->role, ['admin', 'photographer'])` tanpa menyertakan `super_admin`.

### C. Perbedaan Domain Email Akun Pengujian
- **Gejala**: Pengguna mencoba login dengan `admin@photoapp.com` (menggunakan huruf `ph`) dan ditolak karena akun default di database terdaftar sebagai `admin@fotoapp.com` (huruf `f`).
- **Solusi**: Sistem ditambahkan akun alias untuk kedua variasi domain (`@photoapp.com` dan `@fotoapp.com`).

---

## 2. Rincian Penyesuaian Kode (Code Changes)

### 1. Memaksa Skema HTTPS di Balik Reverse Proxy / Cloudflare
File: **[`app/Providers/AppServiceProvider.php`](file:///home/raditya/photo-review-laravel-vue/app/Providers/AppServiceProvider.php)**
```php
use Illuminate\Support\Facades\URL;

public function boot(): void
{
    // Deteksi jika request datang melalui proxy HTTPS (seperti Cloudflare Tunnel)
    if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
        URL::forceScheme('https');
    }

    Vite::prefetch(concurrency: 3);
}
```

### 2. Konfigurasi Trusted Proxies & Redirect Users
File: **[`bootstrap/app.php`](file:///home/raditya/photo-review-laravel-vue/bootstrap/app.php)**
```php
->withMiddleware(function (Middleware $middleware): void {
    // Percayai header proxy dari Cloudflare Tunnel / Docker Network
    $middleware->trustProxies(at: '*');

    $middleware->web(append: [
        \App\Http\Middleware\HandleInertiaRequests::class,
        \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
    ]);

    $middleware->alias([
        'role' => \App\Http\Middleware\RoleMiddleware::class,
    ]);

    // Sertakan super_admin ke redirect admin dashboard
    $middleware->redirectUsersTo(function (Request $request) {
        $user = $request->user();
        if ($user && in_array($user->role, ['super_admin', 'admin', 'photographer'])) {
            return route('admin.dashboard');
        }
        return route('customer.dashboard');
    });
})
```

### 3. Penyesuaian Controller Login & Route
File: **[`app/Http/Controllers/Auth/AuthenticatedSessionController.php`](file:///home/raditya/photo-review-laravel-vue/app/Http/Controllers/Auth/AuthenticatedSessionController.php)**
```php
public function store(LoginRequest $request): RedirectResponse
{
    $request->authenticate();
    $request->session()->regenerate();

    $user = $request->user();

    if (in_array($user->role, ['super_admin', 'admin', 'photographer'])) {
        return redirect()->intended(route('admin.dashboard', absolute: false));
    }

    return redirect()->intended(route('customer.dashboard', absolute: false));
}
```

File: **[`routes/web.php`](file:///home/raditya/photo-review-laravel-vue/routes/web.php)**
```php
Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        if (in_array($user->role, ['super_admin', 'admin', 'photographer'])) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('customer.dashboard');
    }
    return redirect()->route('login');
});
```

### 4. Perbaikan Validasi Form Login di Frontend
File: **[`resources/js/Pages/Auth/Login.vue`](file:///home/raditya/photo-review-laravel-vue/resources/js/Pages/Auth/Login.vue)**
```javascript
const submit = async () => {
    const isFormValid = await v$.value.$validate();
    if (!isFormValid) return;

    form.post(route('login'), {
        onFinish: () => {
            form.reset('password');
            v$.value.$reset(); // Reset state validasi Vuelidate agar tidak memunculkan error palsu
        },
    });
};
```

### 5. Penambahan Akun Alias di Seeder Database
File: **[`database/seeders/AdminSeeder.php`](file:///home/raditya/photo-review-laravel-vue/database/seeders/AdminSeeder.php)**
Telah ditambahkan pendaftaran akun otomatis untuk variasi `@photoapp.com` berdampingan dengan `@fotoapp.com`:
- `superadmin@photoapp.com`
- `admin@photoapp.com`
- `photographer@photoapp.com`

---

## 3. Metode Akses Aplikasi

| Metode Akses | URL / Alamat | Keterangan |
| :--- | :--- | :--- |
| **Localhost** | `http://localhost:8081` | Diakses langsung dari PC host server. |
| **Jaringan LAN (Wi-Fi)** | `http://192.168.0.104:8081` | Diakses dari device lain dalam satu router LAN. |
| **Tailscale VPN** | `http://100.117.108.52:8081` | Diakses melalui jaringan mesh private Tailscale. |
| **Internet / Publik (Cloudflare)** | `https://photo-app.raditya.biz.id` | Akses publik aman via HTTPS Cloudflare Tunnel. |

---

## 4. Kredensial Akun untuk Pengujian

Semua akun menggunakan password bawaan: **`password`**

| Peran | Pilihan Email 1 | Pilihan Email 2 | Password | Halaman Tujuan |
| :--- | :--- | :--- | :--- | :--- |
| **Super Admin** | `superadmin@photoapp.com` | `superadmin@fotoapp.com` | `password` | `/admin/dashboard` |
| **Admin** | `admin@photoapp.com` | `admin@fotoapp.com` | `password` | `/admin/dashboard` |
| **Fotografer** | `photographer@photoapp.com` | `photographer@fotoapp.com` | `password` | `/admin/dashboard` |
| **Customer** | `andi@example.com` | - | `password` | `/customer/dashboard` |

---

## 5. Panduan Maintenance & Re-build Aset

Jika di kemudian hari dilakukan perubahan pada kode Vue atau rute backend:

### 1. Build Aset Frontend (Vite)
```bash
# Mengompilasi frontend di dalam container Node
docker run --rm -v "$(pwd):/app" -w /app node:20-alpine sh -c "npm run build"

# Menyalin hasil build ke container volume Nginx / Laravel
docker compose -f docker-compose.prod.yml cp public/build app:/var/www/html/public/
```

### 2. Optimasi & Bersihkan Cache Laravel
```bash
docker compose -f docker-compose.prod.yml exec -T app php artisan optimize:clear
docker compose -f docker-compose.prod.yml exec -T app php artisan optimize
docker compose -f docker-compose.prod.yml exec -T app php artisan cache:clear
```

### 3. Memperbarui Seeder Data User
```bash
docker compose -f docker-compose.prod.yml exec -T app php artisan db:seed --class=AdminSeeder --force
```
