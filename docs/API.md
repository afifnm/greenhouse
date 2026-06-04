# Greenhouse Management System — API Documentation

**Base URL:** `http://gogreenhouse.my.id/api/v1`  
**Auth:** Laravel Sanctum — Bearer Token  
**Format:** JSON (`Content-Type: application/json`, `Accept: application/json`)

---

## Daftar Isi

1. [Gambaran Sistem](#1-gambaran-sistem)
2. [Autentikasi](#2-autentikasi)
3. [Dashboard](#3-dashboard)
4. [Admin — Greenhouse](#4-admin--greenhouse)
5. [Admin — User](#5-admin--user)
6. [Admin — Varietas Melon](#6-admin--varietas-melon)
7. [Pohon (Trees)](#7-pohon-trees)
8. [Buah (Fruits)](#8-buah-fruits)
9. [Permintaan Bahan (Material Requests)](#9-permintaan-bahan-material-requests)
10. [Laporan Greenhouse (Report)](#10-laporan-greenhouse-report)
11. [Penjualan (Sales)](#11-penjualan-sales)
12. [Format Respons & Error](#12-format-respons--error)
13. [Panduan Integrasi Flutter](#13-panduan-integrasi-flutter)

---

## 1. Gambaran Sistem

Sistem ini mengelola operasional greenhouse pertanian melon. Ada tiga konsep utama yang perlu dipahami:

### Hierarki Data

```
Greenhouse (Kebun)
├── Trees (Pohon per nomor urut)
│   └── Fruits (Buah tiap panen)
├── Material Requests (Permintaan bahan ke admin)
└── Sales (Transaksi penjualan)
    └── Sale Items (Item per varietas dalam satu transaksi)
```

### Peran Pengguna (Role)

| Role | Akses |
|------|-------|
| `admin` | Akses penuh ke semua greenhouse, manajemen user, varietas, dan semua transaksi |
| `manager` | Hanya greenhouse yang ditugaskan. Bisa kelola pohon, buah, bahan, dan lihat penjualan |

### Alur Kerja Umum

1. **Admin** membuat greenhouse, mendaftarkan varietas melon, dan menambah user manager
2. **Admin** menugaskan manager ke greenhouse tertentu
3. **Manager** mencatat pohon-pohon di greenhouse mereka (bisa satu per satu atau bulk)
4. **Manager** mencatat buah hasil panen tiap pohon (kondisi, grade, berat)
5. **Manager/Admin** membuat transaksi penjualan (POS kasir)
6. **Manager** bisa mengajukan permintaan bahan (pupuk, dll) ke admin
7. **Admin/Manager** melihat laporan produksi dan penjualan

---

## 2. Autentikasi

### POST `/login` — Login

Endpoint satu-satunya yang tidak butuh token. Mendukung login via **email** atau **nomor HP**.

**Request Body:**
```json
{
  "login": "admin@greenhouse.id",
  "password": "password"
}
```

> `login` bisa diisi email **atau** nomor HP. Sistem otomatis mendeteksi formatnya.

**Response 200:**
```json
{
  "token": "1|oZvsDcyDPFtajI7pIDhKUDy7GmtBRGYJ3DjuaiOYa",
  "user": {
    "id": 1,
    "name": "Administrator",
    "email": "admin@greenhouse.id",
    "phone": null,
    "role": "admin",
    "is_active": true,
    "created_at": "2026-06-04T06:54:43.000000Z"
  }
}
```

**Response 422 (salah kredensial):**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "login": ["Email/nomor HP atau password salah."]
  }
}
```

**Response 422 (akun tidak aktif):**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "login": ["Akun Anda tidak aktif. Hubungi administrator."]
  }
}
```

---

### POST `/logout` — Logout  🔒

Menghapus token yang sedang dipakai.

**Headers:** `Authorization: Bearer {token}`

**Response 200:**
```json
{ "message": "Berhasil logout." }
```

---

### GET `/me` — Data User Login  🔒

Mengambil data profil user yang sedang login.

**Response 200:**
```json
{
  "id": 2,
  "name": "Budi Santoso",
  "email": "budi@greenhouse.id",
  "phone": "081234567890",
  "role": "manager",
  "is_active": true,
  "created_at": "2026-06-04T06:54:43.000000Z"
}
```

---

## 3. Dashboard

### GET `/dashboard` — Dashboard Role-Aware  🔒

Mengembalikan data berbeda tergantung role user.

**Headers:** `Authorization: Bearer {token}`

#### Respons untuk Admin:
```json
{
  "role": "admin",
  "stats": {
    "total_greenhouses": 3,
    "active_greenhouses": 3,
    "total_revenue": 15750000,
    "today_revenue": 450000,
    "month_revenue": 3200000,
    "total_trees": 4500,
    "alive_trees": 4050,
    "total_fruits": 8098,
    "grade_a_fruits": 1101
  },
  "recent_greenhouses": [
    {
      "id": 1,
      "name": "Greenhouse Utama",
      "code": "GH-001",
      "description": "Greenhouse utama dengan kapasitas besar",
      "is_active": true,
      "created_at": "2026-06-04T06:54:44.000000Z"
    }
  ]
}
```

#### Respons untuk Manager:
```json
{
  "role": "manager",
  "stats": {
    "today_revenue": 250000,
    "month_revenue": 1800000,
    "grade_a_fruits": 337,
    "dead_tree_rate": 10.0
  },
  "greenhouses": [
    {
      "id": 1,
      "name": "Greenhouse Utama",
      "code": "GH-001",
      "description": "...",
      "is_active": true,
      "created_at": "2026-06-04T06:54:44.000000Z"
    }
  ]
}
```

---

### GET `/varieties` — List Semua Varietas  🔒

Dipakai saat membuat pohon atau transaksi penjualan (dropdown). Mengembalikan **semua** tanpa pagination.

**Response 200:**
```json
[
  { "id": 1, "name": "Sugar Honey", "slug": "sugar-honey", "description": "...", "created_at": "..." },
  { "id": 2, "name": "Dalmation",   "slug": "dalmation",   "description": "...", "created_at": "..." }
]
```

---

## 4. Admin — Greenhouse

> Semua endpoint di bawah ini hanya bisa diakses oleh **admin**.  
> Prefix: `/admin/greenhouses`

### GET `/admin/greenhouses` — List Greenhouse

**Query Params:**

| Param | Tipe | Contoh | Keterangan |
|-------|------|--------|-----------|
| `search` | string | `GH-001` | Cari berdasarkan nama atau kode |
| `page` | int | `2` | Halaman (default: 1, 12 per halaman) |

**Response 200:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Greenhouse Utama",
      "code": "GH-001",
      "description": "Greenhouse utama",
      "is_active": true,
      "created_at": "2026-06-04T06:54:44.000000Z"
    }
  ],
  "links": { "first": "...", "last": "...", "prev": null, "next": "..." },
  "meta": { "current_page": 1, "last_page": 3, "total": 30 }
}
```

---

### POST `/admin/greenhouses` — Buat Greenhouse

**Request Body:**
```json
{
  "name": "Greenhouse Selatan",
  "code": "GH-004",
  "description": "Greenhouse area selatan",
  "is_active": true
}
```

| Field | Wajib | Keterangan |
|-------|-------|-----------|
| `name` | Ya | Nama greenhouse |
| `code` | Ya | Kode unik (contoh: GH-001) |
| `description` | Tidak | Deskripsi |
| `is_active` | Tidak | Default: true |

**Response 201:**
```json
{
  "id": 4,
  "name": "Greenhouse Selatan",
  "code": "GH-004",
  "description": "Greenhouse area selatan",
  "is_active": true,
  "created_at": "2026-06-04T10:00:00.000000Z"
}
```

---

### GET `/admin/greenhouses/{id}` — Detail Greenhouse

**Response 200:** Sama seperti format di atas.

---

### PUT `/admin/greenhouses/{id}` — Update Greenhouse

Body sama seperti POST. **Response 200:** Data terbaru.

---

### DELETE `/admin/greenhouses/{id}` — Hapus Greenhouse

**Response 200:**
```json
{ "message": "Greenhouse berhasil dihapus." }
```

---

### GET `/admin/greenhouses/{id}/managers` — List Manager & Status Assignment

Menampilkan semua user dengan role `manager` beserta info mana yang sudah di-assign ke greenhouse ini.

**Response 200:**
```json
{
  "data": [
    { "id": 2, "name": "Budi", "email": "budi@greenhouse.id", "role": "manager", ... },
    { "id": 3, "name": "Sari", "email": "sari@greenhouse.id", "role": "manager", ... }
  ],
  "assigned_ids": [2]
}
```

> `assigned_ids` berisi ID manager yang sudah ditugaskan ke greenhouse ini.

---

### PUT `/admin/greenhouses/{id}/managers` — Assign Manager ke Greenhouse

Operasi **sync** — daftar yang dikirim akan menjadi daftar manager saat ini (yang tidak ada di list akan dilepas).

**Request Body:**
```json
{
  "user_ids": [2, 3]
}
```

**Response 200:**
```json
{ "message": "Manager berhasil disinkronkan." }
```

---

## 5. Admin — User

> Prefix: `/admin/users`

### GET `/admin/users` — List User

**Query Params:**

| Param | Contoh | Keterangan |
|-------|--------|-----------|
| `role` | `manager` | Filter by role (`admin` atau `manager`) |
| `search` | `budi` | Cari nama, email, atau phone |
| `page` | `1` | 15 per halaman |

---

### POST `/admin/users` — Buat User

```json
{
  "name": "Andi Wijaya",
  "email": "andi@greenhouse.id",
  "phone": "082345678901",
  "password": "rahasia123",
  "role": "manager",
  "is_active": true
}
```

| Field | Wajib | Keterangan |
|-------|-------|-----------|
| `name` | Ya | Nama lengkap |
| `email` | Ya | Harus unik |
| `phone` | Ya | Harus unik |
| `password` | Ya | Min 8 karakter |
| `role` | Ya | `admin` atau `manager` |
| `is_active` | Tidak | Default: true |

---

### GET `/admin/users/{id}` — Detail User
### PUT `/admin/users/{id}` — Update User

Sama seperti POST, tapi `password` boleh kosong (tidak diubah jika kosong).

---

### DELETE `/admin/users/{id}` — Hapus User

Tidak bisa menghapus akun diri sendiri.

```json
{ "message": "User berhasil dihapus." }
```

---

## 6. Admin — Varietas Melon

> Prefix: `/admin/varieties`

### GET `/admin/varieties` — List Varietas (Paginated)

15 per halaman, urut abjad.

### POST `/admin/varieties` — Buat Varietas

```json
{
  "name": "Action",
  "description": "Melon varietas action ukuran besar"
}
```

`slug` dibuat otomatis dari `name` (contoh: `"Action"` → `"action"`).

**Response 201:**
```json
{
  "id": 10,
  "name": "Action",
  "slug": "action",
  "description": "Melon varietas action ukuran besar",
  "created_at": "..."
}
```

### GET `/admin/varieties/{id}` — Detail
### PUT `/admin/varieties/{id}` — Update
### DELETE `/admin/varieties/{id}` — Hapus

---

## 7. Pohon (Trees)

> Diakses oleh admin dan manager yang ditugaskan ke greenhouse tersebut.  
> Prefix: `/greenhouse/{greenhouse_id}/trees`

### GET `/greenhouse/{id}/trees` — List Pohon

**Query Params:**

| Param | Nilai | Keterangan |
|-------|-------|-----------|
| `status` | `alive` / `dead` | Filter status pohon |
| `search` | `GH-001-` | Cari berdasarkan nomor pohon |
| `per_page` | `25` / `50` / `100` | Default: 50 |
| `page` | `1` | Nomor halaman |

**Response 200:**
```json
{
  "data": [
    {
      "id": 21,
      "greenhouse_id": 1,
      "tree_number": "GH-001-0001",
      "status": "alive",
      "variety": {
        "id": 3,
        "name": "Honey",
        "slug": "honey",
        "description": "...",
        "created_at": "..."
      },
      "fruits_count": 3,
      "created_at": "2026-06-04T06:54:44.000000Z"
    }
  ],
  "links": { ... },
  "meta": { "current_page": 1, "last_page": 60, "total": 1500, "per_page": 25 }
}
```

---

### POST `/greenhouse/{id}/trees` — Tambah Pohon Satu per Satu

```json
{
  "tree_number": "GH-001-1501",
  "melon_variety_id": 3,
  "status": "alive"
}
```

| Field | Wajib | Keterangan |
|-------|-------|-----------|
| `tree_number` | Ya | Harus unik di seluruh sistem |
| `melon_variety_id` | Tidak | ID varietas (nullable) |
| `status` | Ya | `alive` atau `dead` |

---

### POST `/greenhouse/{id}/trees/bulk` — Buat Pohon Massal

Membuat banyak pohon sekaligus dengan format nomor otomatis `PREFIX-0001`, `PREFIX-0002`, dst.

```json
{
  "prefix": "GH-004",
  "count": 500,
  "melon_variety_id": 1,
  "status": "alive"
}
```

| Field | Wajib | Keterangan |
|-------|-------|-----------|
| `prefix` | Ya | Awalan nomor pohon |
| `count` | Ya | Jumlah pohon (max 2000) |
| `melon_variety_id` | Tidak | Varietas untuk semua pohon |
| `status` | Ya | `alive` atau `dead` |

**Response 201:**
```json
{
  "message": "498 pohon berhasil ditambahkan.",
  "created": 498
}
```

> `created` bisa lebih kecil dari `count` jika ada nomor yang sudah exist (dilewati, bukan error).

---

### GET `/greenhouse/{id}/trees/{tree_id}` — Detail Pohon

Mengembalikan data pohon beserta **semua buah** yang pernah dicatat.

---

### PUT `/greenhouse/{id}/trees/{tree_id}` — Update Pohon

Body sama seperti POST (tapi `tree_number` boleh sama dengan nilai saat ini).

---

### DELETE `/greenhouse/{id}/trees/{tree_id}` — Hapus Pohon

Otomatis menghapus semua data buah yang terkait.

```json
{ "message": "Pohon berhasil dihapus." }
```

---

## 8. Buah (Fruits)

> Prefix: `/greenhouse/{greenhouse_id}/trees/{tree_id}/fruits`

### GET `/greenhouse/{ghId}/trees/{treeId}/fruits` — List Buah

Mengembalikan semua buah dari pohon tersebut, urut terbaru.

**Response 200:**
```json
[
  {
    "id": 101,
    "tree_id": 21,
    "condition": "good",
    "grade": "A",
    "weight": 2.35,
    "notes": "Kualitas sangat baik",
    "created_at": "2026-06-04T08:00:00.000000Z"
  },
  {
    "id": 100,
    "tree_id": 21,
    "condition": "rotten",
    "grade": null,
    "weight": null,
    "notes": "Terkena hama",
    "created_at": "2026-06-03T08:00:00.000000Z"
  }
]
```

> Buah dengan `condition: "rotten"` selalu punya `grade: null` dan `weight: null`.

---

### POST `/greenhouse/{ghId}/trees/{treeId}/fruits` — Catat Buah Baru

```json
{
  "condition": "good",
  "grade": "A",
  "weight": 2.35,
  "notes": "Kualitas sangat baik"
}
```

| Field | Wajib | Nilai | Keterangan |
|-------|-------|-------|-----------|
| `condition` | Ya | `good` / `rotten` | Kondisi buah |
| `grade` | Tidak | `A` / `B` / `C` / `D` | Diabaikan jika `rotten` |
| `weight` | Tidak | angka desimal (kg) | Diabaikan jika `rotten` |
| `notes` | Tidak | string | Catatan tambahan |

> **Aturan bisnis:** Tidak bisa menambah buah ke pohon dengan status `dead`. Akan dapat error 422.

**Response 422 (pohon mati):**
```json
{ "message": "Tidak dapat menambahkan buah ke pohon yang sudah mati." }
```

---

### GET `/greenhouse/{ghId}/trees/{treeId}/fruits/{fruitId}` — Detail Buah

### PUT `/greenhouse/{ghId}/trees/{treeId}/fruits/{fruitId}` — Update Buah

Body sama seperti POST.

### DELETE `/greenhouse/{ghId}/trees/{treeId}/fruits/{fruitId}` — Hapus Buah

```json
{ "message": "Buah berhasil dihapus." }
```

---

## 9. Permintaan Bahan (Material Requests)

> Prefix: `/greenhouse/{greenhouse_id}/material-requests`  
> Manager mengajukan permintaan bahan (pupuk, pestisida, dll). Admin bisa mengubah statusnya.

### GET `/greenhouse/{id}/material-requests` — List Permintaan

**Response 200** (paginated, 20 per halaman, terbaru dulu):
```json
{
  "data": [
    {
      "id": 5,
      "greenhouse_id": 1,
      "material_name": "Pupuk NPK",
      "quantity": 50,
      "unit": "kg",
      "notes": "Untuk pohon baru",
      "status": "pending",
      "requested_by": {
        "id": 2,
        "name": "Budi Santoso",
        "email": "budi@greenhouse.id",
        "phone": "081234567890",
        "role": "manager",
        "is_active": true,
        "created_at": "..."
      },
      "created_at": "2026-06-04T09:00:00.000000Z"
    }
  ],
  "links": { ... },
  "meta": { ... }
}
```

---

### POST `/greenhouse/{id}/material-requests` — Ajukan Permintaan

```json
{
  "material_name": "Pupuk NPK",
  "quantity": 50,
  "unit": "kg",
  "notes": "Untuk pohon baru di blok A"
}
```

| Field | Wajib | Keterangan |
|-------|-------|-----------|
| `material_name` | Ya | Nama bahan |
| `quantity` | Ya | Jumlah (integer) |
| `unit` | Ya | Satuan (kg, liter, pcs, dll) |
| `notes` | Tidak | Catatan tambahan |

> Status otomatis `pending`. `user_id` diambil dari token.

**Response 201:** Data permintaan yang baru dibuat.

---

### PATCH `/greenhouse/{id}/material-requests/{requestId}` — Update Status

Hanya untuk mengubah status (`pending` → `fulfilled` atau sebaliknya).

```json
{
  "status": "fulfilled"
}
```

**Response 200:** Data terbaru.

---

### DELETE `/greenhouse/{id}/material-requests/{requestId}` — Hapus

```json
{ "message": "Permintaan bahan berhasil dihapus." }
```

---

## 10. Laporan Greenhouse (Report)

### GET `/greenhouse/{id}/report` — Laporan Produksi

Laporan komprehensif kondisi pohon dan buah. Bisa difilter berdasarkan rentang tanggal.

**Query Params:**

| Param | Contoh | Keterangan |
|-------|--------|-----------|
| `from` | `2026-01-01` | Tanggal mulai (format: YYYY-MM-DD) |
| `to` | `2026-06-30` | Tanggal akhir (format: YYYY-MM-DD) |

**Response 200:**
```json
{
  "greenhouse": {
    "id": 1,
    "name": "Greenhouse Utama",
    "code": "GH-001"
  },
  "filters": {
    "from": "2026-01-01",
    "to": "2026-06-30"
  },
  "summary": {
    "total_trees": 1500,
    "alive_trees": 1350,
    "dead_trees": 150,
    "alive_rate": 90.0,
    "dead_rate": 10.0,
    "total_fruits": 2708,
    "good_fruits": 1919,
    "rotten_fruits": 789,
    "good_rate": 70.9,
    "rotten_rate": 29.1,
    "fruit_per_alive_tree": 2.01,
    "grade_a": 393,
    "grade_b": 561,
    "grade_c": 673,
    "grade_d": 292,
    "total_weight": 4523.75,
    "avg_weight": 2.35
  },
  "trees_by_variety": [
    {
      "variety_name": "Honey",
      "total_trees": 520,
      "alive_trees": 468,
      "dead_trees": 52
    }
  ],
  "fruits_by_variety": [
    {
      "variety_name": "Honey",
      "total_fruits": 950,
      "good_fruits": 670,
      "rotten_fruits": 280,
      "grade_a": 145,
      "grade_b": 198,
      "grade_c": 231,
      "grade_d": 96,
      "total_weight": 1687.50
    }
  ],
  "weight_by_grade": [
    { "grade": "A", "count": 393, "total_weight": 1099.35, "avg_weight": 2.80 },
    { "grade": "B", "count": 561, "total_weight": 1430.55, "avg_weight": 2.55 },
    { "grade": "C", "count": 673, "total_weight": 1547.90, "avg_weight": 2.30 },
    { "grade": "D", "count": 292, "total_weight": 445.95,  "avg_weight": 1.53 }
  ],
  "weight_by_variety_grade": [
    {
      "variety_name": "Honey",
      "grade": "A",
      "fruit_count": 145,
      "total_weight": 406.00,
      "avg_weight": 2.80
    }
  ]
}
```

---

## 11. Penjualan (Sales)

### GET `/sales` — List Greenhouse + Ringkasan Penjualan

Untuk landing page kasir. Menampilkan semua greenhouse yang bisa diakses user beserta ringkasan omset.

> Admin → semua greenhouse. Manager → hanya greenhouse yang ditugaskan.

**Response 200:**
```json
{
  "data": [
    {
      "greenhouse": {
        "id": 1,
        "name": "Greenhouse Utama",
        "code": "GH-001",
        "description": "...",
        "is_active": true,
        "created_at": "..."
      },
      "sales_count": 47,
      "today_total": 750000.0,
      "month_total": 12500000.0
    }
  ]
}
```

---

### GET `/sales/greenhouse/{id}` — Dashboard Kasir per Greenhouse

```json
{
  "greenhouse": { "id": 1, "name": "Greenhouse Utama", ... },
  "today_revenue": 750000.0,
  "month_revenue": 12500000.0,
  "total_revenue": 85000000.0,
  "transaction_count": 47,
  "recent_sales": [
    {
      "id": 47,
      "greenhouse_id": 1,
      "buyer_name": "Pak Hendra",
      "total": 350000.0,
      "sold_by": { "id": 2, "name": "Budi Santoso", ... },
      "items": [
        {
          "id": 98,
          "melon_variety": { "id": 1, "name": "Sugar Honey", ... },
          "weight_kg": 10.0,
          "price_per_kg": 25000,
          "subtotal": 250000.0
        },
        {
          "id": 99,
          "melon_variety": { "id": 2, "name": "Dalmation", ... },
          "weight_kg": 5.0,
          "price_per_kg": 20000,
          "subtotal": 100000.0
        }
      ],
      "created_at": "2026-06-04T10:30:00.000000Z"
    }
  ]
}
```

---

### POST `/sales/greenhouse/{id}` — Buat Transaksi Penjualan

```json
{
  "buyer_name": "Pak Hendra",
  "sale_items": [
    {
      "melon_variety_id": 1,
      "weight_kg": 10.0,
      "price_per_kg": 25000
    },
    {
      "melon_variety_id": 2,
      "weight_kg": 5.0,
      "price_per_kg": 20000
    }
  ]
}
```

| Field | Wajib | Keterangan |
|-------|-------|-----------|
| `buyer_name` | Ya | Nama pembeli |
| `sale_items` | Ya | Array min 1 item |
| `sale_items[].melon_variety_id` | Ya | ID varietas |
| `sale_items[].weight_kg` | Ya | Berat dalam kg (min 0.001) |
| `sale_items[].price_per_kg` | Ya | Harga per kg |

> `subtotal` dan `total` dihitung **di server** (`weight_kg × price_per_kg`). Jangan kirim nilai `total` dari client.

**Response 201:** Data sale lengkap dengan items.

---

### GET `/sales/{id}` — Detail Transaksi

Mengembalikan detail transaksi beserta greenhouse, kasir, dan semua items.

---

### DELETE `/sales/{id}` — Hapus Transaksi

Otomatis menghapus semua sale items yang terkait.

```json
{ "message": "Penjualan berhasil dihapus." }
```

---

### GET `/sales/greenhouse/{id}/report` — Laporan Penjualan

**Query Params:** `from` dan `to` (format: YYYY-MM-DD)

**Response 200:**
```json
{
  "greenhouse": { "id": 1, "name": "Greenhouse Utama", ... },
  "filters": { "from": "2026-06-01", "to": "2026-06-30" },
  "report": [
    {
      "variety_name": "Sugar Honey",
      "total_transaksi": 18,
      "total_berat": 180.5,
      "total_omset": 4512500.0
    },
    {
      "variety_name": "Dalmation",
      "total_transaksi": 14,
      "total_berat": 112.0,
      "total_omset": 2240000.0
    }
  ],
  "grand_weight": 292.5,
  "grand_omset": 6752500.0
}
```

---

## 12. Format Respons & Error

### Format Sukses

Untuk data tunggal:
```json
{ "id": 1, "name": "...", ... }
```

Untuk list berpaginasi:
```json
{
  "data": [ ... ],
  "links": {
    "first": "http://server/api/v1/...?page=1",
    "last":  "http://server/api/v1/...?page=5",
    "prev":  null,
    "next":  "http://server/api/v1/...?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 5,
    "per_page": 15,
    "to": 15,
    "total": 73
  }
}
```

### Format Error

| HTTP Code | Kondisi | Contoh Respons |
|-----------|---------|----------------|
| 401 | Token tidak ada / expired | `{"message": "Unauthenticated."}` |
| 403 | Role tidak cukup / tidak punya akses GH | `{"message": "Akses ditolak."}` |
| 404 | Resource tidak ditemukan | `{"message": "No query results for model..."}` |
| 422 | Validasi gagal | `{"message": "...", "errors": {"field": ["pesan"]}}` |
| 500 | Server error | `{"message": "Server Error"}` |

### Contoh Error 422:
```json
{
  "message": "The name field is required.",
  "errors": {
    "name": ["The name field is required."],
    "code": ["The code has already been taken."]
  }
}
```

---

## 13. Panduan Integrasi Flutter

### Setup Dio

```dart
// lib/services/api_service.dart
import 'package:dio/dio.dart';
import 'package:shared_preferences/shared_preferences.dart';

class ApiService {
  static final Dio _dio = Dio(BaseOptions(
    baseUrl: 'http://your-server.com/api/v1',
    connectTimeout: const Duration(seconds: 10),
    receiveTimeout: const Duration(seconds: 10),
    headers: {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
    },
  ));

  static Future<void> init() async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('token');
    if (token != null) {
      _dio.options.headers['Authorization'] = 'Bearer $token';
    }
  }

  static Future<void> setToken(String token) async {
    _dio.options.headers['Authorization'] = 'Bearer $token';
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString('token', token);
  }

  static Future<void> clearToken() async {
    _dio.options.headers.remove('Authorization');
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('token');
  }

  static Dio get dio => _dio;
}
```

### Login

```dart
Future<Map<String, dynamic>> login(String login, String password) async {
  final res = await ApiService.dio.post('/login', data: {
    'login': login,
    'password': password,
  });
  await ApiService.setToken(res.data['token']);
  return res.data['user'];
}
```

### Menangani Error

```dart
try {
  final res = await ApiService.dio.get('/dashboard');
  return res.data;
} on DioException catch (e) {
  if (e.response?.statusCode == 401) {
    // Token expired — arahkan ke halaman login
    await ApiService.clearToken();
    // navigasi ke LoginPage
  } else if (e.response?.statusCode == 422) {
    // Validasi error
    final errors = e.response?.data['errors'] as Map<String, dynamic>?;
    final firstError = errors?.values.first?.first as String?;
    throw firstError ?? 'Validasi gagal';
  } else {
    throw e.response?.data['message'] ?? 'Terjadi kesalahan';
  }
}
```

### Buat Transaksi Penjualan

```dart
Future<Map<String, dynamic>> createSale(int greenhouseId, {
  required String buyerName,
  required List<Map<String, dynamic>> items,
}) async {
  final res = await ApiService.dio.post(
    '/sales/greenhouse/$greenhouseId',
    data: {
      'buyer_name': buyerName,
      'sale_items': items.map((item) => {
        'melon_variety_id': item['variety_id'],
        'weight_kg': item['weight'],
        'price_per_kg': item['price'],
      }).toList(),
    },
  );
  return res.data;
}
```

### Paginasi

```dart
Future<Map<String, dynamic>> getTrees(int greenhouseId, {
  int page = 1,
  String? status,
  String? search,
}) async {
  final res = await ApiService.dio.get(
    '/greenhouse/$greenhouseId/trees',
    queryParameters: {
      'page': page,
      'per_page': 50,
      if (status != null) 'status': status,
      if (search != null) 'search': search,
    },
  );
  // res.data['data'] = list pohon
  // res.data['meta']['last_page'] = total halaman
  return res.data;
}
```

---

## Ringkasan Endpoint

| Method | Endpoint | Auth | Role | Deskripsi |
|--------|----------|------|------|-----------|
| POST | `/login` | — | — | Login |
| POST | `/logout` | ✓ | — | Logout |
| GET | `/me` | ✓ | — | Profil user |
| GET | `/dashboard` | ✓ | — | Dashboard (role-aware) |
| GET | `/varieties` | ✓ | — | Semua varietas (dropdown) |
| GET | `/admin/greenhouses` | ✓ | admin | List greenhouse |
| POST | `/admin/greenhouses` | ✓ | admin | Buat greenhouse |
| GET | `/admin/greenhouses/{id}` | ✓ | admin | Detail greenhouse |
| PUT | `/admin/greenhouses/{id}` | ✓ | admin | Update greenhouse |
| DELETE | `/admin/greenhouses/{id}` | ✓ | admin | Hapus greenhouse |
| GET | `/admin/greenhouses/{id}/managers` | ✓ | admin | List manager + status |
| PUT | `/admin/greenhouses/{id}/managers` | ✓ | admin | Assign manager |
| GET | `/admin/users` | ✓ | admin | List user |
| POST | `/admin/users` | ✓ | admin | Buat user |
| GET | `/admin/users/{id}` | ✓ | admin | Detail user |
| PUT | `/admin/users/{id}` | ✓ | admin | Update user |
| DELETE | `/admin/users/{id}` | ✓ | admin | Hapus user |
| GET | `/admin/varieties` | ✓ | admin | List varietas (paginated) |
| POST | `/admin/varieties` | ✓ | admin | Buat varietas |
| GET | `/admin/varieties/{id}` | ✓ | admin | Detail varietas |
| PUT | `/admin/varieties/{id}` | ✓ | admin | Update varietas |
| DELETE | `/admin/varieties/{id}` | ✓ | admin | Hapus varietas |
| GET | `/greenhouse/{id}/trees` | ✓ | gh.access | List pohon |
| POST | `/greenhouse/{id}/trees` | ✓ | gh.access | Tambah pohon |
| POST | `/greenhouse/{id}/trees/bulk` | ✓ | gh.access | Bulk buat pohon |
| GET | `/greenhouse/{id}/trees/{tId}` | ✓ | gh.access | Detail pohon |
| PUT | `/greenhouse/{id}/trees/{tId}` | ✓ | gh.access | Update pohon |
| DELETE | `/greenhouse/{id}/trees/{tId}` | ✓ | gh.access | Hapus pohon |
| GET | `/greenhouse/{id}/trees/{tId}/fruits` | ✓ | gh.access | List buah |
| POST | `/greenhouse/{id}/trees/{tId}/fruits` | ✓ | gh.access | Catat buah |
| GET | `/greenhouse/{id}/trees/{tId}/fruits/{fId}` | ✓ | gh.access | Detail buah |
| PUT | `/greenhouse/{id}/trees/{tId}/fruits/{fId}` | ✓ | gh.access | Update buah |
| DELETE | `/greenhouse/{id}/trees/{tId}/fruits/{fId}` | ✓ | gh.access | Hapus buah |
| GET | `/greenhouse/{id}/material-requests` | ✓ | gh.access | List permintaan |
| POST | `/greenhouse/{id}/material-requests` | ✓ | gh.access | Ajukan permintaan |
| PATCH | `/greenhouse/{id}/material-requests/{rId}` | ✓ | gh.access | Update status |
| DELETE | `/greenhouse/{id}/material-requests/{rId}` | ✓ | gh.access | Hapus permintaan |
| GET | `/greenhouse/{id}/report` | ✓ | gh.access | Laporan produksi |
| GET | `/sales` | ✓ | — | List GH + ringkasan omset |
| GET | `/sales/{id}` | ✓ | — | Detail transaksi |
| DELETE | `/sales/{id}` | ✓ | — | Hapus transaksi |
| GET | `/sales/greenhouse/{id}` | ✓ | — | Dashboard kasir |
| POST | `/sales/greenhouse/{id}` | ✓ | — | Buat transaksi |
| GET | `/sales/greenhouse/{id}/report` | ✓ | — | Laporan penjualan |

**Total: 44 endpoint**
