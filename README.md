<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities



🏆 Platform Lelang Daring
Aplikasi lelang online real-time yang dibangun dengan Laravel 13, Vue.js 3, dan Laravel Reverb untuk komunikasi WebSocket. Platform ini memungkinkan penjual membuat lelang dan pembeli melakukan penawaran secara real-time dengan update otomatis tanpa perlu refresh halaman.









📋 Daftar Isi
Fitur Utama
Teknologi yang Digunakan
Persyaratan Sistem
Instalasi
Konfigurasi
Cara Menjalankan Aplikasi
Cara Testing Real-time
Struktur Proyek
API Endpoints
Real-time Features
Scheduler Tasks
Troubleshooting
Command Cheat Sheet
Kontribusi
Lisensi
✨ Fitur Utama
🔐 Autentikasi & Otorisasi
Login dan Register dengan Laravel Sanctum
Role-based access control (Penjual & Pembeli)
Token-based authentication untuk API
🏪 Manajemen Lelang
Buat Lelang: Penjual dapat membuat lelang dengan detail produk, harga awal, dan durasi
Auto-Update Status: Scheduler otomatis mengubah status lelang (scheduled → active → ended)
Upload Gambar: Upload gambar produk untuk setiap lelang
Deskripsi Lengkap: Tambahkan deskripsi detail untuk setiap produk
💰 Sistem Penawaran (Bidding)
Real-time Bidding: Penawaran dikirim dan diterima secara real-time via WebSocket
Validasi Otomatis: Sistem memvalidasi minimum bid berdasarkan increment
Riwayat Penawaran: Lihat semua penawaran yang masuk dengan timestamp
Notifikasi Outbid: Pemberitahuan real-time ketika penawaran dikalahkan
⏰ Fitur Real-time
Auto-Start Lelang: Lelang otomatis dimulai saat waktu starts_at tiba
Auto-End Lelang: Lelang otomatis berakhir saat waktu ends_at tiba
Live Updates: Semua perubahan status dan penawaran terupdate tanpa refresh
Countdown Timer: Timer countdown untuk setiap lelang aktif
WebSocket Broadcasting: Event broadcast ke semua subscriber via Laravel Reverb
🏆 Penentuan Pemenang
Automatic Winner Detection: Sistem otomatis menentukan pemenang berdasarkan bid tertinggi
Final Price Tracking: Harga akhir lelang tercatat dengan jelas
Winner Notification: Pemenang mendapat notifikasi real-time
📊 Dashboard & Statistik
My Auctions: Penjual dapat melihat semua lelang yang dibuat
My Bids: Pembeli dapat melihat riwayat penawaran
My Wins: Pembeli dapat melihat lelang yang dimenangkan
Statistics: Total penawaran, harga tertinggi, dan statistik lainnya
🛠️ Teknologi yang Digunakan
Backend
Laravel 13 - PHP Framework
Laravel Reverb - WebSocket Server
Laravel Sanctum - Authentication
Laravel Scheduler - Task Scheduling
MySQL - Database
Frontend
Vue.js 3 - JavaScript Framework
Pinia - State Management
Vue Router - Client-side Routing
Axios - HTTP Client
Tailwind CSS - Styling
Vite - Build Tool
Day.js - Date formatting
Real-time Communication
Laravel Echo - WebSocket Client
Pusher Protocol - Broadcasting Protocol
Reverb Server - WebSocket Server
📦 Persyaratan Sistem
PHP >= 8.2
Composer (terbaru)
Node.js >= 18.x
NPM >= 9.x
MySQL >= 8.0
Git
🚀 Instalasi
1. Clone Repository
bash
12
2. Install Backend Dependencies
bash
1
3. Install Frontend Dependencies
bash
123
4. Setup Environment
bash
12345
5. Konfigurasi Database
Edit file .env dan sesuaikan konfigurasi database:
env
123456
6. Konfigurasi Reverb WebSocket
Tambahkan konfigurasi berikut di .env:
env
1234567891011121314
7. Jalankan Migration
bash
1
8. Link Storage
bash
1
⚙️ Konfigurasi
Konfigurasi Broadcasting
Pastikan file config/broadcasting.php menggunakan Reverb:
php
1
Konfigurasi Reverb
Pastikan file config/reverb.php membaca dari .env:
php
12345678
Konfigurasi Channels
Pastikan file routes/channels.php ada dengan definisi channel:
php
1234567891011
Konfigurasi Broadcasting Routes
Tambahkan di routes/web.php:
php
1
🎯 Cara Menjalankan Aplikasi
📋 Persiapan Awal
Sebelum menjalankan aplikasi, pastikan semua service berikut siap:
Service
Command
Fungsi
Laravel Server
php artisan serve
Backend API
Reverb Server
php artisan reverb:start
WebSocket real-time
Scheduler
php artisan schedule:work
Auto-update status lelang
Vite
npm run dev
Frontend development
Queue Worker
php artisan queue:work
Background jobs (opsional)
🖥️ Langkah 1: Buka 4 Terminal
Buka 4 terminal/command prompt secara terpisah di folder project.
⚙️ Terminal 1: Laravel Server
powershell
12
Output yang diharapkan:
1
⚙️ Terminal 2: Reverb WebSocket Server
powershell
12
Output yang diharapkan:
1
💡 Tips: Flag --debug berguna untuk melihat log broadcasting saat development.
️ Terminal 3: Scheduler (Auto-Update Status)
powershell
12
Output yang diharapkan:
1
️ Penting: Scheduler ini yang membuat lelang otomatis berubah status dari scheduled → active → ended sesuai waktu yang ditentukan.
⚙️ Terminal 4: Vite (Frontend)
powershell
12
Output yang diharapkan:
123
🌐 Akses Aplikasi
Buka browser dan akses:
1
🧪 Cara Testing Real-time
Test 1: Auto-Update Status Lelang
Login sebagai penjual
Buat lelang baru dengan waktu mulai 1 menit dari sekarang
Buka 2 tab browser di halaman home
Tunggu 1-2 menit
Lihat hasilnya:
Lelang otomatis pindah dari section "Lelang Akan Datang" ke "Lelang Aktif"
Tanpa refresh halaman!
Test 2: Real-time Bidding
Buka 2 browser berbeda (atau incognito mode)
Login dengan 2 user berbeda
Buka halaman detail lelang yang sama di kedua browser
Lakukan bid dari browser pertama
Lihat browser kedua:
Harga tertinggi otomatis terupdate
Riwayat penawaran bertambah
Tanpa refresh halaman!
Test 3: Notifikasi Outbid
User A melakukan bid tertinggi
User B melakukan bid lebih tinggi
User A mendapat notifikasi: "Anda telah dikalahkan di lelang..."

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
