# ICS Controller - Search Function API Documentation

## Overview
Searches ICS 211 records with optional filters for status, name, and order request number.

## Endpoint
```
GET /api/rul/ics/search
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

### Query Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| status | string | No | Filter by status (pending, ongoing, completed) |
| name | string | No | Filter by ICS name (partial match) |
| order_request_number | string | No | Filter by order request number (partial match) |

### Example Requests

#### Search by Status
```
GET /api/rul/ics/search?status=ongoing
```

#### Search by Name (Partial)
```
GET /api/rul/ics/search?name=Wildfire
```

#### Search by Order Request Number
```
GET /api/rul/ics/search?order_request_number=ORN-2025
```

#### Combined Search
```
GET /api/rul/ics/search?status=ongoing&name=Pine&order_request_number=ORN-2025
```

## Response

### Success Response (200)
```json
{
  "success": true,
  "data": [
    {
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
              "file_path": "certificates/fire_safety_2025.pdf"
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
  ],
  "count": 1
}
```

### Empty Results Response (200)
```json
{
  "success": true,
  "data": [],
  "count": 0
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
public function search(Request $request)
{
    $query = Ics211Record::with(['operators.certificates', 'checkInDetails.personnel']);

    // Filter by status
    if ($request->has('status') && ! empty($request->status)) {
        $query->where('status', $request->status);
    }

    // Filter by name (partial match)
    if ($request->has('name') && ! empty($request->name)) {
        $query->where('name', 'like', '%'.$request->name.'%');
    }

    // Filter by order_request_number (partial match)
    if ($request->has('order_request_number') && ! empty($request->order_request_number)) {
        $query->where('order_request_number', 'like', '%'.$request->order_request_number.'%');
    }

    $records = $query->get();

    return response()->json([
        'success' => true,
        'data' => Ics211RecordResource::collection($records),
        'count' => $records->count(),
    ]);
}
```

### Search Logic

#### Status Filter
- Exact match on status field
- Validates against: `pending`, `ongoing`, `completed`
- Empty values are ignored

#### Name Filter
- Partial match using SQL LIKE operator
- Case-insensitive search (depends on database collation)
- Searches within the name field

#### Order Request Number Filter
- Partial match using SQL LIKE operator
- Useful for finding related incidents
- Supports pattern matching

### Eager Loading
Same as index method:
- `operators.certificates` - Loads all operators and their certificates
- `checkInDetails.personnel` - Loads all check-in details and associated personnel

## Use Cases

### Dashboard Filtering
- Filter active incidents by status
- Quick search by incident name
- Find incidents by order number

### Mobile App Search
- Quick search functionality
- Filtered views for different incident types
- Personnel-specific incident searches

### Administrative Reports
- Status-based reporting
- Incident naming pattern analysis
- Order number tracking

## Query Performance

### Index Recommendations
```sql
-- Improve status filtering
CREATE INDEX idx_ics211_records_status ON ics211_records(status);

-- Improve name searching
CREATE INDEX idx_ics211_records_name ON ics211_records(name);

-- Improve order number searching
CREATE INDEX idx_ics211_records_order_num ON ics211_records(order_request_number);
```

### Optimization Notes
- Partial string searches (LIKE) can be expensive on large datasets
- Consider full-text search for better name searching
- Monitor query performance with multiple filters

## Security Notes
- Input validation prevents SQL injection
- All filters use parameterized queries
- Rate limiting prevents search abuse
- No sensitive data exposed in search parameters

## Related Endpoints
- `GET /api/rul/ics` - Get all ICS records
- `GET /api/rul/ics/{id}/show` - Get specific ICS record
- `POST /api/rul/ics/create` - Create new ICS record

## Testing Examples

### cURL Request - Search by Status
```bash
curl -X GET \
  "https://yourdomain.com/api/rul/ics/search?status=ongoing" \
  -H "Authorization: Bearer your-auth-token" \
  -H "Accept: application/json"
```

### cURL Request - Combined Search
```bash
curl -X GET \
  "https://yourdomain.com/api/rul/ics/search?status=ongoing&name=fire&order_request_number=ORN-2025" \
  -H "Authorization: Bearer your-auth-token" \
  -H "Accept: application/json"
```

### JavaScript/Axios
```javascript
const searchParams = {
  status: 'ongoing',
  name: 'wildfire',
  order_request_number: 'ORN-2025'
};

const response = await axios.get('/api/rul/ics/search', {
  params: searchParams,
  headers: {
    'Authorization': 'Bearer ' + token,
    'Accept': 'application/json'
  }
});

console.log(`Found ${response.data.count} records`);
```

### PHP/Guzzle
```php
$client = new GuzzleHttp\Client();
$response = $client->request('GET', 'https://yourdomain.com/api/rul/ics/search', [
    'headers' => [
        'Authorization' => 'Bearer ' . $token,
        'Accept' => 'application/json'
    ],
    'query' => [
        'status' => 'ongoing',
        'name' => 'fire'
    ]
]);

$data = json_decode($response->getBody(), true);
echo "Found " . $data['count'] . " records";
```