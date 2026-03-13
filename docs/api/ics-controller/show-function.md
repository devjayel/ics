# ICS Controller - Show Function API Documentation

## Endpoint: GET /api/rul/ics/{id}/show

### Function: `show()`

### Overview
Retrieves detailed information for a specific ICS 211 record identified by its UUID, including all associated operators, certifications, and check-in details with personnel information.

---

## Request Details

### HTTP Method
`GET`

### URL
```
/api/rul/ics/{uuid}/show
```

### Authentication
- **Required**: Yes
- **Middleware**: `rul.auth`
- **Rate Limiting**: 60 requests per minute

### Headers
```
Authorization: Bearer {token}
Content-Type: application/json
```

### Path Parameters
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `uuid` | string | Yes | UUID of the ICS 211 record to retrieve |

### Example Request URL
```
/api/rul/ics/550e8400-e29b-41d4-a716-446655440000/show
```

---

## Response Format

### Success Response (200 OK)
```json
{
  "success": true,
  "data": {
    "id": 1,
    "uuid": "550e8400-e29b-41d4-a716-446655440000",
    "token": "ABC12345",
    "name": "Structure Fire Response",
    "type": "Structure Fire",
    "order_request_number": "ORD-2024-001",
    "checkin_location": "123 Main Street, Anytown",
    "region": "District 1",
    "remarks": "Multi-story residential building with possible entrapment",
    "remarks_image_attachment": "ics/remarks/structure_fire_layout.jpg",
    "status": "ongoing",
    "created_at": "2024-03-01T10:30:00Z",
    "updated_at": "2024-03-01T12:45:00Z",
    "operators": [
      {
        "id": 1,
        "uuid": "123e4567-e89b-12d3-a456-426614174000",
        "name": "Chief John Doe",
        "serial_number": "RUL001",
        "department": "City Fire Department",
        "contact_number": "555-0101",
        "avatar": "avatars/chief_doe.jpg",
        "certificates": [
          {
            "id": 1,
            "uuid": "cert-uuid-123",
            "certificate_name": "ICS-300 Intermediate ICS",
            "file_path": "certificates/ics300_cert_doe.pdf",
            "created_at": "2024-01-15T09:00:00Z"
          },
          {
            "id": 2,
            "uuid": "cert-uuid-456",
            "certificate_name": "Advanced Incident Management",
            "file_path": "certificates/advanced_mgmt_doe.pdf",
            "created_at": "2024-02-10T14:30:00Z"
          }
        ]
      },
      {
        "id": 3,
        "uuid": "789e0123-e45f-67g8-h901-234567890abc",
        "name": "Captain Jane Smith",
        "serial_number": "RUL003",
        "department": "County Emergency Services",
        "contact_number": "555-0202",
        "avatar": "avatars/capt_smith.jpg",
        "certificates": [
          {
            "id": 5,
            "uuid": "cert-uuid-789",
            "certificate_name": "ICS-200 Basic ICS",
            "file_path": "certificates/ics200_cert_smith.pdf",
            "created_at": "2024-01-20T11:00:00Z"
          }
        ]
      }
    ],
    "check_in_details": [
      {
        "id": 1,
        "uuid": "checkin-uuid-123",
        "personnel_id": 15,
        "order_request_number": "ORD-2024-001",
        "checkin_date": "2024-03-01",
        "checkin_time": "10:30:00",
        "kind": "Personnel",
        "category": "Firefighting",
        "type": "Engine Company",
        "resource_identifier": "ENG-001",
        "name_of_leader": "Captain Rodriguez",
        "contact_information": "555-0123",
        "quantity": 4,
        "department": "City Fire Department",
        "departure_point_of_origin": "Station 1",
        "departure_date": "2024-03-01",
        "departure_time": "10:15:00",
        "departure_method_of_travel": "Fire Engine",
        "with_manifest": true,
        "incident_assignment": "Primary attack line",
        "other_qualifications": "Hazmat Operations",
        "sent_resl": false,
        "status": "active",
        "created_at": "2024-03-01T10:30:00Z",
        "updated_at": "2024-03-01T11:15:00Z",
        "personnel": {
          "id": 15,
          "uuid": "personnel-uuid-456",
          "name": "Firefighter Mike Johnson",
          "serial_number": "FF001",
          "contact_number": "555-0155",
          "department": "City Fire Department",
          "status": "active",
          "avatar": "personnel/avatars/ff_johnson.jpg",
          "created_at": "2024-01-10T08:00:00Z",
          "updated_at": "2024-03-01T11:15:00Z"
        }
      },
      {
        "id": 2,
        "uuid": "checkin-uuid-789",
        "personnel_id": null,
        "order_request_number": "ORD-2024-001",
        "checkin_date": "2024-03-01",
        "checkin_time": "10:45:00",
        "kind": "Equipment",
        "category": "Emergency Medical",
        "type": "Ambulance",
        "resource_identifier": "AMB-005",
        "name_of_leader": "Paramedic Thompson",
        "contact_information": "555-0198",
        "quantity": 1,
        "department": "County EMS",
        "departure_point_of_origin": "Hospital Base",
        "departure_date": "2024-03-01",
        "departure_time": "10:30:00",
        "departure_method_of_travel": "Emergency Vehicle",
        "with_manifest": false,
        "incident_assignment": "Medical support standby",
        "other_qualifications": "Advanced Life Support",
        "sent_resl": true,
        "status": "staging",
        "created_at": "2024-03-01T10:45:00Z",
        "updated_at": "2024-03-01T10:45:00Z",
        "personnel": null
      }
    ]
  }
}
```

