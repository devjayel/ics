# Check-In Details Table Migration Documentation

## Migration File: `2025_12_01_182304_create_check_in_details_table.php`

### Overview
This migration creates the `check_in_details` table that stores detailed check-in information for personnel and resources assigned to ICS 211 records. This represents the detailed line items on the ICS Form 211.

---

## Check-In Details Table - `check_in_details`

### Purpose
Stores individual check-in entries for personnel and resources on ICS Form 211. Each record represents a single resource (person or equipment) that has checked in for an incident, with comprehensive tracking information for accountability and resource management.

### Table Structure

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | bigint | PRIMARY KEY, AUTO_INCREMENT | Unique identifier |
| `uuid` | varchar(36) | UNIQUE, NOT NULL | UUID for external references |
| `ics211_record_id` | bigint | FOREIGN KEY, NOT NULL | Parent ICS 211 record |
| `personnel_id` | bigint | FOREIGN KEY, NULLABLE | Associated personnel (if applicable) |
| `order_request_number` | varchar(255) | NOT NULL | Official order/request number |
| `checkin_date` | date | NOT NULL | Date of check-in |
| `checkin_time` | time | NOT NULL | Time of check-in |
| `kind` | varchar(255) | NOT NULL | Resource kind (Personnel, Equipment, etc.) |
| `category` | varchar(255) | NOT NULL | Resource category |
| `type` | varchar(255) | NOT NULL | Specific resource type |
| `resource_identifier` | varchar(255) | NOT NULL | Unique resource identifier |
| `name_of_leader` | varchar(255) | NOT NULL | Leader/supervisor name |
| `contact_information` | varchar(255) | NOT NULL | Contact details for leader |
| `quantity` | int | NOT NULL | Number of personnel/items |
| `department` | varchar(255) | NOT NULL | Originating department/agency |
| `departure_point_of_origin` | varchar(255) | NOT NULL | Where resource came from |
| `departure_date` | date | NOT NULL | Date resource left origin |
| `departure_time` | time | NOT NULL | Time resource left origin |
| `departure_method_of_travel` | varchar(255) | NOT NULL | Transportation mode |
| `with_manifest` | boolean | DEFAULT false | Has accompanying manifest |
| `incident_assignment` | varchar(255) | NULLABLE | Specific assignment details |
| `other_qualifications` | varchar(255) | NULLABLE | Additional qualifications |
| `sent_resl` | boolean | DEFAULT false | Resource Status Report sent |
| `status` | varchar(255) | DEFAULT 'pending' | Current operational status |
| `created_at` | timestamp | NOT NULL | Record creation time |
| `updated_at` | timestamp | NOT NULL | Record last update time |

### Indexes
- Primary key on `id`
- Unique index on `uuid`
- Foreign key on `ics211_record_id`
- Foreign key on `personnel_id`

### Foreign Keys
- `ics211_record_id` references `ics211_records(id)` ON DELETE CASCADE
- `personnel_id` references `personnels(id)` ON DELETE CASCADE

---

## Status Values

The `status` field tracks operational state:

| Status | Description |
|--------|-------------|
| `pending` | Newly checked in, awaiting assignment |
| `available` | Ready for assignment |
| `staging` | In staging area preparing for deployment |
| `assigned` | Assigned to specific task/location |
| `active` | Actively engaged in operations |
| `demobalized` | Released from assignment |
| `out_of_service` | Temporarily unavailable |
| `standby` | On standby for potential assignment |

---

## ICS Form 211 Context

### Standard ICS Categories

| Category | Examples |
|----------|----------|
| Personnel | Firefighters, EMTs, Law Enforcement |
| Equipment | Engines, Ambulances, Command Vehicles |
| Aircraft | Helicopters, Air Tankers, Drones |
| Facilities | Command Posts, Staging Areas, Helibases |

### Resource Types Examples

| Kind | Category | Type |
|------|----------|------|
| Personnel | Firefighting | Engine Company |
| Personnel | Medical | EMT Team |
| Equipment | Fire Suppression | Type 1 Engine |
| Equipment | Medical | Ambulance |
| Aircraft | Fixed Wing | Air Tanker |

---

## Personnel Integration

### Linked Personnel
- `personnel_id` links to registered personnel in the system
- When personnel are assigned, their status is automatically updated
- Enables real-time tracking of personnel availability

### Non-Personnel Resources
- `personnel_id` can be null for equipment, facilities, etc.
- Resource information tracked independently
- Leader contact information provides human accountability

---

## Accountability Features

### Check-In Process
- **Date/Time Tracking**: Precise check-in timestamps
- **Origin Documentation**: Tracks where resources came from
- **Travel Information**: Transportation mode and timing
- **Contact Points**: Leader information for communication

### Manifest Integration
- `with_manifest` indicates detailed resource listing
- `sent_resl` tracks Resource Status Report submission
- Supports ICS documentation requirements

---

## Migration Commands

### Apply Migration
```bash
php artisan migrate --path=database/migrations/2025_12_01_182304_create_check_in_details_table.php
```

### Rollback Migration
```bash
php artisan migrate:rollback --path=database/migrations/2025_12_01_182304_create_check_in_details_table.php
```

---

## Related Tables
- `ics211_records` - Parent incident record
- `personnels` - Associated personnel (optional)
- `check_in_detail_histories` - Status change history

## Related Models
- `App\Models\CheckInDetails`
- `App\Models\Ics211Record` (parent relationship)
- `App\Models\Personnel` (optional relationship)

## Dependencies
- Requires `ics211_records` table to exist first
- Requires `personnels` table for personnel linking

## Business Rules
1. Each check-in detail belongs to exactly one ICS 211 record
2. Personnel assignments are optional (equipment doesn't need personnel link)
3. Status changes should trigger notifications via FCM
4. Quantity must be positive integer
5. Contact information required for accountability

## Notes
- CASCADE DELETE: Removing ICS record deletes all check-in details
- Personnel status synchronization maintains operational awareness
- UUID enables secure external API access
- Comprehensive tracking supports ICS accountability requirements
- Boolean fields support ICS documentation compliance