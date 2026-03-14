# ICS Check-in Detail API Documentation

## Overview
This document provides detailed API documentation for managing check-in details within ICS 211 records. Check-in details track resource and personnel deployments, including arrival/departure information, qualifications, and assignments.

**Base Path**: `/api/rul/`
**Authentication**: Required (Laravel Sanctum Bearer Token)
**Middleware**: `rul.auth`, `throttle:60,1`
**Controller**: `IcsCheckInDetailController`

---

## Endpoints Summary

| Method | Endpoint | Purpose |
|--------|----------|---------|
| GET | `/ics/{icsUuid}/checkin` | List all check-in details for an ICS record |
| GET | `/ics/checkin/{uuid}` | Get specific check-in detail |
| POST | `/ics/{icsUuid}/checkin` | Create new check-in detail |
| POST | `/ics/checkin/{uuid}/edit` | Update existing check-in detail |
| POST | `/ics/checkin/{uuid}/delete` | Delete check-in detail |

---

## Endpoint Details

### 1. List Check-in Details

**GET** `/api/rul/ics/{icsUuid}/checkin`

Retrieve all check-in details associated with a specific ICS 211 record.

#### Path Parameters
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `icsUuid` | string | Yes | UUID of the ICS 211 record |

#### Headers
```
Authorization: Bearer {token}
Accept: application/json
```

#### Success Response (200)
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "uuid": "9d8a7f6e-5c4b-3a2d-1e0f-123456789abc",
      "ics211_record_id": 5,
      "personnel_id": 12,
      "order_request_number": "ORN-2026-001",
      "checkin_date": "2026-03-30",
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
      "departure_date": "2026-03-30",
      "departure_time": "07:30:00",
      "departure_method_of_travel": "Fire Engine",
      "with_manifest": true,
      "incident_assignment": "Structure Protection",
      "other_qualifications": "Hazmat Certified",
      "sent_resl": false,
      "status": "pending",
      "created_at": "2026-03-14T10:00:00.000000Z",
      "updated_at": "2026-03-14T10:00:00.000000Z",
      "personnel": {
        "id": 12,
        "uuid": "personnel-uuid-here",
        "name": "Jane Smith",
        "serial_number": "PER-001",
        "department": "Fire Department",
        "status": "standby",
        "contact_number": "+1234567890",
        "avatar": "avatars/jane-smith.jpg"
      }
    }
  ]
}
```

#### Error Response (404)
```json
{
  "success": false,
  "message": "ICS 211 record not found"
}
```

#### Example Request
```bash
curl -X GET \
  'https://api.example.com/api/rul/ics/9d8a7f6e-5c4b-3a2d-1e0f-123456789abc/checkin' \
  -H 'Authorization: Bearer your_token_here' \
  -H 'Accept: application/json'
```

---

### 2. Get Specific Check-in Detail

**GET** `/api/rul/ics/checkin/{uuid}`

Retrieve a single check-in detail by its UUID.

#### Path Parameters
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `uuid` | string | Yes | UUID of the check-in detail |

#### Headers
```
Authorization: Bearer {token}
Accept: application/json
```

#### Success Response (200)
```json
{
  "success": true,
  "data": {
    "id": 1,
    "uuid": "9d8a7f6e-5c4b-3a2d-1e0f-123456789abc",
    "ics211_record_id": 5,
    "personnel_id": 12,
    "order_request_number": "ORN-2026-001",
    "checkin_date": "2026-03-30",
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
    "departure_date": "2026-03-30",
    "departure_time": "07:30:00",
    "departure_method_of_travel": "Fire Engine",
    "with_manifest": true,
    "incident_assignment": "Structure Protection",
    "other_qualifications": "Hazmat Certified",
    "sent_resl": false,
    "status": "pending",
    "created_at": "2026-03-14T10:00:00.000000Z",
    "updated_at": "2026-03-14T10:00:00.000000Z",
    "personnel": {
      "id": 12,
      "uuid": "personnel-uuid-here",
      "name": "Jane Smith",
      "serial_number": "PER-001",
      "department": "Fire Department",
      "status": "standby"
    }
  }
}
```

#### Error Response (404)
```json
{
  "success": false,
  "message": "Check-in detail not found"
}
```

#### Example Request
```bash
curl -X GET \
  'https://api.example.com/api/rul/ics/checkin/9d8a7f6e-5c4b-3a2d-1e0f-123456789abc' \
  -H 'Authorization: Bearer your_token_here' \
  -H 'Accept: application/json'
