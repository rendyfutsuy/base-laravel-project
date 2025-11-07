# Vue.js Frontend Documentation

Frontend aplikasi ini menggunakan Vue.js 2 dengan Vue Router dan Vuex untuk state management.

## Struktur Project

```
resources/js/
├── App.vue                 # Root component
├── app.js                  # Entry point
├── bootstrap.js            # Bootstrap axios
├── router/
│   └── index.js           # Vue Router configuration
├── store/
│   ├── index.js           # Vuex store
│   └── modules/
│       ├── auth.js        # Authentication module
│       ├── users.js       # Users module
│       ├── roles.js       # Roles module
│       ├── permissions.js # Permissions module
│       └── notifications.js # Notifications module
├── services/
│   └── api.js             # Axios API service
├── layouts/
│   └── DashboardLayout.vue # Dashboard layout
└── views/
    ├── Dashboard.vue       # Dashboard page
    ├── Auth/
    │   ├── Login.vue       # Login page
    │   ├── Register.vue   # Register page
    │   └── Profile.vue     # Profile page
    ├── UserManagement/
    │   ├── Users.vue       # Users management
    │   ├── Staffs.vue      # Staffs management
    │   └── Superadmins.vue # Superadmins management
    ├── Hierarchy/
    │   ├── Roles.vue       # Roles management
    │   └── Permissions.vue # Permissions management
    └── Notifications/
        └── Index.vue       # Notifications page
```

## Setup

### 1. Install Dependencies

```bash
npm install
# atau
yarn install
```

### 2. Build Assets

```bash
npm run dev
# atau untuk production
npm run production
```

### 3. Watch for Changes

```bash
npm run watch
```

## Fitur

### Authentication
- **Login**: Login dengan email dan password
- **Register**: Registrasi user baru dengan OTP verification
- **Profile**: Lihat dan update profile
- **Change Password**: Ganti password

### User Management
- **Users**: CRUD untuk users
- **Staffs**: CRUD untuk staffs
- **Superadmins**: CRUD untuk superadmins

### Hierarchy
- **Roles**: CRUD untuk roles, sync permissions
- **Permissions**: CRUD untuk permissions, sync roles dan users

### Notifications
- **List Notifications**: Lihat semua notifications
- **Mark as Read**: Tandai notification sebagai sudah dibaca
- **Delete Notification**: Hapus notification

## Routing

### Public Routes
- `/login` - Login page
- `/register` - Register page

### Protected Routes
- `/` - Dashboard
- `/profile` - Profile page
- `/users` - User management
- `/staffs` - Staff management
- `/superadmins` - Superadmin management
- `/roles` - Role management
- `/permissions` - Permission management
- `/notifications` - Notifications page

## State Management (Vuex)

### Auth Module
- `isAuthenticated`: Status authentication
- `user`: User data
- `token`: Access token
- Actions: `login`, `register`, `logout`, `fetchProfile`, `updateProfile`, `changePassword`

### Users Module
- `users`: List users
- `pagination`: Pagination info
- `loading`: Loading state
- Actions: `fetchUsers`, `createUser`, `updateUser`, `deleteUser`, `fetchUser`

### Roles Module
- `roles`: List roles
- `pagination`: Pagination info
- `loading`: Loading state
- Actions: `fetchRoles`, `createRole`, `updateRole`, `deleteRole`, `syncPermissions`

### Permissions Module
- `permissions`: List permissions
- `pagination`: Pagination info
- `loading`: Loading state
- Actions: `fetchPermissions`, `createPermission`, `updatePermission`, `deletePermission`, `resyncRoles`

### Notifications Module
- `notifications`: List notifications
- `pagination`: Pagination info
- `unreadCount`: Unread notifications count
- `loading`: Loading state
- Actions: `fetchNotifications`, `markAsRead`, `deleteNotification`

## API Service

API service menggunakan Axios dengan:
- Base URL: `process.env.MIX_APP_URL` atau `http://localhost:8000`
- Automatic Bearer token injection
- Automatic error handling untuk 401 (redirect ke login)

## Authentication Flow

1. User login melalui `/login`
2. Token disimpan di localStorage
3. Token ditambahkan ke Axios headers
4. User di-redirect ke Dashboard
5. Profile di-fetch otomatis setelah login
6. Protected routes di-check oleh router guard

## Styling

Aplikasi menggunakan:
- Custom CSS dengan gradient design
- Responsive design
- Modern UI dengan card-based layout
- Modal untuk create/edit forms

## Development

### Hot Reload

```bash
npm run hot
```

### Build for Production

```bash
npm run production
```

## Environment Variables

Tambahkan ke `.env`:

```env
MIX_APP_URL=http://localhost:8000
```

## Notes

- Semua API calls menggunakan Axios dengan automatic token injection
- Authentication state di-manage oleh Vuex
- Router guard otomatis redirect ke login jika tidak authenticated
- Token disimpan di localStorage untuk persist session

