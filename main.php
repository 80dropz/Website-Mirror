<?php
// Linkvertise Token Validation and KeyAuth Key Distributor

// Replace with your Linkvertise API Key
$LINKVERTISE_API_KEY = "YOUR_LINKVERTISE_API_KEY";

// Replace with your KeyAuth API details
$KEYAUTH_API_URL = "https://keyauth.win/api/endpoint/";
$KEYAUTH_API_KEY = "YOUR_KEYAUTH_API_KEY";

// Check if the token is provided
if (!isset($_GET['token'])) {
    die(json_encode(["status" => "error", "message" => "No token provided."]));
}

$token = $_GET['token'];

// Validate the Linkvertise token
$verifyUrl = "https://api.linkvertise.com/v1/redirect/link/token/validate?token=$token";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $verifyUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "apikey: $LINKVERTISE_API_KEY"
]);
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);

// Check if the token is valid
if (!$data['data']['valid']) {
    die(json_encode(["status" => "error", "message" => "Invalid or expired token."]));
}

// Generate a 5-hour key using KeyAuth API
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $KEYAUTH_API_URL);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'type' => 'generate',
    'expiry' => 5, // Expiry time in hours
    'apikey' => $KEYAUTH_API_KEY,
]));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

$keyResponse = curl_exec($ch);
curl_close($ch);

$keyData = json_decode($keyResponse, true);

// Check if the key was successfully generated
if ($keyData['success']) {
    echo json_encode(["status" => "success", "key" => $keyData['key']]);
} else {
    echo json_encode(["status" => "error", "message" => $keyData['message']]);
}
?>
