# ICS Controller - Join ICS Function API Documentation

## Endpoint: POST /api/rul/ics/join

### Function: `joinIcs()`

### Overview
Allows a Resource Unit Leader (RUL) to join an existing ICS 211 record as an operator using an invitation token. This enables multi-agency collaboration and shared incident management.

---

## Request Details

### HTTP Method
`POST`

### URL
```
/api/rul/ics/join
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

### Request Body
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `token` | string | Yes | Invitation token for the ICS record |

### Example Request
```json
{
  "token": "ABC12345"
}
```

---

## Response Format

### Success Response (200 OK)
```json
{
  "success": true,
  "message": "Successfully joined ICS 211 record as operator",
  "data": {
    "id": 1,
    "uuid": "550e8400-e29b-41d4-a716-446655440000",
    "token": "ABC12345",
    "name": "Multi-Agency Wildfire Response",
    "type": "Wildfire",
    "order_request_number": "WILD-2024-003",
    "checkin_location": "Incident Command Post - Highway 101",
    "region": "Northern District",
    "remarks": "Large wildfire threatening structures",
    "remarks_image_attachment": "ics/remarks/wildfire_map.jpg",
    "status": "ongoing",
    "created_at": "2024-03-01T08:00:00Z",
    "updated_at": "2024-03-01T10:30:00Z",
    "operators": [
      {
        "id": 1,
        "uuid": "original-rul-uuid-123",
        "name": "Chief Sarah Williams",
        "serial_number": "RUL001",
        "department": "State Fire Department",
        "contact_number": "555-0101",
        "avatar": "avatars/chief_williams.jpg",
        "certificates": [
          {
            "id": 1,
            "uuid": "cert-uuid-123",
            "certificate_name": "ICS-400 Advanced ICS",
            "file_path": "certificates/ics400_cert.pdf"
          }
        ]
      },
      {
        "id": 3,
        "uuid": "joining-rul-uuid-456",
        "name": "Captain John Martinez",
        "serial_number": "RUL003",
        "department": "County Emergency Services",
        "contact_number": "555-0303",
        "avatar": "avatars/capt_martinez.jpg",
        "certificates": [
          {
            "id": 5,
            "uuid": "cert-uuid-789",
            "certificate_name": "ICS-300 Intermediate ICS",
            "file_path": "certificates/ics300_cert_martinez.pdf"
          }
        ]
      }
    ],
    "check_in_details": [
      {
        "id": 1,
        "uuid": "checkin-uuid-123",
        "personnel_id": 25,
        "order_request_number": "WILD-2024-003",
        "checkin_date": "2024-03-01",
        "checkin_time": "08:30:00",
        "kind": "Personnel",
        "category": "Firefighting",
        "type": "Hotshot Crew",
        "resource_identifier": "HS-001",
        "name_of_leader": "Superintendent Davis",
        "contact_information": "555-0200",
        "quantity": 20,
        "department": "State Fire Department",
        "departure_point_of_origin": "Base Camp Alpha",
        "departure_date": "2024-03-01",
        "departure_time": "08:00:00",
        "departure_method_of_travel": "Crew Transport Vehicle",
        "with_manifest": true,
        "incident_assignment": "Direct attack on fire perimeter",
        "other_qualifications": "Wildland Fire qualified",
        "sent_resl": true,
        "status": "active",
        "created_at": "2024-03-01T08:30:00Z",
        "updated_at": "2024-03-01T09:15:00Z",
        "personnel": {
          "id": 25,
          "uuid": "personnel-uuid-789",
          "name": "Crew Leader Thompson",
          "serial_number": "HS001",
          "contact_number": "555-0225",
          "department": "State Fire Department",
          "status": "active",
          "avatar": "personnel/avatars/crew_thompson.jpg"
        }
      }
    ]
  }
}
```

### Error Response (404 Not Found - Invalid Token)
```json
{
  "success": false,
  "message": "Invalid invitation code. ICS 211 record not found."
}
```

### Error Response (400 Bad Request - Already Operator)
```json
{
  "success": false,
  "message": "You are already an operator of this ICS record"
}
```

### Validation Error Response (422 Unprocessable Entity)
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "token": [
      "The token field is required."
    ]
  }
}
```

---

## Business Logic

### Token-Based Access
- Each ICS record has a unique invitation token
- Token generated when ICS record is created
- Token enables secure sharing without exposing internal IDs
- Token lookup finds the associated ICS record

### Operator Management
- Authenticated RUL automatically added as operator
- Prevents duplicate operator assignments
- Maintains many-to-many relationship between RULs and ICS records
- Creator of ICS record is automatically first operator

### Activity Logging
- Operator addition logged in `ics_logs` table
- Includes RUL name and ICS name for audit trail
- Timestamp and action details recorded
- Supports post-incident analysis

### Access Control
- Only authenticated RULs can join
- No additional permission checks
- Token acts as invitation mechanism
- Duplicate joins prevented automatically

---

## Multi-Agency Collaboration

