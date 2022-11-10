<?php
/**
 * This file is a gateway to perform fraud scoring in real time.
 * 
 * 1. Configure the $API_KEY by putting the value provided in our app (https://app.knytify.com).
 *
 * 2. Put this file in your php-executing server (for example, Apache). Requires CURL allowed as php extension.
 *
 * 3. Now you can call this file with a session id to receive a score (http://your-domain.com/path-to/knight.php?sid=....).
 * 
 * Outputs a dictionary where the keys are the indicator keys, and the values fraud-scoring floats (0=safe, 1=bad)
 * 
 * {
 *  "final": 0.952,
 *  "some-indicator": 0.4 
 * }
 * 
 * To see which indicators are available, check our documentation.
 * 
 * Knytify SARL
 */


/**
 * Configuration
 */
$API_KEY = "...";


/**
 * Request validation
 */
if(empty($_GET['sid']) || strlen($_GET['sid']) < 60 || strlen($_GET['sid']) > 80) {
    http_response_code(400);
    exit;
}

/**
 * Call Knytify to get the scoring.
 */

$headers = ['Content-Type: application/json', 'Api-Key: ' . $API_KEY];
$payload = json_encode(['sid' => $_GET['sid']]);

$ch = curl_init( $url );
curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
@curl_setopt($ch, CURLOPT_HEADER, true);
$output = curl_exec($ch);
$result_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);


/**
 * Validate the response
 */

if($result_code != 200) {
    http_response_code($result_code);
    exit;
}

/**
 * Respond the score
 */
echo $output;

?>