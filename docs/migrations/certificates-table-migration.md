# Certificates Table Migration Documentation

## Migration File: `2025_11_30_010513_create_certificates_table.php`

### Overview
This migration creates the `certificates` table that stores certification documents uploaded by Resource Unit Leaders (RULs).

---

## Certificates Table - `certificates`

### Purpose
Stores certification documents and credentials for Resource Unit Leaders. These certificates validate qualifications and training required for emergency management operations.

### Table Structure

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | bigint | PRIMARY KEY, AUTO_INCREMENT | Unique identifier |
| `uuid` | varchar(36) | UNIQUE, NOT NULL | UUID for external references |
| `rul_id` | bigint | FOREIGN KEY, NOT NULL | Associated Resource Unit Leader |
| `certificate_name` | varchar(255) | NOT NULL | Name/title of the certificate |
| `file_path` | varchar(255) | NOT NULL | Path to uploaded certificate file |
| `created_at` | timestamp | NOT NULL | Record creation time |
| `updated_at` | timestamp | NOT NULL | Record last update time |

### Indexes
- Primary key on `id`
- Unique index on `uuid`
- Foreign key on `rul_id`

### Foreign Keys
- `rul_id` references `resident_unit_leaders(id)` ON DELETE CASCADE

---

## Business Logic

### Certificate Management
- Each certificate belongs to exactly one RUL
- RULs can upload multiple certificates
- Certificate files are stored in filesystem with path tracked in database

### File Handling
- Supports various document formats (PDF, images, etc.)
- File path typically stores relative path from storage root
- UUID provides secure reference for file downloads

### Qualification Tracking
- Certificate names describe the qualification or training
- Used to verify RUL competencies for specific operations
- May be required for certain ICS assignment types

---

## Common Certificate Types

Examples of certificates that might be stored:

| Certificate Name | Description |
|-----------------|-------------|
| ICS-100 | Introduction to ICS |
| ICS-200 | ICS for Single Resources |
| ICS-300 | Intermediate ICS |
| ICS-400 | Advanced ICS |
| Hazmat Operations | Hazardous Materials Response |
| CPR Certification | First Aid/CPR Training |
| Fire Fighter I/II | Basic Firefighting Certification |
| EMT Basic | Emergency Medical Technician |

---

## File Storage

### Typical Storage Structure
```
storage/app/public/certificates/
├── rul_1/
│   ├── ics-100-certificate.pdf
│   └── cpr-certification.jpg
├── rul_2/
│   └── hazmat-training.pdf
└── ...
```

### Security Considerations
- Files should be stored outside web root
- Access controlled through application routes
- UUIDs prevent direct file enumeration

---

## Migration Commands

### Apply Migration
```bash
php artisan migrate --path=database/migrations/2025_11_30_010513_create_certificates_table.php
```

### Rollback Migration
```bash
php artisan migrate:rollback --path=database/migrations/2025_11_30_010513_create_certificates_table.php
```

---

## Related Tables
- `resident_unit_leaders` - Parent RUL (foreign key)

## Related Models
- `App\Models\Certificate`
- `App\Models\Rul` (parent relationship)

## Dependencies
- Requires `resident_unit_leaders` table to exist first

## API Endpoints
Likely includes endpoints for:
- Upload certificate files
- List RUL certificates
- Download certificate files
- Delete certificates

## Notes
- CASCADE DELETE: When a RUL is deleted, all their certificates are also deleted
- File path should be validated to prevent directory traversal attacks
- Consider implementing file size limits and allowed file types
- Certificate expiration dates might be added in future migrations
- UUID enables secure file serving without exposing filesystem paths