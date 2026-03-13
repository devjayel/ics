# Users Table Migration Documentation

## Migration File: `0001_01_01_000000_create_users_table.php`

### Overview
This migration creates three core authentication and session management tables:
- `users` - Main user accounts
- `password_reset_tokens` - Password reset functionality
- `sessions` - User session management

---

## Users Table - `users`

### Purpose
Stores main user account information for the ICS application.

### Table Structure

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | bigint | PRIMARY KEY, AUTO_INCREMENT | Unique identifier |
| `uuid` | varchar(36) | UNIQUE, NOT NULL | UUID for external references |
| `name` | varchar(255) | NOT NULL | User's full name |
| `email` | varchar(255) | UNIQUE, NOT NULL | User's email address |
| `email_verified_at` | timestamp | NULLABLE | Email verification timestamp |
| `password` | varchar(255) | NOT NULL | Hashed password |
| `remember_token` | varchar(100) | NULLABLE | Laravel remember token |
| `created_at` | timestamp | NOT NULL | Record creation time |
| `updated_at` | timestamp | NOT NULL | Record last update time |

### Indexes
- Primary key on `id`
- Unique index on `uuid`
- Unique index on `email`

---

## Password Reset Tokens Table - `password_reset_tokens`

### Purpose
Manages password reset tokens for secure password recovery.

### Table Structure

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `email` | varchar(255) | PRIMARY KEY | User's email address |
| `token` | varchar(255) | NOT NULL | Password reset token |
| `created_at` | timestamp | NULLABLE | Token creation time |

### Indexes
- Primary key on `email`

---

## Sessions Table - `sessions`

### Purpose
Stores user session data for authentication state management.

### Table Structure

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | varchar(255) | PRIMARY KEY | Session identifier |
| `user_id` | bigint | NULLABLE, INDEX | Associated user ID |
| `ip_address` | varchar(45) | NULLABLE | Client IP address |
| `user_agent` | text | NULLABLE | Client user agent string |
| `payload` | longtext | NOT NULL | Serialized session data |
| `last_activity` | int | INDEX | Last activity timestamp |

### Indexes
- Primary key on `id`
- Index on `user_id`
- Index on `last_activity`

### Foreign Keys
- `user_id` references `users(id)`

---

## Migration Commands

### Apply Migration
```bash
php artisan migrate --path=database/migrations/0001_01_01_000000_create_users_table.php
```

### Rollback Migration
```bash
php artisan migrate:rollback --path=database/migrations/0001_01_01_000000_create_users_table.php
```

---

## Related Models
- `App\Models\User`

## Dependencies
- None (Base migration)

## Notes
- This is a foundational migration that other user-related tables depend on
- UUID field is used for external API references instead of exposing internal IDs
- Sessions table supports Laravel's database session driver