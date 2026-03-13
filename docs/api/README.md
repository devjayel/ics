# ICS System API Documentation

## Overview
This documentation covers the REST API endpoints for the Incident Command System (ICS) 211 Record Management System. The API supports incident management, personnel tracking, resource coordination, and real-time collaboration.

## Base URL
```
https://yourdomain.com/api
```

## Authentication
The API uses multiple authentication methods depending on the client type:

### Web Applications
- **Method**: Laravel Sanctum Bearer Tokens
- **Login**: Email/password authentication
- **Headers**: `Authorization: Bearer {token}`

### Mobile Applications
- **Method**: Custom token-based authentication
- **Login**: Serial number authentication
- **Headers**: `Authorization: Bearer {mobile_token}`

## API Controllers Documentation

### 🔐 Authentication
| Controller | Description | Status | Documentation |
|------------|-------------|--------|---------------|
| **LoginController** | Authentication for web and mobile | ✅ Complete | [View Details](login_controller.md) |

**Endpoints Overview:**
- `POST /auth/login` - Web login (email/password)
- `POST /auth/login/app` - Mobile login (serial number)
- `POST /auth/logout` - Web logout
- `POST /auth/logout/app` - Mobile logout
- `POST /auth/validate-token` - Token validation

---

### 🚨 ICS Management
| Controller | Description | Status | Documentation |
|------------|-------------|--------|---------------|
| **IcsController** | Core ICS 211 record management | ✅ Complete | [View Overview](ics_controller_overview.md) |

**Endpoints Overview:**
- `GET /rul/ics` - List all ICS records
- `GET /rul/ics/search` - Search with filters
- `POST /rul/ics/create` - Create new ICS record
- `GET /rul/ics/{uuid}/show` - Get specific ICS record
- `POST /rul/ics/{uuid}/edit` - Update ICS record
- `POST /rul/ics/join` - Join as operator via token
- `POST /rul/ics/{uuid}/status/{status}` - Update status

**Detailed Function Documentation:**
- [index() - List ICS Records](ics_controller_index.md)
- [search() - Search ICS Records](ics_controller_search.md)
- [store() - Create ICS Record](ics_controller_store.md)
- [show() - Get ICS Record](ics_controller_show.md)
- [update() - Update ICS Record](ics_controller_update.md)
- [joinIcs() - Join as Operator](ics_controller_joinIcs.md)

---

### 👥 Personnel Management
| Controller | Description | Status | Documentation |
|------------|-------------|--------|---------------|
| **PersonnelController** | Personnel CRUD operations | ⏳ Pending | *Documentation pending* |
| **PersonnelProfileController** | Personnel profile management | ⏳ Pending | *Documentation pending* |
| **PersonnelIcsController** | Personnel ICS assignments | ⏳ Pending | *Documentation pending* |

**Endpoints Overview (PersonnelController):**
- `GET /rul/personnel` - List personnel
- `POST /rul/personnel/create` - Create personnel
- `GET /rul/personnel/{id}/show` - Get personnel details
- `POST /rul/personnel/{id}/edit` - Update personnel
- `POST /rul/personnel/{id}/delete` - Delete personnel

---

### 👨‍💼 RUL Management
| Controller | Description | Status | Documentation |
|------------|-------------|--------|---------------|
| **RulProfileController** | RUL profile management | ⏳ Pending | *Documentation pending* |

**Endpoints Overview:**
- `GET /rul/profile` - Get RUL profile
- `POST /rul/profile/update` - Update profile
- `POST /rul/profile/change-avatar` - Update avatar

---

### 📊 Analytics & Reporting
| Controller | Description | Status | Documentation |
|------------|-------------|--------|---------------|
| **AnalyticController** | System analytics and reports | ⏳ Pending | *Documentation pending* |
| **DashboardController** | Dashboard data | ⏳ Pending | *Documentation pending* |

**Endpoints Overview:**
- `GET /rul/dashboard` - Dashboard data
- `GET /rul/analytics` - System analytics
- `GET /rul/analytics/map` - Map-based analytics
- `GET /rul/analytics/regions` - Regional data

---

### 📝 Logging & History
| Controller | Description | Status | Documentation |
|------------|-------------|--------|---------------|
| **IcsLogController** | Activity logging and audit | ⏳ Pending | *Documentation pending* |
| **CheckInDetailHistoriesController** | Check-in history tracking | ⏳ Pending | *Documentation pending* |

**Endpoints Overview:**
- `GET /rul/logs/my-logs` - User activity logs
- `GET /rul/ics/{icsUuid}/logs` - ICS-specific logs
- `GET /rul/ics/checkin/{id}/history` - Check-in history

