# TapServe

**Smart QR Ordering & Table Management System**

TapServe adalah sistem pemesanan berbasis QR Code untuk cafe dan restoran. Pelanggan cukup scan QR di meja, pilih menu, pesan, dan bayar — tanpa antre kasir. Dibangun dengan Laravel 12 dan siap dipakai oleh UMKM F&B.

---

## Fitur Utama

### Pelanggan (tanpa login)

- Scan QR code di meja → langsung masuk ke menu
- Lihat menu dengan foto, deskripsi, harga, dan kategori
- Tambah item ke cart, ubah jumlah, atau hapus
- Pilih tipe per item: **Dine In** atau **Bawa Pulang (Takeaway)**
- Bulk-toggle semua item sekaligus ke Dine In / Takeaway
- Checkout dan buat pesanan
- Tambah pesanan lagi di sesi yang sama (Open Table)
- Pantau status pesanan secara realtime (AJAX polling)
- Bayar sekarang atau bayar nanti
- Self-payment via QRIS (scan mandiri, tanpa kasir)
- Tutup transaksi sendiri

### Kasir

- Dashboard sesi meja aktif dengan detail pesanan per sesi
- Lihat item pesanan beserta status Dine In / Takeaway per item
- Tambah pesanan manual (misal: pelanggan ambil item tanpa input sendiri)
- Proses pembayaran dengan berbagai metode (sesuai konfigurasi)
- Kalkulasi kembalian otomatis untuk pembayaran tunai
- Close session setelah pembayaran dikonfirmasi

### Dapur / Kitchen

- Dashboard realtime semua pesanan aktif
- Status per pesanan: Pending → Processing → Ready → Completed
- Badge tipe order per item (Dine In / Bawa Pulang)
- Ringkasan tipe per pesanan (Dine In / Bawa Pulang / Campuran)
- AJAX polling otomatis, tidak perlu refresh

### Admin / Owner

**Menu & Katalog**
- CRUD kategori menu
- CRUD menu dengan upload gambar, harga, dan ketersediaan

**Meja & Sesi**
- CRUD meja dan cetak QR code (satuan atau semua sekaligus)
- Monitor sesi meja aktif dan riwayat sesi
- Cancel / close sesi secara manual

**Pembayaran**
- Manajemen metode pembayaran (nama, ikon, warna, kode, urutan tampil)
- Aktifkan / nonaktifkan metode pembayaran secara dinamis
- Upload gambar QRIS untuk self-payment pelanggan

**Laporan**
- Dashboard ringkasan: total pendapatan, sesi, dan pesanan hari ini
- Laporan transaksi dengan filter rentang tanggal

**Pengaturan**
- Profil toko (nama, alamat, telepon, email, logo)
- Kelola user (tambah, edit, nonaktifkan)

---

## Stack Teknologi

| Layer | Teknologi |
|---|---|
| Backend | Laravel 12, PHP 8.3+ |
| Database | SQLite (dev) / MySQL 8 (prod) |
| Frontend | Blade, Bootstrap 5.3 |
| Realtime | AJAX Polling |
| Notifikasi | SweetAlert2 |
| QR Code | qrcode.js (client-side) |
| Auth | Laravel Breeze |

---

## Arsitektur

Monolith Laravel dengan Service Layer.

```
Frontend (Blade + Bootstrap)
        │
    Controller
        │
   Service Layer
        │
  Model / Eloquent
        │
     Database
```

Services:

- `CartService` — manajemen keranjang per sesi browser
- `OrderService` — buat order dari cart
- `PaymentService` — proses pembayaran dan tutup sesi
- `SessionService` — buat/kelola sesi meja
- `ReportService` — agregasi data laporan
- `ActivityLogService` — audit log aksi kunci

---

## Struktur Database

```
shops
├── payment_methods
├── users
├── settings
├── categories
│      └── menus
└── restaurant_tables
       └── table_sessions
               ├── orders
               │      └── order_items   (order_type: dine_in | takeaway)
               └── payments
```

**Tabel:**
`shops`, `users`, `settings`, `categories`, `menus`, `restaurant_tables`, `table_sessions`, `orders`, `order_items`, `payments`, `payment_methods`, `activity_logs`

---

## Enum Status

| Model | Status |
|---|---|
| `TableSession` | `open`, `payment_pending`, `paid`, `closed`, `cancelled` |
| `Order` | `pending`, `processing`, `ready`, `completed`, `cancelled` |
| `Payment` | `pending`, `paid`, `failed`, `refunded` |
| `RestaurantTable` | `available`, `occupied`, `disabled` |

---

## Alur Sistem

**Pelanggan (Open Table):**
```
Scan QR → Buka menu → Tambah ke cart → Set Dine In/Takeaway
→ Checkout → Pantau status → Tambah lagi (opsional)
→ Minta bayar → Kasir konfirmasi → Sesi tutup
```

**Pelanggan (Self-payment QRIS):**
```
Scan QR → Pesan → Bayar via QRIS mandiri → Sesi tutup otomatis
```

**Kitchen:**
```
Terima pesanan → Processing → Ready → Completed
```

**Kasir:**
```
Lihat sesi aktif → Tambah pesanan manual (opsional)
→ Proses pembayaran → Close sesi
```

---

## Instalasi

```bash
git clone https://github.com/your-org/tapserve.git
cd tapserve

composer install
npm install && npm run build

cp .env.example .env
php artisan key:generate

# Sesuaikan DB_* di .env, lalu:
php artisan migrate --seed

php artisan serve
```

Seed default membuat:
- 1 toko contoh
- User admin (`admin@example.com` / `password`)
- Metode pembayaran: Tunai, QRIS, Transfer, E-Wallet

---

## Role Pengguna

| Role | Akses |
|---|---|
| `admin` | Semua fitur admin + pengaturan |
| `owner` | Sama dengan admin |
| `cashier` | Dashboard kasir + proses pembayaran |
| `kitchen` | Dashboard dapur + update status pesanan |

---

## Belum Termasuk (Post-MVP)

- Inventory & stock
- Promo / voucher
- Reservasi meja
- Loyalty / membership
- Cetak struk (printer)
- Multi-cabang
- WebSocket / Laravel Reverb
- Docker deployment

---

## Status Project

**MVP Selesai — Siap Digunakan**

| Sprint | Deskripsi | Status |
|---|---|---|
| 1 | Auth, role, CRUD menu/kategori/meja, kelola user | ✅ Selesai |
| 2 | QR code, menu publik, cart, checkout, takeaway | ✅ Selesai |
| 3 | Sesi meja, dashboard kitchen, realtime status | ✅ Selesai |
| 4 | Pembayaran kasir, QRIS self-pay, close sesi | ✅ Selesai |
| 5 | Dashboard laporan, activity log, profil toko | ✅ Selesai |
| + | Dine in/takeaway per item, pesanan manual kasir, manajemen metode pembayaran | ✅ Selesai |
| 6 | Multi-tenant SaaS, WebSocket, Docker | ⬜ Roadmap |
