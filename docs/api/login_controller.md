# Login Controller API Documentation

## Overview
The Login Controller handles authentication for both web and mobile applications, supporting different authentication methods for Users, RULs (Resource Unit Leaders), and Personnel.

## Authentication Types
- **Web Auth**: Email/password with Laravel Sanctum tokens
- **Mobile Auth**: Serial number-based authentication with custom tokens
- **Role-based**: Supports Users, RULs, and Personnel authentication

## Endpoints

---

## Standard Web Login

### Endpoint
```
POST /api/auth/login
```

### Authentication
- **Required**: No (this is the login endpoint)
- **Middleware**: `throttle:60,1`

### Request

#### Headers
```
Content-Type: application/json
Accept: application/json
```

#### Body Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| email | string | Yes | User email address |
| password | string | Yes | User password (follows Laravel password rules) |

#### Example Request
```json
{
  "email": "admin@example.com",
  "password": "SecurePassword123!"
}
```

### Response

#### Success Response (200)
```json
{
  "success": true,
  "token": "1|abcdefghijklmnopqrstuvwxyz1234567890abcdef",
  "user": {
    "id": 1,
    "uuid": "550e8400-e29b-41d4-a716-446655440000",
    "name": "Admin User",
    "email": "admin@example.com",
    "email_verified_at": "2025-03-13T08:30:00Z",
    "two_factor_confirmed_at": null,
    "created_at": "2025-03-10T14:00:00Z",
    "updated_at": "2025-03-13T08:30:00Z"
  },
  "message": "Login successful"
}
```

#### Invalid Credentials (422)
```json
{
  "success": false,
  "message": "The provided credentials do not match our records."
}
```

#### Validation Error (422)
```json
{
  "success": false,
  "message": "The given data was invalid",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password field is required."]
  }
}
```

---

## Mobile App Login (Serial Number)

### Endpoint
```
POST /api/auth/login/app
```

### Authentication
- **Required**: No (this is the login endpoint)
- **Middleware**: `throttle:60,1`

### Request

#### Headers
```
Content-Type: application/json
Accept: application/json
```

#### Body Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| serial_number | string | Yes | Personnel or RUL serial number |

#### Example Request
```json
{
  "serial_number": "PER-2025-001"
}
```

### Response

#### Success Response - Personnel (200)
```json
{
  "success": true,
  "token": "abcdefghijklmnopqrstuvwxyz1234567890abcdefghijklmnopqrstuvwx",
  "user": {
    "id": 1,
    "uuid": "a50e8400-e29b-41d4-a716-446655440000",
    "rul_id": 1,
    "avatar": "avatars/john_firefighter.jpg",
    "name": "John Firefighter",
    "contact_number": "+1234567891",
    "serial_number": "PER-2025-001",
    "department": "Metro Fire Department",
    "fcm_token": "fcm_token_here",
    "status": "available",
    "created_at": "2025-03-10T14:00:00Z",
    "updated_at": "2025-03-13T08:30:00Z"
  },
  "role": "personnel"
}
```

#### Success Response - RUL (200)
```json
{
  "success": true,
  "token": "abcdefghijklmnopqrstuvwxyz1234567890abcdefghijklmnopqrstuvwx",
  "user": {
    "id": 1,
    "uuid": "750e8400-e29b-41d4-a716-446655440000",
    "avatar": "avatars/captain_smith.jpg",
    "logo": "logos/metro_fire.png",
    "name": "Captain Jane Smith",
    "contact_number": "+1234567890",
    "serial_number": "RUL-2025-001",
    "department": "Metro Fire Department",
    "signature": "signatures/captain_smith.png",
    "fcm_token": "fcm_token_here",
    "certificates": [
      {
        "id": 1,
        "uuid": "850e8400-e29b-41d4-a716-446655440000",
        "certificate_name": "Fire Safety Training",
        "file_path": "certificates/fire_safety_2025.pdf",
        "created_at": "2025-01-15T10:00:00Z"
      }
    ],
    "created_at": "2025-03-01T09:00:00Z",
    "updated_at": "2025-03-13T08:30:00Z"
  },
  "role": "rul"
}
```

#### Invalid Serial Number (422)
```json
{
  "success": false,
  "message": "The provided serial number does not match our records."
}
```

---

## Mobile App Logout

### Endpoint
```
POST /api/auth/logout/app
```

### Authentication
- **Required**: Yes (Bearer token from mobile login)
- **Middleware**: `throttle:60,1`

### Request

#### Headers
```
Authorization: Bearer {mobile_token}
Content-Type: application/json
Accept: application/json
```

#### Body Parameters
None required.

### Response

#### Success Response (200)
```json
{
  "success": true,
  "message": "Logout successful"
}
```

#### Invalid Token (401)
```json
{
  "success": false,
  "message": "Invalid token"
}
```

---

## Token Validation

### Endpoint
```
POST /api/auth/validate-token
```

### Authentication
- **Required**: Yes (Bearer token to validate)
- **Middleware**: `throttle:60,1`

### Request

#### Headers
```
Authorization: Bearer {token_to_validate}
Content-Type: application/json
Accept: application/json
```

#### Body Parameters
None required.

### Response

#### Valid Token - Personnel (200)
```json
{
  "success": true,
  "user": {
    "id": 1,
    "uuid": "a50e8400-e29b-41d4-a716-446655440000",
    "rul_id": 1,
    "avatar": "avatars/john_firefighter.jpg",
    "name": "John Firefighter",
    "contact_number": "+1234567891",
    "serial_number": "PER-2025-001",
    "department": "Metro Fire Department",
    "fcm_token": "fcm_token_here",
    "status": "available",
    "created_at": "2025-03-10T14:00:00Z",
    "updated_at": "2025-03-13T08:30:00Z"
  },
  "role": "personnel"
}
```

