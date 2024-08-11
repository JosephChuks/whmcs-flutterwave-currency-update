<?php
/* 
    WHMCS Currency Exchange Rate Auto Update
    using Flutterwave Transfer Rate API
    
    Flutterwave secretKey is required
    You can save secretKey in WHMCS configuration.php
    
    @author Joseph Chuks <info@josephchuks.com> 
    @copyright Copyright (c) Joseph Chuks 2024
*/

// Import WHMCS Configuration
include '../configuration.php';
$mysqli = new mysqli($db_host, $db_username, $db_password, $db_name);

$sourceCurrency = 'NGN'; // Source currency 
$destinationCurrency = 'USD'; // Destination currency (WHMCS Base Currency)

header('Content-Type: application/json');
$response = array(
    'status' => 'error',
    'message' => ''
);

if ($mysqli->connect_error) {
    $response['message'] = 'Failed to connect to database';
    echo json_encode($response);
    exit();
}

$ch = curl_init();

$base_url = "https://api.flutterwave.com/v3/transfers/rates";
$query_params = http_build_query([
    'amount' => 1,
    'source_currency' => $sourceCurrency,
    'destination_currency' => $destinationCurrency
]);

$url = $base_url . '?' . $query_params;

$headers = [
    "Authorization: Bearer $flutterwaveSecret",
    "Content-Type: application/json"
];

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$curlResponse = curl_exec($ch);

if (curl_errno($ch)) {
    $response['message'] = 'Error: ' . curl_error($ch);
    curl_close($ch);
    echo json_encode($response);
    exit();
}

curl_close($ch);

$data = json_decode($curlResponse, true);
if (isset($data['data']['rate'])) {
    $rate = $data['data']['rate'];

    $query = "SELECT * FROM tblcurrencies WHERE code = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('s', $sourceCurrency);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $query = "UPDATE tblcurrencies SET rate = ? WHERE code = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('ds', $rate, $sourceCurrency);

        if ($stmt === false) {
            $response['message'] = 'Error preparing statement for update';
        } else {
            if ($stmt->execute()) {
                $response['status'] = "success";
                $response['message'] = "Current $destinationCurrency Rate to $sourceCurrency: 1 $destinationCurrency = $rate $sourceCurrency";
            } else {
                $response['message'] = 'Error executing update: ' . htmlspecialchars($stmt->error);
            }
        }
    } else {
        $response['message'] = "Source Currency: $sourceCurrency not found";
    }
} else {
    $response['message'] = 'Invalid response from API';
}

echo json_encode($response);
?>
