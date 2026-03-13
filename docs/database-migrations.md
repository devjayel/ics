# Database Migrations Documentation

## Overview
This document provides comprehensive documentation for all database migrations in the Laravel ICS (Incident Command System) application. The system manages incident command operations, personnel, and resource tracking.

## Migration History

### Core Laravel Migrations

#### 1. Users Table (`0001_01_01_000000_create_users_table`)
**Purpose**: Basic user authentication and session management.

```sql
CREATE TABLE users (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    uuid VARCHAR(255) UNIQUE,
    name VARCHAR(255),
    email VARCHAR(255) UNIQUE,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255),
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

CREATE TABLE password_reset_tokens (
    email VARCHAR(255) PRIMARY KEY,
    token VARCHAR(255),
    created_at TIMESTAMP NULL
);

CREATE TABLE sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    payload LONGTEXT,
    last_activity INT,
    INDEX(user_id),
    INDEX(last_activity)
);
```

**Key Features**:
- UUID support for users
- Standard Laravel authentication tables
- Session management

#### 2. Cache Table (`0001_01_01_000001_create_cache_table`)
**Purpose**: Application caching system.

#### 3. Jobs Table (`0001_01_01_000002_create_jobs_table`)
**Purpose**: Queue system for background jobs.

#### 4. Two-Factor Authentication (`2025_08_26_100418_add_two_factor_columns_to_users_table`)
**Purpose**: Enhanced security with 2FA support.

---

### Application-Specific Migrations

#### 5. Personal Access Tokens (`2025_11_30_003807_create_personal_access_tokens_table`)
**Purpose**: API authentication using Laravel Sanctum.

#### 6. Certificates Table (`2025_11_30_010513_create_certificates_table`)
**Purpose**: Store certification documents for Resource Unit Leaders (RULs).

```sql
CREATE TABLE certificates (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    uuid VARCHAR(255) UNIQUE,
    rul_id BIGINT UNSIGNED,
    certificate_name VARCHAR(255),
    file_path VARCHAR(255),
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (rul_id) REFERENCES resident_unit_leaders(id) ON DELETE CASCADE
);
```

#### 7. Resource Unit Leaders (`2025_11_30_005736_create_resource_unit_leaders_table`)
**Purpose**: Main table for incident commanders and unit leaders.