```

---

### 3. Create Check-in Detail

**POST** `/api/rul/ics/{icsUuid}/checkin`

Create a new check-in detail for an ICS 211 record.

#### Path Parameters
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `icsUuid` | string | Yes | UUID of the ICS 211 record |

#### Headers
```
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

#### Request Body
| Field | Type | Required | Validation | Description |
|-------|------|----------|------------|-------------|
| `personnel_id` | integer | No | exists:personnels,id | ID of personnel being checked in |
| `order_request_number` | string | Yes | required | Order/request reference number |
| `checkin_date` | date | Yes | required, date | Date of check-in (YYYY-MM-DD) |
| `checkin_time` | time | Yes | required, H:i format | Time of check-in (HH:MM) |
| `kind` | string | Yes | required | Kind of resource (e.g., Personnel, Equipment) |
| `category` | string | Yes | required | Category (e.g., Operations, Logistics) |
| `type` | string | Yes | required | Resource type (e.g., Engine Company, Hand Crew) |
| `resource_identifier` | string | Yes | required | Unique resource identifier |
| `name_of_leader` | string | Yes | required | Name of team/resource leader |
| `contact_information` | string | Yes | required | Contact details (phone, radio, etc.) |
| `quantity` | integer | Yes | required, min:1 | Number of personnel or units |
| `department` | string | Yes | required | Department/agency name |
| `departure_point_of_origin` | string | Yes | required | Where resource is departing from |
| `departure_date` | date | Yes | required, date | Departure date (YYYY-MM-DD) |
| `departure_time` | time | Yes | required, H:i format | Departure time (HH:MM) |
| `departure_method_of_travel` | string | Yes | required | Transportation method |
| `with_manifest` | boolean | No | boolean | Whether manifest is included |
| `incident_assignment` | string | No | - | Specific incident assignment |
| `other_qualifications` | string | No | - | Additional qualifications/certifications |
| `sent_resl` | boolean | No | boolean | Whether sent to RESL (Resource Unit Leader) |

#### Request Example
```json
{
  "personnel_id": 12,
  "order_request_number": "ORN-2026-001",
  "checkin_date": "2026-03-30",
  "checkin_time": "08:00",
  "kind": "Personnel",
  "category": "Operations",
  "type": "Engine Company",
  "resource_identifier": "E-101",
  "name_of_leader": "John Doe",
  "contact_information": "+1234567890",
  "quantity": 4,
  "department": "Fire Department",
  "departure_point_of_origin": "Station 1",
  "departure_date": "2026-03-30",
  "departure_time": "07:30",
  "departure_method_of_travel": "Fire Engine",
  "with_manifest": true,
  "incident_assignment": "Structure Protection",
  "other_qualifications": "Hazmat Certified",
  "sent_resl": false
}
```

#### Success Response (201)
```json
{
  "success": true,
  "message": "Check-in detail created successfully",
  "data": {
    "uuid": "9d8a7f6e-5c4b-3a2d-1e0f-123456789abc",
    "ics211_record_id": 5,
    "personnel_id": 12,
    "order_request_number": "ORN-2026-001",
    "checkin_date": "2026-03-30",
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
    "departure_date": "2026-03-30",
    "departure_time": "07:30:00",
    "departure_method_of_travel": "Fire Engine",
    "with_manifest": true,
    "incident_assignment": "Structure Protection",
    "other_qualifications": "Hazmat Certified",
    "sent_resl": false,
    "status": "pending",
    "created_at": "2026-03-14T10:00:00.000000Z",
    "updated_at": "2026-03-14T10:00:00.000000Z",
    "personnel": {
      "id": 12,
      "uuid": "personnel-uuid-here",
      "name": "Jane Smith",
      "serial_number": "PER-001"
    }
  }
}
```

#### Error Response (404)
```json
{
  "success": false,
  "message": "ICS 211 record not found"
}
```

