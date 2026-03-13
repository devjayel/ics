# Laravel Framework Tables Migration Documentation

## Overview
This document covers the standard Laravel framework tables used for core functionality including caching, job queues, and API authentication.

---

## Cache Tables

### Migration File
- **File**: `0001_01_01_000001_create_cache_table.php`
- **Purpose**: Database caching and cache locking

### Tables Created

#### 1. cache
Stores cached data when using database cache driver.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| key | varchar(191) | PRIMARY KEY | Cache key identifier |
| value | mediumtext | NOT NULL | Cached data (serialized) |
| expiration | int(11) | NOT NULL | Unix timestamp for expiration |

#### 2. cache_locks
Prevents race conditions in cache operations.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| key | varchar(191) | PRIMARY KEY | Lock key identifier |
| owner | varchar(191) | NOT NULL | Lock owner identifier |
| expiration | int(11) | NOT NULL | Unix timestamp for lock expiration |

---

## Job Queue Tables

### Migration File
- **File**: `0001_01_01_000002_create_jobs_table.php`
- **Purpose**: Background job processing and queue management

### Tables Created

#### 1. jobs
Stores queued jobs for background processing.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint(20) | PRIMARY KEY, AUTO_INCREMENT | Job ID |
| queue | varchar(191) | INDEX | Queue name |
| payload | longtext | NOT NULL | Job data and parameters |
| attempts | tinyint(3) unsigned | NOT NULL | Number of processing attempts |
| reserved_at | int(10) unsigned | NULL | When job was claimed by worker |
| available_at | int(10) unsigned | NOT NULL | When job becomes available |
| created_at | int(10) unsigned | NOT NULL | Job creation timestamp |

#### 2. job_batches
Manages groups of related jobs.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | varchar(191) | PRIMARY KEY | Batch identifier |
| name | varchar(191) | NOT NULL | Human-readable batch name |
| total_jobs | int(11) | NOT NULL | Total jobs in batch |
| pending_jobs | int(11) | NOT NULL | Jobs not yet completed |
| failed_jobs | int(11) | NOT NULL | Number of failed jobs |
| failed_job_ids | longtext | NOT NULL | IDs of failed jobs |
| options | mediumtext | NULL | Batch configuration options |
| cancelled_at | int(11) | NULL | When batch was cancelled |
| created_at | int(11) | NOT NULL | Batch creation timestamp |
| finished_at | int(11) | NULL | When batch completed |

#### 3. failed_jobs
Stores jobs that failed processing.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint(20) | PRIMARY KEY, AUTO_INCREMENT | Failed job ID |
| uuid | varchar(191) | UNIQUE | Unique identifier |
| connection | text | NOT NULL | Database connection used |
| queue | text | NOT NULL | Queue name |
| payload | longtext | NOT NULL | Original job data |
| exception | longtext | NOT NULL | Exception details |
| failed_at | timestamp | DEFAULT CURRENT_TIMESTAMP | When job failed |

---

## API Authentication Table

### Migration File
- **File**: `2025_11_30_003807_create_personal_access_tokens_table.php`
- **Purpose**: Laravel Sanctum personal access tokens for API authentication

### Table: personal_access_tokens

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint(20) | PRIMARY KEY, AUTO_INCREMENT | Token ID |
| tokenable_type | varchar(191) | NOT NULL | Model type (polymorphic) |
| tokenable_id | bigint(20) | NOT NULL | Model ID (polymorphic) |
| name | text | NOT NULL | Token name/description |
| token | varchar(64) | UNIQUE | Hashed token value |
| abilities | text | NULL | Token permissions (JSON) |
| last_used_at | timestamp | NULL | When token was last used |
| expires_at | timestamp | NULL, INDEX | Token expiration time |
| created_at | timestamp | NULL | Token creation time |
| updated_at | timestamp | NULL | Token update time |

### Polymorphic Relationship
The `tokenable_type` and `tokenable_id` columns create a polymorphic relationship, allowing tokens to belong to different model types (Users, RULs, Personnel, etc.).

---

## Two-Factor Authentication Enhancement

### Migration File
- **File**: `2025_08_26_100418_add_two_factor_columns_to_users_table.php`
- **Purpose**: Adds 2FA support to existing users table

### Columns Added to users table

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| two_factor_secret | text | NULL | Encrypted TOTP secret |
| two_factor_recovery_codes | text | NULL | Encrypted recovery codes |
| two_factor_confirmed_at | timestamp | NULL | When 2FA was confirmed |

---

## Usage in ICS System

### Caching Strategy
- Cache frequently accessed ICS records
- Store personnel lookup data
- Cache dashboard statistics and analytics

### Job Processing
- Background notifications via FCM
- File processing for uploads
- Batch operations for bulk updates
- Report generation

### API Authentication
- Secure API access for mobile apps
- Token-based authentication for RULs and Personnel
- Granular permissions via abilities

### Two-Factor Security
- Enhanced security for admin users
- Optional 2FA for RUL accounts
- Recovery code management

---

## Maintenance Considerations

### Cache Management
- Regular cleanup of expired cache entries
- Monitor cache hit ratios
- Consider cache driver performance

### Job Queue Monitoring
- Monitor failed job rates
- Implement job retry strategies
- Clean up old completed jobs

### Token Security
- Regular token rotation policies
- Monitor token usage patterns
- Implement token expiration

### Performance Optimization
- Index optimization for job queues
- Cache table partitioning for large datasets
- Regular database maintenance