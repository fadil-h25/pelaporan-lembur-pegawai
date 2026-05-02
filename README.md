# Sistem Pelaporan Lembur Pegawai

Aplikasi web untuk mengelola dan melaporkan lembur pegawai menggunakan Laravel 12.

## Fitur Utama

- ✅ Manajemen pengguna (Admin, Operator, Pegawai)
- ✅ Pengajuan dokumen lembur
- ✅ Sistem nomor surat otomatis dengan logika sisipan
- ✅ Export dokumen ke format Word (.docx)
- ✅ Dashboard statistik
- ✅ Role-based access control

## Konfigurasi Sistem

### System Settings

Aplikasi menggunakan konfigurasi sistem yang disimpan di file `config/system.php`. Untuk mengubah pengaturan sistem, edit file tersebut langsung:

```php
<?php

return [
    'nama_kasek' => 'AWALUDDIN MUSTAFA, S.E., M.Si',  // Ubah nama kasek di sini
    'nip_kasek' => '19740712 200212 1 006',           // Ubah NIP kasek di sini
    'akhiran_surat_spk' => '/SPKL/SN/',               // Ubah akhiran surat SPK di sini
    'akhiran_surat_lpj' => '/LPJ/SN/',                // Ubah akhiran surat LPJ di sini
];
```

### Penjelasan Konfigurasi

- `nama_kasek`: Nama lengkap Kepala Sekretariat yang akan muncul di dokumen cetak
- `nip_kasek`: Nomor Induk Pegawai Kepala Sekretariat
- `akhiran_surat_spk`: Format akhir nomor surat untuk SPK (contoh: `/SPKL/SN/` akan menghasilkan `0001.0/SPKL/SN/05/2026`)
- `akhiran_surat_lpj`: Format akhir nomor surat untuk LPJ (contoh: `/LPJ/SN/` akan menghasilkan `0001.0/LPJ/SN/05/2026`)

### DEPRECATED Features

Beberapa fitur berikut sudah tidak digunakan lagi dan digantikan dengan config file:

- Halaman `/pengaturan-sistem` (deprecated)
- Tabel `system_settings` di database (deprecated)
- Model `SystemSetting` (deprecated)
- Environment variables `NAMA_KASEK`, `NIP_KASEK`, `AKHIRAN_SURAT` (deprecated)

Fitur deprecated tetap ada untuk backward compatibility tapi tidak direkomendasikan untuk digunakan.

## Instalasi

1. Clone repository
2. Copy `.env.example` ke `.env`
3. Konfigurasi database di `.env`
4. Jalankan `composer install`
5. Jalankan `php artisan migrate`
6. Jalankan `php artisan serve`

## Penggunaan

### Login

- **Admin**: username admin, password sesuai setup
- **Operator**: username operator, password sesuai setup
- **Pegawai**: username pegawai, password sesuai setup

### Fitur Admin

- Manajemen user
- Melihat semua dokumen lembur
- Mengubah konfigurasi sistem via `.env`

### Fitur Pegawai

- Mengajukan lembur
- Melihat riwayat lembur sendiri
- Download dokumen SPK dan LPJ

---

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

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
