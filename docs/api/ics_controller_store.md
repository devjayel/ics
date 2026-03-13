# ICS Controller - Store Function (Create) API Documentation

## Overview
Creates a new ICS 211 record with check-in details and automatically assigns the creator as an operator.

## Endpoint
```
POST /api/rul/ics/create
```

## Authentication
- **Required**: Yes
- **Middleware**: `rul.auth`, `throttle:60,1`
- **Access Level**: Resource Unit Leader (RUL)

## Request

### Headers
```
Authorization: Bearer {token}
Content-Type: multipart/form-data (if uploading files) or application/json
Accept: application/json
```

### Body Parameters

#### Required Fields

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| name | string | Yes | Name/title of the incident |
| type | string | Yes | Type of incident (fire, flood, rescue, etc.) |
| order_request_number | string | Yes | Official order or request number |
| checkin_location | string | Yes | Physical location for check-in |

#### Optional Fields

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| region | string | No | Geographic region or area |
| remarks | text | No | Additional notes or comments |
| remarks_image_attachment | file | No | Image file attachment |
| status | string | No | Initial status (default: 'pending') |
| check_in_details | array | No | Array of check-in detail objects |

#### Check-In Details Structure

When providing `check_in_details`, each item should contain:

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| personnel_id | integer | No | Reference to personnel ID (nullable) |
| order_request_number | string | Yes | Order or request number for this entry |
| checkin_date | date | Yes | Date of check-in (YYYY-MM-DD) |
| checkin_time | time | Yes | Time of check-in (HH:MM:SS) |
| kind | string | Yes | Kind of resource (Personnel, Equipment, etc.) |
| category | string | Yes | Category (Personnel, Equipment, etc.) |
| type | string | Yes | Specific type within category |
| resource_identifier | string | Yes | Identifier for the resource |
| name_of_leader | string | Yes | Name of group/unit leader |
| contact_information | string | Yes | Contact details for the leader |
| quantity | integer | Yes | Number of personnel or items |
| department | string | Yes | Originating department/agency |
| departure_point_of_origin | string | Yes | Where resource came from |
| departure_date | date | Yes | Date of departure from origin |
| departure_time | time | Yes | Time of departure from origin |
| departure_method_of_travel | string | Yes | How they traveled |
| with_manifest | boolean | No | Whether resource comes with manifest (default: false) |
| incident_assignment | string | No | Specific incident assignment |
| other_qualifications | string | No | Additional qualifications or notes |
| sent_resl | boolean | No | Whether sent to RESL (default: false) |

### Example Request Body (JSON)

```json
{
  "name": "Wildfire Response - Pine Valley",
  "type": "wildfire",
  "order_request_number": "ORN-2025-001",
  "checkin_location": "Cedar Creek Fire Station",
  "region": "Northern District",
  "remarks": "High priority incident requiring immediate response",
  "status": "pending",
  "check_in_details": [
    {
      "personnel_id": 1,
      "order_request_number": "ORN-2025-001-A",
      "checkin_date": "2025-03-13",
      "checkin_time": "08:30:00",
      "kind": "Personnel",
      "category": "Personnel",
      "type": "Fire Suppression",
      "resource_identifier": "ENG-01",
      "name_of_leader": "Captain Smith",
      "contact_information": "+1234567890",
      "quantity": 4,
      "department": "Metro Fire Department",
      "departure_point_of_origin": "Station 15",
      "departure_date": "2025-03-13",
      "departure_time": "08:00:00",
      "departure_method_of_travel": "Fire Engine",
      "with_manifest": true,
      "incident_assignment": "Structure Protection",
      "other_qualifications": "Hazmat certified",
      "sent_resl": false
    }
  ]
}
```

## Response

### Success Response (201)
```json
{
  "success": true,
  "message": "ICS 211 record created successfully",
  "data": {
    "id": 1,
    "uuid": "550e8400-e29b-41d4-a716-446655440000",
    "token": "ABC12345",
    "name": "Wildfire Response - Pine Valley",
    "type": "wildfire",
    "order_request_number": "ORN-2025-001",
    "checkin_location": "Cedar Creek Fire Station",
    "region": "Northern District",
    "remarks": "High priority incident requiring immediate response",
    "remarks_image_attachment": "ics/remarks/image_123.jpg",
    "status": "pending",
    "created_at": "2025-03-13T08:30:00Z",
    "updated_at": "2025-03-13T08:30:00Z",
    "operators": [
      {
        "id": 1,
        "uuid": "750e8400-e29b-41d4-a716-446655440000",
        "name": "Captain Jane Smith",
        "contact_number": "+1234567890",
        "serial_number": "RUL-2025-001",
        "department": "Metro Fire Department",
        "certificates": []
      }
    ],
    "check_in_details": [
      {
        "id": 1,
        "uuid": "950e8400-e29b-41d4-a716-446655440000",
        "order_request_number": "ORN-2025-001-A",
        "checkin_date": "2025-03-13",
        "checkin_time": "08:30:00",
        "kind": "Personnel",
        "category": "Personnel",
        "type": "Fire Suppression",
        "resource_identifier": "ENG-01",
        "name_of_leader": "Captain Smith",
        "contact_information": "+1234567890",
        "quantity": 4,
        "department": "Metro Fire Department",
        "departure_point_of_origin": "Station 15",
        "departure_date": "2025-03-13",
        "departure_time": "08:00:00",
        "departure_method_of_travel": "Fire Engine",
        "with_manifest": true,
        "incident_assignment": "Structure Protection",
        "other_qualifications": "Hazmat certified",
        "sent_resl": false,
        "status": "pending",
        "personnel": {
          "id": 1,
          "uuid": "a50e8400-e29b-41d4-a716-446655440000",
          "name": "John Firefighter",
          "contact_number": "+1234567891",
          "serial_number": "PER-2025-001",
          "department": "Metro Fire Department",
          "status": "standby"
        }
      }
    ]
  }
}
```

