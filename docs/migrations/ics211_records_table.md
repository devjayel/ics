# ICS 211 Records Table Migration Documentation

## Overview
This migration creates the `ics211_records` table for managing ICS 211 (Incident Check-In List) records, which are core incident management documents.

## Migration File
- **File**: `2025_11_30_020220_create_ics211_records_table.php`
- **Created**: November 30, 2025

## Table: ics211_records

### Purpose
Stores ICS 211 form data, which is used for tracking personnel and resources checking into an incident. This is a central table in the incident management system.

### Schema

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint(20) | PRIMARY KEY, AUTO_INCREMENT | Primary key |
| uuid | varchar(191) | UNIQUE, NOT NULL | Unique identifier for public access |
| token | varchar(191) | UNIQUE, NOT NULL | Invitation/access token for joining |
| name | varchar(191) | NOT NULL | Name/title of the incident or ICS record |
| type | varchar(191) | NOT NULL | Type of incident (fire, flood, rescue, etc.) |
| order_request_number | varchar(191) | NOT NULL | Official order or request number |
| checkin_location | varchar(191) | NOT NULL | Physical location for check-in |
| region | varchar(191) | NULL | Geographic region or area |
| remarks | text | NULL | Additional notes or comments |
| remarks_image_attachment | varchar(191) | NULL | File path for attached images |
| status | varchar(191) | NOT NULL, DEFAULT 'pending' | Current status of the ICS record |
| created_at | timestamp | NULL | Record creation timestamp |
| updated_at | timestamp | NULL | Record update timestamp |

## Indexes
- `ics211_records.uuid` - Unique index for public identification
- `ics211_records.token` - Unique index for access tokens

## Status Values
The `status` field can have the following values:
- `pending` - ICS record created but not yet active (default)
- `ongoing` - Incident is active and ongoing
- `completed` - Incident has been completed or resolved

## Key Features

### Access Token System
- Each ICS record has a unique `token` field
- Used for invitation-based access control
- Allows RULs to join as operators using the token
- Should be a random, secure string

### File Attachments
- `remarks_image_attachment` stores file paths for images
- Supports documentation with visual evidence
- Files should be stored securely with proper validation

## Relationships
- **Has Many**: `check_in_details` (ICS records contain multiple check-in entries)
- **Has Many**: `ics_logs` (Activity logging for the ICS record)
- **Belongs To Many**: `resident_unit_leaders` (through `ics_operators` pivot table)

## Usage Examples

### Creating an ICS 211 Record
```php
Ics211Record::create([
    'uuid' => Str::uuid(),
    'token' => Str::random(8),
    'name' => 'Wildfire Response - Pine Valley',
    'type' => 'wildfire',
    'order_request_number' => 'ORN-2025-001',
    'checkin_location' => 'Cedar Creek Fire Station',
    'region' => 'Northern District',
    'remarks' => 'High priority incident requiring immediate response',
    'status' => 'pending'
]);
```

### Finding ICS Records by Status
```php
$ongoingIncidents = Ics211Record::where('status', 'ongoing')
                                ->with(['checkInDetails', 'operators'])
                                ->get();
```

### Joining as Operator via Token
```php
$ics211Record = Ics211Record::where('token', $inviteToken)->first();
if ($ics211Record) {
    $ics211Record->operators()->attach($currentUserId);
}
```

## Security Considerations
- Tokens should be cryptographically secure and difficult to guess
- Access to ICS records should be properly authenticated
- File uploads should be validated for type and size
- Consider implementing token expiration for enhanced security

## Operational Workflow
1. **Creation**: RUL creates ICS 211 record with status 'pending'
2. **Invitation**: Other RULs can join using the access token
3. **Activation**: Status changed to 'ongoing' when incident becomes active
4. **Management**: Personnel and resources are added via check-in details
5. **Completion**: Status changed to 'completed' when incident is resolved

## Integration Points
- Connected to `check_in_details` for personnel/resource tracking
- Linked to `ics_logs` for activity auditing
- Associated with `ics_operators` for multi-RUL collaboration

## Rollback
Running the down migration will drop the `ics211_records` table and all associated data.

## Dependencies
This migration is independent but should be created before:
- `check_in_details` table
- `ics_logs` table
- `ics_operators` table