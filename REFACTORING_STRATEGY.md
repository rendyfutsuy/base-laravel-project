# Strategi Refactoring Testing untuk Menghapus Database Dependencies

## Tujuan
Menghapus semua logic untuk periksa Database secara keseluruhan dan menggantinya dengan mocking.

## Pendekatan

### 1. Mock Services dan Repositories
- Mock semua services yang berinteraksi dengan database
- Mock repositories yang melakukan query database
- Gunakan dependency injection untuk memudahkan mocking

### 2. Mock Authentication
- Mock AuthenticationService untuk login/logout
- Mock User model untuk authentication
- Mock Passport tokens

### 3. Mock Eloquent Models
- Jangan gunakan Eloquent models langsung di test
- Mock semua query builder methods
- Gunakan instance mock untuk models

### 4. Update phpunit.xml
- Hapus konfigurasi database dari phpunit.xml
- Set DB_CONNECTION ke null atau hapus sama sekali
- Pastikan test bisa berjalan tanpa database connection

## Langkah-langkah Implementasi

### Phase 1: Setup Infrastructure
1. ✅ Buat MockAuthHelper trait
2. ✅ Refactor AuthCase trait
3. Update phpunit.xml untuk tidak menggunakan database

### Phase 2: Refactor Feature Tests
1. Refactor Authentication tests
2. Refactor UserManagement tests
3. Refactor Hierarchy tests
4. Refactor Notification tests

### Phase 3: Refactor Unit Tests
1. Refactor semua Unit tests yang masih menggunakan database
2. Pastikan semua test menggunakan mocking

### Phase 4: Cleanup
1. Hapus semua database seeding dari test
2. Hapus semua database migration dari test setup
3. Verifikasi semua test bisa berjalan tanpa database

## Contoh Implementasi

### Mock AuthenticationService
```php
$authServiceMock = Mockery::mock(AuthenticationService::class);
$authServiceMock->shouldReceive('login')
    ->once()
    ->andReturn($userMock);
$this->app->instance(AuthenticationService::class, $authServiceMock);
```

### Mock User Model
```php
$userMock = Mockery::mock(User::class)->makePartial();
$userMock->id = 'user-id-123';
$userMock->email = 'test@mailinator.com';
$userMock->shouldReceive('createToken')->andReturn($tokenMock);
```

### Mock Repository
```php
$userRepositoryMock = Mockery::mock(UserContract::class);
$userRepositoryMock->shouldReceive('store')
    ->once()
    ->andReturn($userMock);
$this->app->instance(UserContract::class, $userRepositoryMock);
```

## Catatan Penting

- Untuk Feature tests yang menggunakan HTTP testing, kita masih perlu mock services
- Jangan gunakan database real, gunakan mocking untuk semua data
- Pastikan semua test bisa berjalan tanpa database connection
- Gunakan `$this->app->instance()` untuk bind mock ke service container

