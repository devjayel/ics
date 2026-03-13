# API Documentation

## Overview
This document provides comprehensive documentation for the Laravel ICS (Incident Command System) REST API. The API manages incident command operations, personnel tracking, and resource management.

**Base URL**: `/api/`
**API Version**: v1
**Authentication**: Laravel Sanctum (Bearer Token)

---

## Authentication

### Authentication Endpoints

#### POST `/auth/login`
**Purpose**: Authenticate Resource Unit Leader (RUL).

**Headers**:
```
Content-Type: application/json
```

**Request Body**:
```json
{
  "email": "string",
  "password": "string"
}
```

**Response**:
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "token": "Bearer_token_string",
    "user": {
      "uuid": "string",
      "name": "string",
      "email": "string"
    }
  }
}
```

**Rate Limit**: 60 requests per minute

#### POST `/auth/login/app`
**Purpose**: Authenticate through mobile app with additional data.

**Request Body**:
```json
{
  "serial_number": "string",
  "password": "string",
  "fcm_token": "string"
}
```

#### POST `/auth/logout`
**Purpose**: Logout current user.

**Headers**:
```
Authorization: Bearer {token}
```

**Response**:
```json
{
  "success": true,
  "message": "Logged out successfully"
}
```

#### POST `/auth/validate-token`
**Purpose**: Validate current authentication token.

---

## Resource Unit Leader (RUL) Endpoints

**Base Path**: `/rul/`
**Middleware**: `rul.auth`, `throttle:60,1`

### Dashboard

#### GET `/rul/dashboard`
**Purpose**: Get dashboard overview data.

**Response**:
```json
{
  "success": true,
  "data": {
    "total_ics_records": 15,
    "active_incidents": 3,
    "total_personnel": 45,
    "my_ics_records": 8
  }
}
```

### Analytics

#### GET `/rul/analytics`
**Purpose**: Get general analytics overview.

#### GET `/rul/analytics/map`
**Purpose**: Get map-based analytics data.

#### GET `/rul/analytics/regions`
**Purpose**: Get regional analytics.

#### GET `/rul/analytics/region/{region}`
**Purpose**: Get specific region analytics.

#### GET `/rul/analytics/ics/{uuid}`
**Purpose**: Get analytics for specific ICS record.

### ICS 211 Management

#### GET `/rul/ics`
**Purpose**: List all ICS 211 records.

**Response**:
```json
{
  "success": true,
  "data": [
    {
      "uuid": "string",
      "token": "string",
      "name": "Emergency Response Alpha",
      "type": "Wildfire",
      "order_request_number": "ORN-2024-001",
      "checkin_location": "Base Camp Alpha",
      "region": "North Region",
      "status": "ongoing",
      "operators": [],
      "checkInDetails": [],
      "created_at": "2024-01-01T00:00:00.000000Z"
    }
  ]
}
```

#### GET `/rul/ics/search`
**Purpose**: Search ICS records with filters.

**Query Parameters**:
- `status`: Filter by status (pending, ongoing, completed)
- `name`: Partial match on incident name
- `order_request_number`: Partial match on order request number

**Response**:
```json
{
  "success": true,
  "data": [],
  "count": 0
}
```

#### POST `/rul/ics/create`
**Purpose**: Create new ICS 211 record.

**Request Body**:
```json
{
  "name": "Emergency Response Beta",
  "type": "Flood",
  "order_request_number": "ORN-2024-002",
  "checkin_location": "Base Camp Beta",
  "region": "South Region",
  "remarks": "Initial response setup",
  "status": "pending",
  "check_in_details": [
    {
      "personnel_id": 1,
      "order_request_number": "ORN-2024-002",
      "checkin_date": "2024-01-15",
      "checkin_time": "08:00:00",
      "kind": "Personnel",
      "category": "Operations",
      "type": "Engine Company",
      "resource_identifier": "E-101",
      "name_of_leader": "John Doe",
      "contact_information": "+1234567890",
      "quantity": 4,
      "department": "Fire Department",
      "departure_point_of_origin": "Station 1",
      "departure_date": "2024-01-15",
      "departure_time": "07:30:00",
      "departure_method_of_travel": "Fire Engine",
      "with_manifest": true,
      "incident_assignment": "Structure Protection",
      "other_qualifications": "Hazmat Certified"
    }
  ]
}
```

**Response**:
```json
{
  "success": true,
  "message": "ICS 211 record created successfully",
  "data": {
    "uuid": "generated_uuid",
    "token": "generated_token",
    // ... full ICS record data
  }
}
```

#### GET `/rul/ics/{id}/show`
**Purpose**: Get specific ICS 211 record by UUID.

#### POST `/rul/ics/{id}/edit`
**Purpose**: Update existing ICS 211 record.

**Request Body**: Same as create, with optional fields for partial updates.

#### POST `/rul/ics/{id}/delete`
**Purpose**: Delete ICS 211 record.

**Response**:
```json
{
  "success": true,
  "message": "ICS 211 record deleted successfully"
}
```

#### POST `/rul/ics/{id}/status/{status}`
**Purpose**: Update ICS record status.

**Parameters**:
- `{status}`: pending, ongoing, completed

**Request Body**:
```json
{
  "remarks": "Status change reason"
}
```

#### POST `/rul/ics/checkin/{uuid}/status`
**Purpose**: Update check-in detail status.

**Request Body**:
```json
{
  "status": "active"
}
```

**Allowed Status Values**:
- `available`
- `staging`
- `assigned`
- `active`
- `demobalized`
- `out_of_service`
- `standby`

#### POST `/rul/ics/join`
**Purpose**: Join ICS as operator using invitation token.

**Request Body**:
```json
{
  "token": "invitation_token"
}
```

### Personnel Management

#### GET `/rul/personnel`
**Purpose**: List all personnel under current RUL.

**Response**:
```json
{
  "success": true,
  "data": [
    {
      "uuid": "string",
      "name": "Jane Smith",
      "contact_number": "+1234567890",
      "serial_number": "PER-001",
      "department": "Fire Department",
      "status": "available",
      "avatar": "avatars/filename.jpg",
      "rul": {
        "name": "John Commander",
        "department": "Emergency Services"
      }
    }
  ]
}
```

#### POST `/rul/personnel/create`
**Purpose**: Create new personnel record.

**Request Body**:
```json
{
  "name": "Jane Smith",
  "contact_number": "+1234567890",
  "serial_number": "PER-001",
  "department": "Fire Department"
}
```

**Validation**:
- `name`: Required, max 255 characters
- `contact_number`: Required, max 20 characters
- `serial_number`: Required, max 100 characters, unique
- `department`: Required, max 100 characters

#### GET `/rul/personnel/{id}/show`
**Purpose**: Get specific personnel by UUID.

#### POST `/rul/personnel/{id}/edit`
**Purpose**: Update personnel record.

#### POST `/rul/personnel/{id}/delete`
**Purpose**: Delete personnel record.

### Profile Management

#### GET `/rul/profile`
**Purpose**: Get current RUL profile.

**Response**:
```json
{
  "success": true,
  "data": {
    "uuid": "string",
    "name": "John Commander",
    "contact_number": "+1234567890",
    "serial_number": "RUL-001",
    "department": "Emergency Services",
    "avatar": "avatars/filename.jpg",
    "logo": "logos/filename.jpg",
    "signature": "signatures/filename.png",
    "certificates": [
      {
        "uuid": "cert_uuid",
        "certificate_name": "ICS Certification",
        "file_path": "certificates/filename.pdf"
      }
    ]
  }
}
```

#### POST `/rul/profile/update`
**Purpose**: Update RUL profile.

**Request Body** (multipart/form-data):
```
name: string
contact_number: string
department: string
certificates[]: file[] (PDF, JPG, JPEG, PNG, max 10MB each)
signature: string (base64 encoded image data)
logo: file (JPG, JPEG, PNG, max 5MB)
remove_certificates[]: array of certificate UUIDs to remove
remove_signature: boolean
remove_logo: boolean
```

#### POST `/rul/profile/change-avatar`
**Purpose**: Update profile avatar.

**Request Body** (multipart/form-data):
```
avatar: file (JPG, JPEG, PNG, max 5MB)
```

### Logging and Activity

#### GET `/rul/ics/{icsUuid}/logs`
**Purpose**: Get logs for specific ICS record.

#### GET `/rul/logs/my-logs`
**Purpose**: Get current user's activity logs.

#### GET `/rul/logs/my-logs/action/{action}`
**Purpose**: Get logs by specific action type.

#### GET `/rul/logs/my-activity-summary`
**Purpose**: Get activity summary statistics.

#### POST `/rul/logs/my-logs/date-range`
**Purpose**: Get logs within date range.

**Request Body**:
```json
{
  "start_date": "2024-01-01",
  "end_date": "2024-01-31"
}
```

### Check-in Detail History

#### GET `/rul/ics/checkin/{id}/history`
**Purpose**: Get check-in detail history.

#### POST `/rul/ics/checkin/history/{id}/status/{status}`
**Purpose**: Update historical check-in detail status.

---

## Personnel Endpoints

**Base Path**: `/personnel/`
**Middleware**: `personnel.auth`, `throttle:60,1`

### Dashboard

#### GET `/personnel/dashboard`
**Purpose**: Get personnel dashboard data.

### Analytics

#### GET `/personnel/analytics`
**Purpose**: Get analytics accessible to personnel.

#### GET `/personnel/analytics/map`
**Purpose**: Get map-based analytics.

#### GET `/personnel/analytics/regions`
**Purpose**: Get regional analytics.

#### GET `/personnel/analytics/region/{region}`
**Purpose**: Get specific region analytics.

#### GET `/personnel/analytics/ics/{uuid}`
**Purpose**: Get analytics for specific ICS record.

### ICS Records (Personnel View)

#### GET `/personnel/ics`
**Purpose**: Get ICS records assigned to current personnel.

#### GET `/personnel/ics/latest`
**Purpose**: Get latest ICS assignment.

#### GET `/personnel/ics/{id}/show`
**Purpose**: Get specific ICS record details.

#### POST `/personnel/ics/checkin/{uuid}/status`
**Purpose**: Update own check-in status.

### Profile Management

#### GET `/personnel/profile`
**Purpose**: Get personnel profile.

**Response**:
```json
{
  "success": true,
  "data": {
    "uuid": "string",
    "name": "Jane Smith",
    "contact_number": "+1234567890",
    "serial_number": "PER-001",
    "department": "Fire Department",
    "status": "available",
    "avatar": "avatars/filename.jpg",
    "rul": {
      "name": "John Commander",
      "department": "Emergency Services"
    }
  }
}
```

#### POST `/personnel/profile/update`
**Purpose**: Update personnel profile (limited fields).

#### POST `/personnel/profile/change-avatar`
**Purpose**: Update personnel avatar.

---

## Common Response Formats

### Success Response
```json
{
  "success": true,
  "message": "Operation successful",
  "data": {}
}
```

### Error Response
```json
{
  "success": false,
  "message": "Error description",
  "errors": {
    "field_name": ["Validation error message"]
  }
}
```

### Validation Error (422)
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password field is required."]
  }
}
```