---

## Common Response Formats

### Success Response
```json
{
  "success": true,
  "message": "Operation completed successfully",
  "data": { ... }
}
```

### Error Response
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

## HTTP Status Codes

| Code | Meaning | Description |
|------|---------|-------------|
| 200 | OK | Request successful |
| 201 | Created | Resource created successfully |
| 400 | Bad Request | Invalid request or business logic error |
| 401 | Unauthorized | Authentication required |
| 403 | Forbidden | Insufficient permissions |
| 404 | Not Found | Resource not found |
| 422 | Unprocessable Entity | Validation error |
| 429 | Too Many Requests | Rate limit exceeded |
| 500 | Internal Server Error | Server error |

## Rate Limiting
- **Limit**: 60 requests per minute per user
- **Scope**: Applied to all authenticated endpoints
- **Response**: HTTP 429 when exceeded

## Data Models

### Core Entities
- **ICS 211 Records**: Central incident management documents
- **Check-In Details**: Resource and personnel assignments
- **Personnel**: Individual staff members
- **RULs**: Resource Unit Leaders (managers)
- **Operators**: RULs assigned to specific incidents

### Status Workflows
- **ICS Records**: pending → ongoing → completed
- **Personnel**: available → standby → assigned → active
- **Check-In Details**: pending → accepted/rejected → ongoing → completed

## Security Features

### Authentication Methods
- Multi-factor authentication support
- Role-based access control
- Token-based authentication
- Session management

### Data Protection
- Input validation and sanitization
- SQL injection prevention
- XSS protection
- CSRF protection

### Audit & Compliance
- Comprehensive activity logging
- Change tracking and history
- User accountability
- Compliance reporting

## Testing & Integration

### Development Environment
```bash
# Base API URL for development
API_BASE_URL=http://localhost:8000/api

# Example authentication
curl -H "Authorization: Bearer {token}" \
     -H "Accept: application/json" \
     http://localhost:8000/api/rul/ics
```

### Production Environment
```bash
# Base API URL for production
API_BASE_URL=https://yourdomain.com/api

# Always use HTTPS in production
curl -H "Authorization: Bearer {token}" \
     -H "Accept: application/json" \
     https://yourdomain.com/api/rul/ics
```

### SDKs and Libraries
- **JavaScript**: Axios, Fetch API
- **PHP**: Guzzle HTTP Client
- **Python**: Requests library
- **Mobile**: Platform-specific HTTP clients

## Error Handling Best Practices

### Client-Side Implementation
```javascript
const apiCall = async (endpoint, options = {}) => {
  try {
    const response = await fetch(endpoint, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        ...options.headers
      },
      ...options
    });

    const data = await response.json();

    if (!response.ok) {
      throw new Error(data.message || 'API request failed');
    }

    return data;

  } catch (error) {
    console.error('API Error:', error);
    throw error;
  }
};
```

### Retry Logic
```javascript
const apiCallWithRetry = async (endpoint, options, maxRetries = 3) => {
  for (let i = 0; i <= maxRetries; i++) {
    try {
      return await apiCall(endpoint, options);
    } catch (error) {
      if (i === maxRetries) throw error;

      // Exponential backoff
      await new Promise(resolve => setTimeout(resolve, 1000 * Math.pow(2, i)));
    }
  }
};
```

## Pagination (Future Enhancement)
While not currently implemented, pagination will be added to list endpoints:

```json
{
  "success": true,
  "data": [...],
  "meta": {
    "current_page": 1,
    "total_pages": 10,
    "per_page": 20,
    "total_count": 200
  }
}
```

## WebSocket Integration (Future)
Real-time updates will be implemented via WebSockets for:
- Live incident status updates
- Real-time personnel assignments
- Collaborative editing notifications
- Push notifications delivery

## Support & Resources

### Documentation Roadmap
- ✅ Migration Documentation (Complete)
- ✅ ICS Controller API (Complete)
- ✅ Authentication API (Complete)
- ⏳ Personnel Management API (Pending)
- ⏳ Analytics & Reporting API (Pending)
- ⏳ Logging & History API (Pending)

### Additional Resources
- Database Schema Documentation: `docs/migrations/`
- API Function Documentation: `docs/api/`
- Integration Examples: Per endpoint documentation
- Postman Collection: *To be created*

### Support Contacts
- **Technical Issues**: Development team
- **Business Questions**: Product team
- **Security Concerns**: Security team

---

*Last Updated: March 13, 2026*
*API Version: 1.0*
*Documentation Status: In Progress*