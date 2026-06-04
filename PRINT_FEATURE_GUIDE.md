# Print Feature Testing Guide

## Overview
Halaman print sales (`/sales/{sale}/print`) memiliki dua mode:

1. **Manual Print** - User klik tombol "Cetak Sekarang"
2. **Auto Print** - Ketika user submit form dengan tombol "Bayar & Print", halaman akan auto-trigger print

## Manual Testing

### Step 1: Login dan Buat Sale
1. Buka http://127.0.0.1:8000
2. Login dengan:
   - Email: `admin@greenhouse.id`
   - Password: `password`
3. Navigate ke: Sales → Pilih Greenhouse → Buat Penjualan Baru
4. Isi form dengan data test:
   - Nama Pembeli: Test Buyer
   - Pilih varietas melon
   - Berat: 5 kg
   - Harga/kg: Rp 50.000
5. Klik tombol "Bayar Kemudian" (untuk test manual print dulu)

### Step 2: Test Manual Print
Setelah sale disimpan, halaman akan redirect ke halaman kasir. Sekarang test manual print:
1. Buat sale lagi dengan tombol "Bayar Kemudian"
2. Di halaman kasir, cari sale yang baru dibuat, klik untuk lihat detail
3. Klik tombol "Cetak" atau navigasi ke `/sales/{id}/print`
4. Di halaman print, klik tombol **"Cetak Sekarang"** (tombol hijau)
5. Print dialog akan muncul → klik "Print" untuk mengirim ke printer

### Step 3: Test Auto Print
1. Buat sale baru dengan mengikuti step 1
2. **KALI INI KLIK TOMBOL "Bayar & Print"** (tombol hijau di create form)
3. Halaman akan otomatis:
   - Redirect ke halaman print
   - Trigger print dialog OTOMATIS (tanpa user klik tombol)
   - Printer akan menerima perintah print langsung
4. User hanya perlu close print dialog atau press Escape untuk kembali
5. Klik "Kembali ke Kasir" untuk kembali ke halaman kasir sebelumnya

## Technical Details

### Print Button Structure
```html
<button onclick="window.print()">🖨 Cetak Sekarang</button>
```
- Onclick langsung trigger `window.print()` yang adalah JavaScript standard API
- Akan membuka Print Dialog browser

### Auto-Print Script
```blade
@if(session('auto_print'))
<script>
    window.addEventListener('load', () => {
        setTimeout(() => window.print(), 500);
    });
</script>
@endif
```
- Hanya execute jika session `auto_print` bernilai true
- Trigger pada saat halaman fully loaded
- Delay 500ms untuk memastikan halaman fully rendered sebelum print dialog muncul

### Form Submission Flow

#### "Bayar Kemudian" (pay_later)
```
Form Submit → Controller Store Sale → Direct Redirect to Kasir Page
                                   ↓
                            No Print Process
```

#### "Bayar & Print" (pay_and_print)
```
Form Submit → Controller Store Sale → Redirect to Print Page with auto_print=true
                                   ↓
                         Auto-Print Dialog Triggered
                                   ↓
                        User Close Dialog / Print
                                   ↓
                        Click "Kembali ke Kasir"
```

## Expected Behavior

### Manual Print
- ✅ Tombol "Cetak Sekarang" clickable
- ✅ onClick trigger `window.print()`
- ✅ Browser print dialog muncul
- ✅ User dapat select printer dan klik "Print"

### Auto Print
- ✅ Halaman load, print dialog muncul otomatis
- ✅ Printer device yang tersambung akan menerima perintah print
- ✅ User tidak perlu klik apapun untuk trigger print
- ✅ Receipt akan tercetak otomatis ke printer default

## Printer Setup Notes

Untuk fitur auto-print bekerja dengan optimal:
1. **Desktop/Web**: Set default printer di system settings
   - Windows: Settings → Devices → Printers → Set as default
   - Mac: System Preferences → Printers & Scanners → Set Default
   - Linux: `lpadmin -d <printer-name>`

2. **Mobile/Tablet**: 
   - iOS: AirPrint-compatible printer diperlukan
   - Android: Google Cloud Print atau printer app yang support

3. **Receipt Printer** (thermal printer):
   - Setting ukuran kertas ke 80mm di print settings
   - Print view sudah di-optimize untuk 80mm thermal receipt printer
   - Check CSS: `@media print { @page { size: 80mm auto; } }`

## Testing Checklist

- [ ] Manual Print: Tombol "Cetak Sekarang" bekerja
- [ ] Manual Print: Print dialog muncul saat diklik
- [ ] Manual Print: Receipt tercetak dengan format benar
- [ ] Auto Print: Form submission dengan "Bayar & Print" redirect ke print page
- [ ] Auto Print: Print dialog muncul otomatis tanpa user action
- [ ] Auto Print: Receipt tercetak otomatis ke default printer
- [ ] Back Link: "Kembali ke Kasir" link bekerja dan redirect ke kasir page
- [ ] Print Quality: Teks readble, layout konsisten, footer visible

## Troubleshooting

### Print dialog tidak muncul pada auto-print
- [ ] Cek browser console untuk JavaScript errors
- [ ] Verifikasi session `auto_print` ter-set: cek Network tab, check response headers
- [ ] Coba hard refresh halaman (Ctrl+Shift+R atau Cmd+Shift+R)

### Receipt format tidak benar saat print
- [ ] Verify CSS media query: `@media print { ... }`
- [ ] Check page margin setting di print dialog
- [ ] Ubah "Margins" ke "None" di print dialog
- [ ] Set "Page size" ke "Custom 80mm × auto" untuk thermal printer

### Printer tidak menerima perintah
- [ ] Cek apakah printer online dan connected ke computer
- [ ] Verify di System Settings bahwa printer set sebagai "Default"
- [ ] Try manual print test dulu sebelum auto-print
- [ ] Check printer queue di Windows (Devices and Printers) atau Mac (System Prefs)

## Code Files Modified

- `resources/views/sales/create.blade.php` - Added dual submit buttons
- `app/Http/Controllers/SaleController.php` - Added action validation & conditional redirect
- `resources/views/sales/print.blade.php` - Added auto-print script