### Error Response (404 Not Found)
```json
{
  "success": false,
  "message": "ICS 211 record not found"
}
```

### Error Response (401 Unauthorized)
```json
{
  "message": "Unauthenticated.",
  "status": 401
}
```

### Error Response (403 Forbidden)
```json
{
  "message": "Access denied. RUL authentication required.",
  "status": 403
}
```

---

## Response Fields

### Root Response
| Field | Type | Description |
|-------|------|-------------|
| `success` | boolean | Indicates request success |
| `data` | object | Complete ICS record with relationships |

### ICS Record Fields
| Field | Type | Description |
|-------|------|-------------|
| `id` | integer | Internal database ID |
| `uuid` | string | Unique external identifier |
| `token` | string | Invitation token for joining |
| `name` | string | ICS operation name |
| `type` | string | Type of incident |
| `order_request_number` | string | Official order number |
| `checkin_location` | string | Physical check-in location |
| `region` | string | Geographic region |
| `remarks` | string | Additional notes |
| `remarks_image_attachment` | string | Path to attached image |
| `status` | string | Current status |
| `created_at` | datetime | Creation timestamp |
| `updated_at` | datetime | Last update timestamp |
| `operators` | array | Associated RUL operators |
| `check_in_details` | array | Check-in records |

### Operator Object
| Field | Type | Description |
|-------|------|-------------|
| `id` | integer | Operator RUL ID |
| `uuid` | string | Operator UUID |
| `name` | string | Full name |
| `serial_number` | string | RUL identifier |
| `department` | string | Department/agency |
| `contact_number` | string | Phone number |
| `avatar` | string | Profile picture path |
| `certificates` | array | Training certifications |

### Certificate Object
| Field | Type | Description |
|-------|------|-------------|
| `id` | integer | Certificate ID |
| `uuid` | string | Certificate UUID |
| `certificate_name` | string | Certificate name/title |
| `file_path` | string | Document file path |
| `created_at` | datetime | Upload timestamp |

### Check-in Detail Object
| Field | Type | Description |
|-------|------|-------------|
| `id` | integer | Check-in detail ID |
| `uuid` | string | Check-in UUID |
| `personnel_id` | integer/null | Associated personnel ID |
| `order_request_number` | string | Order number |
| `checkin_date` | date | Check-in date |
| `checkin_time` | time | Check-in time |
| `kind` | string | Resource kind |
| `category` | string | Resource category |
| `type` | string | Resource type |
| `resource_identifier` | string | Resource ID |
| `name_of_leader` | string | Leader name |
| `contact_information` | string | Contact details |
| `quantity` | integer | Number of resources |
| `department` | string | Originating department |
| `departure_point_of_origin` | string | Origin location |
| `departure_date` | date | Departure date |
| `departure_time` | time | Departure time |
| `departure_method_of_travel` | string | Transportation method |
| `with_manifest` | boolean | Has manifest |
| `incident_assignment` | string | Specific assignment |
| `other_qualifications` | string | Additional quals |
| `sent_resl` | boolean | RESL status |
| `status` | string | Current operational status |
| `personnel` | object/null | Associated personnel details |

