# ICS Controller - Update Status Function API Documentation

## Endpoint: POST /api/rul/ics/{id}/status/{status}

### Function: `updateStatus()`

### Overview
Updates the operational status of an ICS 211 record with optional remarks. Automatically manages personnel status when incidents are completed and logs all status changes for audit trail.

---

## Request Details

### HTTP Method
`POST`

### URL
```
/api/rul/ics/{uuid}/status/{status}
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
| `uuid` | string | Yes | UUID of the ICS 211 record to update |
| `status` | string | Yes | New status value |

### Valid Status Values
| Status | Description |
|--------|-------------|
| `pending` | Initial state, not yet active |
| `ongoing` | Active incident with deployed resources |
| `completed` | Incident resolved, resources released |

### Request Body
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `remarks` | string | No | Additional notes about status change |

### Example Request URLs
```
POST /api/rul/ics/550e8400-e29b-41d4-a716-446655440000/status/ongoing
POST /api/rul/ics/550e8400-e29b-41d4-a716-446655440000/status/completed
POST /api/rul/ics/550e8400-e29b-41d4-a716-446655440000/status/pending
```

### Example Request Body
```json
{
  "remarks": "All personnel accounted for, incident contained"
}
```

---

## Response Format

### Success Response (200 OK)
```json
{
  "success": true,
  "message": "ICS 211 record status updated successfully",
  "data": {
    "id": 1,
    "uuid": "550e8400-e29b-41d4-a716-446655440000",
    "token": "ABC12345",
    "name": "Structure Fire Response",
    "type": "Structure Fire",
    "order_request_number": "ORD-2024-001",
    "checkin_location": "123 Main Street, Anytown",
    "region": "District 1",
    "remarks": "All personnel accounted for, incident contained",
    "remarks_image_attachment": "ics/remarks/incident_layout.jpg",
    "status": "completed",
    "created_at": "2024-03-01T10:30:00Z",
    "updated_at": "2024-03-01T15:45:00Z",
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
        "status": "active",
        "created_at": "2024-03-01T10:30:00Z",
        "updated_at": "2024-03-01T12:15:00Z",
        "personnel": {
          "id": 15,
          "uuid": "personnel-uuid-456",
          "name": "Firefighter Mike Johnson",
          "serial_number": "FF001",
          "contact_number": "555-0155",
          "department": "City Fire Department",
          "status": "available",
          "avatar": "personnel/avatars/ff_johnson.jpg"
        }
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

### Error Response (400 Bad Request - Invalid Status)
```json
{
  "success": false,
  "message": "Invalid status provided"
}
```

### Validation Error Response (422 Unprocessable Entity)
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "remarks": [
      "The remarks field must be a string."
    ]
  }
}
```

---

## Business Logic

### Status Transitions
- **pending → ongoing**: Incident activation
- **ongoing → completed**: Incident resolution
- **completed → pending**: Incident reactivation (rare)
- **Any valid transition**: All transitions allowed

### Automatic Personnel Management
When status changes to `completed`:
- All assigned personnel automatically updated to `available`
- Applies to all personnel in check-in details
- Enables personnel reassignment to new incidents

### Activity Logging
All status changes are logged with:
- Previous status value
- New status value
- Timestamp of change
- RUL who made the change
- Optional remarks

### Remarks Handling
- Updates existing remarks if provided
- Preserves existing remarks if not provided
- Supports detailed documentation of status rationale

---

## Status Change Effects

### pending → ongoing
- **Personnel**: Assigned personnel remain in current status
- **Resources**: Resources become actively deployed
- **Logging**: Activation logged in audit trail

### ongoing → completed
- **Personnel**: All assigned personnel → `available`
- **Resources**: Resources released from incident
- **Logging**: Completion and demobilization logged
- **Notifications**: Personnel may receive completion notifications

### completed → ongoing (Reactivation)
- **Personnel**: Previously assigned personnel remain `available`
- **Resources**: Manual reassignment required
- **Logging**: Reactivation logged as special event

---

## Usage Examples

### Mark Incident as Ongoing
```bash
curl -X POST \
  "https://api.example.com/api/rul/ics/550e8400-e29b-41d4-a716-446655440000/status/ongoing" \
  -H "Authorization: Bearer your_access_token" \
  -H "Content-Type: application/json" \
  -d '{
    "remarks": "All resources deployed and actively engaged"
  }'