#### Validation Error Response (422)
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "checkin_date": ["The checkin date field is required."],
    "quantity": ["The quantity must be at least 1."]
  }
}
```

#### Side Effects
1. **Personnel Status Update**: If `personnel_id` is provided, the associated personnel's status is automatically updated to `standby`
2. **Audit Logging**: An ICS log entry is created documenting the personnel addition with action type `personnel_added`
3. **UUID Generation**: A unique UUID is automatically generated for the check-in detail

#### Example Request
```bash
curl -X POST \
  'https://api.example.com/api/rul/ics/9d8a7f6e-5c4b-3a2d-1e0f-123456789abc/checkin' \
  -H 'Authorization: Bearer your_token_here' \
  -H 'Content-Type: application/json' \
  -H 'Accept: application/json' \
  -d '{
    "personnel_id": 12,
    "order_request_number": "ORN-2026-001",
    "checkin_date": "2026-03-30",
    "checkin_time": "08:00",
    "kind": "Personnel",
    "category": "Operations",
    "type": "Engine Company",
    "resource_identifier": "E-101",
    "name_of_leader": "John Doe",
    "contact_information": "+1234567890",
    "quantity": 4,
    "department": "Fire Department",
    "departure_point_of_origin": "Station 1",
    "departure_date": "2026-03-30",
    "departure_time": "07:30",
    "departure_method_of_travel": "Fire Engine",
    "with_manifest": true,
    "incident_assignment": "Structure Protection",
    "other_qualifications": "Hazmat Certified"
  }'
```

---

### 4. Update Check-in Detail

**POST** `/api/rul/ics/checkin/{uuid}/edit`

Update an existing check-in detail.

#### Path Parameters
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `uuid` | string | Yes | UUID of the check-in detail to update |

#### Headers
```
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

#### Request Body
Same fields as Create endpoint. All fields are required.

#### Request Example
```json
{
  "personnel_id": 15,
  "order_request_number": "ORN-2026-001",
  "checkin_date": "2026-03-30",
  "checkin_time": "08:30",
  "kind": "Personnel",
  "category": "Operations",
  "type": "Engine Company",
  "resource_identifier": "E-101",
  "name_of_leader": "John Doe",
  "contact_information": "+1234567890",
  "quantity": 5,
  "department": "Fire Department",
  "departure_point_of_origin": "Station 1",
  "departure_date": "2026-03-30",
  "departure_time": "07:30",
  "departure_method_of_travel": "Fire Engine",
  "with_manifest": true,
  "incident_assignment": "Structure Protection",
  "other_qualifications": "Hazmat Certified, EMT",
  "sent_resl": false
}
```

#### Success Response (200)
```json
{
  "success": true,
  "message": "Check-in detail updated successfully",
  "data": {
    "id": 1,
    "uuid": "9d8a7f6e-5c4b-3a2d-1e0f-123456789abc",
    "ics211_record_id": 5,
    "personnel_id": 15,
    "order_request_number": "ORN-2026-001",
    "checkin_date": "2026-03-30",
    "checkin_time": "08:30:00",
    "quantity": 5,
    "other_qualifications": "Hazmat Certified, EMT",
    "personnel": {
      "id": 15,
      "uuid": "new-personnel-uuid",
      "name": "Robert Johnson",
      "serial_number": "PER-015"
    }
  }
}
```

#### Error Response (404)
```json
{
  "success": false,
  "message": "Check-in detail not found"
}
```

#### Validation Error Response (422)
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "checkin_time": ["The checkin time does not match the format H:i."]
  }
}
```

#### Side Effects
1. **Personnel Status Changes**: If `personnel_id` is updated:
   - Old personnel's status is reset to `available`
   - New personnel's status is set to `standby`
2. **Audit Logging**: Two ICS log entries are created:
   - `personnel_removed` for the old personnel
   - `personnel_added` for the new personnel
3. **No Change Optimization**: If `personnel_id` remains the same, no status changes or logs are created

#### Example Request
```bash
curl -X POST \
  'https://api.example.com/api/rul/ics/checkin/9d8a7f6e-5c4b-3a2d-1e0f-123456789abc/edit' \
  -H 'Authorization: Bearer your_token_here' \
  -H 'Content-Type: application/json' \
  -H 'Accept: application/json' \
  -d '{
    "personnel_id": 15,
    "order_request_number": "ORN-2026-001",
    "checkin_date": "2026-03-30",
    "checkin_time": "08:30",
    "kind": "Personnel",
    "category": "Operations",
    "type": "Engine Company",
    "resource_identifier": "E-101",
    "name_of_leader": "John Doe",
    "contact_information": "+1234567890",
    "quantity": 5,
    "department": "Fire Department",
    "departure_point_of_origin": "Station 1",
    "departure_date": "2026-03-30",
    "departure_time": "07:30",
    "departure_method_of_travel": "Fire Engine",
    "with_manifest": true,
    "incident_assignment": "Structure Protection",
    "other_qualifications": "Hazmat Certified, EMT"
  }'