### Not Found Error (404)
```json
{
  "success": false,
  "message": "Resource not found"
}
```

### Unauthorized Error (401)
```json
{
  "success": false,
  "message": "Unauthorized"
}
```

---

## Rate Limiting

**Default Rate Limit**: 60 requests per minute per user
**Rate Limit Headers**:
- `X-RateLimit-Limit`: Request limit
- `X-RateLimit-Remaining`: Remaining requests
- `X-RateLimit-Reset`: Reset time

**Rate Limit Exceeded (429)**:
```json
{
  "message": "Too Many Attempts."
}
```

---

## File Uploads

### Supported File Types

#### Avatars/Images
- **Formats**: JPG, JPEG, PNG
- **Max Size**: 5MB
- **Storage**: `storage/app/public/avatars/`

#### Certificates
- **Formats**: PDF, JPG, JPEG, PNG
- **Max Size**: 10MB
- **Storage**: `storage/app/public/certificates/`

#### Logos
- **Formats**: JPG, JPEG, PNG
- **Max Size**: 5MB
- **Storage**: `storage/app/public/logos/`

#### Signatures
- **Format**: Base64 encoded PNG
- **Storage**: `storage/app/public/signatures/`

### File Upload Response
```json
{
  "success": true,
  "message": "File uploaded successfully",
  "data": {
    "file_path": "avatars/filename.jpg",
    "file_url": "http://domain.com/storage/avatars/filename.jpg"
  }
}
```

