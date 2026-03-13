# ICS Controller - Show Function API Documentation

## Overview
Retrieves a specific ICS 211 record by UUID with all related operators and check-in details.

## Endpoint
```
GET /api/rul/ics/{uuid}/show
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

### URL Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| uuid | string | Yes | UUID of the ICS 211 record |

### Example Request
```
GET /api/rul/ics/550e8400-e29b-41d4-a716-446655440000/show
```

## Response

### Success Response (200)
```json
{
  "success": true,
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
        "certificates": [
          {
            "id": 1,
            "uuid": "850e8400-e29b-41d4-a716-446655440000",
            "certificate_name": "Fire Safety Training",
            "file_path": "certificates/fire_safety_2025.pdf",
            "created_at": "2025-01-15T10:00:00Z"
          }
        ]
      },
      {
        "id": 2,
        "uuid": "860e8400-e29b-41d4-a716-446655440000",
        "name": "Chief Robert Johnson",
        "contact_number": "+1234567892",
        "serial_number": "RUL-2025-002",
        "department": "County Emergency Services",
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
        "status": "accepted",
        "created_at": "2025-03-13T08:30:00Z",
        "updated_at": "2025-03-13T08:35:00Z",
        "personnel": {
          "id": 1,
          "uuid": "a50e8400-e29b-41d4-a716-446655440000",
          "name": "John Firefighter",
          "contact_number": "+1234567891",
          "serial_number": "PER-2025-001",
          "department": "Metro Fire Department",
          "status": "assigned",
          "created_at": "2025-03-10T14:00:00Z"
        }
      },
      {
        "id": 2,
        "uuid": "960e8400-e29b-41d4-a716-446655440000",
        "order_request_number": "ORN-2025-001-B",
        "checkin_date": "2025-03-13",
        "checkin_time": "09:00:00",
        "kind": "Equipment",
        "category": "Equipment",
        "type": "Heavy Equipment",
        "resource_identifier": "DOZER-02",
        "name_of_leader": "Operator Mike Wilson",
        "contact_information": "+1234567893",
        "quantity": 1,
        "department": "Public Works",
        "departure_point_of_origin": "Equipment Yard",
        "departure_date": "2025-03-13",
        "departure_time": "08:45:00",
        "departure_method_of_travel": "Self-deployed",
        "with_manifest": false,
        "incident_assignment": "Fire Line Construction",
        "other_qualifications": "Heavy equipment operator certified",
        "sent_resl": true,
        "status": "pending",
        "created_at": "2025-03-13T09:00:00Z",
        "updated_at": "2025-03-13T09:00:00Z",
        "personnel": null
      }
    ]
  }
}
```

### Not Found Response (404)
```json
{
  "success": false,
  "message": "ICS 211 record not found"
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
public function show($ics211Record)
{
    $ics211Record = Ics211Record::where('uuid', $ics211Record)->first();
    if (! $ics211Record) {
        return response()->json([
            'success' => false,
            'message' => 'ICS 211 record not found',
        ], 404);
    }
    $ics211Record->load(['operators.certificates', 'checkInDetails.personnel']);

    return response()->json([
        'success' => true,
        'data' => new Ics211RecordResource($ics211Record),
    ]);
}
```

### Key Features

#### UUID-based Lookup
- Uses UUID instead of sequential ID for security
- Prevents enumeration attacks
- Provides user-friendly URLs

#### Eager Loading
- Loads all operators and their certificates
- Loads all check-in details and associated personnel
- Optimizes database queries to prevent N+1 problems

#### Resource Transformation
- Uses `Ics211RecordResource` for consistent formatting
- Handles sensitive data exposure
- Provides standardized API responses

## Use Cases

### Incident Detail View
- Display comprehensive incident information
- Show all assigned operators and personnel
- View complete check-in history

### Mobile App Synchronization
- Download specific incident data
- Sync offline incident details
- Update local cache with latest information

### Report Generation
- Extract detailed incident data
- Generate compliance reports
- Create incident documentation

### Operator Collaboration
- View other operators on the incident
- See personnel assignments
- Access shared incident information

## Data Privacy Considerations

### Exposed Information
The endpoint exposes:
- Complete ICS record details
- All operator information and certificates
- All check-in details and personnel information
- Contact information for operators and personnel

### Security Implications
- Ensure proper authorization before accessing
- Consider implementing field-level permissions
- Monitor access to sensitive contact information
- Log access for audit trails

## Performance Optimizations

### Caching Strategy
```php
// Recommended caching for frequently accessed records
$cacheKey = "ics_record_{$uuid}_with_relations";
$ics211Record = Cache::remember($cacheKey, 600, function () use ($uuid) {
    return Ics211Record::where('uuid', $uuid)
        ->with(['operators.certificates', 'checkInDetails.personnel'])
        ->first();
});
```

### Database Indexes
```sql
-- Essential for UUID lookups
CREATE INDEX idx_ics211_records_uuid ON ics211_records(uuid);

-- Support for eager loading
CREATE INDEX idx_certificates_rul_id ON certificates(rul_id);
CREATE INDEX idx_check_in_details_ics_id ON check_in_details(ics211_record_id);
CREATE INDEX idx_check_in_details_personnel_id ON check_in_details(personnel_id);
```

## Related Endpoints
- `GET /api/rul/ics` - List all ICS records
- `GET /api/rul/ics/search` - Search ICS records
- `POST /api/rul/ics/{id}/edit` - Update ICS record
- `POST /api/rul/ics/{id}/status/{status}` - Update ICS status
- `GET /api/rul/ics/{icsUuid}/logs` - Get ICS activity logs

## Testing Examples

### cURL Request
```bash
curl -X GET \
  "https://yourdomain.com/api/rul/ics/550e8400-e29b-41d4-a716-446655440000/show" \
  -H "Authorization: Bearer your-auth-token" \
  -H "Accept: application/json"
```

### JavaScript/Axios
```javascript
const uuid = '550e8400-e29b-41d4-a716-446655440000';

const response = await axios.get(`/api/rul/ics/${uuid}/show`, {
  headers: {
    'Authorization': 'Bearer ' + token,
    'Accept': 'application/json'
  }
});

console.log('ICS Record:', response.data.data);
console.log('Operators:', response.data.data.operators);
console.log('Check-in Details:', response.data.data.check_in_details);
```

### PHP/Guzzle
```php
$uuid = '550e8400-e29b-41d4-a716-446655440000';
$client = new GuzzleHttp\Client();

$response = $client->request('GET', "https://yourdomain.com/api/rul/ics/{$uuid}/show", [
    'headers' => [
        'Authorization' => 'Bearer ' . $token,
        'Accept' => 'application/json'
    ]
]);

$data = json_decode($response->getBody(), true);

if ($data['success']) {
    $icsRecord = $data['data'];
    echo "ICS: " . $icsRecord['name'] . "\n";
    echo "Status: " . $icsRecord['status'] . "\n";
    echo "Operators: " . count($icsRecord['operators']) . "\n";
    echo "Check-ins: " . count($icsRecord['check_in_details']) . "\n";
}
```

## Error Handling

### UUID Validation
```javascript
// Client-side UUID validation
function isValidUUID(uuid) {
    const uuidRegex = /^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i;
    return uuidRegex.test(uuid);
}

// Use before making request
if (!isValidUUID(uuid)) {
    throw new Error('Invalid UUID format');
}
```

### Response Validation
```javascript
// Validate response structure
if (response.data.success && response.data.data) {
    const icsRecord = response.data.data;
    // Process the record
} else {
    console.error('Invalid response or record not found');
}
```