```

---

### 5. Delete Check-in Detail

**POST** `/api/rul/ics/checkin/{uuid}/delete`

Delete a check-in detail and clean up associated data.

#### Path Parameters
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `uuid` | string | Yes | UUID of the check-in detail to delete |

#### Headers
```
Authorization: Bearer {token}
Accept: application/json
```

#### Success Response (200)
```json
{
  "success": true,
  "message": "Check-in detail deleted successfully"
}
```

#### Error Response (404)
```json
{
  "success": false,
  "message": "Check-in detail not found"
}
```

#### Side Effects
1. **Personnel Status Reset**: If the check-in detail has an associated personnel, their status is reset to `available`
2. **Audit Logging**: An ICS log entry is created with action type `personnel_removed`
3. **Cascade Deletion**: All related check-in detail histories are also deleted (database cascade)

#### Example Request
```bash
curl -X POST \
  'https://api.example.com/api/rul/ics/checkin/9d8a7f6e-5c4b-3a2d-1e0f-123456789abc/delete' \
  -H 'Authorization: Bearer your_token_here' \
  -H 'Accept: application/json'
```

---

## Data Models

### Check-in Detail Model

```typescript
interface CheckInDetail {
  id: number;
  uuid: string;
  ics211_record_id: number;
  personnel_id: number | null;
  order_request_number: string;
  checkin_date: string;           // Format: YYYY-MM-DD
  checkin_time: string;           // Format: HH:MM:SS
  kind: string;
  category: string;
  type: string;
  resource_identifier: string;
  name_of_leader: string;
  contact_information: string;
  quantity: number;
  department: string;
  departure_point_of_origin: string;
  departure_date: string;         // Format: YYYY-MM-DD
  departure_time: string;         // Format: HH:MM:SS
  departure_method_of_travel: string;
  with_manifest: boolean;
  incident_assignment: string | null;
  other_qualifications: string | null;
  sent_resl: boolean;
  status: 'pending' | 'ongoing' | 'completed';
  created_at: string;             // ISO 8601 timestamp
  updated_at: string;             // ISO 8601 timestamp
  personnel?: Personnel;          // Eager loaded relationship
}
```

### Personnel Model (Related)

```typescript
interface Personnel {
  id: number;
  uuid: string;
  name: string;
  serial_number: string;
  contact_number: string;
  department: string;
  status: 'available' | 'standby' | 'staging' | 'assigned' | 'active' | 'demobalized' | 'out_of_service';
  avatar: string | null;
  created_at: string;
  updated_at: string;
}
```

---

## Status Values

### Check-in Detail Status
- `pending`: Check-in is scheduled but not yet active
- `ongoing`: Check-in is currently in progress
- `completed`: Check-in has been completed

### Personnel Status Transitions
- `available` → `standby`: When personnel is assigned to check-in detail
- `standby` → `available`: When personnel is removed from check-in detail or check-in is deleted

---

## Common HTTP Status Codes

| Code | Description | When Used |
|------|-------------|-----------|
| 200 | OK | Successful GET, PUT, DELETE operations |
| 201 | Created | Successful POST operation creating new resource |
| 404 | Not Found | ICS record or check-in detail not found |
| 422 | Unprocessable Entity | Validation errors in request data |
| 401 | Unauthorized | Missing or invalid authentication token |
| 429 | Too Many Requests | Rate limit exceeded (60 requests/minute) |

---

## Authentication

All endpoints require authentication via Laravel Sanctum bearer token.

### Including Authentication Token
```
Authorization: Bearer your_token_here
```

### Getting a Token
Authenticate via the login endpoint to receive a bearer token:

```bash
POST /api/auth/login
{
  "email": "user@example.com",
  "password": "your_password"
}
```

Response:
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "token": "1|your_bearer_token_here",
    "user": { ... }
  }
}
```