### Validation Error Response (422)
```json
{
  "success": false,
  "message": "The given data was invalid",
  "errors": {
    "name": ["The name field is required."],
    "type": ["The type field is required."],
    "check_in_details.0.checkin_date": ["The checkin date field is required."]
  }
}
```

### Error Responses

#### Unauthorized (401)
```json
{
  "success": false,
  "message": "Unauthenticated"
}
```

#### Too Many Requests (429)
```json
{
  "success": false,
  "message": "Too Many Attempts"
}
```

## Implementation Details

### Automatic Field Generation
- **UUID**: Automatically generated unique identifier
- **Token**: Random 8-character string for access control
- **Creator as Operator**: User who creates the ICS is automatically assigned as operator

### File Upload Handling
- `remarks_image_attachment` is stored in `ics/remarks` directory
- Files are stored in the `public` storage disk
- File validation should be implemented in the request class

### Personnel Status Updates
When personnel_id is provided in check-in details:
- Personnel status is automatically updated to 'standby'
- Activity is logged in the ics_logs table
- Push notifications can be triggered (currently commented)

### Automatic Logging
The system automatically logs:
- ICS record creation
- Personnel additions
- All activities with detailed descriptions

### Database Transactions
The creation process involves multiple database operations:
1. Create ICS 211 record
2. Attach operator relationship
3. Create check-in details
4. Update personnel statuses
5. Create log entries

## Business Logic

### Status Values
- Default status is 'pending'
- Valid statuses: 'pending', 'ongoing', 'completed'

### Personnel Assignment
- Personnel can be assigned to check-in details
- Personnel status automatically changes to 'standby'
- Multiple personnel can be assigned to the same ICS record

### Access Control
- Creator becomes an operator automatically
- Other RULs can join using the generated token
- Operators can manage the ICS record

## Use Cases

### Emergency Response
- Quick incident setup during emergencies
- Immediate personnel assignment
- Real-time status tracking

### Planned Operations
- Pre-planned incident management
- Resource allocation and tracking
- Multi-agency coordination

### Training Exercises
- Drill and exercise management
- Personnel training tracking
- Scenario-based operations

## Security Considerations

### File Upload Security
- Validate file types and sizes
- Scan uploads for malware
- Store files outside web root when possible

### Data Validation
- All input is validated through form requests
- SQL injection protection via Eloquent ORM
- XSS protection through proper output escaping

### Access Logging
- All creation activities are logged
- Audit trail maintained
- User accountability ensured

## Testing Examples

### cURL Request
```bash
curl -X POST \
  "https://yourdomain.com/api/rul/ics/create" \
  -H "Authorization: Bearer your-auth-token" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Test Incident",
    "type": "fire",
    "order_request_number": "TEST-001",
    "checkin_location": "Test Station"
  }'
```

### JavaScript/Axios
```javascript
const incidentData = {
  name: "Emergency Response Exercise",
  type: "exercise",
  order_request_number: "EX-2025-001",
  checkin_location: "Training Center",
  region: "Training District",
  status: "pending"
};

const response = await axios.post('/api/rul/ics/create', incidentData, {
  headers: {
    'Authorization': 'Bearer ' + token,
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
});

console.log('Created ICS:', response.data.data);
```

### PHP/Guzzle
```php
$client = new GuzzleHttp\Client();
$response = $client->request('POST', 'https://yourdomain.com/api/rul/ics/create', [
    'headers' => [
        'Authorization' => 'Bearer ' . $token,
        'Content-Type' => 'application/json',
        'Accept' => 'application/json'
    ],
    'json' => [
        'name' => 'Test Incident',
        'type' => 'fire',
        'order_request_number' => 'TEST-001',
        'checkin_location' => 'Test Station'
    ]
]);

$data = json_decode($response->getBody(), true);
```

## Related Endpoints
- `GET /api/rul/ics` - List all ICS records
- `GET /api/rul/ics/{id}/show` - Get specific ICS record
- `POST /api/rul/ics/{id}/edit` - Update ICS record
- `POST /api/rul/ics/join` - Join ICS as operator