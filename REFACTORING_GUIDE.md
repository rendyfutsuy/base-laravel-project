# Panduan Refactoring Testing dengan Mocking

## Tujuan
Mengubah semua test untuk menggunakan mocking agar tidak ada dependency ke database.

## Prinsip Dasar

### 1. Mock Services dan Repositories
- Jangan mock Eloquent models langsung
- Mock services dan repositories yang menggunakan models
- Gunakan dependency injection untuk memudahkan mocking

### 2. Mock Facades
- Gunakan `Facade::shouldReceive()` untuk Laravel facades
- Mock Mail, Queue, Cache, dll

### 3. Mock Dependencies
- Mock semua external dependencies
- Mock HTTP clients (Guzzle)
- Mock Firebase services
- Mock third-party APIs

## Contoh Refactoring

### Unit Tests
Unit tests harus menguji logic tanpa database:
- Helper functions
- Service classes dengan dependencies yang di-mock
- Utility classes

### Feature Tests
Feature tests bisa menggunakan HTTP testing tanpa database:
- Mock services yang dipanggil oleh controllers
- Mock authentication
- Mock external services

## Langkah-langkah Refactoring

1. Identifikasi dependencies di setiap test
2. Buat mock untuk setiap dependency
3. Inject mock ke service/controller
4. Verifikasi behavior tanpa database

## Contoh Implementasi

Lihat file test yang sudah direfactor:
- `Modules/Notification/Tests/Unit/NotificationServiceTest.php` (contoh)
- `tests/Unit/HelperTest.php` (sudah tidak pakai database)

## Catatan Penting

- Untuk Feature tests yang menggunakan HTTP testing, kita masih perlu mock services
- Jangan gunakan database real, gunakan mocking untuk semua data
- Pastikan semua test bisa berjalan tanpa database connection
