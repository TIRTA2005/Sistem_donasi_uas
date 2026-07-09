# 🌟 DonasiKita - Sistem Manajemen Donasi Terpadu

DonasiKita adalah sebuah aplikasi **Sistem Donasi Berbasis Web** yang dibangun menggunakan PHP murni (Native) dengan arsitektur prosedural. Sistem ini memfasilitasi proses penggalangan dana secara digital yang melibatkan berbagai entitas pengguna, mulai dari Donatur hingga Verifikator kampanye. 

Proyek ini dibuat sebagai syarat ketuntasan **Ujian Akhir Semester (UAS)**.

---

## 👥 Hak Akses & Entitas Pengguna (Role)

Sistem ini memiliki **4 level pengguna (Role)** dengan hak akses masing-masing:

1. **👑 Admin**
   - Mengelola seluruh data pengguna (Tambah, Edit, Hapus User).
   - Menetapkan atau mengubah *Role* dari setiap akun.
   - Mengawasi keseluruhan aktivitas dalam sistem.

2. **📝 Campaigner (Penggalang Dana)**
   - Mengajukan kampanye/program donasi baru.
   - Mengunggah gambar (banner) dan menetapkan target dana.
   - Mengelola kampanye miliknya dan memperbarui laporan progres kampanye yang telah berjalan.

3. **🛡️ Verifikator**
   - Meninjau kampanye donasi yang diajukan oleh *Campaigner*.
   - Menerima (Approve) atau Menolak (Reject) kampanye agar dapat tayang di halaman utama.

4. **🤝 Donatur**
   - Melihat daftar kampanye donasi yang sudah disetujui (*Active*).
   - Melakukan donasi melalui **Simulasi Pembayaran (Offline Gateway)** tanpa perlu koneksi ke pihak ketiga.
   - Melihat riwayat transaksi donasi pribadi.

---

## 🛠️ Teknologi yang Digunakan

- **Backend**: PHP 8.x (Native / Prosedural)
- **Database**: MySQL (Menggunakan ekstensi PDO untuk keamanan injeksi SQL)
- **Frontend**: HTML5, CSS3 murni, dan Bootstrap 5 (untuk struktur Dashboard & UI)
- **Keamanan (Security)**:
  - *Password Hashing* (BCrypt)
  - Pencegahan *SQL Injection* (PDO Prepared Statements)
  - Pencegahan *XSS* (Escaping HTML output)
  - Pencegahan *Session Fixation* pada mekanisme Login
  - Validasi keamanan tipe file (MIME type) untuk *Upload Banner*.

---

## 📁 Struktur Direktori

```text
Sistem_donasi_uas/Donasi_uas/
├── assets/
│   └── css/               # File CSS terpisah (landing.css, auth.css, dll)
├── config/
│   └── koneksi.php        # Konfigurasi penghubung ke database MySQL (PDO)
├── core/
│   ├── middleware.php     # Fungsi pembatas hak akses dan sesi (Auth Middleware)
│   └── security.php       # Fungsi keamanan (XSS Filtering)
├── uploads/               # Direktori penyimpanan gambar banner kampanye (JPG/PNG)
├── index.php              # Halaman Utama (Landing Page) & Daftar Kampanye Aktif
├── login.php              # Halaman Autentikasi Masuk
├── register.php           # Halaman Pendaftaran Donatur Baru
├── dashboard_*.php        # Halaman Dasbor khusus untuk masing-masing Role (Admin, Campaigner, dll)
├── kelola_*.php           # Modul pengolahan data (User & Kategori)
└── update_status_ajax.php # Handler API (Backend) untuk simulasi pembayaran donasi offline
```

---

## 🚀 Panduan Instalasi (Menjalankan Proyek)

Ikuti langkah-langkah berikut untuk menjalankan sistem secara lokal:

1. **Siapkan Lingkungan Server Lokal**
   Pastikan Anda telah menginstal XAMPP, WAMP, atau aplikasi web server serupa.
2. **Kloning/Salin Direktori**
   Pindahkan folder proyek ini ke dalam folder `htdocs` (jika menggunakan XAMPP). Contoh: `C:\xampp\htdocs\Sistem_donasi_uas\Donasi_uas\`
3. **Konfigurasi Database**
   - Buka `phpMyAdmin` (biasanya melalui `http://localhost/phpmyadmin`).
   - Buat database baru.
   - *Import* rancangan tabel database sistem donasi ke dalam database yang baru dibuat (atau jalankan skrip `fix_db.php` jika tersedia).
4. **Sesuaikan Konfigurasi Koneksi**
   Buka file `config/koneksi.php` dan pastikan nama database, *username*, dan *password* cocok dengan konfigurasi database server lokal Anda.
5. **Jalankan Aplikasi**
   Buka peramban (browser) dan akses `http://localhost/Sistem_donasi_uas/Donasi_uas/`.

---

## 💳 Simulasi Pembayaran (Mock Gateway)

Proyek ini telah dilengkapi dengan fitur **Simulasi Pembayaran (Mock Payment)** secara *offline*. 
Saat donatur menekan tombol "Kirim Donasi", sistem akan menampilkan *popup* (Modal) seolah-olah seperti *Payment Gateway* asli (menggantikan integrasi Midtrans untuk keperluan demonstrasi *offline*). Status donasi akan otomatis diperbarui tanpa bergantung pada API server pihak ketiga.

---

> Dibuat dengan 💻 untuk melengkapi penugasan Ujian Akhir Semester.
