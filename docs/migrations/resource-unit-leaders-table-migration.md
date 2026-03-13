# Resource Unit Leaders Table Migration Documentation

## Migration File: `2025_11_30_005736_create_resource_unit_leaders_table.php`

### Overview
This migration creates the `resident_unit_leaders` table that stores information about Resource Unit Leaders (RULs) who manage personnel and ICS operations.

---

## Resource Unit Leaders Table - `resident_unit_leaders`

### Purpose
Stores Resource Unit Leader profiles who are responsible for managing personnel and ICS 211 records in the emergency management system.

### Table Structure

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | bigint | PRIMARY KEY, AUTO_INCREMENT | Unique identifier |
| `uuid` | varchar(36) | UNIQUE, NOT NULL | UUID for external references |
| `avatar` | varchar(255) | NULLABLE | Profile avatar image path |
| `logo` | varchar(255) | NULLABLE | Organization logo path |
| `name` | varchar(255) | NOT NULL | RUL's full name |
| `contact_number` | varchar(255) | NOT NULL | Phone/contact number |
| `serial_number` | varchar(255) | UNIQUE, NOT NULL | Unique RUL identifier/badge number |
| `department` | varchar(255) | NOT NULL | Associated department/agency |
| `signature` | varchar(255) | NULLABLE | Digital signature file path |
| `fcm_token` | varchar(255) | NULLABLE | Firebase Cloud Messaging token |
| `token` | varchar(255) | NULLABLE, UNIQUE | API authentication token |
| `created_at` | timestamp | NOT NULL | Record creation time |
| `updated_at` | timestamp | NOT NULL | Record last update time |

### Indexes
- Primary key on `id`
- Unique index on `uuid`
- Unique index on `serial_number`
- Unique index on `token`

### Key Features
- **Avatar Support**: Profile picture upload capability
- **Logo Support**: Organization/department logo
- **Digital Signatures**: For official document signing
- **Push Notifications**: FCM token for mobile notifications
- **API Authentication**: Unique token for API access
- **Department Tracking**: Associates RUL with specific department

---

## Business Logic

### Authentication
- RULs use their `serial_number` and password for login
- API access controlled via unique `token` field
- FCM tokens enable push notifications for mobile apps

### Profile Management
- Supports avatar and logo uploads
- Digital signature storage for document authorization
- Contact information for emergency communication

### Security Features
- UUID used for external references instead of internal IDs
- Serial number must be unique across all RULs
- Token-based API authentication

---

## Migration Commands

### Apply Migration
```bash
php artisan migrate --path=database/migrations/2025_11_30_005736_create_resource_unit_leaders_table.php
```

### Rollback Migration
```bash
php artisan migrate:rollback --path=database/migrations/2025_11_30_005736_create_resource_unit_leaders_table.php
```

---

## Related Tables
- `personnels` - RULs manage personnel (foreign key: `rul_id`)
- `certificates` - RUL certifications (foreign key: `rul_id`)
- `ics211_records` - Many-to-many relationship via `ics_operators` table
- `ics_logs` - Activity logs (foreign key: `rul_id`)

## Related Models
- `App\Models\Rul`

## Dependencies
- None (Independent table)

## File Storage Locations
- **Avatars**: Typically stored in `storage/app/public/avatars/`
- **Logos**: Typically stored in `storage/app/public/logos/`
- **Signatures**: Typically stored in `storage/app/public/signatures/`

## Notes
- Table name uses `resident_unit_leaders` but model likely named `Rul`
- Serial number acts as a business identifier separate from database ID
- FCM token enables real-time notifications for mobile applications
- Digital signatures support electronic document approval workflows