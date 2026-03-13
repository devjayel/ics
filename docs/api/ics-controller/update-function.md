# ICS Controller - Update Function API Documentation

## Endpoint: POST /api/rul/ics/{id}/edit

### Function: `update()`

### Overview
Updates an existing ICS 211 record and its associated check-in details. Handles file uploads, personnel status synchronization, and comprehensive activity logging. Supports adding, updating, and removing check-in details with automatic personnel status management.

---

## Request Details

### HTTP Method
`POST`

### URL
```
/api/rul/ics/{uuid}/edit
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

### Path Parameters
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `uuid` | string | Yes | UUID of the ICS 211 record to update |

### Request Body

#### ICS Record Fields (Optional)
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `name` | string | No | ICS operation name |
| `type` | string | No | Type of incident |
| `order_request_number` | string | No | Official order number |
| `checkin_location` | string | No | Physical check-in location |
| `region` | string | No | Geographic region |
| `remarks` | string | No | Additional notes |
| `remarks_image_attachment` | file | No | New image attachment |
| `status` | string | No | Status (pending, ongoing, completed) |

#### Check-in Details Array
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `check_in_details` | array | No | Array of check-in detail objects |

#### Check-in Detail Object Structure
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `uuid` | string | No* | UUID for updating existing detail |
| `personnel_id` | integer | No | Associated personnel ID |
| `order_request_number` | string | Yes** | Order number |
| `checkin_date` | date | Yes** | Check-in date (YYYY-MM-DD) |
| `checkin_time` | time | Yes** | Check-in time (HH:MM:SS) |
| `kind` | string | Yes** | Resource kind |
| `category` | string | Yes** | Resource category |
| `type` | string | Yes** | Resource type |
| `resource_identifier` | string | Yes** | Resource identifier |
| `name_of_leader` | string | Yes** | Leader name |
| `contact_information` | string | Yes** | Contact info |
| `quantity` | integer | Yes** | Quantity of resources |
| `department` | string | Yes** | Department |
| `departure_point_of_origin` | string | Yes** | Origin point |
| `departure_date` | date | Yes** | Departure date |
| `departure_time` | time | Yes** | Departure time |
| `departure_method_of_travel` | string | Yes** | Travel method |
| `with_manifest` | boolean | No | Has manifest (default: false) |
| `incident_assignment` | string | No | Assignment details |
| `other_qualifications` | string | No | Other qualifications |
| `sent_resl` | boolean | No | RESL sent (default: false) |

*UUID required for updating existing check-in details
**Required when creating new check-in details

### Example Request
```json
{
  "name": "Structure Fire Response - Updated",
  "status": "ongoing",
  "remarks": "Escalated to second alarm, additional resources requested",
  "check_in_details": [
    {
      "uuid": "existing-checkin-uuid-123",
      "personnel_id": 20,
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
      "incident_assignment": "Primary attack",
      "other_qualifications": "Hazmat Operations",
      "sent_resl": false
    },
    {
      "order_request_number": "ORD-2024-001",
      "checkin_date": "2024-03-01",
      "checkin_time": "11:00:00",
      "kind": "Personnel",
      "category": "Firefighting",
      "type": "Ladder Company",
      "resource_identifier": "LAD-002",
      "name_of_leader": "Lieutenant Thompson",
      "contact_information": "555-0199",
      "quantity": 3,
      "department": "City Fire Department",
      "departure_point_of_origin": "Station 2",
      "departure_date": "2024-03-01",
      "departure_time": "10:45:00",
      "departure_method_of_travel": "Ladder Truck"
    }
  ]
}
```

---

## Response Format

### Success Response (200 OK)
```json
{
  "success": true,
  "message": "ICS 211 record updated successfully",
  "data": {
    "id": 1,
    "uuid": "550e8400-e29b-41d4-a716-446655440000",
    "token": "ABC12345",
    "name": "Structure Fire Response - Updated",
    "type": "Structure Fire",
    "order_request_number": "ORD-2024-001",
    "checkin_location": "123 Main Street, Anytown",
    "region": "District 1",
    "remarks": "Escalated to second alarm, additional resources requested",
    "remarks_image_attachment": "ics/remarks/updated_image_001.jpg",
    "status": "ongoing",
    "created_at": "2024-03-01T10:30:00Z",
    "updated_at": "2024-03-01T13:15:00Z",
    "operators": [
      {
        "id": 1,
        "uuid": "123e4567-e89b-12d3-a456-426614174000",
        "name": "Chief John Doe",
        "serial_number": "RUL001",
        "department": "City Fire Department",
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
        "uuid": "existing-checkin-uuid-123",
        "personnel_id": 20,
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
        "status": "active",
        "created_at": "2024-03-01T10:30:00Z",
        "updated_at": "2024-03-01T13:15:00Z",
        "personnel": {
          "id": 20,
          "uuid": "personnel-uuid-789",
          "name": "Sergeant Wilson",
          "serial_number": "FF020",
          "status": "standby"
        }
      },
      {
        "id": 2,
        "uuid": "new-checkin-uuid-456",
        "personnel_id": null,
        "order_request_number": "ORD-2024-001",
        "checkin_date": "2024-03-01",
        "checkin_time": "11:00:00",
        "kind": "Personnel",
        "category": "Firefighting",
        "type": "Ladder Company",
        "resource_identifier": "LAD-002",
        "name_of_leader": "Lieutenant Thompson",
        "contact_information": "555-0199",
        "quantity": 3,
        "department": "City Fire Department",
        "status": "pending",
        "created_at": "2024-03-01T13:15:00Z",
        "updated_at": "2024-03-01T13:15:00Z",
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

### Validation Error Response (422 Unprocessable Entity)
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "check_in_details.1.checkin_date": [
      "The check_in_details.1.checkin_date field is required."
    ]
  }
}
```

---

## Business Logic

### Update Behavior
1. **Partial Updates**: Only provided fields are updated
2. **File Handling**: New image upload replaces existing file
3. **Check-in Management**:
   - Items with UUID are updated
   - Items without UUID are created as new
   - Items not in request are deleted

### Personnel Status Management
- **Assignment Changes**: Personnel status updates automatically
- **Removal**: Removed personnel status becomes 'available'
- **Addition**: Added personnel status becomes 'standby'
- **Reassignment**: Handles personnel switching between check-ins

### Activity Logging
All changes are automatically logged:
- ICS record modifications
- Personnel additions/removals
- Status changes
- Before/after values captured in JSON

### Notification System
- FCM notifications sent to affected personnel
- Real-time status updates via Pusher channels

---

## Check-in Detail Management

### Update Existing Detail
```json
{
  "uuid": "existing-uuid-123",
  "personnel_id": 25,
  ...other fields
}
```
- Requires UUID of existing check-in detail
- Updates all provided fields
- Handles personnel reassignment

### Create New Detail
```json
{
  "personnel_id": 30,
  "order_request_number": "ORD-2024-001",
  ...required fields
}
```
- No UUID provided = new record
- All required fields must be provided
- Automatically assigns UUID

### Delete Detail
- Details not included in request array are deleted
- Associated personnel status reset to 'available'
- Deletion logged in activity trail

---

## File Upload Handling

### Image Replacement
- New `remarks_image_attachment` replaces existing file
- Old file automatically deleted from storage
- File path updated in database
- Supports standard image formats

### File Storage Path
```
storage/app/public/ics/remarks/
├── updated_image_001.jpg
├── incident_diagram_002.png
└── site_layout_003.pdf
```

---

## Usage Examples

### Update ICS Information Only
```bash
curl -X POST \
  https://api.example.com/api/rul/ics/550e8400-e29b-41d4-a716-446655440000/edit \
  -H "Authorization: Bearer your_access_token" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Updated Incident Name",
    "status": "ongoing",
    "remarks": "Situation escalated, additional resources deployed"
  }'
```

### Update with New Check-in Detail
```javascript
const updateData = {
  status: "ongoing",
  check_in_details: [
    // Keep existing detail (with UUID)
    {
      uuid: "existing-checkin-123",
      personnel_id: 15,
      quantity: 4,
      status: "active"
      // ... other required fields
    },
    // Add new detail (no UUID)
    {
      personnel_id: 22,
      order_request_number: "ORD-2024-001",
      checkin_date: "2024-03-01",
      checkin_time: "11:30:00",
      kind: "Equipment",
      category: "Medical",
      type: "Ambulance"
      // ... other required fields
    }
  ]
};

const response = await axios.post(`/api/rul/ics/${uuid}/edit`, updateData, {
  headers: {
    'Authorization': `Bearer ${token}`
  }
});
```

### File Upload with Update
```javascript
const formData = new FormData();
formData.append('status', 'ongoing');
formData.append('remarks', 'Updated site information');
formData.append('remarks_image_attachment', fileInput.files[0]);

const response = await axios.post(`/api/rul/ics/${uuid}/edit`, formData, {
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'multipart/form-data'
  }
});
```

---

## Personnel Impact Scenarios

### Scenario 1: Personnel Reassignment
- Previous: PersonnelA (ID: 15) assigned
- Update: PersonnelB (ID: 20) assigned
- Result: PersonnelA → 'available', PersonnelB → 'standby'

### Scenario 2: Check-in Removal
- Previous: Check-in with PersonnelA exists
- Update: Check-in not included in request
- Result: PersonnelA → 'available', check-in deleted

### Scenario 3: New Assignment
- Previous: No personnel assigned
- Update: PersonnelC (ID: 25) assigned
- Result: PersonnelC → 'standby'

---

## Related Endpoints
- `GET /api/rul/ics/{id}/show` - View current record
- `POST /api/rul/ics/{id}/status/{status}` - Update status only
- `POST /api/rul/ics/{id}/delete` - Delete record
- `GET /api/rul/ics/{icsUuid}/logs` - View change history

## Notes
- Supports partial updates - only send changed fields
- Complex personnel status management handled automatically
- File uploads replace existing attachments
- Comprehensive activity logging for audit trail
- Real-time notifications sent to affected personnel
- Check-in details use UUID-based update/create logic
- Validation ensures data integrity throughout update process