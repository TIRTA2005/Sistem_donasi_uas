# 🎬 Skenario Demo Aplikasi (UAS)

Dokumen ini adalah panduan langkah demi langkah (skrip) yang bisa Anda ikuti saat melakukan presentasi / demo aplikasi **DonasiKita** di depan dosen penguji. Ikuti alur ini agar semua fitur unggulan aplikasi Anda terlihat dengan jelas!

---

## 🕒 Persiapan Sebelum Demo
1. Pastikan XAMPP (Apache & MySQL) sudah berjalan.
2. Pastikan Anda sudah memiliki setidaknya 4 akun dengan role yang berbeda (Admin, Campaigner, Verifikator, Donatur). *Bisa menggunakan akun dummy yang sudah kita buat sebelumnya.*
3. Buka browser dan siapkan tab pada alamat: `http://localhost/Sistem_donasi_uas/Donasi_uas/index.php`.
4. Pastikan Anda dalam keadaan **Log Out** (belum login akun siapapun).

---

## 🎬 Mulai Presentasi

### 1️⃣ Pembukaan (Halaman Utama / Landing Page)
- **Yang Dilakukan**: Buka `index.php`. Scroll perlahan dari atas ke bawah.
- **Yang Diucapkan**: 
  > *"Selamat pagi/siang Bapak/Ibu. Ini adalah proyek sistem donasi berbasis web yang saya bangun bernama **DonasiKita**. Di halaman utama ini, pengunjung bisa langsung melihat daftar kampanye donasi yang sedang aktif dan berjalan, lengkap dengan progres dana yang terkumpul."*

### 2️⃣ Skenario Penggalang Dana (Campaigner)
- **Yang Dilakukan**: Klik tombol **Login** di pojok kanan atas. Masuk menggunakan akun **Campaigner**. Setelah masuk, Anda akan diarahkan ke *Dashboard Campaigner*.
- **Yang Dilakukan**: Di Form "Ajukan Kampanye Baru", isi form:
  - Judul: *Bantu Pembangunan Panti Asuhan X*
  - Kategori: *Pendidikan / Sosial*
  - Deskripsi: *Panti asuhan ini membutuhkan dana untuk perbaikan atap.*
  - Target Dana: *Rp 5.000.000*
  - Gambar Banner: *(Pilih gambar bebas dari komputer)*
- **Yang Dilakukan**: Klik tombol **Ajukan Kampanye**. Tunjukkan di tabel bawah bahwa status kampanye adalah **"Pending"** (kuning).
- **Yang Diucapkan**: 
  > *"Sebagai penggalang dana (Campaigner), saya bisa membuat kampanye baru dengan mengunggah banner. Tapi demi keamanan, kampanye yang baru dibuat tidak akan langsung tampil di halaman depan. Statusnya masih 'Pending' dan butuh persetujuan."*
- **Yang Dilakukan**: Klik tombol **Logout**.

### 3️⃣ Skenario Verifikator (Validasi Data)
- **Yang Dilakukan**: Login menggunakan akun **Verifikator**.
- **Yang Dilakukan**: Di *Dashboard Verifikator*, tunjukkan kampanye yang baru saja dibuat tadi.
- **Yang Dilakukan**: Klik tombol **Setujui (Approve)**. Statusnya akan berubah menjadi **Active** (hijau).
- **Yang Diucapkan**:
  > *"Sistem ini dilengkapi role Verifikator untuk mencegah penipuan. Verifikator bertugas mengecek kelayakan kampanye. Setelah disetujui, barulah kampanye ini akan tayang dan bisa menerima donasi."*
- **Yang Dilakukan**: Klik tombol **Logout**.

### 4️⃣ Skenario Donatur & Simulasi Pembayaran (Mock Gateway)
- **Yang Dilakukan**: Login menggunakan akun **Donatur** (atau daftar akun baru jika ingin menunjukkan fitur Register).
- **Yang Dilakukan**: Pergi ke **Beranda (Halaman Utama)**. Tunjukkan bahwa kampanye panti asuhan tadi sekarang sudah muncul di halaman depan.
- **Yang Dilakukan**: Klik **Berdonasi** pada kampanye tersebut.
- **Yang Dilakukan**: Masukkan nominal donasi, misalnya: `100000`. Lalu klik **Kirim Donasi via Midtrans**.
- **Yang Dilakukan**: *Popup* **Simulasi Pembayaran (Offline)** akan muncul.
- **Yang Diucapkan**:
  > *"Untuk mempermudah demonstrasi secara offline (tanpa API Key sungguhan), saya telah membuat sistem Simulasi Payment Gateway. Ini meniru cara kerja popup asli (seperti Midtrans). Donatur bisa menyelesaikan pembayaran secara lokal."*
- **Yang Dilakukan**: Klik **Bayar Sekarang**. Akan muncul alert sukses.
- **Yang Dilakukan**: Cek **Riwayat Donasi** di bawah form, tunjukkan statusnya sudah berubah menjadi **Berhasil** (hijau) dan dana terkumpul di halaman utama sudah bertambah.
- **Yang Dilakukan**: Klik tombol **Logout**.

### 5️⃣ Skenario Administrator (Super User)
- **Yang Dilakukan**: Login menggunakan akun **Admin**.
- **Yang Dilakukan**: Buka **Kelola Pengguna**. Tunjukkan daftar pengguna dan cara mengganti role (misal dari Donatur ke Campaigner).
- **Yang Diucapkan**:
  > *"Terakhir, Admin memiliki kendali penuh untuk mengelola pengguna, menghapus akun, serta mengatur kategori donasi agar terstruktur dengan rapi."*

### 6️⃣ Penutup (Keunggulan Sistem & Keamanan)
- **Yang Diucapkan**:
  > *"Sebagai tambahan, saya juga telah menanamkan beberapa fitur keamanan (Security) pada kode proyek ini. Di antaranya adalah sistem pencegahan **SQL Injection** menggunakan PDO, pencegahan unggah file berbahaya dengan verifikasi tipe file (MIME) pada gambar banner, serta sistem keamanan Login yang kebal terhadap serangan Session Fixation."*
  > *"Demikian presentasi dari sistem DonasiKita, terima kasih atas perhatiannya."*

---

> **Tips Tambahan**: Bicaralah dengan santai dan tidak terburu-buru. Pastikan internet tidak diperlukan selama demo karena semua sistem (termasuk *payment gateway*) sudah dirancang untuk berjalan 100% *offline*. Semoga nilai UAS Anda memuaskan! 🎉
