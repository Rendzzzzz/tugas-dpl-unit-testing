# Pusat Data & Informasi Alumni (Sistem Pelacakan Lulusan)

Sistem Informasi Pelacakan Alumni berskala enterprise (berbasis PHP & MySQL) yang dikembangkan untuk memanajemen, melacak, serta melakukan analisis data lulusan dalam skala besar (teruji hingga 142.000+ baris data).

Sistem ini didesain dengan konsep **Dashboard Premium** (*Glassmorphism*, Sidebar Modern) serta mematuhi **standar 8 Poin Data Lulusan** secara komprehensif.

---

## 🎯 Fitur & Kemampuan Utama

### 1. Manajemen 8 Poin Data Esensial Khusus
Aplikasi dirancang agar secara ketat mampu menyerap, mengkategorikan, serta mengekspor parameter kunci berikut:
1. **Portal Media Sosial Pribadi:** LinkedIn, Instagram, Facebook, TikTok.
2. **Email Aktif.**
3. **No. Handphone / WhatsApp.**
4. **Nama Instansi Tempat Bekerja.**
5. **Alamat Lengkap Perusahaan.**
6. **Posisi & Jabatan.**
7. **Sektor Profesi:** Klasifikasi spesifik menjadi ASN/PNS, Pegawai Swasta, atau Wirausahawan.
8. **Portal Media Sosial Perusahaan/Instansi.**

### 2. High-Performance Batch Data (Big Data Grade)
*   **Transaksi Jutaan Baris:** Sanggup melakukan validasi dan import massal `csv` tanpa jeda kritis *(Server-side array batch commit per 5.000 row)*.
*   **Smart Pagination:** Daftar alumni dengan sistem terpotong berdasarkan halaman untuk mitigasi bottleneck serta dilengkapi sistem pencarian multi-filter pintar.
*   **Auto Data Generation:** Terdapat modul force-fill yang dapat mengenerate ribuan informasi tiruan serealistis mungkin (Sesuai 8 poin utama) untuk kepentingan demonstrasi proyek / testing.

### 3. Modul Enterprise-Ready UI (Anti-Generik)
Meninggalkan antarmuka tabel biasa, aplikasi menggunakan:
*   Tipografi interaktif dari `Inter`.
*   FontAwesome `6.4.0` terintegrasi penuh.
*   Skema warna Navy-Slate-Blue yang menonjol dan minimalis.
*   Smart contact-pills & Social Media link icons.

### 4. Sistem Laporan (Reporting & Exporting)
*   **Printable Rekapitulasi (Induk Data):** Halaman yang siap dicetak bersih menjadi dokumen cetak legal (Print-friendly format).
*   **Export to Microsoft Excel:** Kemampuan mengekspor lebih dari 142,000 baris ke file ekstensif `.csv` / `.xls` dengan penjabaran titik kolom presisi tinggi menggunakan pure stream-loop.

---

## 💻 Tech Stack
- **Bahasa Engine**: Native PHP 8.x
- **Database Engine**: MySQL / MariaDB (UTF8MB4 Encoding)
- **Frontend Layer**: HTML5, Vanilla Premium CSS, FontAwesome Iconography

---

## ⚙️ Panduan Instalasi (Laragon / XAMPP)

1. Klon / letakkan folder project ini pada direktori `www` (Laragon) atau `htdocs` (XAMPP).
2. Nyalakan layanan **Apache** dan **MySQL** Anda.
3. Buka **phpMyAdmin** (`http://localhost/phpmyadmin/`).
4. Buatlah database kosong baru dengan nama sesuka Anda (Disarankan: `alumni_db`).
5. Impor file relasional tabel dari `./database.sql` ke dalam database tersebut.
6. Konfigurasikan konektor di `config.php`:
   ```php
   $servername = "localhost";
   $username = "root"; // Default Laragon/XAMPP
   $password = "";     // Password Anda (Kosongi jika default)
   $dbname = "alumni_db";
   ```
7. Akses sistem via: `http://localhost/alumni-tracking-system/login.php`

> **Note:** Kredensial default admin adalah:  
> **Username:** admin  
> **Password:** admin

---

## 🔒 Sekuritas
*   Semua titik endpoint dilindungi oleh sistem token validasi `auth.php` yang menjaga sesi multi-layer.
*   Halaman login terisolasi total (`index-free leak`).

---

## 🧪 Tabel Hasil Pengujian Sistem (Quality Testing)
Berikut adalah daftar pengujian fungsionalitas dan ketahanan sistem yang telah dilakukan secara komprehensif.

| No | Ruang Lingkup / Skenario Pengujian | Aspek Kualitas | Ekspektasi Hasil | Status Pengujian |
|:---:|---|---|---|:---:|
| 1 | **Login Validation & Session Firewall** | Keamanan (Security) | Mencegah akses paksa ke panel admin tanpa login yang sah. | ✅ **Pass** |
| 2 | **Bulk Batch Import (142.000 Baris Data)** | Performa (Stress Test) | Menelan, membaca, dan memvalidasi ratusan ribu data tanpa *Server Timeout*. | ✅ **Pass** |
| 3 | **Filter Pencarian Skala Besar** | Fungsionalitas | Mampu menemukan spesifik 1 nama NIM dari kolam pencarian 142.000+ data secara instan. | ✅ **Pass** |
| 4 | **Suntik Integrasi 8 Poin Data Khusus** | Fungsionalitas & Akurasi | Men-generate lengkap semua poin sosmed & alamat kerja per orang tanpa ada null. | ✅ **Pass** |
| 5 | **Smart Pagination Management** | Usability / UI | Membatasi tampilan memori rendering DOM dengan *limiter* cerdas per halaman. | ✅ **Pass** |
| 6 | **Export Excel Module `.csv`** | Keandalan (Reliability) | Ekspor file rapi berisi 17 kolom mendetail (termasuk 8 target poin) tanpa *data corrupt*. | ✅ **Pass** |

---

## ✍️ Kontributor
*Hak Cipta Dilindungi © 2026. Pusat Data & Informasi Alumni.*
# Sistem-informasi-alumni
# tugas-dpl
