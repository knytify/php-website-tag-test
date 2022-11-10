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
 *  "result": {
 *    "final": 0.952,
 *    "some-indicator": 0.4 
 *  }
 * }
 * 
 * To see which indicators are available, check our documentation.
 * 
 * Knytify SARL
 */


/**
 * Configuration
 */
$API_KEY = file_get_contents("api_key.txt");
$DEBUG = true;


/**
 * Request validation
 */
if(empty($_GET['sid']) || strlen($_GET['sid']) < 40 || strlen($_GET['sid']) > 70) {
    http_response_code(400);
    die("Wrong SID");
}

/**
 * Call Knytify to get the scoring.
 */

$headers = ['Content-Type: multipart/form-data', 'Api-Key: ' . $API_KEY];
$payload = ['sid' => $_GET['sid']];

$ch = curl_init( "https://live.knytify.com/predict/" );
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLINFO_HEADER_OUT, 1);

$output = curl_exec($ch);
$result_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$request_content_type = curl_getinfo($ch,CURLINFO_CONTENT_TYPE);

$error = null;
if(curl_errno($ch)) {
    $error = curl_error($ch);
} 
curl_close($ch);


/**
 * Validate the response
 */

if(!empty($error)) {
    http_response_code($result_code);
    if($DEBUG) {
        echo "content type: " . $request_content_type . "\n";
        echo $error;
    }
    exit;
}

/**
 * Respond the score
 */
echo $output;

?>