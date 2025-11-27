# Debug Folder

Folder ini berisi file-file testing dan debugging untuk pengembangan produk katalog.

## 📋 Struktur File

### Test Files (test\_\*.php)

-   `test_filter_comprehensive.php` - Test database query filtering (untuk tinker/REPL)
-   `test_filter_api_endpoints.php` - Dokumentasi endpoint filter API dan testing guide
-   `test_activation_logic.php` - Test logika aktivasi produk
-   `test_cache.php` - Test cache behavior
-   `test_catalog_api.php` - Test catalog API responses
-   `test_categories.php` - Test kategori functionality
-   `test_dashboard.php` - Test dashboard API endpoints
-   `test_image_fix.php` - Test image path normalization
-   `test_is_active.php` - Test is_active field
-   `test_pagination.php` - Test pagination logic
-   `test_public_products.php` - Test public product listing

### Check Files (check\_\*.php)

-   `check_admin.php` - Check admin-related functionality
-   `check_categories.php` - Check categories in database
-   `check_db_image.php` - Check image paths in database
-   `check_db_raw.php` - Raw database inspection
-   `check_images.php` - Check image handling

### Debug Files (debug\_\*.php)

-   `debug_images.php` - Debug image processing

### Deprecated Files

-   `DEPRECATED_fix_images_db.php` - Historical: One-time migration script (no longer needed)

## 🚀 Cara Menggunakan

### Option 1: Menjalankan file PHP directly

```bash
php debug/test_filter_api_endpoints.php
```

### Option 2: Menggunakan untuk testing di Postman

Lihat file `test_filter_api_endpoints.php` untuk dokumentasi lengkap endpoint dan parameter.

### Option 3: Untuk interactive testing dengan tinker

```bash
php artisan tinker
>>> include 'debug/test_filter_comprehensive.php'
```

## 📝 Konvensi Penamaan

-   **test\_\*.php** - File testing untuk feature/functionality tertentu
-   **check\_\*.php** - File untuk melihat/inspect data di database
-   **debug\_\*.php** - File untuk debug issues
-   **DEPRECATED\_\*.php** - File yang sudah tidak digunakan (untuk referensi historis)

## ✅ Latest Changes (Filter System)

### Backend Updates

-   `ProductPublicController.php`: Added support for district_id, village_id filtering, dan rating_desc sorting

### Frontend Updates

-   `ProductFilter.jsx`: Added price field labels dan "Rating: Tertinggi" sort option
-   `Catalog.jsx`: Fixed parameter mapping (province → province_id, etc.)

### Test Files Added

-   `test_filter_api_endpoints.php` - Complete API testing documentation

---

Last Updated: November 27, 2025
