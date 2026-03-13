# ICS Controller - Update Function API Documentation

## Overview
Updates an existing ICS 211 record, including its check-in details, with comprehensive change tracking and personnel status management.

## Endpoint
```
POST /api/rul/ics/{uuid}/edit
```

## Authentication
- **Required**: Yes
- **Middleware**: `rul.auth`, `throttle:60,1`
- **Access Level**: Resource Unit Leader (RUL) with operator access

## Request

### Headers
```
Authorization: Bearer {token}
Content-Type: multipart/form-data (if uploading files) or application/json
Accept: application/json
```

### URL Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| uuid | string | Yes | UUID of the ICS 211 record to update |

### Body Parameters
All parameters are optional for updates. Only provide the fields you want to change.

#### ICS Record Fields

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| name | string | No | Name/title of the incident |
| type | string | No | Type of incident (fire, flood, rescue, etc.) |
| order_request_number | string | No | Official order or request number |
| checkin_location | string | No | Physical location for check-in |
| region | string | No | Geographic region or area |
| remarks | text | No | Additional notes or comments |
| remarks_image_attachment | file | No | Image file attachment (replaces existing) |
| status | string | No | Current status (pending, ongoing, completed) |
| check_in_details | array | No | Array of check-in detail objects |

#### Check-In Details Update Behavior
- **Existing items**: Include `uuid` field to update existing check-in details
- **New items**: Omit `uuid` field to create new check-in details
- **Deleted items**: Exclude from array to delete existing check-in details

### Example Request Body (JSON)

```json
{
  "name": "Updated Wildfire Response - Pine Valley",
  "status": "ongoing",
  "remarks": "Incident escalated, additional resources requested",
  "check_in_details": [
    {
      "uuid": "950e8400-e29b-41d4-a716-446655440000",
      "personnel_id": 2,
      "order_request_number": "ORN-2025-001-A",
      "checkin_date": "2025-03-13",
      "checkin_time": "08:30:00",
      "kind": "Personnel",
      "category": "Personnel",
      "type": "Fire Suppression",
      "resource_identifier": "ENG-01",
      "name_of_leader": "Captain Smith",
      "contact_information": "+1234567890",
      "quantity": 6,
      "department": "Metro Fire Department",
      "departure_point_of_origin": "Station 15",
      "departure_date": "2025-03-13",
      "departure_time": "08:00:00",
      "departure_method_of_travel": "Fire Engine",
      "with_manifest": true,
      "incident_assignment": "Structure Protection",
      "other_qualifications": "Hazmat certified",
      "sent_resl": false
    },
    {
      "personnel_id": 3,
      "order_request_number": "ORN-2025-001-C",
      "checkin_date": "2025-03-13",
      "checkin_time": "10:00:00",
      "kind": "Personnel",
      "category": "Personnel",
      "type": "Medical Support",
      "resource_identifier": "MEDIC-01",
      "name_of_leader": "Paramedic Jones",
      "contact_information": "+1234567894",
      "quantity": 2,
      "department": "EMS Department",
      "departure_point_of_origin": "Hospital",
      "departure_date": "2025-03-13",
      "departure_time": "09:45:00",
      "departure_method_of_travel": "Ambulance",
      "with_manifest": true,
      "incident_assignment": "Medical Support",
      "other_qualifications": "Advanced Life Support",
      "sent_resl": false
    }
  ]
}
```

## Response

