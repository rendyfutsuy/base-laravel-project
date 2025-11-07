# API Documentation dengan Scribe

Project ini menggunakan [Scribe](https://scribe.knuckles.wtf/) untuk generate dokumentasi API secara otomatis.

## Akses Dokumentasi

Setelah generate dokumentasi, Anda dapat mengaksesnya di:

- **HTML Documentation**: `http://your-domain.com/docs`
- **Postman Collection**: `http://your-domain.com/docs.postman`
- **OpenAPI Specification**: `http://your-domain.com/docs.openapi`

## Generate Dokumentasi

Untuk generate dokumentasi API, jalankan:

```bash
php artisan scribe:generate
```

Dokumentasi akan di-generate ke:
- **Blade Views**: `resources/views/scribe/`
- **Assets**: `public/vendor/scribe/`
- **Postman Collection**: `storage/app/scribe/collection.json`
- **OpenAPI Spec**: `storage/app/scribe/openapi.yaml`

## Menambahkan Dokumentasi di Controller

Untuk menambahkan dokumentasi di controller, gunakan annotation Scribe:

```php
/**
 * @group Authentication
 * @authenticated
 * 
 * Get authenticated user's profile.
 * 
 * @response 200 {
 *   "id": "user-id-123",
 *   "name": "John Doe",
 *   "email": "user@example.com",
 *   "roles": []
 * }
 */
public function profile()
{
    // ...
}
```

### Annotation yang Tersedia

- `@group {GroupName}` - Mengelompokkan endpoint ke dalam group
- `@authenticated` - Menandai endpoint yang memerlukan authentication
- `@unauthenticated` - Menandai endpoint yang tidak memerlukan authentication
- `@bodyParam {name} {type} {description}` - Mendokumentasikan body parameter
- `@queryParam {name} {type} {description}` - Mendokumentasikan query parameter
- `@urlParam {name} {type} {description}` - Mendokumentasikan URL parameter
- `@response {status} {json}` - Mendokumentasikan response
- `@header {name} {value}` - Mendokumentasikan header

### Contoh Lengkap

```php
/**
 * @group Authentication
 * 
 * Authenticate user and receive access token.
 * 
 * @bodyParam email string required The user's email address. Example: user@example.com
 * @bodyParam password string required The user's password. Example: password123
 * 
 * @response 200 {
 *   "id": "user-id-123",
 *   "name": "John Doe",
 *   "email": "user@example.com",
 *   "expires_in": 3600,
 *   "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
 *   "refresh_token": "refresh-token-123"
 * }
 * @response 400 {
 *   "status": "failed",
 *   "message": "Invalid credentials",
 *   "status_code": 400
 * }
 */
public function login(AuthLoginRequest $request)
{
    // ...
}
```

## Konfigurasi

Konfigurasi Scribe dapat ditemukan di `config/scribe.php`. Beberapa konfigurasi penting:

- **Title**: Judul dokumentasi
- **Description**: Deskripsi API
- **Base URL**: Base URL untuk API
- **Authentication**: Konfigurasi authentication (Bearer token)
- **Routes**: Routes yang akan di-dokumentasikan
- **Groups**: Pengelompokan endpoint

## Menambahkan bodyParameters() di FormRequest

Untuk dokumentasi yang lebih lengkap, tambahkan method `bodyParameters()` di FormRequest:

```php
class RegisterRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:8',
        ];
    }

    /**
     * Get the body parameters for the request.
     *
     * @return array
     */
    public function bodyParameters()
    {
        return [
            'name' => [
                'description' => 'The user\'s full name.',
                'example' => 'John Doe',
            ],
            'email' => [
                'description' => 'The user\'s email address.',
                'example' => 'user@example.com',
            ],
            'password' => [
                'description' => 'The user\'s password. Must be at least 8 characters.',
                'example' => 'password123',
            ],
            'password_confirmation' => [
                'description' => 'The password confirmation. Must match the password.',
                'example' => 'password123',
            ],
        ];
    }
}
```

## Tips

1. **Gunakan Groups**: Kelompokkan endpoint yang terkait ke dalam group yang sama
2. **Dokumentasikan Response**: Selalu dokumentasikan response yang mungkin terjadi
3. **Gunakan Examples**: Berikan contoh nilai untuk parameter
4. **Update Secara Berkala**: Generate ulang dokumentasi setiap kali ada perubahan API

## Resources

- [Scribe Documentation](https://scribe.knuckles.wtf/laravel)
- [Scribe GitHub](https://github.com/knuckleswtf/scribe)

