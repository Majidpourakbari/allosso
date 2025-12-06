# AlloSSO API Documentation

## Base URL
```
https://www.allo-sso.com/api/v1
```

## Authentication

All API requests require an API key for authentication. You can provide the API key in two ways:

### Method 1: HTTP Header (Recommended)
```
X-API-Key: your-api-key-here
```

### Method 2: Query Parameter
```
?api_key=your-api-key-here
```

## Setup

1. Add your API key to the `.env` file:
```env
ALLO_SSO_API_KEY=your-secret-api-key-here
```

2. Generate a secure API key (recommended length: 32+ characters)

## Endpoints

### 1. Verify Allohash (Full User Information)

Verify an allohash and retrieve complete user information.

**Endpoint:** `POST /api/v1/verify-allohash`

**Headers:**
```
X-API-Key: your-api-key-here
Content-Type: application/json
```

**Request Body:**
```json
{
    "allohash": "hashed-user-identifier"
}
```

**Success Response (200):**
```json
{
    "success": true,
    "message": "User verified successfully",
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "phone": "+1234567890",
        "allohash": "hashed-user-identifier",
        "access_erp": true,
        "access_admin_portal": false,
        "access_ai_developer": true,
        "created_at": "2025-01-01T00:00:00.000000Z"
    }
}
```

**Error Response (404):**
```json
{
    "success": false,
    "message": "Invalid allohash",
    "data": null
}
```

**Error Response (401):**
```json
{
    "success": false,
    "message": "Invalid API key"
}
```

**cURL Example:**
```bash
curl -X POST https://www.allo-sso.com/api/v1/verify-allohash \
  -H "X-API-Key: your-api-key-here" \
  -H "Content-Type: application/json" \
  -d '{"allohash": "hashed-user-identifier"}'
```

---

### 2. Check Allohash (Lightweight Validation)

Quickly check if an allohash is valid without retrieving user details.

**Endpoint:** `GET /api/v1/check-allohash`

**Headers:**
```
X-API-Key: your-api-key-here
```

**Query Parameters:**
- `allohash` (required): The allohash to validate

**Success Response (200):**
```json
{
    "success": true,
    "valid": true,
    "message": "Allohash is valid"
}
```

**Invalid Allohash Response (200):**
```json
{
    "success": true,
    "valid": false,
    "message": "Allohash is invalid"
}
```

**cURL Example:**
```bash
curl -X GET "https://www.allo-sso.com/api/v1/check-allohash?allohash=hashed-user-identifier" \
  -H "X-API-Key: your-api-key-here"
```

---

### 3. External Authentication (Email & Password)

Authenticate users from external sites using email and password. If the user doesn't exist, they will be created automatically. Returns allohash for SSO login.

**Endpoint:** `POST /api/v1/external-auth`

**Headers:**
```
X-API-Key: your-api-key-here
Content-Type: application/json
```

**Request Body:**
```json
{
    "email": "user@example.com",
    "password": "user-password",
    "name": "User Name (optional)"
}
```

**Success Response (200):**
```json
{
    "success": true,
    "message": "Authentication successful",
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "user@example.com",
        "allohash": "$2y$12$WxVHNAEY7rw1CcZJIleCZugboCL/7fNc321RjDQRNlWQCkjzNxNMq",
        "created_at": "2025-01-01T00:00:00.000000Z"
    }
}
```

**Error Response (401):**
```json
{
    "success": false,
    "message": "Invalid email or password"
}
```

**Error Response (422):**
```json
{
    "success": false,
    "message": "The email field is required.",
    "errors": {
        "email": ["The email field is required."]
    }
}
```

**cURL Example:**
```bash
curl -X POST https://www.allo-sso.com/api/v1/external-auth \
  -H "X-API-Key: your-api-key-here" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user@example.com",
    "password": "user-password",
    "name": "John Doe"
  }'
```

**PHP Example for Allolancer:**
```php
<?php
$apiKey = 'your-api-key-here';
$email = $_POST['email'];
$password = $_POST['password'];

$ch = curl_init('https://www.allo-sso.com/api/v1/external-auth');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'X-API-Key: ' . $apiKey,
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'email' => $email,
    'password' => $password
]));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$data = json_decode($response, true);

if ($data['success'] && $httpCode === 200) {
    $allohash = $data['data']['allohash'];
    // Redirect to AlloSSO with platform parameter for branding
    header('Location: https://www.allo-sso.com?platform=allolancer&allohash=' . urlencode($allohash));
    exit;
} else {
    echo json_encode([
        'success' => false,
        'error' => $data['message'] ?? 'Authentication failed'
    ]);
}
?>
```

**How it works:**
1. External site (e.g., allolancer.com) receives email and password from user
2. External site validates credentials against its own database
3. If valid, external site calls this API with email and password
4. API checks if user exists in AlloSSO:
   - If user exists: validates password and returns allohash
   - If user doesn't exist: creates new user and returns allohash
5. External site uses the returned allohash to redirect user to SSO dashboard
6. **Platform Detection:** When redirecting, include `?platform=allolancer` to show platform-specific branding

