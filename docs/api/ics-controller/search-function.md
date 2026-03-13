# ICS Controller - Search Function API Documentation

## Endpoint: GET /api/rul/ics/search

### Function: `search()`

### Overview
Searches and filters ICS 211 records based on specified criteria including status, name, and order request number. Supports partial matching and returns count information.

---

## Request Details

### HTTP Method
`GET`

### URL
```
/api/rul/ics/search
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

### Query Parameters
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `status` | string | No | Filter by ICS status (pending, ongoing, completed) |
| `name` | string | No | Filter by ICS name (partial match) |
| `order_request_number` | string | No | Filter by order number (partial match) |

### Example Request URLs
```
/api/rul/ics/search?status=ongoing
/api/rul/ics/search?name=fire
/api/rul/ics/search?order_request_number=ORD-2024
/api/rul/ics/search?status=ongoing&name=structure&order_request_number=001
```

---

## Response Format

### Success Response (200 OK)
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "uuid": "550e8400-e29b-41d4-a716-446655440000",
      "token": "ABC12345",
      "name": "Structure Fire Response",
      "type": "Structure Fire",
      "order_request_number": "ORD-2024-001",
      "checkin_location": "123 Main Street",
      "region": "District 1",
      "remarks": "Multi-story building involvement",
      "remarks_image_attachment": "ics/remarks/image_001.jpg",
      "status": "ongoing",
      "created_at": "2024-03-01T10:30:00Z",
      "updated_at": "2024-03-01T12:15:00Z",
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
          "status": "active",
          "personnel": {
            "id": 1,
            "uuid": "personnel-uuid-123",
            "name": "Mike Johnson",
            "serial_number": "FF001",
            "status": "active"
          }
        }
      ]
    }
  ],
  "count": 1
}
```

### Success Response (No Results)
```json
{
  "success": true,
  "data": [],
  "count": 0
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

## Filter Behavior

### Status Filter
- **Exact Match**: Filters by exact status value
- **Valid Values**: `pending`, `ongoing`, `completed`
- **Case Sensitive**: Must match exact case
- **Empty Parameter**: Ignored if empty

### Name Filter
- **Partial Match**: Uses LIKE '%{name}%' pattern
- **Case Insensitive**: Database collation dependent
- **Wildcards**: Automatically added by system
- **Empty Parameter**: Ignored if empty

### Order Request Number Filter
- **Partial Match**: Uses LIKE '%{order_request_number}%' pattern
- **Case Insensitive**: Database collation dependent
- **Wildcards**: Automatically added by system
- **Empty Parameter**: Ignored if empty

---

## Response Fields

### Root Response
| Field | Type | Description |
|-------|------|-------------|
| `success` | boolean | Always true for successful requests |
| `data` | array | Array of matching ICS records |
| `count` | integer | Number of records found |

### ICS Record Structure
Same as Index function - includes complete nested relationships:
- Operators with certificates
- Check-in details with personnel
- All ICS record fields

---

## Usage Examples

### Search by Status
```bash
curl -X GET \
  "https://api.example.com/api/rul/ics/search?status=ongoing" \
  -H "Authorization: Bearer your_access_token" \
  -H "Content-Type: application/json"
```

### Search by Name (Partial)
```bash
curl -X GET \
  "https://api.example.com/api/rul/ics/search?name=fire" \
  -H "Authorization: Bearer your_access_token" \
  -H "Content-Type: application/json"
```

### Multiple Filters
```bash
curl -X GET \
  "https://api.example.com/api/rul/ics/search?status=ongoing&name=structure" \
  -H "Authorization: Bearer your_access_token" \
  -H "Content-Type: application/json"
```

### JavaScript (Axios)
```javascript
const searchParams = new URLSearchParams({
  status: 'ongoing',
  name: 'fire'
});

const response = await axios.get(`/api/rul/ics/search?${searchParams}`, {
  headers: {
    'Authorization': `Bearer ${token}`
  }
});

console.log(`Found ${response.data.count} records`);
console.log(response.data.data);
```

### PHP
```php
$queryParams = http_build_query([
    'status' => 'ongoing',
    'name' => 'fire'
]);

$response = $guzzle->get("/api/rul/ics/search?{$queryParams}", [
    'headers' => [
        'Authorization' => 'Bearer ' . $token
    ]
]);

$result = json_decode($response->getBody());
$records = $result->data;
$count = $result->count;
```

---

## Business Logic

### Filter Combination
- **AND Logic**: All provided filters must match
- **Partial Matching**: Name and order number support partial matches
- **Flexible Search**: Empty parameters are ignored

### Performance
- Uses database indexes for efficient filtering
- Eager loads relationships to prevent N+1 queries
- Returns count for pagination planning

### Data Access
- Returns same data structure as index endpoint
- No additional access restrictions based on search criteria
- All visible ICS records can be found through search

---

## Common Use Cases

### Dashboard Filtering
```javascript
// Show only ongoing incidents
const activeIncidents = await searchIcs({ status: 'ongoing' });
```

### Quick Search
```javascript
// Search by incident name
const searchResults = await searchIcs({ name: userInput });
```

### Report Generation
```javascript
// Find completed incidents for reporting
const completedIncidents = await searchIcs({ status: 'completed' });
```

---

## Related Endpoints
- `GET /api/rul/ics` - Get all records without filtering
- `GET /api/rul/ics/{id}/show` - Get specific record
- `POST /api/rul/ics/create` - Create new record

## Notes
- Supports multiple simultaneous filters
- Partial matching enables flexible search experience
- Count field allows for pagination implementation
- Same data structure as index endpoint for consistency
- Empty filters are ignored rather than causing errors