### Personnel Object
| Field | Type | Description |
|-------|------|-------------|
| `id` | integer | Personnel ID |
| `uuid` | string | Personnel UUID |
| `name` | string | Full name |
| `serial_number` | string | Personnel identifier |
| `contact_number` | string | Phone number |
| `department` | string | Department |
| `status` | string | Current status |
| `avatar` | string | Profile picture path |

---

## Usage Examples

### cURL Request
```bash
curl -X GET \
  https://api.example.com/api/rul/ics/550e8400-e29b-41d4-a716-446655440000/show \
  -H "Authorization: Bearer your_access_token" \
  -H "Content-Type: application/json"
```

### JavaScript (Axios)
```javascript
const uuid = '550e8400-e29b-41d4-a716-446655440000';

try {
  const response = await axios.get(`/api/rul/ics/${uuid}/show`, {
    headers: {
      'Authorization': `Bearer ${token}`
    }
  });

  const icsRecord = response.data.data;
  console.log(`ICS: ${icsRecord.name}`);
  console.log(`Status: ${icsRecord.status}`);
  console.log(`Operators: ${icsRecord.operators.length}`);
  console.log(`Check-ins: ${icsRecord.check_in_details.length}`);
} catch (error) {
  if (error.response?.status === 404) {
    console.error('ICS record not found');
  }
}
```

### PHP
```php
$uuid = '550e8400-e29b-41d4-a716-446655440000';

try {
    $response = $guzzle->get("/api/rul/ics/{$uuid}/show", [
        'headers' => [
            'Authorization' => 'Bearer ' . $token
        ]
    ]);

    $icsRecord = json_decode($response->getBody())->data;

    echo "ICS Name: " . $icsRecord->name . "\n";
    echo "Status: " . $icsRecord->status . "\n";
    echo "Operators: " . count($icsRecord->operators) . "\n";

} catch (GuzzleHttp\Exception\ClientException $e) {
    if ($e->getCode() === 404) {
        echo "ICS record not found\n";
    }
}
```

---

## Business Logic

### Data Loading
- Uses eager loading to prevent N+1 queries
- Loads complete operator information with certificates
- Loads all check-in details with associated personnel
- Returns comprehensive view for detailed analysis

### Access Control
- Requires RUL authentication
- No additional permission checks
- All authenticated RULs can view any ICS record

### UUID Lookup
- Uses UUID instead of internal ID for security
- Prevents enumeration attacks
- Enables secure external references

---

## Common Use Cases

### Incident Command Dashboard
```javascript
// Load full incident details for command dashboard
const incident = await getIcsDetails(incidentUuid);
displayIncidentOverview(incident);
```

### Resource Status Review
```javascript
// Check resource status and assignments
const response = await axios.get(`/api/rul/ics/${uuid}/show`);
const resources = response.data.data.check_in_details;
updateResourceStatus(resources);
```

### Personnel Tracking
```javascript
// Track personnel assignments
const ics = await getIcsDetails(uuid);
const assignedPersonnel = ics.check_in_details
  .filter(detail => detail.personnel)
  .map(detail => detail.personnel);
```

---

## Related Endpoints
- `GET /api/rul/ics` - List all ICS records
- `GET /api/rul/ics/search` - Search ICS records
- `POST /api/rul/ics/{id}/edit` - Update ICS record
- `POST /api/rul/ics/{id}/status/{status}` - Update status
- `GET /api/rul/ics/{icsUuid}/logs` - View activity logs

## Notes
- Returns complete nested data structure
- UUID-based lookup for security
- Includes operator certificates and personnel details
- Suitable for detailed incident management interfaces
- 404 response when UUID not found
- No pagination - returns complete record structure
- Personnel field can be null for non-personnel resources