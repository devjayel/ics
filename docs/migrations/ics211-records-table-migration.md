# ICS 211 Records Table Migration Documentation

## Migration File: `2025_11_30_020220_create_ics211_records_table.php`

### Overview
This migration creates the `ics211_records` table that stores Incident Command System (ICS) Form 211 records, which are used for tracking check-in information for personnel and resources during emergency responses.

---

## ICS 211 Records Table - `ics211_records`

### Purpose
Stores ICS Form 211 main record information. ICS Form 211 is the standard form used to track the check-in process for personnel and resources at incident sites, ensuring accountability and resource management during emergency operations.

### Table Structure

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | bigint | PRIMARY KEY, AUTO_INCREMENT | Unique identifier |
| `uuid` | varchar(36) | UNIQUE, NOT NULL | UUID for external references |
| `token` | varchar(255) | UNIQUE, NOT NULL | Unique invitation/access token |
| `name` | varchar(255) | NOT NULL | Name/title of the incident or operation |
| `type` | varchar(255) | NOT NULL | Type of incident (fire, flood, earthquake, etc.) |
| `order_request_number` | varchar(255) | NOT NULL | Official order or request number |
| `checkin_location` | varchar(255) | NOT NULL | Physical location for check-in |
| `region` | varchar(255) | NULLABLE | Geographic region or district |
| `remarks` | text | NULLABLE | Additional notes or instructions |
| `remarks_image_attachment` | varchar(255) | NULLABLE | Path to attached image file |
| `status` | varchar(255) | DEFAULT 'pending' | Current operational status |
| `created_at` | timestamp | NOT NULL | Record creation time |
| `updated_at` | timestamp | NOT NULL | Record last update time |

### Indexes
- Primary key on `id`
- Unique index on `uuid`
- Unique index on `token`

---

## Status Values

The `status` field tracks the operational state:

| Status | Description |
|--------|-------------|
| `pending` | Newly created, not yet active (default) |
| `ongoing` | Active incident with personnel deployed |
| `completed` | Incident resolved, personnel demobilized |

---

## Business Logic

### ICS Form 211 Purpose
- **Personnel Accountability**: Track who is on-site
- **Resource Management**: Monitor available resources
- **Safety Compliance**: Ensure proper check-in procedures
- **Documentation**: Maintain official records for reporting

### Token-Based Access
- `token` field enables secure sharing of ICS records
- RULs can join as operators using the token
- Provides controlled access without exposing internal IDs

### Multi-RUL Operations
- Multiple RULs can be operators on a single ICS record
- Managed through `ics_operators` pivot table
- Enables collaborative incident management

### Status Workflow
1. **pending**: Initial creation, planning phase
2. **ongoing**: Active operations with deployed personnel
3. **completed**: Operations concluded, resources released

---

## File Attachments

### Remarks Images
- `remarks_image_attachment` stores supplementary visual information
- Typical uses: maps, diagrams, site photos, operational sketches
- Files stored in filesystem with path tracked in database

### Storage Location
```
storage/app/public/ics/remarks/
├── incident_001_map.jpg
├── site_layout_002.png
└── safety_diagram_003.pdf
```

---

## Migration Commands

### Apply Migration
```bash
php artisan migrate --path=database/migrations/2025_11_30_020220_create_ics211_records_table.php
```

### Rollback Migration
```bash
php artisan migrate:rollback --path=database/migrations/2025_11_30_020220_create_ics211_records_table.php
```

---

## Related Tables
- `check_in_details` - Personnel/resource assignments (foreign key: `ics211_record_id`)
- `ics_operators` - Many-to-many with `resident_unit_leaders`
- `ics_logs` - Activity audit trail (foreign key: `ics211_record_id`)

## Related Models
- `App\Models\Ics211Record`
- `App\Models\CheckInDetails` (child relationship)
- `App\Models\Rul` (many-to-many through operators)

## Dependencies
- None (Independent table, but works with others)

## Key Features
- **Collaborative Management**: Multiple RULs can operate single incident
- **Secure Sharing**: Token-based access control
- **Visual Documentation**: Image attachment support
- **Status Tracking**: Clear operational state management
- **Audit Trail**: All changes logged via `ics_logs`

## Notes
- Token must be unique for secure sharing functionality
- Status changes should trigger personnel status updates
- Remarks images provide visual context for operations
- UUID enables secure external references in APIs
- Order request number provides official documentation linkage