#### Valid Token - RUL (200)
```json
{
  "success": true,
  "user": {
    "id": 1,
    "uuid": "750e8400-e29b-41d4-a716-446655440000",
    "avatar": "avatars/captain_smith.jpg",
    "logo": "logos/metro_fire.png",
    "name": "Captain Jane Smith",
    "contact_number": "+1234567890",
    "serial_number": "RUL-2025-001",
    "department": "Metro Fire Department",
    "signature": "signatures/captain_smith.png",
    "fcm_token": "fcm_token_here",
    "certificates": [
      {
        "id": 1,
        "uuid": "850e8400-e29b-41d4-a716-446655440000",
        "certificate_name": "Fire Safety Training",
        "file_path": "certificates/fire_safety_2025.pdf",
        "created_at": "2025-01-15T10:00:00Z"
      }
    ],
    "created_at": "2025-03-01T09:00:00Z",
    "updated_at": "2025-03-13T08:30:00Z"
  },
  "role": "rul"
}
```

#### Invalid Token (401)
```json
{
  "success": false,
  "message": "Invalid token"
}
```

---

## Standard Web Logout

### Endpoint
```
POST /api/auth/logout
```

### Authentication
- **Required**: Yes (Laravel Sanctum token)
- **Middleware**: `auth:sanctum`, `throttle:60,1`

### Request

#### Headers
```
Authorization: Bearer {sanctum_token}
Content-Type: application/json
Accept: application/json
```

#### Body Parameters
None required.

### Response

#### Success Response (200)
```json
{
  "success": true,
  "message": "Logout successful"
}
```

---

## Implementation Details

### Token Generation

#### Web Authentication (Sanctum)
- Uses Laravel Sanctum personal access tokens
- Tokens stored in `personal_access_tokens` table
- Automatic expiration and management

#### Mobile Authentication (Custom)
- Generates 60-character random tokens
- Tokens stored directly in user/personnel/rul tables
- Manual token management

### Authentication Flow

#### Web Application Flow
1. Submit email/password
2. Validate credentials against users table
3. Generate Sanctum token
4. Return token and user data

#### Mobile Application Flow
1. Submit serial number
2. Search in personnel table first
3. If not found, search in RUL table
4. Generate custom token
5. Return token, user data, and role

### Role Detection
- **Personnel**: Identified by serial number format and personnel table lookup
- **RUL**: Identified by serial number format and RUL table lookup with certificates
- **User**: Web-based authentication only

## Security Considerations

### Rate Limiting
- 60 requests per minute per IP
- Prevents brute force attacks
- Applies to all authentication endpoints

### Token Security
- Custom tokens are 60 characters long
- Tokens invalidated on logout
- No token expiration (manual management required)

### Input Validation
- Email format validation for web login
- Serial number validation for mobile login
- Password complexity requirements

### Data Protection
- Passwords never returned in responses
- Sensitive user data properly handled
- Role-based access control

## Use Cases

### Web Dashboard Access
- Administrative users login with email/password
- Full featured dashboard access
- Session-based authentication

### Mobile Field Operations
- Personnel login with serial numbers for quick access
- RULs login for incident management
- Offline-capable authentication tokens

### Token Validation
- Middleware authentication checks
- API request validation
- Session management

## Testing Examples

### Web Login Test
```bash
curl -X POST \
  "https://yourdomain.com/api/auth/login" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "SecurePassword123!"
  }'
```

### Mobile Login Test
```bash
curl -X POST \
  "https://yourdomain.com/api/auth/login/app" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "serial_number": "PER-2025-001"
  }'
```

### Token Validation Test
```bash
curl -X POST \
  "https://yourdomain.com/api/auth/validate-token" \
  -H "Authorization: Bearer your-token-here" \
  -H "Accept: application/json"
```

## Integration Examples

### JavaScript/React Integration
```javascript
// Web login
const loginWeb = async (email, password) => {
  const response = await fetch('/api/auth/login', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    },
    body: JSON.stringify({ email, password })
  });

  const data = await response.json();
  if (data.success) {
    localStorage.setItem('token', data.token);
    localStorage.setItem('user', JSON.stringify(data.user));
  }
  return data;
};

// Mobile login
const loginMobile = async (serialNumber) => {
  const response = await fetch('/api/auth/login/app', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    },
    body: JSON.stringify({ serial_number: serialNumber })
  });

  const data = await response.json();
  if (data.success) {
    localStorage.setItem('mobile_token', data.token);
    localStorage.setItem('user', JSON.stringify(data.user));
    localStorage.setItem('role', data.role);
  }
  return data;
};
```

### PHP Integration
```php
// Web login
$client = new GuzzleHttp\Client();
$response = $client->post('/api/auth/login', [
    'json' => [
        'email' => 'admin@example.com',
        'password' => 'SecurePassword123!'
    ],
    'headers' => ['Accept' => 'application/json']
]);

$data = json_decode($response->getBody(), true);
```

## Error Handling Best Practices

### Client-Side Validation
```javascript
const validateCredentials = (email, password) => {
  const errors = {};

  if (!email || !email.includes('@')) {
    errors.email = 'Valid email required';
  }

  if (!password || password.length < 8) {
    errors.password = 'Password must be at least 8 characters';
  }

  return errors;
};
```

### Response Handling
```javascript
const handleAuthResponse = (response) => {
  if (response.success) {
    // Success: store token and redirect
    handleSuccessfulAuth(response);
  } else {
    // Error: display message
    displayError(response.message);

    if (response.errors) {
      // Validation errors
      displayValidationErrors(response.errors);
    }
  }
};
```