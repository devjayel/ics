# Resident Unit Leaders Table Migration Documentation

## Overview
This migration creates the `resident_unit_leaders` table for storing Resource Unit Leader (RUL) profiles and authentication information.

## Migration File
- **File**: `2025_11_30_005736_create_resource_unit_leaders_table.php`
- **Created**: November 30, 2025
- **Note**: File name mentions "resource_unit_leaders" but creates "resident_unit_leaders" table

## Table: resident_unit_leaders

### Purpose
Stores comprehensive information about Resource Unit Leaders (RULs) who manage personnel and coordinate incident response activities.

### Schema

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint(20) | PRIMARY KEY, AUTO_INCREMENT | Primary key |
| uuid | varchar(191) | UNIQUE, NOT NULL | Unique identifier for public access |
| avatar | varchar(191) | NULL | Profile picture/avatar file path |
| logo | varchar(191) | NULL | Organization/unit logo file path |
| name | varchar(191) | NOT NULL | Full name of the RUL |
| contact_number | varchar(191) | NOT NULL | Phone/contact number |
| serial_number | varchar(191) | UNIQUE, NOT NULL | Unique RUL identification number |
| department | varchar(191) | NOT NULL | Department/organization name |
| signature | varchar(191) | NULL | Digital signature file path |
| fcm_token | varchar(191) | NULL | Firebase Cloud Messaging token |
| token | varchar(191) | UNIQUE, NULL | Authentication/access token |
| created_at | timestamp | NULL | Record creation timestamp |
| updated_at | timestamp | NULL | Record update timestamp |

## Indexes
- `resident_unit_leaders.uuid` - Unique index for public identification
- `resident_unit_leaders.serial_number` - Unique index for RUL identification
- `resident_unit_leaders.token` - Unique index for authentication tokens

## Key Features

### Profile Management
- Supports avatar and logo uploads for visual identification
- Stores both personal and organizational information
- Includes digital signature support for document signing

### Authentication System
- Uses unique serial numbers for identification
- Supports token-based authentication
- Integrates with mobile apps via FCM tokens

### Contact Information
- Stores essential contact details
- Links to department/organization information
- Enables communication during incidents

## Relationships
- **Has Many**: `personnels` (RULs manage multiple personnel)
- **Has Many**: `certificates` (RULs can have multiple certificates)
- **Has Many**: `ics_logs` (Activity logging for RUL actions)
- **Belongs To Many**: `ics211_records` (through `ics_operators` pivot table)

## Usage Examples

### Creating a RUL Profile
```php
ResidentUnitLeader::create([
    'uuid' => Str::uuid(),
    'name' => 'Captain Jane Smith',
    'contact_number' => '+1234567890',
    'serial_number' => 'RUL-2025-001',
    'department' => 'Metro Fire Department',
    'avatar' => 'avatars/captain_smith.jpg',
    'logo' => 'logos/metro_fire.png'
]);
```

### Finding RUL by Serial Number
```php
$rul = ResidentUnitLeader::where('serial_number', $serialNumber)->first();
```

### Authentication Token Management
```php
$rul = ResidentUnitLeader::where('token', $authToken)->first();
if ($rul) {
    // RUL is authenticated
}
```

### Loading Relationships
```php
$rul = ResidentUnitLeader::with(['personnels', 'certificates', 'operatorIcsRecords'])
                        ->where('uuid', $uuid)
                        ->first();
```

## File Management

### Avatar and Logo Storage
- Store files securely with proper validation
- Implement image resizing and optimization
- Consider CDN integration for better performance
- Validate file types and sizes

### Digital Signatures
- Store signature files securely
- Implement signature verification
- Consider legal requirements for digital signatures
- Ensure proper access controls

## Push Notifications
- `fcm_token` enables real-time notifications
- Update tokens when RUL logs into mobile apps
- Send notifications for:
  - New ICS assignments
  - Personnel status updates
  - Important incident updates

## Authentication and Security

### Token Management
- Generate cryptographically secure tokens
- Implement token expiration policies
- Support token refresh mechanisms
- Log authentication activities

### Access Control
- Serial numbers serve as unique identifiers
- Implement role-based access controls
- Support multi-factor authentication
- Monitor login activities

## Data Validation

### Required Fields
- Name, contact_number, serial_number, department are required
- Serial number must be unique across all RULs
- Contact number should be validated for format

### Optional Fields
- Avatar, logo, signature, fcm_token, token can be null
- UUID is auto-generated and required
- Timestamps are automatically managed

## Mobile App Integration

### FCM Token Management
```php
// Update FCM token on mobile login
$rul->update(['fcm_token' => $newFcmToken]);

// Send notification
$firebaseService->sendNotification($rul->fcm_token, $message);
```

### API Authentication
```php
// Authenticate via token
$rul = ResidentUnitLeader::where('token', $request->bearerToken())->first();
```

## Reporting and Analytics
- Track RUL activity patterns
- Monitor incident response participation
- Generate performance reports
- Analyze organizational effectiveness

## Security Considerations
- Protect PII (personally identifiable information)
- Secure file uploads with proper validation
- Implement proper access controls for sensitive data
- Regular security audits for token management

## Rollback
Running the down migration will drop the `resident_unit_leaders` table and all associated data.

## Dependencies
This migration is foundational and doesn't depend on other custom tables, but other tables depend on it:
- `personnels` table
- `certificates` table
- `ics_logs` table
- `ics_operators` table