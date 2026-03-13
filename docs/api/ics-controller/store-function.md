# ICS Controller - Store Function API Documentation

## Endpoint: POST /api/rul/ics/create

### Function: `store()`

### Overview
Creates a new ICS 211 record with optional check-in details, file attachments, and automatic operator assignment. Automatically logs the creation action and updates personnel status.

---

## Request Details

### HTTP Method
`POST`

### URL
```
/api/rul/ics/create
```

### Authentication
- **Required**: Yes
- **Middleware**: `rul.auth`
- **Rate Limiting**: 60 requests per minute

### Headers
```
Authorization: Bearer {token}
Content-Type: multipart/form-data
or
Content-Type: application/json
```

### Request Body

#### Required Fields
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `name` | string | Yes | ICS operation name |
| `type` | string | Yes | Type of incident |
| `order_request_number` | string | Yes | Official order number |
| `checkin_location` | string | Yes | Physical check-in location |

#### Optional Fields
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `region` | string | No | Geographic region |
| `remarks` | string | No | Additional notes |
| `remarks_image_attachment` | file | No | Image attachment |
| `status` | string | No | Initial status (default: 'pending') |
| `check_in_details` | array | No | Array of check-in detail objects |

#### Check-in Details Structure
Each item in `check_in_details` array:
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `personnel_id` | integer | No | Associated personnel ID |
| `order_request_number` | string | Yes | Order number |
| `checkin_date` | date | Yes | Check-in date (YYYY-MM-DD) |
| `checkin_time` | time | Yes | Check-in time (HH:MM:SS) |
| `kind` | string | Yes | Resource kind |
| `category` | string | Yes | Resource category |
| `type` | string | Yes | Resource type |
| `resource_identifier` | string | Yes | Resource identifier |
| `name_of_leader` | string | Yes | Leader name |
| `contact_information` | string | Yes | Contact info |
| `quantity` | integer | Yes | Quantity of resources |
| `department` | string | Yes | Department |
| `departure_point_of_origin` | string | Yes | Origin point |
| `departure_date` | date | Yes | Departure date |
| `departure_time` | time | Yes | Departure time |
| `departure_method_of_travel` | string | Yes | Travel method |
| `with_manifest` | boolean | No | Has manifest (default: false) |
| `incident_assignment` | string | No | Assignment details |
| `other_qualifications` | string | No | Other qualifications |
| `sent_resl` | boolean | No | RESL sent (default: false) |

### Example Request (JSON)
```json
{
  "name": "Structure Fire Response",
  "type": "Structure Fire",
  "order_request_number": "ORD-2024-001",
  "checkin_location": "123 Main Street, Anytown",
  "region": "District 1",
  "remarks": "Multi-story residential building",
  "status": "pending",
  "check_in_details": [
    {
      "personnel_id": 15,
      "order_request_number": "ORD-2024-001",
      "checkin_date": "2024-03-01",
      "checkin_time": "10:30:00",
      "kind": "Personnel",
      "category": "Firefighting",
      "type": "Engine Company",
      "resource_identifier": "ENG-001",
      "name_of_leader": "Captain Smith",
      "contact_information": "555-0123",
      "quantity": 4,
      "department": "Fire Department",
      "departure_point_of_origin": "Fire Station 1",
      "departure_date": "2024-03-01",
      "departure_time": "10:15:00",
      "departure_method_of_travel": "Fire Engine",
      "with_manifest": true,
      "incident_assignment": "Primary suppression",
      "other_qualifications": "Hazmat certified",
      "sent_resl": false
    }
  ]
}
```

---

## Response Format

### Success Response (201 Created)
```json
{
  "success": true,
  "message": "ICS 211 record created successfully",
  "data": {
    "id": 1,
    "uuid": "550e8400-e29b-41d4-a716-446655440000",
    "token": "ABC12345",
    "name": "Structure Fire Response",
    "type": "Structure Fire",
    "order_request_number": "ORD-2024-001",
    "checkin_location": "123 Main Street, Anytown",
    "region": "District 1",
    "remarks": "Multi-story residential building",
    "remarks_image_attachment": "ics/remarks/image_001.jpg",
    "status": "pending",
    "created_at": "2024-03-01T10:30:00Z",
    "updated_at": "2024-03-01T10:30:00Z",
    "operators": [
      {
        "id": 1,
        "uuid": "123e4567-e89b-12d3-a456-426614174000",
        "name": "John Doe",
        "serial_number": "RUL001",
        "department": "Fire Department",
        "certificates": [
          {
            "id": 1,
            "uuid": "cert-uuid-123",
            "certificate_name": "ICS-300",
            "file_path": "certificates/ics300_cert.pdf"
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
        "name_of_leader": "Captain Smith",
        "contact_information": "555-0123",
        "quantity": 4,
        "department": "Fire Department",
        "departure_point_of_origin": "Fire Station 1",
        "departure_date": "2024-03-01",
        "departure_time": "10:15:00",
        "departure_method_of_travel": "Fire Engine",
        "with_manifest": true,
        "incident_assignment": "Primary suppression",
        "other_qualifications": "Hazmat certified",
        "sent_resl": false,
        "status": "pending",
        "created_at": "2024-03-01T10:30:00Z",
        "updated_at": "2024-03-01T10:30:00Z",
        "personnel": {
          "id": 15,
          "uuid": "personnel-uuid-456",
          "name": "Mike Johnson",
          "serial_number": "FF001",
          "status": "standby"
        }
      }
    ]
  }
}
```

