# Check-In Detail Histories Table Migration Documentation

## Overview
This migration creates the `check_in_detail_histories` table for tracking historical changes and status updates to check-in details.

## Migration File
- **File**: `2025_12_03_072949_create_check_in_detail_histories_table.php`
- **Created**: December 3, 2025

## Table: check_in_detail_histories

### Purpose
Maintains a historical record of changes and status updates to check-in details, providing an audit trail for incident management operations.

### Schema

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint(20) | PRIMARY KEY, AUTO_INCREMENT | Primary key |
| uuid | varchar(191) | UNIQUE, NOT NULL | Unique identifier for history entry |
| ics211_record_id | bigint(20) | FOREIGN KEY, NOT NULL | Reference to ics211_records.id |
| order_request_number | varchar(191) | NOT NULL | Order or request number for reference |
| remarks | text | NULL | Additional notes about the change |
| description | text | NULL | Description of what changed |
| status | varchar(191) | NOT NULL, DEFAULT 'pending' | Status at this point in history |
| created_at | timestamp | NULL | When this history entry was created |
| updated_at | timestamp | NULL | When this history entry was modified |

## Indexes
- `check_in_detail_histories.uuid` - Unique index for public identification
- `check_in_detail_histories.ics211_record_id` - Foreign key index for ICS record relationships

## Foreign Keys
- `ics211_record_id` → `ics211_records.id`
  - **Constraint**: ON DELETE CASCADE
  - **Behavior**: When an ICS record is deleted, all history entries are automatically deleted

## Status Values
The `status` field can have the following values:
- `pending` - Status when history entry was created (default)
- `accepted` - Change was accepted
- `rejected` - Change was rejected
- `ongoing` - Change is currently being processed
- `completed` - Change process completed

## Use Cases

### Audit Trail
- Track all changes to check-in details over time
- Provide accountability for status changes
- Enable investigation of operational decisions

### Status History
- Maintain complete history of status transitions
- Track who made changes and when
- Provide timeline for incident analysis

### Change Tracking
- Record what changes were made to check-in details
- Maintain remarks and descriptions for context
- Enable rollback capabilities if needed

## Relationships
- **Belongs To**: `ics211_records` (Each history entry relates to one ICS record)

## Usage Examples

### Creating History Entry
```php
CheckInDetailHistory::create([
    'uuid' => Str::uuid(),
    'ics211_record_id' => $icsRecordId,
    'order_request_number' => 'ORN-2025-001-A',
    'remarks' => 'Personnel status updated due to equipment availability',
    'description' => 'Status changed from pending to accepted',
    'status' => 'accepted'
]);
```

### Retrieving History for ICS Record
```php
$history = CheckInDetailHistory::where('ics211_record_id', $icsId)
                              ->orderBy('created_at', 'desc')
                              ->get();
```

### Finding History by Order Number
```php
$orderHistory = CheckInDetailHistory::where('order_request_number', $orderNumber)
                                   ->orderBy('created_at')
                                   ->get();
```

## Integration Points

### Automatic History Creation
- Should be automatically created when check-in details are modified
- Can be triggered by model events or explicit service calls
- Maintains chronological order of changes

### API Integration
- History entries can be created via API endpoints
- Allows external systems to record changes
- Enables real-time audit trail maintenance

## Data Retention
- Consider implementing data retention policies
- Archive old history entries for long-term storage
- Balance between audit requirements and storage costs

## Security Considerations
- History entries should be immutable once created
- Access should be controlled based on user roles
- Sensitive information in remarks should be handled appropriately

## Reporting and Analytics
- Enables trend analysis of status changes
- Supports operational efficiency reporting
- Provides data for incident post-analysis

## Rollback
Running the down migration will drop the `check_in_detail_histories` table and all associated data.

## Dependencies
This migration depends on:
- `ics211_records` table (must exist for foreign key constraint)