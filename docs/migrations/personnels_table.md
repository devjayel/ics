# Personnels Table Migration Documentation

## Overview
This migration creates the `personnels` table for managing personnel/staff members under Resource Unit Leaders (RULs).

## Migration File
- **File**: `2025_11_30_012251_create_personnels_table.php`
- **Created**: November 30, 2025

## Table: personnels

### Purpose
Stores information about personnel/staff members who can be assigned to ICS 211 records and managed by RULs.

### Schema

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint(20) | PRIMARY KEY, AUTO_INCREMENT | Primary key |
| uuid | varchar(191) | UNIQUE, NOT NULL | Unique identifier for personnel |
| rul_id | bigint(20) | FOREIGN KEY, NOT NULL | Reference to resident_unit_leaders.id |
| avatar | varchar(191) | NULL | Profile picture/avatar file path |
| name | varchar(191) | NOT NULL | Full name of personnel |
| contact_number | varchar(191) | NOT NULL | Phone/contact number |
| serial_number | varchar(191) | UNIQUE, NOT NULL | Unique personnel identification number |
| department | varchar(191) | NOT NULL | Department/unit assignment |
| fcm_token | varchar(191) | NULL | Firebase Cloud Messaging token for push notifications |
| token | varchar(191) | UNIQUE, NULL | Authentication/access token |
| status | varchar(191) | NOT NULL, DEFAULT 'available' | Current availability status |
| created_at | timestamp | NULL | Record creation timestamp |
| updated_at | timestamp | NULL | Record update timestamp |

## Indexes
- `personnels.uuid` - Unique index for public identification
- `personnels.serial_number` - Unique index for personnel identification
- `personnels.token` - Unique index for authentication tokens
- `personnels.rul_id` - Foreign key index for RUL relationships

## Foreign Keys
- `rul_id` → `resident_unit_leaders.id`
  - **Constraint**: ON DELETE CASCADE
  - **Behavior**: When a RUL is deleted, all their personnel are automatically deleted

## Status Values
The `status` field can have the following values:
- `available` - Available for assignment (default)
- `standby` - On standby, prepared for deployment
- `assigned` - Assigned to an incident
- `active` - Actively working on an incident
- `demobalized` - Demobilized from incident
- `out_of_service` - Not available for assignment
- `staging` - In staging area

## Relationships
- **Belongs To**: `resident_unit_leaders` (Many personnel belong to one RUL)
- **Has Many**: `check_in_details` (Personnel can be assigned to multiple ICS records)

## Usage Examples

### Creating Personnel Records
```php
Personnel::create([
    'uuid' => Str::uuid(),
    'rul_id' => 1,
    'name' => 'John Doe',
    'contact_number' => '+1234567890',
    'serial_number' => 'PER-2025-001',
    'department' => 'Fire Department',
    'status' => 'available'
]);
```

### Finding Available Personnel
```php
$availablePersonnel = Personnel::where('status', 'available')
                              ->where('rul_id', $rulId)
                              ->get();
```

### Updating Personnel Status
```php
Personnel::where('id', $personnelId)
         ->update(['status' => 'assigned']);
```

## Push Notifications
- The `fcm_token` field stores Firebase Cloud Messaging tokens
- Used to send real-time notifications about ICS assignments
- Should be updated when personnel login to mobile applications

## Authentication
- The `token` field can store authentication tokens for mobile app access
- Should be unique and properly secured
- Can be null if personnel doesn't use mobile authentication

## Security Considerations
- Serial numbers must be unique across the system
- Contact information should be treated as sensitive data
- Avatar files should be properly validated and secured
- FCM tokens should be refreshed periodically

## Rollback
Running the down migration will drop the `personnels` table and all associated data.

## Dependencies
This migration depends on:
- `resident_unit_leaders` table (must exist for foreign key constraint)