### Typical Workflow
1. **Initial ICS Creation**: Primary agency creates ICS record
2. **Token Sharing**: Creator shares token with other agencies
3. **Operator Joining**: Secondary agencies join using token
4. **Collaborative Management**: All operators can manage incident

### Example Scenarios

#### Wildfire Response
- **Forest Service** creates ICS (primary)
- **Local Fire Department** joins as operator
- **Sheriff's Department** joins for law enforcement
- **Emergency Services** joins for medical support

#### Multi-Jurisdictional Incident
- **City Fire** creates initial response
- **County Fire** joins for mutual aid
- **State Agency** joins for specialized resources
- **Federal** joins for specific authorities

### Operator Privileges
Once joined, operators can typically:
- View all incident information
- Add/modify personnel assignments
- Update incident status
- Access real-time data
- Coordinate resource deployment

---

## Token Security

### Token Characteristics
- 8-character random string
- Unique across all ICS records
- Case-sensitive
- No expiration (until incident closed)

### Security Considerations
- Token should be shared through secure channels
- No rate limiting on token usage
- Invalid tokens return 404 (not 403) to prevent enumeration
- Token doesn't reveal sensitive information

---

## Usage Examples

### Basic Join Request
```bash
curl -X POST \
  https://api.example.com/api/rul/ics/join \
  -H "Authorization: Bearer your_access_token" \
  -H "Content-Type: application/json" \
  -d '{
    "token": "ABC12345"
  }'
```

### JavaScript Implementation
```javascript
async function joinIncident(invitationToken) {
  try {
    const response = await axios.post('/api/rul/ics/join', {
      token: invitationToken
    }, {
      headers: {
        'Authorization': `Bearer ${authToken}`
      }
    });

    const icsRecord = response.data.data;

    console.log(`Successfully joined: ${icsRecord.name}`);
    console.log(`Number of operators: ${icsRecord.operators.length}`);
    console.log(`Your role: Operator`);

    return icsRecord;
  } catch (error) {
    if (error.response?.status === 404) {
      console.error('Invalid invitation code');
    } else if (error.response?.status === 400) {
      console.error('Already an operator on this incident');
    }
    throw error;
  }
}

// Usage
const invitationCode = 'ABC12345';
await joinIncident(invitationCode);
```

### PHP Implementation
```php
function joinIcsAsOperator($invitationToken) {
    global $guzzle, $authToken;

    try {
        $response = $guzzle->post('/api/rul/ics/join', [
            'headers' => [
                'Authorization' => 'Bearer ' . $authToken
            ],
            'json' => [
                'token' => $invitationToken
            ]
        ]);

        $icsRecord = json_decode($response->getBody())->data;

        echo "Successfully joined: " . $icsRecord->name . "\n";
        echo "Number of operators: " . count($icsRecord->operators) . "\n";

        return $icsRecord;
    } catch (GuzzleHttp\Exception\ClientException $e) {
        $statusCode = $e->getCode();
        if ($statusCode === 404) {
            echo "Invalid invitation code\n";
        } elseif ($statusCode === 400) {
            echo "Already an operator on this incident\n";
        }
        throw $e;
    }
}
```

### Mobile App Integration
```javascript
// QR Code scanning integration
function handleQRCodeScan(scannedData) {
  // Extract token from QR code data
  const token = extractTokenFromQRData(scannedData);

  if (token) {
    joinIncident(token)
      .then(incident => {
        navigateToIncidentDashboard(incident.uuid);
      })
      .catch(error => {
        showErrorMessage('Failed to join incident');
      });
  }
}
```

---

## Integration Patterns

### Token Distribution Methods
- **Email**: Send token in secure email
- **SMS**: Text message to authorized personnel
- **QR Code**: Generate QR code for mobile scanning
- **Radio**: Communicate over secure radio channels
- **In-Person**: Share during briefings

### Response Handling
```javascript
// Handle all possible responses
try {
  const result = await joinIncident(token);

  // Success - navigate to incident dashboard
  redirectToIncidentPage(result.uuid);

} catch (error) {
  switch (error.response?.status) {
    case 400:
      showMessage('You are already managing this incident');
      break;
    case 404:
      showMessage('Invalid invitation code. Please check and try again.');
      break;
    case 422:
      showMessage('Please provide a valid invitation code');
      break;
    default:
      showMessage('Unable to join incident. Please try again.');
  }
}
```

---

## Related Endpoints
- `GET /api/rul/ics` - List ICS records where user is operator
- `GET /api/rul/ics/{id}/show` - View incident details
- `POST /api/rul/ics/create` - Create new ICS record
- `POST /api/rul/ics/{id}/edit` - Manage incident (requires operator access)

## Notes
- Token-based invitation system enables secure collaboration
- Prevents duplicate operator assignments automatically
- Activity logging tracks all operator additions
- No expiration on tokens - valid until incident closed
- Returns complete incident data upon successful join
- Supports multi-agency incident command structure
- Token can be shared through various secure channels
- QR code integration recommended for mobile applications