### Validation Error Response (422 Unprocessable Entity)
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "name": [
      "The name field is required."
    ],
    "check_in_details.0.checkin_date": [
      "The check_in_details.0.checkin_date field is required."
    ]
  }
}
```

### Error Response (401 Unauthorized)
```json
{
  "message": "Unauthenticated.",
  "status": 401
}
```

---

## File Upload

### Image Attachment
- **Field Name**: `remarks_image_attachment`
- **Accepted Types**: Images (jpg, png, gif, etc.)
- **Storage Location**: `storage/app/public/ics/remarks/`
- **Max Size**: Typically 10MB (configured in Laravel)

### Example with File Upload (FormData)
```javascript
const formData = new FormData();
formData.append('name', 'Structure Fire Response');
formData.append('type', 'Structure Fire');
formData.append('order_request_number', 'ORD-2024-001');
formData.append('checkin_location', '123 Main Street');
formData.append('remarks_image_attachment', fileInput.files[0]);

const response = await axios.post('/api/rul/ics/create', formData, {
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'multipart/form-data'
  }
});
```

---

## Business Logic

### Automatic Operations
1. **UUID Generation**: Automatic UUID assignment for external references
2. **Token Generation**: Random 8-character invitation token
3. **Operator Assignment**: Creating RUL automatically becomes operator
4. **Personnel Status Update**: Assigned personnel status becomes 'standby'
5. **Activity Logging**: Creation logged in `ics_logs` table
6. **Personnel Activity Logging**: Personnel assignments logged

### Status Management
- **Default Status**: 'pending' if not specified
- **Personnel Impact**: Assigned personnel status updated to 'standby'
- **Notification**: FCM notifications sent to assigned personnel (commented out)

### Data Integrity
- Check-in details linked to parent ICS record
- Personnel assignments validated
- File uploads stored securely

---

## Usage Examples

### Basic ICS Creation
```bash
curl -X POST \
  https://api.example.com/api/rul/ics/create \
  -H "Authorization: Bearer your_access_token" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Medical Emergency Response",
    "type": "Medical Emergency",
    "order_request_number": "MED-2024-001",
    "checkin_location": "City Hospital"
  }'
```

### With Check-in Details
```javascript
const icsData = {
  name: "Wildfire Suppression",
  type: "Wildfire",
  order_request_number: "WILD-2024-002",
  checkin_location: "Incident Command Post",
  region: "Northern District",
  check_in_details: [
    {
      personnel_id: 10,
      order_request_number: "WILD-2024-002",
      checkin_date: "2024-03-01",
      checkin_time: "08:00:00",
      kind: "Personnel",
      category: "Firefighting",
      type: "Hotshot Crew",
      resource_identifier: "HS-001",
      name_of_leader: "Crew Boss Johnson",
      contact_information: "555-0199",
      quantity: 20,
      department: "Forest Service",
      departure_point_of_origin: "Base Camp Alpha",
      departure_date: "2024-03-01",
      departure_time: "07:30:00",
      departure_method_of_travel: "Crew Transport"
    }
  ]
};

const response = await axios.post('/api/rul/ics/create', icsData, {
  headers: {
    'Authorization': `Bearer ${token}`
  }
});
```

---

## Related Endpoints
- `GET /api/rul/ics` - List all ICS records
- `GET /api/rul/ics/{id}/show` - View created record
- `POST /api/rul/ics/{id}/edit` - Update record
- `POST /api/rul/ics/join` - Join record as operator

## Notes
- Creator automatically becomes first operator
- Personnel status automatically updated when assigned
- Activity logging provides audit trail
- File uploads stored in public disk
- Token enables secure sharing with other RULs
- Check-in details are optional but commonly included
- Validation ensures data integrity and completeness