---

## Data Models

### ICS 211 Record
```json
{
  "uuid": "string",
  "token": "string",
  "name": "string",
  "type": "string",
  "order_request_number": "string",
  "checkin_location": "string",
  "region": "string|null",
  "remarks": "string|null",
  "remarks_image_attachment": "string|null",
  "status": "pending|ongoing|completed",
  "created_at": "timestamp",
  "updated_at": "timestamp",
  "operators": "array",
  "checkInDetails": "array"
}
```

### Personnel
```json
{
  "uuid": "string",
  "name": "string",
  "contact_number": "string",
  "serial_number": "string",
  "department": "string",
  "status": "available|staging|assigned|active|demobalized|out_of_service|standby",
  "avatar": "string|null",
  "created_at": "timestamp",
  "updated_at": "timestamp",
  "rul": "object"
}
```

### Check-in Detail
```json
{
  "uuid": "string",
  "personnel_id": "integer|null",
  "order_request_number": "string",
  "checkin_date": "date",
  "checkin_time": "time",
  "kind": "string",
  "category": "string",
  "type": "string",
  "resource_identifier": "string",
  "name_of_leader": "string",
  "contact_information": "string",
  "quantity": "integer",
  "department": "string",
  "departure_point_of_origin": "string",
  "departure_date": "date",
  "departure_time": "time",
  "departure_method_of_travel": "string",
  "with_manifest": "boolean",
  "incident_assignment": "string|null",
  "other_qualifications": "string|null",
  "sent_resl": "boolean",
  "status": "string",
  "created_at": "timestamp",
  "updated_at": "timestamp"
}
```

