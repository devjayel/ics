# Personnels Table Migration Documentation

## Migration File: `2025_11_30_012251_create_personnels_table.php`

### Overview
This migration creates the `personnels` table that stores information about individual personnel/responders who can be assigned to ICS 211 records.

---

## Personnels Table - `personnels`

### Purpose
Stores individual personnel records for emergency responders who can be assigned to ICS 211 operations. Each personnel record is managed by a specific Resource Unit Leader (RUL).

### Table Structure

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | bigint | PRIMARY KEY, AUTO_INCREMENT | Unique identifier |
| `uuid` | varchar(36) | UNIQUE, NOT NULL | UUID for external references |
| `rul_id` | bigint | FOREIGN KEY, NOT NULL | Associated Resource Unit Leader |
| `avatar` | varchar(255) | NULLABLE | Profile avatar image path |
| `name` | varchar(255) | NOT NULL | Personnel's full name |
| `contact_number` | varchar(255) | NOT NULL | Phone/contact number |
| `serial_number` | varchar(255) | UNIQUE, NOT NULL | Unique personnel identifier/badge |
| `department` | varchar(255) | NOT NULL | Associated department/agency |
| `fcm_token` | varchar(255) | NULLABLE | Firebase Cloud Messaging token |
| `token` | varchar(255) | NULLABLE, UNIQUE | API authentication token |
| `status` | varchar(255) | DEFAULT 'available' | Current operational status |
| `created_at` | timestamp | NOT NULL | Record creation time |
| `updated_at` | timestamp | NOT NULL | Record last update time |

### Indexes
- Primary key on `id`
- Unique index on `uuid`
- Unique index on `serial_number`
- Unique index on `token`
- Foreign key on `rul_id`

### Foreign Keys
- `rul_id` references `resident_unit_leaders(id)` ON DELETE CASCADE

---

## Status Values

The `status` field tracks operational availability:

| Status | Description |
|--------|-------------|
| `available` | Ready for assignment (default) |
| `staging` | In staging area, preparing for deployment |
| `assigned` | Assigned to an ICS operation |
| `active` | Actively working in the field |
| `demobalized` | Released from assignment |
| `out_of_service` | Temporarily unavailable |
| `standby` | On standby for potential assignment |

---

## Business Logic

### Personnel Management
- Each personnel is managed by exactly one RUL (`rul_id`)
- RULs can create, update, and delete their personnel
- Serial numbers must be unique across the entire system

### Assignment Workflow
1. Personnel start with `available` status
2. When assigned to ICS 211 record → status becomes `standby`
3. During operations → status can be `staging`, `assigned`, or `active`
4. After operations → status becomes `demobalized` or returns to `available`

### Authentication
- Personnel can authenticate using `serial_number` and password
- Mobile app access via `token` field
- Push notifications enabled via `fcm_token`

---

## Migration Commands

### Apply Migration
```bash
php artisan migrate --path=database/migrations/2025_11_30_012251_create_personnels_table.php
```

### Rollback Migration
```bash
php artisan migrate:rollback --path=database/migrations/2025_11_30_012251_create_personnels_table.php
```

---

## Related Tables
- `resident_unit_leaders` - Parent RUL (foreign key)
- `check_in_details` - Assignment records (foreign key: `personnel_id`)

## Related Models
- `App\Models\Personnel`
- `App\Models\Rul` (parent relationship)

## Dependencies
- Requires `resident_unit_leaders` table to exist first

## File Storage Locations
- **Avatars**: Typically stored in `storage/app/public/personnel/avatars/`

## Notes
- CASCADE DELETE: When a RUL is deleted, all their personnel are also deleted
- Serial number acts as business identifier for personnel tracking
- Status field is crucial for operational readiness and assignment management
- FCM tokens enable real-time status updates and notifications
- Each personnel belongs to exactly one RUL for clear chain of command