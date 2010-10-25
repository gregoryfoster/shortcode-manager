<?php
// PHP Proxy for performing cross-domain AJAX requests.
// Only responds to HTTP GET requests.
// Adapted from http://developer.yahoo.com/javascript/howto-proxy.html

// Open the Curl session.
$session = curl_init($_GET['target_uri']);

// Don't return HTTP headers. Do return the contents of the call.
curl_setopt($session, CURLOPT_HEADER, false);
curl_setopt($session, CURLOPT_RETURNTRANSFER, true);

// Forward the user's IP address to retrieve accurate availability info.
curl_setopt($session, CURLOPT_HTTPHEADER, array('X-FORWARDED-FOR' => $_SERVER['REMOTE_ADDR']));

// Make the call.
$xml = curl_exec($session);

// The web service returns XML. Set the Content-Type appropriately.
header("Content-Type: text/xml");
echo $xml;

curl_close($session);

?>
