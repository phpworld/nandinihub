// Test script to verify authentication fix
const testAuthResponse = {
  "refresh_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
  "token_type": "Bearer",
  "user": {
    "address": "LGF 10 Anora kalan papnamow Road",
    "city": "Lucknow",
    "created_at": "2025-05-26 11:41:02",
    "email": "vinaysingh43@gmail.com",
    "first_name": "Vinay",
    "id": "3",
    "is_active": "1",
    "last_name": "Singh",
    "phone": "919457790679",
    "pincode": "226028",
    "role": "customer",
    "state": "Utter Pradesh",
    "updated_at": "2025-05-26 12:57:36"
  }
};

// Test the logic
function testAuthLogic(response) {
  console.log('Testing auth logic with response:', response);
  
  // The authService returns the response.data directly, which contains user, token, etc.
  if (response && response.user && response.token) {
    console.log('✅ Login successful, updating auth state with user:', response.user.first_name);
    return { success: true, user: response.user };
  } else if (response && response.success && response.data && response.data.user) {
    // Fallback for different response structure
    console.log('✅ Login successful (fallback structure), updating auth state with user:', response.data.user.first_name);
    return { success: true, user: response.data.user };
  } else {
    console.log('❌ Login failed - invalid response structure:', response);
    return { success: false, message: 'Login failed' };
  }
}

// Run test
const result = testAuthLogic(testAuthResponse);
console.log('Test result:', result);

module.exports = { testAuthLogic };
