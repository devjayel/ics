# Users Table Migration Documentation

## Overview
This migration creates the `users` table, `password_reset_tokens` table, and `sessions` table for user authentication and session management.

## Migration File
- **File**: `0001_01_01_000000_create_users_table.php`
- **Created**: Base Laravel migration

## Tables Created

### 1. users
Main user authentication table.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint(20) | PRIMARY KEY, AUTO_INCREMENT | Primary key |
| uuid | varchar(191) | UNIQUE, NOT NULL | Unique identifier for users |
| name | varchar(191) | NOT NULL | User's full name |
| email | varchar(191) | UNIQUE, NOT NULL | User's email address |
| email_verified_at | timestamp | NULL | Email verification timestamp |
| password | varchar(191) | NOT NULL | Hashed password |
| remember_token | varchar(100) | NULL | Remember token for authentication |
| created_at | timestamp | NULL | Record creation timestamp |
| updated_at | timestamp | NULL | Record update timestamp |

### 2. password_reset_tokens
Table for storing password reset tokens.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| email | varchar(191) | PRIMARY KEY | User's email address |
| token | varchar(191) | NOT NULL | Password reset token |
| created_at | timestamp | NULL | Token creation timestamp |

### 3. sessions
Table for storing user session data.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | varchar(191) | PRIMARY KEY | Session ID |
| user_id | bigint(20) | FOREIGN KEY, INDEX | Reference to users.id |
| ip_address | varchar(45) | NULL | User's IP address |
| user_agent | text | NULL | User's browser/device info |
| payload | longtext | NOT NULL | Session data payload |
| last_activity | int(11) | INDEX | Last activity timestamp |

## Indexes
- `users.uuid` - Unique index
- `users.email` - Unique index
- `sessions.user_id` - Index for faster lookups
- `sessions.last_activity` - Index for session cleanup

## Foreign Keys
- `sessions.user_id` → `users.id`

## Usage Notes
- The `uuid` field provides a public identifier that can be safely exposed in URLs
- `users` table serves as the base for authentication
- `password_reset_tokens` enables password recovery functionality
- `sessions` table manages user sessions across requests

## Rollback
Running the down migration will drop all three tables:
- `users`
- `password_reset_tokens`
- `sessions`