**Note:** This endpoint supports CORS and can be called from any domain. The API key authentication ensures only authorized sites can use this endpoint.

---

## Platform Detection & Branding

AlloSSO supports platform-specific branding and filtering. When users are redirected from external platforms, the system can detect the platform and show customized branding.

### Supported Platforms

| Platform | Domain | Logo File | Query Parameter |
|----------|--------|-----------|-----------------|
| AlloLancer | allolancer.com | allolaner.jpg | `platform=allolancer` |
| AlloAI | alloai.com | alloai.jpg | `platform=alloai` |

### How Platform Detection Works

The system detects the platform through two methods:

1. **HTTP Referer Header**: Automatically detects platform from the `Referer` header when users navigate from external sites
2. **Query Parameter**: Explicitly specify platform using `?platform=allolancer` or `?platform=alloai`

### Platform-Specific Features

When a platform is detected:

- **Login/Register Pages**: Display platform logo and Persian message: "برای ورود به سایت [Platform Name] باید ورود و عضویت کنید"
- **Dashboard**: Shows only the platform-specific access item (filters out other platforms)
- **Session Persistence**: Platform information is stored in session and persists throughout the authentication flow

### Redirect Examples

**With Platform Parameter:**
```
https://www.allo-sso.com?platform=allolancer
https://www.allo-sso.com?platform=alloai
```

**With Allohash and Platform:**
```
https://www.allo-sso.com?platform=allolancer&allohash=user-allohash
```

**From External Site (Automatic Detection):**
When users click a link from `allolancer.com` to `www.allo-sso.com`, the system automatically detects the platform from the HTTP Referer header.

### Integration Example with Platform Detection

```php
<?php
// After successful authentication on external site
$allohash = get_allohash_from_api();

// Option 1: Redirect with platform parameter (recommended)
header('Location: https://www.allo-sso.com?platform=allolancer&allohash=' . urlencode($allohash));

// Option 2: Redirect without platform (will use referrer detection)
header('Location: https://www.allo-sso.com?allohash=' . urlencode($allohash));
?>
```

**JavaScript Example:**
```javascript
// After getting allohash from API
const allohash = response.data.allohash;
window.location.href = `https://www.allo-sso.com?platform=allolancer&allohash=${encodeURIComponent(allohash)}`;
```

---

## Error Codes

| Status Code | Description |
|------------|-------------|
| 200 | Success |
| 401 | Unauthorized - Invalid or missing API key |
| 404 | Not Found - Invalid allohash |
| 422 | Validation Error - Missing or invalid parameters |
| 500 | Internal Server Error |

## Response Format

All API responses follow this structure:

**Success:**
```json
{
    "success": true,
    "message": "Description of the result",
    "data": { ... }
}
```

**Error:**
```json
{
    "success": false,
    "message": "Error description",
    "data": null
}
```

## Integration Examples

### PHP Example
```php
<?php
$apiKey = 'your-api-key-here';
$allohash = 'user-allohash';

$ch = curl_init('https://www.allo-sso.com/api/v1/verify-allohash');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'X-API-Key: ' . $apiKey,
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['allohash' => $allohash]));

$response = curl_exec($ch);
$data = json_decode($response, true);

if ($data['success']) {
    $user = $data['data'];
    echo "User: " . $user['name'] . " (" . $user['email'] . ")";
} else {
    echo "Error: " . $data['message'];
}
curl_close($ch);
?>
```

### JavaScript Example
```javascript
const apiKey = 'your-api-key-here';
const allohash = 'user-allohash';

fetch('https://www.allo-sso.com/api/v1/verify-allohash', {
    method: 'POST',
    headers: {
        'X-API-Key': apiKey,
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({ allohash: allohash })
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        console.log('User:', data.data.name, data.data.email);
    } else {
        console.error('Error:', data.message);
    }
})
.catch(error => console.error('Request failed:', error));
```

### Python Example
```python
import requests

api_key = 'your-api-key-here'
allohash = 'user-allohash'

response = requests.post(
    'https://www.allo-sso.com/api/v1/verify-allohash',
    headers={
        'X-API-Key': api_key,
        'Content-Type': 'application/json'
    },
    json={'allohash': allohash}
)

data = response.json()
if data['success']:
    user = data['data']
    print(f"User: {user['name']} ({user['email']})")
else:
    print(f"Error: {data['message']}")
```

## Security Best Practices

1. **Never expose your API key** in client-side code or public repositories
2. **Use HTTPS** for all API requests
3. **Store API keys securely** in environment variables or secure configuration files
4. **Rotate API keys** periodically
5. **Monitor API usage** for suspicious activity

## Rate Limiting

Currently, there are no rate limits implemented. However, please use the API responsibly and avoid making excessive requests.

## Support

For API support or questions, please contact the AlloSSO platform team.

---

**Last Updated:** 2025-12-06

**Recent Updates:**
- Added platform detection and branding support
- Platform-specific logo and message display on login/register pages
- Dashboard filtering to show only platform-specific access items
- Support for `allolancer` and `alloai` platforms

