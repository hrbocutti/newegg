<?php
// Request Newegg API! REST Web Service using
// HTTP POST with curl. PHP4/PHP5
// Allows retrieval of HTTP status code for error reporting

error_reporting(E_ALL);

$SellerID = 'A006';

// The POST URL and parameters
// Please make sure your request URL is all in lower case

$request = 'https://api.newegg.com/marketplace/ordermgmt/orderstatus/orders/12345678?sellerid='.$SellerID;

$header_array =array(
	'Content-Type:application/xml',
	'Accept:application/xml',
	'Authorization: your API-key here',
	'SecretKey: your secretkey here');
try{
// Get the curl session object

	$session = curl_init($request);

// Set the POST options.

	curl_setopt($session, CURLOPT_HEADER, 1);
	curl_setopt($session,CURLOPT_HTTPHEADER,$header_array);
	curl_setopt($session, CURLOPT_HEADER, false);
	curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($session, CURLOPT_SSL_VERIFYPEER, 0);

// Do the POST and then close the session
	$response = curl_exec($session);
	curl_close($session);
	print $response;
}catch(InvalidArgumentException $e){
	curl_close($session);
	throw $e;
}
catch(Exception $e){
	curl_close($session);
	throw $e;
}