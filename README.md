# TapServe

Smart QR Ordering & Table Management System

TapServe adalah sistem pemesanan menu berbasis QR Code untuk cafe/restoran dengan konsep **Open Table + Instant Payment**, dibangun menggunakan Laravel 12 dan MySQL.


Project ditujukan sebagai **MVP yang siap dipakai UMKM**, namun memiliki arsitektur yang cukup fleksibel untuk dikembangkan menjadi produk SaaS multi-tenant.

Target:
MVP → Digunakan UMKM → Produk SaaS

---

# 1. Tujuan Project

Membangun sistem yang memungkinkan pelanggan:

- Scan QR di meja
- Melihat menu
- Memesan makanan/minuman
- Menambah pesanan berkali-kali
- Membayar langsung atau nanti
- Menutup transaksi sendiri tanpa antre kasir

Serta mendukung operasional:

- Dashboard kasir
- Dashboard dapur
- Status order realtime
- Manajemen menu
- Manajemen meja
- Laporan sederhana

---

# 2. Scope MVP (LOCKED)

## Customer

Tanpa login.

Fitur:

- Scan QR meja
- Lihat menu
- Tambah ke cart
- Checkout
- Tambah pesanan lagi
- Lihat status pesanan
- Bayar sekarang
- Bayar nanti
- Tutup transaksi sendiri

---

## Admin / Owner

Fitur:

- Login
- CRUD menu
- CRUD kategori
- CRUD meja
- Kelola user
- Monitoring order
- Laporan

---

## Kasir

Fitur:

- Lihat session aktif
- Terima pembayaran
- Close session
- Monitoring order

---

## Kitchen / Barista

Fitur:

- Lihat order masuk
- Update status:
    - Pending
    - Processing
    - Ready
    - Completed

---

# 3. Teknologi (LOCKED)

Backend:

- Laravel 12
- PHP 8.3+
- MySQL 8

Frontend:

- Blade
- Bootstrap 5
- SweetAlert2
- DataTables
- AlpineJS (opsional)

Realtime:

MVP:

AJAX Polling

Future:

Laravel Reverb / WebSocket

Deployment:

Docker

Container:

- nginx
- php-fpm
- mysql

---

# 4. Konsep Bisnis (LOCKED)

Project menggunakan konsep:

## Open Table (Default)

Customer dapat:

Pesan → Tambah Pesanan → Tambah Lagi → Bayar → Close

Semua masuk ke satu sesi meja.

---

## Instant Payment

Customer:

Pesan → Bayar → Session selesai

Jika pesan lagi:

Session baru dibuat.

---

## Order Type

### Dine In

Menggunakan meja.

```
order_type = dine_in
table_id != null
```

---

### Takeaway

Tanpa meja.

```
order_type = takeaway
table_id = null
```

Rule:

Takeaway selalu:

```
payment_mode = instant
```

---

## Payment Mode

### Open Table

Bayar di akhir.

---

### Instant

Bayar langsung.

---

# 5. Workflow Sistem (LOCKED)

Customer:

```text
Scan QR
↓
Session dibuat / lanjut
↓
Tambah menu
↓
Submit order
↓
Tambah lagi (opsional)
↓
Bayar sekarang / nanti
↓
Close session
```

Kitchen:

```text
Order baru
↓
Processing
↓
Ready
↓
Completed
```

Kasir:

```text
Lihat session aktif
↓
Terima pembayaran
↓
Close session
```

---

# 6. Arsitektur Aplikasi (LOCKED)

Monolith Laravel.

Tidak menggunakan microservice.

Struktur:

```text
Frontend (Blade)
        │
Controller
        │
Service Layer
        │
Model / Eloquent
        │
MySQL
```

---

Direktori utama:

```text
app/
├── Http/Controllers
├── Services
├── Models
├── Policies
├── Jobs
└── Notifications
```

Service layer digunakan untuk:

- Create Order
- Payment
- Session
- QR
- Notification

---

# 7. ERD (LOCKED)

Relasi utama:

```text
shops
│
├── users
├── settings
├── categories
│      └── menus
│
├── restaurant_tables
│      └── table_sessions
│              │
│              ├── orders
│              │      └── order_items
│              │
│              └── payments
│
└── activity_logs
```

---

# 8. Tabel Database (LOCKED)

Tabel:

1. shops
2. users
3. settings
4. categories
5. menus
6. restaurant_tables
7. table_sessions
8. orders
9. order_items
10. payments
11. activity_logs

---

# 9. Status Enum (LOCKED)

## table_sessions

```text
open
payment_pending
paid
closed
cancelled
```

---

## orders

```text
pending
processing
ready
completed
cancelled
```

---

## payments

```text
pending
paid
failed
refunded
```

---

## restaurant_tables

```text
available
occupied
disabled
```

---

# 10. Constraint Penting (LOCKED)

Rule:

Takeaway:

```text
table_id = NULL
payment_mode = instant
```

---

Dine In:

```text
table_id required
```

---

Session closed:

Tidak boleh tambah order.

---

Payment success:

Session auto close.

---

# 11. Hal yang Sengaja Tidak Dibuat (MVP)

Belum termasuk:

- Inventory
- Promo
- Reservasi
- Loyalty
- Membership
- Printer
- Multi cabang
- Supplier
- Shift kasir
- Stock opname

---

# 12. Roadmap

## Sprint 1

Fondasi:

- Auth
- Role
- CRUD menu
- CRUD kategori
- CRUD meja

---

## Sprint 2

Customer ordering:

- QR
- Menu publik
- Cart
- Checkout

---

## Sprint 3

Operational:

- Session
- Kitchen
- Order status

---

## Sprint 4

Payment:

- Cash
- QRIS
- Close session

---

## Sprint 5

Reporting:

- Dashboard
- Laporan

---

## Sprint 6

Scaling:

- Multi tenant
- Subscription
- SaaS

---

# 13. Coding Guideline

Gunakan:

- Service Layer
- Form Request Validation
- Eloquent Relationship
- Resource Controller
- Soft Delete jika diperlukan

Hindari:

- Query berat di Blade
- Business logic di Controller
- Hardcoded status string

Gunakan constant/enum.

---

# 14. Naming Convention

Model:

```text
Shop
Menu
Order
Payment
TableSession
RestaurantTable
```

Controller:

```text
OrderController
MenuController
KitchenController
PaymentController
```

Service:

```text
OrderService
PaymentService
SessionService
QRService
```

---

# 15. Current Status

✅ Arsitektur dikunci  
✅ Konsep bisnis dikunci  
✅ Workflow dikunci  
✅ ERD dikunci  

Next:

→ Migration Laravel  
→ Model  
→ Seeder  
→ Authentication  
→ CRUD dasar

---

Project Status:

MVP Planning Complete
