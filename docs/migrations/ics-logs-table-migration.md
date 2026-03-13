# ICS Logs Table Migration Documentation

## Migration File: `2026_02_06_120000_create_ics_logs_table.php`

### Overview
This migration creates the `ics_logs` table that provides comprehensive audit trail and activity logging for all ICS 211 operations, tracking changes and actions performed by Resource Unit Leaders.

---

## ICS Logs Table - `ics_logs`

### Purpose
Maintains detailed audit logs of all activities performed on ICS 211 records. This supports accountability, compliance, forensic analysis, and operational monitoring for emergency management operations.

### Table Structure

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | bigint | PRIMARY KEY, AUTO_INCREMENT | Unique identifier |
| `uuid` | varchar(36) | UNIQUE, NOT NULL | UUID for external references |
| `ics211_record_id` | bigint | FOREIGN KEY, NOT NULL | Related ICS 211 record |
| `rul_id` | bigint | FOREIGN KEY, NOT NULL | RUL who performed the action |
| `action` | varchar(255) | NOT NULL | Type of action performed |
| `description` | text | NOT NULL | Detailed description of action |
| `old_values` | json | NULLABLE | Previous values before change |
| `new_values` | json | NULLABLE | New values after change |
| `created_at` | timestamp | NOT NULL | When action was performed |
| `updated_at` | timestamp | NOT NULL | Record last update time |

### Indexes
- Primary key on `id`
- Unique index on `uuid`
- Index on `ics211_record_id`
- Index on `rul_id`
- Index on `created_at`

### Foreign Keys
- `ics211_record_id` references `ics211_records(id)` ON DELETE CASCADE
- `rul_id` references `resident_unit_leaders(id)` ON DELETE CASCADE

---

## Action Types

The `action` field categorizes different types of logged activities:

| Action | Description |
|--------|-------------|
| `created` | ICS 211 record creation |
| `updated` | ICS record information modified |
| `personnel_added` | Personnel assigned to ICS |
| `personnel_removed` | Personnel removed from ICS |
| `status_changed` | ICS operational status changed |
| `operator_added` | New RUL joined as operator |
| `checkin_updated` | Check-in detail modified |
| `deleted` | ICS record deleted |

---

## JSON Data Structure

### old_values and new_values Examples

#### Status Change Log
```json
{
  "old_values": {
    "status": "pending"
  },
  "new_values": {
    "status": "ongoing"
  }
}
```

#### Personnel Addition Log
```json
{
  "old_values": null,
  "new_values": {
    "personnel_id": 123,
    "personnel_name": "John Smith",
    "serial_number": "FF001"
  }
}
```

#### ICS Update Log
```json
{
  "old_values": {
    "name": "Structure Fire Response",
    "type": "Structure Fire",
    "remarks": "Initial assessment"
  },
  "new_values": {
    "name": "Structure Fire Response - Multi-Alarm",
    "type": "Structure Fire",
    "remarks": "Escalated to second alarm"
  }
}
```

---

## Business Logic

### Comprehensive Auditing
- Every significant action is logged automatically
- Immutable record of who did what and when
- JSON fields capture before/after state changes

### Compliance Support
- Meets regulatory requirements for emergency management
- Provides forensic audit capabilities
- Supports post-incident analysis and reporting

### Operational Monitoring
- Real-time activity tracking
- Performance metrics and analytics
- Resource utilization analysis

---

## Logging Patterns

### Automatic Logging
Actions automatically logged by the system:
- ICS record CRUD operations
- Personnel status changes
- Operator management
- Status transitions

### Manual Logging
Administrative actions that may be logged:
- Special remarks or observations
- Manual status overrides
- Administrative decisions

---

## Query Examples

### Recent Activity for ICS
```sql
SELECT * FROM ics_logs
WHERE ics211_record_id = ?
ORDER BY created_at DESC
LIMIT 50;
```

### RUL Activity Summary
```sql
SELECT action, COUNT(*) as count
FROM ics_logs
WHERE rul_id = ?
GROUP BY action;
```

### Personnel Assignment History
```sql
SELECT * FROM ics_logs
WHERE action IN ('personnel_added', 'personnel_removed')
AND JSON_EXTRACT(new_values, '$.personnel_id') = ?;
```

---

## Migration Commands

### Apply Migration
```bash
php artisan migrate --path=database/migrations/2026_02_06_120000_create_ics_logs_table.php
```

### Rollback Migration
```bash
php artisan migrate:rollback --path=database/migrations/2026_02_06_120000_create_ics_logs_table.php
```

---

## Related Tables
- `ics211_records` - Parent ICS record being logged
- `resident_unit_leaders` - RUL who performed the action

## Related Models
- `App\Models\IcsLog`
- `App\Models\Ics211Record` (parent relationship)
- `App\Models\Rul` (actor relationship)

## Dependencies
- Requires `ics211_records` table to exist
- Requires `resident_unit_leaders` table to exist

## Key Features
- **Immutable Audit Trail**: Complete record of all actions
- **JSON Change Tracking**: Before/after values for modifications
- **Performance Optimized**: Indexed for efficient querying
- **Compliance Ready**: Meets regulatory audit requirements
- **Analytics Enabled**: Supports reporting and analysis

## Security Considerations
- Logs are append-only (no updates or deletes in normal operation)
- UUID prevents enumeration attacks
- Sensitive data should be excluded from JSON fields
- Access should be restricted to authorized personnel

## Notes
- CASCADE DELETE: Removing ICS record deletes all related logs
- JSON fields enable flexible schema for different action types
- Indexes support efficient filtering and sorting
- Created_at index enables time-based analysis
- Description field provides human-readable action summary
- System automatically populates logs via application logic