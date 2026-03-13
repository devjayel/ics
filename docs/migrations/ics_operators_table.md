# ICS Operators Table Migration Documentation

## Overview
This migration creates the `ics_operators` table, which serves as a pivot table connecting Resource Unit Leaders (RULs) to ICS 211 records.

## Migration File
- **File**: `2026_02_06_095429_create_ics_operators_table.php`
- **Created**: February 6, 2026

## Table: ics_operators

### Purpose
Implements a many-to-many relationship between `resident_unit_leaders` and `ics211_records`, allowing multiple RULs to collaborate on managing the same ICS record.

### Schema

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint(20) | PRIMARY KEY, AUTO_INCREMENT | Primary key |
| rul_id | bigint(20) | FOREIGN KEY, NOT NULL | Reference to resident_unit_leaders.id |
| ics_id | bigint(20) | FOREIGN KEY, NOT NULL | Reference to ics211_records.id |

## Indexes
- `ics_operators.rul_id` - Foreign key index for RUL relationships
- `ics_operators.ics_id` - Foreign key index for ICS record relationships
- `unique(rul_id, ics_id)` - Composite unique index preventing duplicate associations

## Foreign Keys
- `rul_id` → `resident_unit_leaders.id`
  - **Constraint**: ON DELETE CASCADE
  - **Behavior**: When a RUL is deleted, all their operator associations are automatically removed
- `ics_id` → `ics211_records.id`
  - **Constraint**: ON DELETE CASCADE
  - **Behavior**: When an ICS record is deleted, all operator associations are automatically removed

## Unique Constraints
- **Composite Unique**: `(rul_id, ics_id)`
  - **Purpose**: Prevents a RUL from being added as an operator to the same ICS record multiple times
  - **Behavior**: Database will reject attempts to create duplicate associations

## Relationships

### Many-to-Many Relationship
This table enables:
- One RUL can be an operator on multiple ICS records
- One ICS record can have multiple RUL operators
- Collaborative incident management across multiple units

## Use Cases

### Multi-Agency Response
- Allows coordination between different departments/agencies
- Enables shared responsibility for incident management
- Facilitates resource sharing and communication

### Operator Management
- Track who has access to manage specific incidents
- Control permissions for ICS record modifications
- Enable collaborative decision-making

### Access Control
- Determine which RULs can view/modify ICS records
- Implement role-based access through operator status
- Support delegation of incident management responsibilities

## Usage Examples

### Adding RUL as Operator
```php
// Via Eloquent relationship
$ics211Record->operators()->attach($rulId);

// Via direct model creation
IcsOperator::create([
    'rul_id' => $rulId,
    'ics_id' => $icsRecordId
]);
```

### Removing Operator
```php
// Via Eloquent relationship
$ics211Record->operators()->detach($rulId);

// Via direct model deletion
IcsOperator::where('rul_id', $rulId)
           ->where('ics_id', $icsRecordId)
           ->delete();
```

### Finding ICS Records for a RUL
```php
$rulIcsRecords = Ics211Record::whereHas('operators', function ($query) use ($rulId) {
    $query->where('rul_id', $rulId);
})->get();
```

### Finding Operators for an ICS Record
```php
$operators = ResidentUnitLeader::whereHas('operatorIcsRecords', function ($query) use ($icsId) {
    $query->where('ics_id', $icsId);
})->get();
```

## Authorization Integration

### Permission Checking
```php
// Check if RUL is operator of specific ICS record
public function isOperator($rulId, $icsRecordId)
{
    return IcsOperator::where('rul_id', $rulId)
                     ->where('ics_id', $icsRecordId)
                     ->exists();
}
```

### Middleware Implementation
- Can be used in middleware to restrict access to ICS operations
- Ensures only authorized operators can modify ICS records
- Supports role-based security implementation

## Token-Based Joining

### Integration with ICS Token System
- RULs can join as operators using the ICS record's access token
- Provides secure invitation-based access control
- Enables distributed team formation

### Access Flow
1. ICS creator shares access token with other RULs
2. RULs use token to join as operators
3. System creates entry in `ics_operators` table
4. RUL gains collaborative access to ICS record

## Audit Integration
- Changes to operator associations should be logged in `ics_logs`
- Track when operators are added/removed
- Maintain accountability for access changes

## Performance Considerations
- Composite index on `(rul_id, ics_id)` optimizes lookups
- Consider caching operator relationships for frequently accessed ICS records
- Index on individual foreign keys supports various query patterns

## Rollback
Running the down migration will drop the `ics_operators` table and all operator associations.

## Dependencies
This migration depends on:
- `resident_unit_leaders` table (must exist for foreign key constraint)
- `ics211_records` table (must exist for foreign key constraint)