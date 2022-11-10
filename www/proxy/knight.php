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

$headers = ['Content-Type: application/json', 'api-key: ' . $API_KEY];
$payload = http_build_query(['sid' => $_GET['sid']]);

$ch = curl_init(  );
curl_setopt($ch, CURLOPT_URL, "https://live.knytify.com/predict/");
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload );
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_HEADER, false);

$output = curl_exec($ch);
$result_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

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
        echo $error;
    }
    exit;
}

/**
 * Respond the score
 */
echo $output;

?>