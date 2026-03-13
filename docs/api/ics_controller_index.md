# ICS Controller - Index Function API Documentation

## Overview
Retrieves a list of all ICS 211 records with their related operators and check-in details.

## Endpoint
```
GET /api/rul/ics
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

### Parameters
None required.

### Query Parameters
None.

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
  ]
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
public function index()
{
    $records = Ics211Record::with(['operators.certificates', 'checkInDetails.personnel'])->get();

    return response()->json([
        'success' => true,
        'data' => Ics211RecordResource::collection($records),
    ]);
}
```

### Eager Loading
The method uses eager loading to prevent N+1 query problems:
- `operators.certificates` - Loads all operators and their certificates
- `checkInDetails.personnel` - Loads all check-in details and associated personnel

### Resource Transformation
- Uses `Ics211RecordResource` for consistent data formatting
- Handles data transformation and field exposure
- Provides consistent API response structure

## Use Cases

### Dashboard Display
- Load all ICS records for dashboard overview
- Display current incident status
- Show operator assignments

### Administrative Management
- View all active incidents
- Monitor overall system activity
- Generate reports and analytics

### Mobile App Synchronization
- Sync all ICS data to mobile applications
- Provide offline capability data
- Update local caches

## Performance Considerations

### Database Optimization
- Consider pagination for large datasets
- Add database indexes on frequently queried fields
- Monitor query performance with many relationships

### Caching Strategy
```php
// Recommended caching implementation
$records = Cache::remember('ics_records_with_relations', 300, function () {
    return Ics211Record::with(['operators.certificates', 'checkInDetails.personnel'])->get();
});
```

### Response Size
- Large datasets may impact response time
- Consider implementing pagination
- Add field selection options for mobile clients

## Security Notes
- Only authenticated RULs can access this endpoint
- Rate limiting prevents abuse
- No sensitive authentication tokens exposed
- Personnel contact information included (ensure authorized access)

## Related Endpoints
- `GET /api/rul/ics/search` - Search with filters
- `GET /api/rul/ics/{id}/show` - Get specific ICS record
- `POST /api/rul/ics/create` - Create new ICS record

## Testing Examples

### cURL Request
```bash
curl -X GET \
  "https://yourdomain.com/api/rul/ics" \
  -H "Authorization: Bearer your-auth-token" \
  -H "Accept: application/json"
```

### JavaScript/Axios
```javascript
const response = await axios.get('/api/rul/ics', {
  headers: {
    'Authorization': 'Bearer ' + token,
    'Accept': 'application/json'
  }
});

console.log(response.data);
```

### PHP/Guzzle
```php
$client = new GuzzleHttp\Client();
$response = $client->request('GET', 'https://yourdomain.com/api/rul/ics', [
    'headers' => [
        'Authorization' => 'Bearer ' . $token,
        'Accept' => 'application/json'
    ]
]);

$data = json_decode($response->getBody(), true);
```