# Asesmen CPL Mahasiswa

Sistem Informasi Asesmen Capaian Pembelajaran Lulusan (CPL) dan Capaian Pembelajaran Mata Kuliah (CPMK) untuk mahasiswa.

## Deskripsi Project

Aplikasi web berbasis Laravel untuk mengelola dan menilai pencapaian pembelajaran mahasiswa sesuai dengan standar CPL dan CPMK yang telah ditetapkan. Sistem ini memungkinkan administrator untuk:

- Mengelola data **CPL** (Capaian Pembelajaran Lulusan) per program studi
- Mengelola data **CPMK** (Capaian Pembelajaran Mata Kuliah) dan relasinya dengan CPL
- Mengelola **Matriks CPMK** dengan bobot komponen penilaian (Part, Tugas, UTS, UAS)
- Melakukan **pemetaan mata kuliah** terhadap CPL
- Mengelola data **mahasiswa** dan **nilai** per mata kuliah
- Melihat **capaian CPL mahasiswa** secara individual maupun agregat
- **Import data** CPL, CPMK, dan Matriks CPMK dari file Excel dengan preview sebelum disimpan

## Tech Stack

- **Framework**: Laravel 8.x
- **Database**: MySQL
- **Frontend**: Bootstrap 5, Font Awesome, AdminLTE
- **Excel Processing**: Maatwebsite Excel
- **Authentication**: Laravel Breeze (default)

## Model Data

### Entitas Utama

| Model | Deskripsi |
|-------|-----------|
| `Prodi` | Program Studi |
| `Cpl` | Capaian Pembelajaran Lulusan |
| `Cpmk` | Capaian Pembelajaran Mata Kuliah |
| `MatriksCpmk` | Matriks CPMK dengan bobot Part, Tugas, UTS, UAS |
| `PemetaanMk` | Pemetaan Mata Kuliah terhadap CPL |
| `Mahasiswa` | Data Mahasiswa |
| `NilaiMahasiswa` | Nilai mahasiswa per CPMK |
| `CapaianCplMahasiswa` | Capaian CPL per mahasiswa |

### Relasi Antar Entitas

```
Prodi (1) ─── (N) Cpl (1) ─── (N) Cpmk (1) ─── (N) MatriksCpmk
                                      │
                                      └── (N) NilaiMahasiswa (N) Mahasiswa
                                                              │
                                                              └── (N) CapaianCplMahasiswa
```

## Fitur

### Manajemen Data Master
- CRUD lengkap untuk CPL, CPMK, Matriks CPMK
- Filter dan pencarian data
- Pagination untuk data besar

### Import Excel
- Import data CPL dari file Excel
- Import data CPMK dan Matriks CPMK
- Preview sebelum proses import
- Deteksi data baru vs update
- Download template Excel

### Penilaian
- Input nilai mahasiswa per CPMK
- Komponen penilaian: Part, Tugas, UTS, UAS
- Perhitungan otomatis capaian CPL mahasiswa

### Dashboard & Pelaporan
- View capaian CPL mahasiswa
- View detail CPL dan CPMK
- Statistik per program studi

## Instalasi

### Prerequisites

- PHP >= 7.4
- Composer
- MySQL >= 5.7
- Laravel requirements (ext-dom, ext-zip, dll)

### Langkah Instalasi

```bash
# Clone repository
git clone <repository-url>
cd asesmen_cpl_mhs

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Edit .env untuk konfigurasi database
# DB_DATABASE=asesmen_cpl_mhs
# DB_USERNAME=root
# DB_PASSWORD=

# Generate application key
php artisan key:generate

# Jalankan migrasi database
php artisan migrate

# (Opsional) Seed data awal
php artisan db:seed

# Jalankan server development
php artisan serve
```

Akses aplikasi di `http://localhost:8000`

## Struktur Direktori

```
asesmen_cpl_mhs/
├── app/
│   ├── Http/Controllers/Admin/    # Controller untuk fitur admin
│   │   ├── CplController.php
│   │   ├── CpmkController.php
│   │   ├── MatriksCpmkController.php
│   │   ├── ImportMasterController.php
│   │   └── ...
│   └── Models/                    # Eloquent Models
│       ├── Cpl.php
│       ├── Cpmk.php
│       ├── MatriksCpmk.php
│       └── ...
├── database/migrations/           # Database migrations
├── resources/views/admin/         # Blade views untuk admin
│   ├── cpl/
│   ├── cpmk/
│   ├── matriks_cpmk/
│   └── ...
└── routes/web.php                # Route definitions
```

## Route Utama

| URI | Deskripsi |
|-----|----------|
| `/admin/cpl` | Manajemen CPL |
| `/admin/cpmk` | Manajemen CPMK |
| `/admin/matriks-cpmk` | Manajemen Matriks CPMK |
| `/admin/mahasiswa` | Manajemen Mahasiswa |
| `/admin/nilai-mahasiswa` | Input & Edit Nilai |
| `/admin/pemetaan-mk` | Pemetaan Mata Kuliah |
| `/admin/capaian-cpl` | Capaian CPL Mahasiswa |
| `/admin/import-master` | Import Data Master |

## Konfigurasi Bobot Penilaian

Bobot default untuk komponen penilaian di Matriks CPMK:

| Komponen | Bobot Default |
|----------|--------------|
| Part | 10% |
| Tugas | 20% |
| UTS | 30% |
| UAS | 40% |

> Bobot dapat disesuaikan per CPMK saat membuat/mengedit matriks.

## Kontribusi

1. Fork repository
2. Buat branch baru (`git checkout -b fitur-baru`)
3. Commit perubahan (`git commit -m 'Menambah fitur baru'`)
4. Push ke branch (`git push origin fitur-baru`)
5. Buat Pull Request

## Lisensi

Project ini adalah open-source software.

## Catatan Development

- Gunakan `php artisan serve` untuk development server
- Untuk reset database: `php artisan migrate:fresh --seed`
- Log database query: cek `storage/logs/laravel.log`
