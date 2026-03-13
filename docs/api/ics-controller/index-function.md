# ICS Controller - Index Function API Documentation

## Endpoint: GET /api/rul/ics

### Function: `index()`

### Overview
Retrieves a complete list of all ICS 211 records with their associated operators and check-in details. This endpoint provides a comprehensive view of all active and historical ICS operations.

---

## Request Details

### HTTP Method
`GET`

### URL
```
/api/rul/ics
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

### Request Parameters
None required.

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
  ]
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

### Error Response (429 Too Many Requests)
```json
{
  "message": "Too Many Attempts.",
  "status": 429
}
```

---

## Response Fields

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
| `status` | string | Current status (pending, ongoing, completed) |
| `created_at` | datetime | Creation timestamp |
| `updated_at` | datetime | Last update timestamp |
| `operators` | array | Associated RUL operators |
| `check_in_details` | array | Check-in records |

### Operator Fields
| Field | Type | Description |
|-------|------|-------------|
| `id` | integer | RUL ID |
| `uuid` | string | RUL UUID |
| `name` | string | RUL name |
| `serial_number` | string | RUL identifier |
| `department` | string | RUL department |
| `certificates` | array | RUL certifications |

### Check-in Detail Fields
| Field | Type | Description |
|-------|------|-------------|
| `id` | integer | Check-in detail ID |
| `uuid` | string | Check-in detail UUID |
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
| `status` | string | Current operational status |
| `personnel` | object | Associated personnel (if any) |

---

## Usage Examples

### cURL Request
```bash
curl -X GET \
  https://api.example.com/api/rul/ics \
  -H "Authorization: Bearer your_access_token" \
  -H "Content-Type: application/json"
```

### JavaScript (Axios)
```javascript
const response = await axios.get('/api/rul/ics', {
  headers: {
    'Authorization': `Bearer ${token}`
  }
});

console.log(response.data.data); // Array of ICS records
```

### PHP
```php
$response = $guzzle->get('/api/rul/ics', [
    'headers' => [
        'Authorization' => 'Bearer ' . $token
    ]
]);

$icsRecords = json_decode($response->getBody())->data;
```

---

## Business Logic

### Data Relationships
- Loads complete ICS records with nested relationships
- Includes operator information and certifications
- Includes all check-in details with personnel data

### Access Control
- RUL authentication required
- Returns all ICS records visible to authenticated RUL
- No filtering based on RUL permissions (shows all records)

### Performance Considerations
- Uses eager loading to prevent N+1 queries
- Large datasets may impact response time
- Consider pagination for production use

---

## Related Endpoints
- `GET /api/rul/ics/search` - Search with filters
- `GET /api/rul/ics/{id}/show` - Get specific record
- `POST /api/rul/ics/create` - Create new record

## Notes
- Response includes complete nested data structure
- No pagination implemented - returns all records
- Suitable for dashboard overviews and administrative interfaces
- Consider implementing pagination for large datasets
- Status field indicates current operational state