### Success Response (200)
```json
{
  "success": true,
  "message": "ICS 211 record updated successfully",
  "data": {
    "id": 1,
    "uuid": "550e8400-e29b-41d4-a716-446655440000",
    "token": "ABC12345",
    "name": "Updated Wildfire Response - Pine Valley",
    "type": "wildfire",
    "order_request_number": "ORN-2025-001",
    "checkin_location": "Cedar Creek Fire Station",
    "region": "Northern District",
    "remarks": "Incident escalated, additional resources requested",
    "remarks_image_attachment": "ics/remarks/image_124.jpg",
    "status": "ongoing",
    "created_at": "2025-03-13T08:30:00Z",
    "updated_at": "2025-03-13T10:15:00Z",
    "operators": [
      {
        "id": 1,
        "uuid": "750e8400-e29b-41d4-a716-446655440000",
        "name": "Captain Jane Smith",
        "contact_number": "+1234567890",
        "serial_number": "RUL-2025-001",
        "department": "Metro Fire Department",
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
        "quantity": 6,
        "department": "Metro Fire Department",
        "departure_point_of_origin": "Station 15",
        "departure_date": "2025-03-13",
        "departure_time": "08:00:00",
        "departure_method_of_travel": "Fire Engine",
        "with_manifest": true,
        "incident_assignment": "Structure Protection",
        "other_qualifications": "Hazmat certified",
        "sent_resl": false,
        "status": "pending",
        "personnel": {
          "id": 2,
          "uuid": "a60e8400-e29b-41d4-a716-446655440000",
          "name": "Jane Firefighter",
          "contact_number": "+1234567895",
          "serial_number": "PER-2025-002",
          "department": "Metro Fire Department",
          "status": "standby"
        }
      },
      {
        "id": 3,
        "uuid": "970e8400-e29b-41d4-a716-446655440000",
        "order_request_number": "ORN-2025-001-C",
        "checkin_date": "2025-03-13",
        "checkin_time": "10:00:00",
        "kind": "Personnel",
        "category": "Personnel",
        "type": "Medical Support",
        "resource_identifier": "MEDIC-01",
        "name_of_leader": "Paramedic Jones",
        "contact_information": "+1234567894",
        "quantity": 2,
        "department": "EMS Department",
        "departure_point_of_origin": "Hospital",
        "departure_date": "2025-03-13",
        "departure_time": "09:45:00",
        "departure_method_of_travel": "Ambulance",
        "with_manifest": true,
        "incident_assignment": "Medical Support",
        "other_qualifications": "Advanced Life Support",
        "sent_resl": false,
        "status": "pending",
        "personnel": {
          "id": 3,
          "uuid": "a70e8400-e29b-41d4-a716-446655440000",
          "name": "Bob Paramedic",
          "contact_number": "+1234567896",
          "serial_number": "PER-2025-003",
          "department": "EMS Department",
          "status": "standby"
        }
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

### Validation Error Response (422)
```json
{
  "success": false,
  "message": "The given data was invalid",
  "errors": {
    "status": ["The status field must be one of: pending, ongoing, completed"],
    "check_in_details.0.quantity": ["The quantity field must be an integer"]
  }
}
```

## Implementation Details

### Smart Update Logic
- Only updates fields that are provided in the request
- Uses `array_filter` to remove null/empty values
- Preserves existing values for non-provided fields

### File Upload Management
- Replaces existing image if new file uploaded
- Deletes old file from storage when replaced
- Maintains existing file if no new upload provided

### Check-In Details Management

#### Update Existing Items
- Items with `uuid` are updated in place
- Personnel assignments can be changed
- All fields can be modified

#### Create New Items
- Items without `uuid` are created as new entries
- Automatic UUID generation
- Full validation applied

#### Delete Removed Items
- Items not included in the array are deleted
- Associated personnel status reset to 'available'
- Logging of personnel removal

### Personnel Status Management

#### Automatic Status Updates
- Personnel assigned to check-in details: status → 'standby'
- Personnel removed from check-in details: status → 'available'
- Status changes are logged in ics_logs table

#### Personnel Changes Tracking
- Detects when personnel_id changes in existing check-in details
- Handles old personnel cleanup and new personnel assignment
- Sends push notifications for personnel changes

### Comprehensive Logging
All changes are automatically logged:
- ICS record field updates
- Personnel additions and removals
- Check-in detail modifications
- Status transitions

### Change Detection
```php
$oldValues = $ics211Record->toArray();
// ... perform updates ...
$newValues = $ics211Record->fresh()->toArray();
$changes = array_diff_assoc($newValues, $oldValues);
```

## Business Logic

### Personnel Assignment Rules
1. Personnel can only be assigned to one active incident at a time
2. Personnel status automatically reflects their assignment
3. Removing personnel resets their status to 'available'
4. Personnel changes trigger notifications

### File Management Rules
1. Only image files accepted for remarks attachments
2. Old files are deleted when replaced
3. File storage uses Laravel's storage system
4. Files are stored in `public` disk under `ics/remarks`

### Status Validation
- Valid statuses: 'pending', 'ongoing', 'completed'
- Status transitions should be logical
- Status changes are logged for audit trails

## Use Cases

### Incident Escalation
- Update status from 'pending' to 'ongoing'
- Add additional personnel and resources
- Update remarks with current situation

### Resource Reallocation
- Change personnel assignments
- Modify quantities and equipment
- Update contact information

### Incident Documentation
- Update remarks and attachments
- Record resource modifications
- Track operational changes

### Multi-Agency Coordination
- Add resources from different agencies
- Update contact information
- Coordinate resource deployment

## Security Considerations

### Authorization
- Only operators can update ICS records
- Validate user has operator privileges
- Log all update activities

### Data Validation
- Comprehensive validation via form requests
- File upload security checks
- SQL injection protection

### Audit Trail
- All changes logged with before/after values
- User accountability maintained
- Change timestamps recorded

## Performance Optimization

### Database Transactions
Consider wrapping updates in transactions:
```php
DB::transaction(function () {
    // All update operations
});
```

### Eager Loading
- Load relationships efficiently
- Prevent N+1 query problems
- Optimize for large datasets

### Caching Invalidation
- Clear relevant caches after updates
- Update cached data with new values
- Consider cache tags for selective invalidation

## Testing Examples

### cURL Request
```bash
curl -X POST \
  "https://yourdomain.com/api/rul/ics/550e8400-e29b-41d4-a716-446655440000/edit" \
  -H "Authorization: Bearer your-auth-token" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "status": "ongoing",
    "remarks": "Incident escalated"
  }'
```

### JavaScript/Axios
```javascript
const uuid = '550e8400-e29b-41d4-a716-446655440000';
const updates = {
  status: 'ongoing',
  remarks: 'Additional resources deployed'
};

const response = await axios.post(`/api/rul/ics/${uuid}/edit`, updates, {
  headers: {
    'Authorization': 'Bearer ' + token,
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
});

console.log('Updated ICS:', response.data.data);
```

## Related Endpoints
- `GET /api/rul/ics/{id}/show` - Get current ICS record
- `POST /api/rul/ics/{id}/status/{status}` - Update status only
- `POST /api/rul/ics/checkin/{uuid}/status` - Update check-in detail status
- `GET /api/rul/ics/{icsUuid}/logs` - View change history