---

## Real-time Features

### Push Notifications
The API integrates with Laravel Pusher for real-time notifications:

- **Personnel Status Updates**: Notified when assigned/removed from incidents
- **ICS Status Changes**: Operators notified of status changes
- **Channel Format**: `ics-{personnel_uuid}`

---

## Security Features

### Authentication
- Laravel Sanctum bearer tokens
- Token validation on protected routes
- Role-based access (RUL vs Personnel)

### Data Protection
- Input validation on all endpoints
- File upload security
- CSRF protection
- SQL injection prevention

### Access Control
- Entity ownership verification
- Role-based endpoint access
- Resource-level permissions

---

## Error Handling

### Common HTTP Status Codes
- **200**: OK - Request successful
- **201**: Created - Resource created successfully
- **400**: Bad Request - Invalid request data
- **401**: Unauthorized - Authentication required
- **403**: Forbidden - Access denied
- **404**: Not Found - Resource not found
- **422**: Unprocessable Entity - Validation failed
- **429**: Too Many Requests - Rate limit exceeded
- **500**: Internal Server Error - Server error

### Error Response Format
All errors follow consistent JSON format with `success: false` and descriptive error messages.

---

## Testing

### API Testing Tools
- **Postman**: Import collection for testing
- **Laravel Debugbar**: Development request monitoring
- **PHPUnit**: Automated API testing

### Test Authentication
```bash
# Generate test token
php artisan tinker
>>> $user = App\Models\Rul::first()
>>> $token = $user->createToken('test')->plainTextToken
```

---

## Changelog

### Version 1.0.0
- Initial API release
- ICS 211 management
- Personnel tracking
- File upload support
- Real-time notifications
- Audit logging

---

## Support

For API questions or issues:
1. Check this documentation first
2. Review Laravel logs for error details
3. Contact development team with specific error messages
4. Include request/response examples in bug reports