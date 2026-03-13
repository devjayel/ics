# ICS Operators Table Migration Documentation

## Migration File: `2026_02_06_095429_create_ics_operators_table.php`

### Overview
This migration creates the `ics_operators` table, which is a pivot table establishing many-to-many relationships between Resource Unit Leaders (RULs) and ICS 211 records for collaborative incident management.

---

## ICS Operators Table - `ics_operators`

### Purpose
Enables multiple Resource Unit Leaders to collaborate as operators on a single ICS 211 record. This supports multi-agency incident management where different RULs need shared access to manage personnel and resources.

### Table Structure

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | bigint | PRIMARY KEY, AUTO_INCREMENT | Unique identifier |
| `rul_id` | bigint | FOREIGN KEY, NOT NULL | Resource Unit Leader ID |
| `ics_id` | bigint | FOREIGN KEY, NOT NULL | ICS 211 Record ID |

### Indexes and Constraints
- Primary key on `id`
- Foreign key on `rul_id`
- Foreign key on `ics_id`
- **Unique constraint** on `['rul_id', 'ics_id']` combination

### Foreign Keys
- `rul_id` references `resident_unit_leaders(id)` ON DELETE CASCADE
- `ics_id` references `ics211_records(id)` ON DELETE CASCADE

---

## Business Logic

### Many-to-Many Relationship
- **One RUL** can be an operator on **multiple ICS records**
- **One ICS record** can have **multiple RUL operators**
- Enables collaborative incident management across departments/agencies

### Operator Permissions
RULs designated as operators can typically:
- View all check-in details for the incident
- Add/modify personnel assignments
- Update incident status and information
- Access real-time incident data
- Coordinate resource management

### Access Control
- Original ICS creator is automatically an operator
- Additional RULs join via invitation token
- Unique constraint prevents duplicate operator assignments

---

## Collaboration Scenarios

### Multi-Agency Response
```
Incident: Large Wildfire
- Fire Department RUL (creator)
- Police Department RUL (joins via token)
- EMS Department RUL (joins via token)
- Public Works RUL (joins via token)
```

### Unified Command
- Multiple agencies share operational control
- Each agency retains authority over their personnel
- Shared situational awareness across all operators

### Resource Sharing
- Cross-agency personnel assignment
- Coordinated equipment deployment
- Unified reporting and documentation

---

## Token-Based Joining

### Process Flow
1. **Creator**: RUL creates ICS 211 record, becomes first operator
2. **Invitation**: Creator shares unique token with other RULs
3. **Joining**: Other RULs use token to join as operators
4. **Collaboration**: All operators can manage the incident

### Security Features
- Token prevents unauthorized access
- Unique constraint prevents multiple joins by same RUL
- CASCADE DELETE maintains data integrity

---

## Migration Commands

### Apply Migration
```bash
php artisan migrate --path=database/migrations/2026_02_06_095429_create_ics_operators_table.php
```

### Rollback Migration
```bash
php artisan migrate:rollback --path=database/migrations/2026_02_06_095429_create_ics_operators_table.php
```

---

## Related Tables
- `resident_unit_leaders` - RUL operator records
- `ics211_records` - ICS incident records

## Related Models
- `App\Models\Rul` - Has many-to-many relationship with ICS records
- `App\Models\Ics211Record` - Has many-to-many relationship with RULs
- No direct model for pivot table (handled by Eloquent relationships)

## Dependencies
- Requires `resident_unit_leaders` table to exist
- Requires `ics211_records` table to exist

## Laravel Relationship Examples

### In Rul Model
```php
public function icsRecords()
{
    return $this->belongsToMany(Ics211Record::class, 'ics_operators', 'rul_id', 'ics_id');
}
```

### In Ics211Record Model
```php
public function operators()
{
    return $this->belongsToMany(Rul::class, 'ics_operators', 'ics_id', 'rul_id');
}
```

## Key Features
- **Multi-Agency Support**: Enables collaborative incident management
- **Access Control**: Token-based operator joining
- **Data Integrity**: CASCADE DELETE prevents orphaned records
- **Unique Constraints**: Prevents duplicate operator assignments
- **Scalable**: Supports any number of operators per incident

## Notes
- No timestamps in pivot table (uses parent record timestamps)
- Unique constraint ensures one membership per RUL per incident
- CASCADE DELETE maintains referential integrity
- Supports ICS unified command structure
- Enables cross-jurisdictional collaboration
- Token-based joining provides security without complex permissions