# Check-In Detail Histories Table Migration Documentation

## Migration File: `2025_12_03_072949_create_check_in_detail_histories_table.php`

### Overview
This migration creates the `check_in_detail_histories` table that stores historical status changes and workflow tracking for check-in details related to ICS 211 records.

---

## Check-In Detail Histories Table - `check_in_detail_histories`

### Purpose
Tracks status changes, workflow progression, and administrative actions for check-in details. This provides an audit trail and history of how resources progress through various operational states during incident management.

### Table Structure

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | bigint | PRIMARY KEY, AUTO_INCREMENT | Unique identifier |
| `uuid` | varchar(36) | UNIQUE, NOT NULL | UUID for external references |
| `ics211_record_id` | bigint | FOREIGN KEY, NOT NULL | Parent ICS 211 record |
| `order_request_number` | varchar(255) | NOT NULL | Official order/request number |
| `remarks` | text | NULLABLE | Administrative notes or comments |
| `description` | text | NULLABLE | Detailed status change description |
| `status` | varchar(255) | DEFAULT 'pending' | Current workflow status |
| `created_at` | timestamp | NOT NULL | Record creation time |
| `updated_at` | timestamp | NOT NULL | Record last update time |

### Indexes
- Primary key on `id`
- Unique index on `uuid`
- Foreign key on `ics211_record_id`

### Foreign Keys
- `ics211_record_id` references `ics211_records(id)` ON DELETE CASCADE

---

## Status Values

The `status` field tracks workflow progression:

| Status | Description |
|--------|-------------|
| `pending` | Initial state, awaiting review (default) |
| `accepted` | Request approved and accepted |
| `rejected` | Request denied or rejected |
| `ongoing` | Currently being processed |
| `completed` | Processing completed successfully |

---

## Business Logic

### Workflow Tracking
- Records administrative decisions and status changes
- Provides audit trail for resource requests and assignments
- Links to specific ICS operations via `ics211_record_id`

### Documentation Purpose
- **Remarks**: Administrative notes, reasons for status changes
- **Description**: Detailed explanation of actions taken
- **Order Request Number**: Links to official documentation

### Administrative Workflow
1. **pending**: Initial request submitted
2. **accepted/rejected**: Administrative review complete
3. **ongoing**: Request being processed
4. **completed**: Request fulfilled

---

## Use Cases

### Resource Request Management
- Track approval/rejection of resource requests
- Document reasons for administrative decisions
- Maintain audit trail for accountability

### Status Change History
- Record when and why check-in details change status
- Provide context for operational decisions
- Support after-action reviews and analysis

### Compliance Documentation
- Maintain official records for regulatory compliance
- Document decision-making process
- Support legal and administrative requirements

---

## Migration Commands

### Apply Migration
```bash
php artisan migrate --path=database/migrations/2025_12_03_072949_create_check_in_detail_histories_table.php
```

### Rollback Migration
```bash
php artisan migrate:rollback --path=database/migrations/2025_12_03_072949_create_check_in_detail_histories_table.php
```

---

## Related Tables
- `ics211_records` - Parent incident record
- `check_in_details` - Related check-in entries (indirectly)

## Related Models
- `App\Models\CheckInDetailHistory`
- `App\Models\Ics211Record` (parent relationship)

## Dependencies
- Requires `ics211_records` table to exist first

## Key Features
- **Audit Trail**: Complete history of administrative actions
- **Decision Documentation**: Reasons for approvals/rejections
- **Workflow Management**: Status progression tracking
- **Compliance Support**: Official record maintenance

## Notes
- CASCADE DELETE: Removing ICS record deletes all history entries
- Order request number provides linkage to official documents
- Text fields allow detailed documentation of decisions
- Status values support standard approval workflows
- UUID enables secure external references
- Timestamps provide temporal audit capabilities