---

## Rate Limiting

- **Limit**: 60 requests per minute per authenticated user
- **Headers**: Response includes rate limit information
  - `X-RateLimit-Limit`: Maximum requests allowed
  - `X-RateLimit-Remaining`: Remaining requests
  - `X-RateLimit-Reset`: Time when limit resets

### Rate Limit Exceeded Response (429)
```json
{
  "message": "Too Many Attempts."
}
```

---

## Error Handling

### Standard Error Response Format
```json
{
  "success": false,
  "message": "Error description",
  "errors": {
    "field_name": ["Validation error message"]
  }
}
```

### Common Validation Errors
- **Invalid date format**: `"The checkin date does not match the format Y-m-d."`
- **Invalid time format**: `"The checkin time does not match the format H:i."`
- **Missing required field**: `"The [field name] field is required."`
- **Invalid foreign key**: `"The selected personnel id is invalid."`
- **Minimum value**: `"The quantity must be at least 1."`

---

## Best Practices

### 1. Date and Time Formats
Always use ISO 8601 format for dates and 24-hour format for times:
- Date: `2026-03-30`
- Time: `08:00` (will be stored as `08:00:00`)

### 2. Personnel Assignment
- Ensure personnel exists before assigning (`personnel_id`)
- Check personnel availability status before assignment
- Personnel can only be assigned to one check-in detail at a time

### 3. Logging and Audit Trail
All create, update, and delete operations are automatically logged in the `ics_logs` table with:
- Action type
- User who performed the action
- Old and new values (for updates)
- Timestamp

### 4. Resource Identifiers
Use unique, meaningful resource identifiers that follow your organization's naming conventions (e.g., `E-101`, `BC-1`, `CR-205`)

### 5. Quantity Field
- Minimum value is 1
- Represents number of personnel in a crew or units of equipment
- Should match the actual number of resources being deployed

---

## Examples

### Complete Workflow Example

#### 1. Create ICS 211 Record
```bash
POST /api/rul/ics/create
# Returns ICS record with UUID
```

#### 2. Add Check-in Detail
```bash
POST /api/rul/ics/{icsUuid}/checkin
{
  "personnel_id": 12,
  "order_request_number": "ORN-2026-001",
  "checkin_date": "2026-03-30",
  "checkin_time": "08:00",
  ...
}
# Personnel status automatically updated to 'standby'
# Returns check-in detail with UUID
```

#### 3. Update Check-in Detail
```bash
POST /api/rul/ics/checkin/{checkInUuid}/edit
{
  "quantity": 5,  # Updated from 4 to 5
  ...
}
```

#### 4. List All Check-ins for ICS Record
```bash
GET /api/rul/ics/{icsUuid}/checkin
# Returns array of all check-in details
```

#### 5. Delete Check-in Detail
```bash
POST /api/rul/ics/checkin/{checkInUuid}/delete
# Personnel status automatically reset to 'available'
```

---

## Testing

### Using cURL

```bash
# Set your token
TOKEN="your_bearer_token_here"
API_URL="https://api.example.com/api/rul"

# List check-ins
curl -X GET \
  "${API_URL}/ics/{icsUuid}/checkin" \
  -H "Authorization: Bearer ${TOKEN}" \
  -H "Accept: application/json"

# Create check-in
curl -X POST \
  "${API_URL}/ics/{icsUuid}/checkin" \
  -H "Authorization: Bearer ${TOKEN}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d @checkin-data.json
```

### Using Postman

1. Import the API collection
2. Set up environment variables:
   - `base_url`: Your API base URL
   - `token`: Your bearer token
   - `ics_uuid`: ICS record UUID
   - `checkin_uuid`: Check-in detail UUID
3. Use the pre-configured requests

---

## Support and Contact

For API support:
- Review Laravel logs: `storage/logs/laravel.log`
- Check ICS logs table for audit trail
- Verify authentication and rate limits
- Ensure all required fields are provided

---

## Changelog

### Version 1.0.0 (March 2026)
- Initial release of Check-in Detail Management API
- Full CRUD operations for check-in details
- Automatic personnel status management
- Comprehensive audit logging
- Integration with ICS 211 records

---

*Last Updated: March 14, 2026*
