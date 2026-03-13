# ICS Controller API Documentation - Overview

## Introduction
The ICS Controller manages all operations related to ICS 211 (Incident Check-In List) records, which are central to incident management and resource tracking.

## Authentication
All endpoints require:
- **Authentication**: Bearer token authentication
- **Middleware**: `rul.auth`, `throttle:60,1`
- **Access Level**: Resource Unit Leader (RUL)

## Base URL
```
/api/rul/ics
```

## Endpoints Summary

### Core CRUD Operations

| Method | Endpoint | Description | Documentation |
|--------|----------|-------------|---------------|
| GET | `/` | List all ICS records | [View Details](ics_controller_index.md) |
| GET | `/search` | Search ICS records with filters | [View Details](ics_controller_search.md) |
| POST | `/create` | Create new ICS record | [View Details](ics_controller_store.md) |
| GET | `/{uuid}/show` | Get specific ICS record | [View Details](ics_controller_show.md) |
| POST | `/{uuid}/edit` | Update ICS record | [View Details](ics_controller_update.md) |
| POST | `/{uuid}/delete` | Delete ICS record | *Documentation pending* |

### Status Management

| Method | Endpoint | Description | Documentation |
|--------|----------|-------------|---------------|
| POST | `/{uuid}/status/{status}` | Update ICS record status | *Documentation pending* |
| POST | `/checkin/{uuid}/status` | Update check-in detail status | *Documentation pending* |

### Collaboration

| Method | Endpoint | Description | Documentation |
|--------|----------|-------------|---------------|
| POST | `/join` | Join ICS as operator using token | [View Details](ics_controller_joinIcs.md) |

### Logging and History

| Method | Endpoint | Description | Documentation |
|--------|----------|-------------|---------------|
| GET | `/{icsUuid}/logs` | Get ICS activity logs | *Documentation pending* |

## Common Response Format

### Success Response Structure
```json
{
  "success": true,
  "message": "Operation completed successfully",
  "data": { ... }
}
```

### Error Response Structure
```json
{
  "success": false,
  "message": "Error description"
}
```

### Validation Error Response
```json
{
  "success": false,
  "message": "The given data was invalid",
  "errors": {
    "field_name": ["Error message"]
  }
}
```

## Data Models

### ICS 211 Record Structure
```json
{
  "id": 1,
  "uuid": "550e8400-e29b-41d4-a716-446655440000",
  "token": "ABC12345",
  "name": "Incident Name",
  "type": "incident_type",
  "order_request_number": "ORN-2025-001",
  "checkin_location": "Check-in Location",
  "region": "Geographic Region",
  "remarks": "Additional notes",
  "remarks_image_attachment": "path/to/image.jpg",
  "status": "pending|ongoing|completed",
  "created_at": "2025-03-13T08:30:00Z",
  "updated_at": "2025-03-13T08:30:00Z",
  "operators": [ ... ],
  "check_in_details": [ ... ]
}
```

### Check-In Detail Structure
```json
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
  "name_of_leader": "Leader Name",
  "contact_information": "Contact Details",
  "quantity": 4,
  "department": "Department Name",
  "departure_point_of_origin": "Origin Location",
  "departure_date": "2025-03-13",
  "departure_time": "08:00:00",
  "departure_method_of_travel": "Travel Method",
  "with_manifest": true,
  "incident_assignment": "Assignment Details",
  "other_qualifications": "Additional Notes",
  "sent_resl": false,
  "status": "pending|accepted|rejected|ongoing|completed",
  "personnel": { ... }
}
```

## Status Workflows

### ICS Record Status Flow
1. **pending** → **ongoing** → **completed**

### Check-In Detail Status Flow
1. **pending** → **accepted**/**rejected**
2. **accepted** → **ongoing** → **completed**

### Personnel Status Integration
- When assigned to ICS: status → **standby**
- When ICS is ongoing: status → **assigned**/**active**
- When ICS completed: status → **available**

## Business Rules

### Access Control
- Only operators can view/modify ICS records
- Creator automatically becomes operator
- Token-based invitation system for additional operators

### Personnel Management
- Personnel can only be assigned to one active incident
- Status automatically updates based on assignments
- Personnel removal resets status to 'available'

### File Handling
- Remarks can include image attachments
- Files stored in `public/ics/remarks` directory
- Old files deleted when new ones uploaded

### Activity Logging
- All operations automatically logged
- Comprehensive audit trail maintained
- User accountability ensured

## Error Handling

### Common HTTP Status Codes
- **200**: Success
- **201**: Created successfully
- **400**: Bad request (already exists, invalid operation)
- **401**: Unauthorized
- **404**: Resource not found
- **422**: Validation error
- **429**: Too many requests

### Rate Limiting
- 60 requests per minute per authenticated user
- Prevents abuse and ensures fair usage
- Applies to all ICS endpoints

## Security Considerations

### Authentication
- Bearer token required for all endpoints
- Tokens should be kept secure
- Regular token rotation recommended

### Authorization
- Operator-level access required for modifications
- UUID-based resource identification prevents enumeration
- Input validation prevents injection attacks

### Data Privacy
- Contact information included in responses
- Ensure proper authorization before exposing data
- Consider field-level permissions for sensitive data

## Performance Tips

### Efficient Queries
- Endpoints use eager loading to prevent N+1 problems
- Consider pagination for large datasets
- Use search endpoint instead of client-side filtering

### Caching Strategies
- Cache frequently accessed ICS records
- Invalidate caches on updates
- Consider Redis for distributed caching

## Integration Examples

### Mobile App Integration
```javascript
// Authenticate and get ICS records
const response = await fetch('/api/rul/ics', {
  headers: {
    'Authorization': 'Bearer ' + token,
    'Accept': 'application/json'
  }
});
```

### External System Integration
```php
// Join ICS from external system
$client = new GuzzleHttp\Client();
$response = $client->post('/api/rul/ics/join', [
    'headers' => ['Authorization' => 'Bearer ' . $token],
    'json' => ['token' => $inviteToken]
]);
```

## Additional Resources

### Related Controllers
- **RulProfileController** - RUL profile management
- **PersonnelController** - Personnel management
- **IcsLogController** - Activity logging
- **AnalyticController** - Reporting and analytics

### Documentation Status
- ✅ Core CRUD operations (index, search, store, show, update)
- ✅ Collaboration (joinIcs)
- ⏳ Status management endpoints
- ⏳ Logging endpoints
- ⏳ Specialized operations

For complete endpoint documentation, refer to individual function documentation files.