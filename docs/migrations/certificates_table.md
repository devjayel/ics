# Certificates Table Migration Documentation

## Overview
This migration creates the `certificates` table for storing certificate files associated with Resource Unit Leaders (RULs).

## Migration File
- **File**: `2025_11_30_010513_create_certificates_table.php`
- **Created**: November 30, 2025

## Table: certificates

### Purpose
Stores certification documents and files for Resource Unit Leaders.

### Schema

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint(20) | PRIMARY KEY, AUTO_INCREMENT | Primary key |
| uuid | varchar(191) | UNIQUE, NOT NULL | Unique identifier for certificates |
| rul_id | bigint(20) | FOREIGN KEY, NOT NULL | Reference to resident_unit_leaders.id |
| certificate_name | varchar(191) | NOT NULL | Name/title of the certificate |
| file_path | varchar(191) | NOT NULL | Path to the certificate file |
| created_at | timestamp | NULL | Record creation timestamp |
| updated_at | timestamp | NULL | Record update timestamp |

## Indexes
- `certificates.uuid` - Unique index for public identification
- `certificates.rul_id` - Foreign key index for RUL relationships

## Foreign Keys
- `rul_id` → `resident_unit_leaders.id`
  - **Constraint**: ON DELETE CASCADE
  - **Behavior**: When a RUL is deleted, all their certificates are automatically deleted

## Relationships
- **Belongs To**: `resident_unit_leaders` (Many certificates can belong to one RUL)

## Usage Examples

### Creating a Certificate Record
```php
Certificate::create([
    'uuid' => Str::uuid(),
    'rul_id' => 1,
    'certificate_name' => 'Fire Safety Training Certificate',
    'file_path' => 'certificates/fire_safety_2025.pdf'
]);
```

### Retrieving Certificates for a RUL
```php
$certificates = Certificate::where('rul_id', $rulId)->get();
```

## File Storage Considerations
- Certificate files should be stored securely with proper access controls
- Consider using Laravel's storage system with appropriate disk configuration
- Implement file cleanup when certificate records are deleted

## Security Notes
- Certificate files may contain sensitive information
- Ensure proper authorization before allowing access to certificate files
- Consider implementing file integrity checks

## Rollback
Running the down migration will drop the `certificates` table and all associated data.

## Dependencies
This migration depends on:
- `resident_unit_leaders` table (must exist for foreign key constraint)