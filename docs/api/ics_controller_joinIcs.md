# ICS Controller - Join ICS Function API Documentation

## Overview
Allows a Resource Unit Leader (RUL) to join an existing ICS 211 record as an operator using an invitation token.

## Endpoint
```
POST /api/rul/ics/join
```

## Authentication
- **Required**: Yes
- **Middleware**: `rul.auth`, `throttle:60,1`
- **Access Level**: Resource Unit Leader (RUL)

## Request

### Headers
```
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

### Body Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| token | string | Yes | Invitation/access token for the ICS record |

### Example Request Body

```json
{
  "token": "ABC12345"
}
```

## Response

### Success Response (200)
```json
{
  "success": true,
  "message": "Successfully joined ICS 211 record as operator",
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
    "status": "ongoing",
    "created_at": "2025-03-13T08:30:00Z",
    "updated_at": "2025-03-13T09:15:00Z",
    "operators": [
      {
        "id": 1,
        "uuid": "750e8400-e29b-41d4-a716-446655440000",
        "name": "Captain Jane Smith",
        "contact_number": "+1234567890",
        "serial_number": "RUL-2025-001",
        "department": "Metro Fire Department",
        "certificates": []
      },
      {
        "id": 2,
        "uuid": "760e8400-e29b-41d4-a716-446655440000",
        "name": "Chief Robert Johnson",
        "contact_number": "+1234567892",
        "serial_number": "RUL-2025-002",
        "department": "County Emergency Services",
        "certificates": [
          {
            "id": 2,
            "uuid": "870e8400-e29b-41d4-a716-446655440000",
            "certificate_name": "Emergency Management Certification",
            "file_path": "certificates/emergency_mgmt_2025.pdf"
          }
        ]
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
        "status": "accepted",
        "personnel": {
          "id": 1,
          "uuid": "a50e8400-e29b-41d4-a716-446655440000",
          "name": "John Firefighter",
          "contact_number": "+1234567891",
          "serial_number": "PER-2025-001",
          "department": "Metro Fire Department",
          "status": "assigned"
        }
      }
    ]
  }
}
```

### Invalid Token Response (404)
```json
{
  "success": false,
  "message": "Invalid invitation code. ICS 211 record not found."
}
```

### Already Operator Response (400)
```json
{
  "success": false,
  "message": "You are already an operator of this ICS record"
}
```

### Validation Error Response (422)
```json
{
  "success": false,
  "message": "The given data was invalid",
  "errors": {
    "token": ["The token field is required"]
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

### Controller Method
```php
public function joinIcs(Request $request)
{
    $validated = $request->validate([
        'token' => 'required|string',
    ]);

    // Find ICS record by token
    $ics211Record = Ics211Record::where('token', $validated['token'])->first();

    if (! $ics211Record) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid invitation code. ICS 211 record not found.',
        ], 404);
    }

    $currentUser = $request->user();

    // Check if user is already an operator
    if ($ics211Record->operators()->where('resident_unit_leaders.id', $currentUser->id)->exists()) {
        return response()->json([
            'success' => false,
            'message' => 'You are already an operator of this ICS record',
        ], 400);
    }

    // Attach current user as operator
    $ics211Record->operators()->attach($currentUser->id);

    // Log operator addition
    $this->logAction(
        $ics211Record,
        'operator_added',
        "RUL {$currentUser->name} joined {$ics211Record->name} operators",
        null,
        ['operator_name' => $currentUser->name],
        $currentUser->id
    );

    // Load relationships
    $ics211Record->load(['operators.certificates', 'checkInDetails.personnel']);

    return response()->json([
        'success' => true,
        'message' => 'Successfully joined ICS 211 record as operator',
        'data' => new Ics211RecordResource($ics211Record),
    ], 200);
}
```

### Key Features

#### Token-Based Access Control
- Uses unique 8-character token for invitation
- Secure invitation sharing between RULs
- No need to share sensitive IDs or URLs

#### Duplicate Prevention
- Checks if RUL is already an operator
- Prevents duplicate operator assignments
- Returns appropriate error message

#### Automatic Logging
- Logs operator addition activity
- Records who joined and when
- Provides audit trail for operator changes

#### Relationship Management
- Uses Laravel's many-to-many relationship
- Efficient database operations
- Proper constraint handling

## Business Logic

### Access Control Model
1. **Token Generation**: Random 8-character tokens generated on ICS creation
2. **Invitation Sharing**: ICS creator shares token with other RULs
3. **Self-Service Joining**: RULs join autonomously using tokens
4. **Operator Permissions**: Once joined, RUL has full operator privileges

### Security Considerations
- Tokens should be cryptographically secure
- Consider token expiration for enhanced security
- Rate limiting prevents brute-force token guessing
- Tokens are unique across all ICS records

### Collaboration Benefits
- Multi-agency coordination
- Shared responsibility for incident management
- Distributed access control
- Real-time collaboration

## Use Cases

### Multi-Agency Response
- Fire department creates ICS for wildfire
- Police department joins using token
- EMS services join for medical support
- Public works joins for infrastructure

### Regional Coordination
- Regional command creates incident
- Local agencies join based on involvement
- Mutual aid agreements facilitated
- Resource sharing coordination

### Training Exercises
- Exercise coordinator creates scenario
- Participating agencies join
- Role-playing and simulation
- Training evaluation and assessment

### Planned Events
- Special event coordination
- Security and safety planning
- Resource allocation and management
- Multi-jurisdictional cooperation

## Security Best Practices

### Token Management
```php
// Generate secure tokens
$token = Str::random(8); // Consider longer tokens for production

// Consider token expiration
$expiresAt = now()->addHours(24);
```

### Access Control
- Validate tokens on every request
- Log all token usage attempts
- Monitor for suspicious activity
- Implement rate limiting

### Audit Trail
- Log all operator additions
- Track token usage patterns
- Monitor unauthorized access attempts
- Maintain compliance records

## Integration Points

### Push Notifications
- Notify existing operators when new RUL joins
- Alert incident creator of new assignments
- Real-time collaboration updates

### Mobile App Support
- QR code generation for token sharing
- Offline token storage capability
- Automatic sync when connectivity restored

### External Systems
- Integration with CAD systems
- Mutual aid system connections
- Regional coordination platforms

## Testing Examples

### cURL Request
```bash
curl -X POST \
  "https://yourdomain.com/api/rul/ics/join" \
  -H "Authorization: Bearer your-auth-token" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "token": "ABC12345"
  }'
```

### JavaScript/Axios
```javascript
const invitationToken = 'ABC12345';

try {
  const response = await axios.post('/api/rul/ics/join', {
    token: invitationToken
  }, {
    headers: {
      'Authorization': 'Bearer ' + token,
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    }
  });

  console.log('Successfully joined ICS:', response.data.data.name);
  console.log('Now operator alongside:', response.data.data.operators.length - 1, 'other operators');

} catch (error) {
  if (error.response?.status === 404) {
    console.error('Invalid invitation token');
  } else if (error.response?.status === 400) {
    console.error('Already an operator of this ICS');
  } else {
    console.error('Failed to join ICS:', error.message);
  }
}
```

### PHP/Guzzle
```php
$client = new GuzzleHttp\Client();

try {
    $response = $client->request('POST', 'https://yourdomain.com/api/rul/ics/join', [
        'headers' => [
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ],
        'json' => [
            'token' => 'ABC12345'
        ]
    ]);

    $data = json_decode($response->getBody(), true);

    if ($data['success']) {
        echo "Successfully joined: " . $data['data']['name'] . "\n";
        echo "Total operators: " . count($data['data']['operators']) . "\n";
    }

} catch (GuzzleHttp\Exception\ClientException $e) {
    $statusCode = $e->getResponse()->getStatusCode();
    $body = json_decode($e->getResponse()->getBody(), true);

    echo "Error {$statusCode}: " . $body['message'] . "\n";
}
```

## Related Endpoints
- `GET /api/rul/ics/{id}/show` - View ICS record details
- `POST /api/rul/ics/create` - Create new ICS record (generates token)
- `POST /api/rul/ics/{id}/edit` - Update ICS record as operator
- `GET /api/rul/ics/{icsUuid}/logs` - View ICS activity logs

## Workflow Integration

### Token Sharing Methods
1. **Direct Communication**: Share token via phone/radio
2. **Digital Channels**: Email, messaging systems
3. **QR Codes**: Mobile app integration
4. **Command Center**: Central distribution point

### Post-Join Actions
1. Review incident details
2. Assess resource requirements
3. Coordinate with other operators
4. Begin resource deployment