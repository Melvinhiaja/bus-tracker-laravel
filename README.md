# 🚌 Laravel User Admin – Sistem Pemesanan Tiket & Live Tracking Bus

Aplikasi ini adalah sistem **pemesanan tiket bus gratis** berbasis Laravel yang mendukung **multi-role login** (Admin, Driver, Karyawan).  
Proyek ini dirancang untuk membantu pengelolaan penumpang secara digital serta melacak posisi bus dan validasi penumpang berbasis lokasi dan identitas.

---

## 📌 Fitur Utama

- 🔐 **Autentikasi Multi-Role**: Admin, Driver, dan Karyawan memiliki dashboard masing-masing
- 🧭 **Live Tracking** posisi bus & penumpang menggunakan TomTom Maps + HTML5 Geolocation
- 🪪 **Scan KTP (OCR)** untuk validasi penumpang lokal
- 🧾 **Generate & Scan QR Code** tiket per penumpang
- 📈 **Export Laporan** ke PDF, Excel, dan Word
- ⚠️ **Validasi Penumpang** (naik / tidak naik) berbasis sistem

---

## ⚙️ Teknologi & Tools

### Backend (Laravel)
- Laravel 10+
- Spatie Laravel-Permission (Role Management)
- Laravel Validator & Session
- Blade Templates
- Routing (web.php)

### Pemetaan & Pelacakan
- **TomTom Maps SDK**
  - Map rendering
  - Routing API
  - Marker & polyline untuk rute
- **Geolocation API (HTML5)**
  - `getCurrentPosition()` dan `watchPosition()` untuk update posisi

### Frontend
- HTML, Bootstrap
- SweetAlert2 (notifikasi popup)
- JavaScript (AJAX, DOM Manipulation)
- Custom marker dari Flaticon CDN

### Realtime Movement
- `setInterval()` setiap 5 detik untuk refresh posisi
- Marker peta update otomatis

### Tiket & Validasi
- **Tesseract.js** (OCR) untuk scan KTP
- QRCode generator (misalnya: `simple-qrcode`)
- **Laravel-Excel** untuk export laporan ke:
  - `.pdf` (PDF)
  - `.xlsx` (Excel)
  - `.doc` (Word)

---


