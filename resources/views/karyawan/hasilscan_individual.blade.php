# 🚌 Laravel User Admin – Bus Ticket Booking & Live Tracking System  
# 🚌 Laravel User Admin – Sistem Pemesanan Tiket & Pelacakan Bus

This is a **Laravel-based free bus ticket booking system** that supports **multi-role login** (Admin, Driver, Employee).  
The project is designed to manage passengers digitally and track the position of buses and validate passengers based on location and identity.

Ini adalah sistem **pemesanan tiket bus gratis berbasis Laravel** yang mendukung **multi-role login** (Admin, Driver, Karyawan).  
Proyek ini dirancang untuk mengelola penumpang secara digital serta melacak posisi bus dan memvalidasi penumpang berdasarkan lokasi dan identitas.

---

## 📌 Main Features  
## 📌 Fitur Utama

- 🔐 **Multi-Role Authentication**: Admin, Driver, and Employee dashboards  
- 🔐 **Autentikasi Multi-Role**: Dashboard berbeda untuk Admin, Driver, dan Karyawan

- 🧭 **Live Tracking** of buses & passengers using TomTom Maps + HTML5 Geolocation  
- 🧭 **Pelacakan Langsung** posisi bus & penumpang dengan TomTom Maps + Geolocation HTML5

- 🪪 **ID Card Scanning (OCR)** for validating local residents  
- 🪪 **Pemindaian KTP (OCR)** untuk validasi warga lokal

- 🧾 **QR Code Generation & Scanning** for individual tickets  
- 🧾 **Pembuatan & Pemindaian QR Code** untuk setiap tiket penumpang

- 📈 **Report Exporting** to PDF, Excel, and Word  
- 📈 **Ekspor Laporan** ke PDF, Excel, dan Word

- ⚠️ **Passenger Validation** (boarded / not boarded) based on system logic  
- ⚠️ **Validasi Penumpang** (naik / tidak naik) berbasis sistem

---

## ⚙️ Technologies & Tools  
## ⚙️ Teknologi & Tools

### Backend (Laravel)
- Laravel 10+
- Spatie Laravel-Permission (Role Management)
- Laravel Validator & Session
- Blade Templates
- Routing (web.php)

### Pemetaan & Pelacakan
- Laravel 10+
- Spatie Laravel-Permission (Manajemen Role)
- Laravel Validator & Session
- Blade Templates
- Routing (web.php)

### Mapping & Tracking  
- **TomTom Maps SDK**  
  - Map rendering  
  - Routing API  
  - Route markers and polylines  

- **Geolocation API (HTML5)**  
  - `getCurrentPosition()` and `watchPosition()` for real-time position updates  

### Pemetaan & Pelacakan  
- **TomTom Maps SDK**  
  - Render peta  
  - API routing  
  - Marker & polyline rute  

- **Geolocation API (HTML5)**  
  - `getCurrentPosition()` dan `watchPosition()` untuk update posisi realtime

### Frontend  
- HTML, Bootstrap  
- SweetAlert2 (pop-up notifications)  
- JavaScript (AJAX, DOM manipulation)  
- Custom map marker (Flaticon CDN)

### Frontend  
- HTML, Bootstrap  
- SweetAlert2 (popup notifikasi)  
- JavaScript (AJAX, manipulasi DOM)  
- Marker peta kustom dari Flaticon CDN

### Realtime Movement  
- `setInterval()` every 5 seconds for position refresh  
- Auto-update markers on map  

### Gerakan Realtime  
- `setInterval()` setiap 5 detik untuk refresh posisi  
- Marker peta update otomatis  

### Tickets & Validation  
- **Tesseract.js** (OCR) for ID card scanning  
- QRCode generator (e.g. `simple-qrcode`)  
- **Laravel-Excel** to export reports to:
  - `.pdf` (PDF)
  - `.xlsx` (Excel)
  - `.doc` (Word)

### Tiket & Validasi  
- **Tesseract.js** (OCR) untuk scan KTP  
- QRCode generator (misalnya `simple-qrcode`)  
- **Laravel-Excel** untuk ekspor laporan ke:
  - `.pdf` (PDF)
  - `.xlsx` (Excel)
  - `.doc` (Word)

---

## 🗂️ Project Folder Structure  
## 🗂️ Struktur Folder Proyek

```bash
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   └── Middleware/
│   ├── Models/
├── resources/
│   ├── views/
├── routes/
│   └── web.php
├── public/
│   └── maps/
├── config/
├── storage/
├── database/
│   └── migrations/
├── .env
├── composer.json
└── README.md