```

### Complete Incident
```bash
curl -X POST \
  "https://api.example.com/api/rul/ics/550e8400-e29b-41d4-a716-446655440000/status/completed" \
  -H "Authorization: Bearer your_access_token" \
  -H "Content-Type: application/json" \
  -d '{
    "remarks": "Fire extinguished, overhaul complete, all personnel accounted for"
  }'
```

### JavaScript Implementation
```javascript
async function updateIncidentStatus(uuid, newStatus, remarks = null) {
  const requestBody = remarks ? { remarks } : {};

  try {
    const response = await axios.post(
      `/api/rul/ics/${uuid}/status/${newStatus}`,
      requestBody,
      {
        headers: {
          'Authorization': `Bearer ${token}`
        }
      }
    );

    console.log(`Status updated to: ${response.data.data.status}`);

    if (newStatus === 'completed') {
      console.log('All personnel automatically released');
    }

    return response.data.data;
  } catch (error) {
    if (error.response?.status === 400) {
      console.error('Invalid status value provided');
    } else if (error.response?.status === 404) {
      console.error('ICS record not found');
    }
    throw error;
  }
}

// Usage examples
await updateIncidentStatus(uuid, 'ongoing', 'Resources deployed');
await updateIncidentStatus(uuid, 'completed', 'Incident resolved');
```

### PHP Implementation
```php
function updateIcsStatus($uuid, $status, $remarks = null) {
    global $guzzle, $token;

    $requestBody = [];
    if ($remarks) {
        $requestBody['remarks'] = $remarks;
    }

    try {
        $response = $guzzle->post("/api/rul/ics/{$uuid}/status/{$status}", [
            'headers' => [
                'Authorization' => 'Bearer ' . $token
            ],
            'json' => $requestBody
        ]);

        $result = json_decode($response->getBody())->data;

        echo "Status updated to: " . $result->status . "\n";

        if ($status === 'completed') {
            echo "All personnel automatically released\n";
        }

        return $result;
    } catch (GuzzleHttp\Exception\ClientException $e) {
        $statusCode = $e->getCode();
        if ($statusCode === 400) {
            echo "Invalid status value\n";
        } elseif ($statusCode === 404) {
            echo "ICS record not found\n";
        }
        throw $e;
    }
}
```

---

## Status Change Workflows

### Incident Lifecycle
```
pending → ongoing → completed
   ↑                     ↓
   ←→ (reactivation) ←→
```

### Personnel Status Impact
| ICS Status | Personnel Effect |
|------------|------------------|
| `pending` | No automatic changes |
| `ongoing` | No automatic changes |
| `completed` | All → `available` |

### Common Status Change Scenarios

#### Fire Incident Workflow
1. **pending**: Initial dispatch, resources en route
2. **ongoing**: On-scene operations active
3. **completed**: Fire suppressed, scene secured

#### Medical Emergency Workflow
1. **pending**: EMS dispatched
2. **ongoing**: Patient care in progress
3. **completed**: Patient transported, units clear

---

## Related Endpoints
- `GET /api/rul/ics/{id}/show` - View current status
- `POST /api/rul/ics/{id}/edit` - Full record update
- `GET /api/rul/ics/{icsUuid}/logs` - View status change history
- `POST /api/rul/ics/checkin/{uuid}/status` - Update individual check-in status

## Notes
- Status validation prevents invalid values
- Personnel demobilization is automatic on completion
- Activity logging provides complete audit trail
- Remarks are optional but recommended for documentation
- Status changes trigger real-time notifications
- All status transitions are permitted for operational flexibility
- Personnel status synchronization maintains system consistency