# ICS Logs Table Migration Documentation

## Overview
This migration creates the `ics_logs` table for comprehensive activity logging and audit tracking for ICS 211 records.

## Migration File
- **File**: `2026_02_06_120000_create_ics_logs_table.php`
- **Created**: February 6, 2026

## Table: ics_logs

### Purpose
Maintains a comprehensive audit trail of all activities, changes, and operations performed on ICS 211 records, providing accountability and traceability for incident management operations.

### Schema

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint(20) | PRIMARY KEY, AUTO_INCREMENT | Primary key |
| uuid | varchar(191) | UNIQUE, NOT NULL | Unique identifier for log entry |
| ics211_record_id | bigint(20) | FOREIGN KEY, NOT NULL | Reference to ics211_records.id |
| rul_id | bigint(20) | FOREIGN KEY, NOT NULL | Reference to resident_unit_leaders.id (who performed action) |
| action | varchar(191) | NOT NULL | Type of action performed |
| description | text | NOT NULL | Detailed description of the action |
| old_values | json | NULL | Previous values before change (JSON format) |
| new_values | json | NULL | New values after change (JSON format) |
| created_at | timestamp | NULL | When the action was performed |
| updated_at | timestamp | NULL | When this log entry was modified |

## Indexes
- `ics_logs.uuid` - Unique index for public identification
- `ics_logs.ics211_record_id` - Index for ICS record lookups (with index comment)
- `ics_logs.rul_id` - Index for RUL activity lookups (with index comment)
- `ics_logs.created_at` - Index for chronological queries (with index comment)

## Foreign Keys
- `ics211_record_id` → `ics211_records.id`
  - **Constraint**: ON DELETE CASCADE
  - **Behavior**: When an ICS record is deleted, all related logs are automatically deleted
- `rul_id` → `resident_unit_leaders.id`
  - **Constraint**: ON DELETE CASCADE
  - **Behavior**: When a RUL is deleted, all their log entries are automatically deleted

## Action Types
The `action` field can contain various action types including:
- `created` - ICS record creation
- `updated` - ICS record updates
- `personnel_added` - Personnel assigned to ICS
- `personnel_removed` - Personnel removed from ICS
- `status_changed` - Status transitions
- `operator_added` - New operator joined
- `operator_removed` - Operator left/removed
- `checkin_updated` - Check-in details modified
- `remarks_updated` - Remarks or notes updated

## JSON Data Storage

### Old Values
Stores the previous state of data before changes:
```json
{
  "status": "pending",
  "remarks": "Initial incident report",
  "personnel_count": 3
}
```

### New Values
Stores the new state of data after changes:
```json
{
  "status": "ongoing",
  "remarks": "Incident escalated, additional resources requested",
  "personnel_count": 8
}
```

## Relationships
- **Belongs To**: `ics211_records` (Each log entry relates to one ICS record)
- **Belongs To**: `resident_unit_leaders` (Each log entry has one RUL who performed the action)

## Usage Examples

### Creating Log Entry (Manual)
```php
IcsLog::create([
    'uuid' => Str::uuid(),
    'ics211_record_id' => $icsRecordId,
    'rul_id' => $rulId,
    'action' => 'status_changed',
    'description' => 'ICS 211 record status changed from pending to ongoing',
    'old_values' => ['status' => 'pending'],
    'new_values' => ['status' => 'ongoing']
]);
```

### Creating Log Entry via Helper Method
```php
private function logAction(Ics211Record $ics211Record, string $action, string $description, ?array $oldValues = null, ?array $newValues = null, ?int $rulId = null)
{
    IcsLog::create([
        'uuid' => Str::uuid(),
        'ics211_record_id' => $ics211Record->id,
        'rul_id' => $rulId ?? auth()->id(),
        'action' => $action,
        'description' => $description,
        'old_values' => $oldValues,
        'new_values' => $newValues,
    ]);
}
```

### Querying Logs by ICS Record
```php
$logs = IcsLog::where('ics211_record_id', $icsRecordId)
              ->with(['rul'])
              ->orderBy('created_at', 'desc')
              ->get();
```

### Querying Logs by Action Type
```php
$personnelLogs = IcsLog::where('ics211_record_id', $icsRecordId)
                      ->whereIn('action', ['personnel_added', 'personnel_removed'])
                      ->orderBy('created_at')
                      ->get();
```

### Finding User Activity
```php
$userActivity = IcsLog::where('rul_id', $rulId)
                     ->where('created_at', '>=', $dateFrom)
                     ->where('created_at', '<=', $dateTo)
                     ->get();
```

## Automatic Logging Integration

### Model Events
- Integrate with Eloquent model events for automatic logging
- Log changes on create, update, and delete operations
- Capture before/after states automatically

### Service Layer Integration
- Implement logging in service methods
- Ensure consistent logging across all operations
- Provide context-rich descriptions

## Query Optimization

### Indexed Queries
- Use indexed columns for efficient searching
- Combine indexes for complex queries
- Consider query patterns for index design

### Date Range Queries
```php
// Efficient date range queries using created_at index
$recentLogs = IcsLog::where('ics211_record_id', $icsId)
                   ->where('created_at', '>=', now()->subDays(7))
                   ->orderBy('created_at', 'desc')
                   ->get();
```

## Audit and Compliance

### Compliance Requirements
- Provides audit trail for regulatory compliance
- Maintains data integrity documentation
- Supports incident investigation requirements

### Data Retention
- Consider implementing log archiving policies
- Balance between audit requirements and storage costs
- Implement automatic cleanup for old logs

## Analytics and Reporting

### Activity Patterns
- Track user activity patterns
- Identify high-activity incidents
- Monitor operational efficiency

### Change Analysis
- Analyze frequency of different action types
- Track status change patterns
- Identify bottlenecks in workflows

## Security Considerations
- Log entries should be immutable once created
- Protect sensitive data in old_values/new_values fields
- Implement access controls for log viewing
- Consider encryption for sensitive logged data

## Performance Considerations
- Large log tables may impact query performance
- Consider partitioning by date for large datasets
- Implement appropriate indexing strategies
- Monitor disk space usage for JSON columns

## Rollback
Running the down migration will drop the `ics_logs` table and all audit trail data.

## Dependencies
This migration depends on:
- `ics211_records` table (must exist for foreign key constraint)
- `resident_unit_leaders` table (must exist for foreign key constraint)