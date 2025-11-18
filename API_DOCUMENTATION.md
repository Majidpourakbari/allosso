# AlloSSO API Documentation

## Base URL
```
https://allo-sso.com/api/v1
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
curl -X POST https://allo-sso.com/api/v1/verify-allohash \
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
curl -X GET "https://allo-sso.com/api/v1/check-allohash?allohash=hashed-user-identifier" \
  -H "X-API-Key: your-api-key-here"
```

---

## Error Codes

| Status Code | Description |
|------------|-------------|
| 200 | Success |
| 401 | Unauthorized - Invalid or missing API key |
| 404 | Not Found - Invalid allohash |
| 422 | Validation Error - Missing or invalid parameters |

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

$ch = curl_init('https://allo-sso.com/api/v1/verify-allohash');
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

fetch('https://allo-sso.com/api/v1/verify-allohash', {
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
    'https://allo-sso.com/api/v1/verify-allohash',
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

**Last Updated:** 2025-11-17

