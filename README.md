# Peduli Karhutla

Peduli Karhutla adalah aplikasi web untuk membantu penggalangan dana bagi warga, satwa, dan relawan yang terdampak kebakaran hutan dan lahan. Penggalang dapat mengajukan kampanye dan mencatat perkembangan penyaluran dana. Donatur dapat memilih kampanye, melakukan donasi, serta melihat sertifikat setelah pembayaran berhasil.

Aplikasi ini dibuat menggunakan Laravel. Pembayaran dapat dicoba melalui simulasi lokal atau Midtrans Sandbox. **Mode Sandbox dan simulasi hanya untuk pengujian; keduanya tidak memotong uang sungguhan.**

## Fitur

- Pendaftaran dan masuk sebagai donatur atau penggalang dana.
- Pengajuan kampanye oleh penggalang dana.
- Pemeriksaan dan persetujuan kampanye oleh admin.
- Donasi dan pemantauan status pembayaran.
- Sertifikat untuk donasi yang berhasil.
- Dashboard penggalang untuk memantau dana terkumpul, jumlah donasi, kabar kampanye, dan laporan penyaluran.
- Pemeriksaan laporan penyaluran oleh admin.
- Tampilan berbahasa Indonesia dan Inggris.

## Teknologi

- PHP 8.2 atau lebih baru
- Laravel 11
- MySQL
- Composer
- Node.js dan npm
- Midtrans Sandbox (opsional)

## Menjalankan aplikasi di komputer lokal

### 1. Siapkan kebutuhan aplikasi

Pastikan PHP, Composer, Node.js, dan npm sudah terpasang. Jalankan MySQL melalui Laragon atau perangkat lokal lain.

### 2. Unduh source code

```powershell
git clone https://github.com/ElaineKeishamp/PeduliKarhutlaApplication.git
cd PeduliKarhutlaApplication
```

### 3. Pasang dependensi

```powershell
composer install
npm install
```

### 4. Siapkan file konfigurasi

Buat file `.env` dari contoh yang tersedia:

```powershell
Copy-Item .env.example .env
```

Buat application key Laravel:

```powershell
php artisan key:generate
```

### 5. Siapkan database

Buat database MySQL bernama `peduli_karhutla`. Sesuaikan bagian database di `.env` dengan pengaturan MySQL di komputer:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=peduli_karhutla
DB_USERNAME=root
DB_PASSWORD=
```

Jika MySQL-mu menggunakan nama pengguna atau kata sandi berbeda, sesuaikan nilainya. Tabel aplikasi dibuat melalui migration Laravel; file `database/desain_database.sql` disertakan sebagai referensi struktur database.

Jalankan migration dan data awal:

```powershell
php artisan migrate --seed
```

Seeder membuat kategori kampanye dan satu akun admin untuk pengujian lokal:

- Email: `admin@pedulikarhutla.test`
- Kata sandi: `password`

Kredensial tersebut hanya untuk demo lokal. Ganti kata sandi setelah masuk dan jangan gunakan kredensial demo untuk aplikasi publik.

### 6. Siapkan file publik dan aset

Buat symbolic link agar gambar yang diunggah dapat ditampilkan:

```powershell
php artisan storage:link
```

Bangun aset frontend:

```powershell
npm run build
```

### 7. Jalankan aplikasi

```powershell
php artisan serve
```

Buka alamat berikut di browser:

```text
http://127.0.0.1:8000
```

## Mencoba alur aplikasi

1. Daftar akun sebagai **penggalang dana**.
2. Ajukan kampanye dari menu penggalang.
3. Masuk menggunakan akun admin demo untuk meninjau dan menyetujui kampanye.
4. Daftar akun lain sebagai **donatur**, lalu masuk dan pilih kampanye yang sudah disetujui.
5. Lakukan donasi dan ikuti status pembayaran pada halaman tagihan.
6. Setelah pembayaran tercatat berhasil, sertifikat dapat dilihat dari menu donasi.

Untuk menguji laporan penyaluran, masuk sebagai penggalang pemilik kampanye, kirim laporan beserta foto bukti, lalu tinjau laporan tersebut menggunakan akun admin.

## Pengujian pembayaran

Secara bawaan, `.env.example` menggunakan pengaturan berikut:

```dotenv
MIDTRANS_ENABLED=false
MIDTRANS_SERVER_KEY=
MIDTRANS_CLIENT_KEY=
MIDTRANS_IS_PRODUCTION=false
```

Dengan pengaturan tersebut, aplikasi menggunakan **simulasi pembayaran lokal**. Simulasi ini hanya mengubah status transaksi untuk keperluan demonstrasi; tidak ada uang yang ditagihkan.

Midtrans Sandbox bersifat opsional. Untuk mencobanya, isi `MIDTRANS_SERVER_KEY` dan `MIDTRANS_CLIENT_KEY` dengan kunci dari dashboard **Sandbox** Midtrans, lalu ubah:

```dotenv
MIDTRANS_ENABLED=true
MIDTRANS_IS_PRODUCTION=false
```

Setelah mengubah `.env`, jalankan:

```powershell
php artisan config:clear
```

Gunakan kunci Sandbox untuk pengujian. Jangan memasukkan kunci Production ke repository atau membagikannya.

## Akun dan email

Akun donatur dan penggalang dana dapat dibuat melalui halaman pendaftaran. Akun admin dibuat oleh seeder seperti dijelaskan di atas.

Konfigurasi contoh menggunakan `MAIL_MAILER=log`, sehingga email seperti tautan reset kata sandi tidak dikirim ke kotak masuk. Untuk pengujian lokal, periksa log aplikasi di `storage/logs/laravel.log`.

## Menjalankan pengujian otomatis

```powershell
php artisan test
```

## Catatan konfigurasi

File `.env` berisi pengaturan lokal dan tidak disertakan di repository. Setelah menyalin `.env.example`, setiap pengguna perlu mengatur koneksi database dan membuat `APP_KEY` sendiri.

Aplikasi ini disiapkan untuk demonstrasi dan pengujian lokal. Pembayaran Sandbox maupun simulasi bukan transaksi sungguhan.
```
