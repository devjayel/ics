# Check-In Details Table Migration Documentation

## Overview
This migration creates the `check_in_details` table for storing detailed check-in information for personnel and resources in ICS 211 records.

## Migration File
- **File**: `2025_12_01_182304_create_check_in_details_table.php`
- **Created**: December 1, 2025

## Table: check_in_details

### Purpose
Stores detailed check-in information for each personnel or resource entry within an ICS 211 record. This table contains the bulk of operational data for incident management.

### Schema

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint(20) | PRIMARY KEY, AUTO_INCREMENT | Primary key |
| uuid | varchar(191) | UNIQUE, NOT NULL | Unique identifier for check-in entry |
| ics211_record_id | bigint(20) | FOREIGN KEY, NOT NULL | Reference to ics211_records.id |
| personnel_id | bigint(20) | FOREIGN KEY, NULL | Reference to personnels.id (nullable) |
| order_request_number | varchar(191) | NOT NULL | Order or request number for this entry |
| checkin_date | date | NOT NULL | Date of check-in |
| checkin_time | time | NOT NULL | Time of check-in |
| kind | varchar(191) | NOT NULL | Kind of resource (Personnel, Equipment, etc.) |
| category | varchar(191) | NOT NULL | Category (Personnel, Equipment, etc.) |
| type | varchar(191) | NOT NULL | Specific type within category |
| resource_identifier | varchar(191) | NOT NULL | Identifier for the resource |
| name_of_leader | varchar(191) | NOT NULL | Name of group/unit leader |
| contact_information | varchar(191) | NOT NULL | Contact details for the leader |
| quantity | int(11) | NOT NULL | Number of personnel or items |
| department | varchar(191) | NOT NULL | Originating department/agency |
| departure_point_of_origin | varchar(191) | NOT NULL | Where resource came from |
| departure_date | date | NOT NULL | Date of departure from origin |
| departure_time | time | NOT NULL | Time of departure from origin |
| departure_method_of_travel | varchar(191) | NOT NULL | How they traveled (vehicle, walking, etc.) |
| with_manifest | boolean | NOT NULL, DEFAULT false | Whether resource comes with manifest |
| incident_assignment | varchar(191) | NULL | Specific incident assignment |
| other_qualifications | varchar(191) | NULL | Additional qualifications or notes |
| sent_resl | boolean | NOT NULL, DEFAULT false | Whether sent to RESL (Resource Unit) |
| status | varchar(191) | NOT NULL, DEFAULT 'pending' | Current status of this check-in entry |
| created_at | timestamp | NULL | Record creation timestamp |
| updated_at | timestamp | NULL | Record update timestamp |

## Indexes
- `check_in_details.uuid` - Unique index for public identification
- `check_in_details.ics211_record_id` - Foreign key index for ICS record relationships
- `check_in_details.personnel_id` - Foreign key index for personnel relationships

## Foreign Keys
- `ics211_record_id` → `ics211_records.id`
  - **Constraint**: ON DELETE CASCADE
  - **Behavior**: When an ICS record is deleted, all check-in details are automatically deleted
- `personnel_id` → `personnels.id`
  - **Constraint**: ON DELETE CASCADE, NULLABLE
  - **Behavior**: Links to specific personnel when applicable

## Status Values
The `status` field can have the following values:
- `pending` - Check-in recorded but not yet processed (default)
- `accepted` - Check-in accepted and personnel/resource ready
- `rejected` - Check-in rejected for various reasons
- `ongoing` - Currently active in incident operations
- `completed` - Assignment completed

## Key Features

### Nullable Personnel ID
- `personnel_id` can be null to accommodate external resources
- When linked to personnel, enables tracking and status updates
- Allows for both registered personnel and external resources

### Comprehensive Resource Tracking
- Tracks both personnel and equipment/resources
- Includes departure and arrival information
- Records contact information and leadership details
- Supports manifest tracking and RESL integration

### Time-based Operations
- Separate fields for check-in date/time and departure date/time
- Enables timeline analysis and operational planning
- Supports historical tracking of resource movements

## Relationships
- **Belongs To**: `ics211_records` (Each check-in detail belongs to one ICS record)
- **Belongs To**: `personnels` (Optional - when personnel_id is set)

## Usage Examples

### Creating Check-In Detail for Personnel
```php
CheckInDetails::create([
    'uuid' => Str::uuid(),
    'ics211_record_id' => $icsRecordId,
    'personnel_id' => $personnelId,
    'order_request_number' => 'ORN-2025-001-A',
    'checkin_date' => '2025-03-13',
    'checkin_time' => '08:30:00',
    'kind' => 'Personnel',
    'category' => 'Personnel',
    'type' => 'Fire Suppression',
    'resource_identifier' => 'ENG-01',
    'name_of_leader' => 'Captain Smith',
    'contact_information' => '+1234567890',
    'quantity' => 4,
    'department' => 'Metro Fire Department',
    'departure_point_of_origin' => 'Station 15',
    'departure_date' => '2025-03-13',
    'departure_time' => '08:00:00',
    'departure_method_of_travel' => 'Fire Engine',
    'with_manifest' => true,
    'status' => 'pending'
]);
```

### Finding Check-In Details for an ICS Record
```php
$checkInDetails = CheckInDetails::where('ics211_record_id', $icsId)
                               ->with(['personnel'])
                               ->orderBy('checkin_date')
                               ->orderBy('checkin_time')
                               ->get();
```

### Updating Status
```php
CheckInDetails::where('uuid', $checkInUuid)
              ->update(['status' => 'accepted']);
```

## Data Validation Considerations
- Date/time fields should be validated for logical consistency
- Departure time should be before or equal to check-in time
- Contact information should be validated for format
- Quantity should be positive integer
- Status transitions should be controlled

## Integration with Personnel Management
When `personnel_id` is set:
- Personnel status is automatically updated
- Push notifications can be sent
- Personnel tracking across incidents becomes possible
- Automated reporting and analytics are enhanced

## RESL Integration
- `sent_resl` flag indicates resource has been sent to Resource Unit
- Enables proper resource tracking and accountability
- Important for ICS compliance and documentation

## Rollback
Running the down migration will drop the `check_in_details` table and all associated data.

## Dependencies
This migration depends on:
- `ics211_records` table (must exist for foreign key constraint)
- `personnels` table (must exist for foreign key constraint)