```sql
CREATE TABLE resident_unit_leaders (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    uuid VARCHAR(255) UNIQUE,
    avatar VARCHAR(255) NULL,
    logo VARCHAR(255) NULL,
    name VARCHAR(255),
    contact_number VARCHAR(255),
    serial_number VARCHAR(255) UNIQUE,
    department VARCHAR(255),
    signature VARCHAR(255) NULL,
    fcm_token VARCHAR(255) NULL,
    token VARCHAR(255) UNIQUE NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

**Key Features**:
- Avatar and logo support for branding
- Digital signature support
- FCM token for push notifications
- Unique serial numbers for identification

#### 8. Personnel Table (`2025_11_30_012251_create_personnels_table`)
**Purpose**: Personnel under Resource Unit Leaders.

```sql
CREATE TABLE personnels (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    uuid VARCHAR(255) UNIQUE,
    rul_id BIGINT UNSIGNED,
    avatar VARCHAR(255) NULL,
    name VARCHAR(255),
    contact_number VARCHAR(255),
    serial_number VARCHAR(255) UNIQUE,
    department VARCHAR(255),
    fcm_token VARCHAR(255) NULL,
    token VARCHAR(255) UNIQUE NULL,
    status VARCHAR(255) DEFAULT 'available',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (rul_id) REFERENCES resident_unit_leaders(id) ON DELETE CASCADE
);
```

**Status Values**:
- `available`: Ready for assignment
- `staging`: Preparing for deployment
- `assigned`: Assigned to incident
- `active`: Currently deployed
- `demobalized`: Released from assignment
- `out_of_service`: Unavailable
- `standby`: On standby

#### 9. ICS 211 Records (`2025_11_30_020220_create_ics211_records_table`)
**Purpose**: Central incident tracking based on ICS 211 form standard.

```sql
CREATE TABLE ics211_records (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    uuid VARCHAR(255) UNIQUE,
    token VARCHAR(255) UNIQUE,
    name VARCHAR(255),
    type VARCHAR(255),
    order_request_number VARCHAR(255),
    checkin_location VARCHAR(255),
    region VARCHAR(255) NULL,
    remarks TEXT NULL,
    remarks_image_attachment VARCHAR(255) NULL,
    status VARCHAR(255) DEFAULT 'pending',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

**Status Values**:
- `pending`: Awaiting activation
- `ongoing`: Currently active
- `completed`: Mission completed

#### 10. Check-in Details (`2025_12_01_182304_create_check_in_details_table`)
**Purpose**: Detailed resource and personnel check-in information.

```sql
CREATE TABLE check_in_details (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    uuid VARCHAR(255) UNIQUE,
    ics211_record_id BIGINT UNSIGNED,
    personnel_id BIGINT UNSIGNED NULL,
    order_request_number VARCHAR(255),
    checkin_date DATE,
    checkin_time TIME,
    kind VARCHAR(255),
    category VARCHAR(255),
    type VARCHAR(255),
    resource_identifier VARCHAR(255),
    name_of_leader VARCHAR(255),
    contact_information VARCHAR(255),
    quantity INT,
    department VARCHAR(255),
    departure_point_of_origin VARCHAR(255),
    departure_date DATE,
    departure_time TIME,
    departure_method_of_travel VARCHAR(255),
    with_manifest BOOLEAN DEFAULT FALSE,
    incident_assignment VARCHAR(255) NULL,
    other_qualifications VARCHAR(255) NULL,
    sent_resl BOOLEAN DEFAULT FALSE,
    status VARCHAR(255) DEFAULT 'pending',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (ics211_record_id) REFERENCES ics211_records(id) ON DELETE CASCADE,
    FOREIGN KEY (personnel_id) REFERENCES personnels(id) ON DELETE CASCADE
);
```

#### 11. Check-in Detail Histories (`2025_12_03_072949_create_check_in_detail_histories_table`)
**Purpose**: Historical tracking of check-in detail changes.

#### 12. ICS Operators (`2026_02_06_095429_create_ics_operators_table`)
**Purpose**: Many-to-many relationship between RULs and ICS records.

```sql
CREATE TABLE ics_operators (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    rul_id BIGINT UNSIGNED,
    ics_id BIGINT UNSIGNED,
    FOREIGN KEY (rul_id) REFERENCES resident_unit_leaders(id) ON DELETE CASCADE,
    FOREIGN KEY (ics_id) REFERENCES ics211_records(id) ON DELETE CASCADE,
    UNIQUE(rul_id, ics_id)
);
```

#### 13. ICS Logs (`2026_02_06_120000_create_ics_logs_table`)
**Purpose**: Comprehensive audit trail for all ICS operations.

```sql
CREATE TABLE ics_logs (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    uuid VARCHAR(255) UNIQUE,
    ics211_record_id BIGINT UNSIGNED,
    rul_id BIGINT UNSIGNED,
    action VARCHAR(255),
    description TEXT,
    old_values JSON NULL,
    new_values JSON NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (ics211_record_id) REFERENCES ics211_records(id) ON DELETE CASCADE,
    FOREIGN KEY (rul_id) REFERENCES resident_unit_leaders(id) ON DELETE CASCADE,
    INDEX(ics211_record_id),
    INDEX(rul_id),
    INDEX(created_at)
);
```

**Logged Actions**:
- `created`: ICS record creation
- `updated`: ICS record updates
- `personnel_added`: Personnel assignments
- `personnel_removed`: Personnel removals
- `status_changed`: Status changes
- `operator_added`: New operators joining

---

## Database Relationships

### Entity Relationship Overview

```
Users (1:1) → Resident_Unit_Leaders
Resident_Unit_Leaders (1:M) → Personnels
Resident_Unit_Leaders (M:M) → ICS211_Records (via ics_operators)
Resident_Unit_Leaders (1:M) → Certificates
ICS211_Records (1:M) → Check_In_Details
Check_In_Details (M:1) → Personnels [nullable]
ICS211_Records (1:M) → ICS_Logs
Check_In_Details (1:M) → Check_In_Detail_Histories
```

### Key Foreign Key Constraints

1. **Cascading Deletes**: Most relationships use `ON DELETE CASCADE` to maintain referential integrity
2. **Nullable Relationships**: Personnel assignments in check-in details are optional
3. **Unique Constraints**: Serial numbers, tokens, and UUIDs enforce uniqueness

---

## Migration Best Practices

### 1. Running Migrations
```bash
# Run all pending migrations
php artisan migrate

# Rollback last migration batch
php artisan migrate:rollback

# Check migration status
php artisan migrate:status
```

### 2. Creating New Migrations
```bash
# Create a new migration
php artisan make:migration create_example_table

# Create migration with model
php artisan make:model Example -m
```

### 3. Migration Safety
- Always backup database before running migrations in production
- Test migrations in development/staging environments first
- Use database transactions where possible
- Consider downtime requirements for large table modifications

---

## Data Seeding

The application includes seeders for initial data:
- `IcsSeeder`: Sample ICS records and personnel
- Default RUL accounts for testing

```bash
# Run all seeders
php artisan db:seed

# Run specific seeder
php artisan db:seed --class=IcsSeeder
```

---

## Performance Considerations

### Database Indexes
- UUID fields are indexed for fast lookups
- Foreign keys are automatically indexed
- Created_at timestamp indexed on logs for time-based queries
- Composite unique indexes on junction tables

### Query Optimization
- Use eager loading for related models
- Consider pagination for large datasets
- Monitor query performance with Laravel Debugbar

---

## Backup and Recovery

### Regular Backups
```bash
# MySQL backup
mysqldump -u username -p database_name > backup.sql

# Restore
mysql -u username -p database_name < backup.sql
```

### Migration Recovery
- Keep track of migration files in version control
- Test rollback procedures
- Maintain separate environments for testing

---

## Security Considerations

1. **Data Encryption**: Sensitive fields should be encrypted at application level
2. **Access Control**: Foreign key constraints prevent unauthorized data access
3. **Audit Trail**: ICS logs provide complete activity tracking
4. **File Storage**: Uploaded files (avatars, certificates) stored securely

---

## Troubleshooting Common Issues

### Migration Failures
```bash
# Check current schema
php artisan schema:dump

# Reset and re-run migrations (development only)
php artisan migrate:fresh

# Fix migration conflicts
php artisan migrate:reset
```

### Data Integrity Issues
- Check foreign key constraints
- Verify unique constraint violations
- Review cascade delete impacts

---

## Future Considerations

### Planned Enhancements
- Additional audit logging
- Enhanced file management
- Performance optimization indices
- Archive/historical data management

### Maintenance
- Regular index analysis
- Query performance monitoring
- Database size management
- Backup strategy review