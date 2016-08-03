<?php

require("xmlProdutos.php");

// Request Newegg API! REST Web Service using
// HTTP POST with curl. PHP4/PHP5
// Allows retrieval of HTTP status code for error reporting

define('Authorization', 'c00e3b31d3652a2c1c30c67e92314130');
define('SecretKey', 'a81f0ad9-66eb-4db3-b022-ca3cd76f7020');

define('ITEM_DATA','ITEM_DATA');
define('INVENTORY_AND_PRICE_DATA','INVENTORY_AND_PRICE_DATA');
define('SellerID', 'AB4P');



error_reporting(E_ALL);

echo $request = 'https://api.newegg.com/marketplace/datafeedmgmt/feeds/submitfeed?sellerid='.SellerID.'&requesttype='.ITEM_DATA;

$xml = new XMLNewegg();
$xmlRetorno = $xml->xmlProdutos();

$body = $xmlRetorno;

$header_array =array('Content-Type:application/xml',
	                 'Accept:application/xml',
	                 'Authorization:' . Authorization,
	                 'SecretKey:' . SecretKey);


//var_dump($header_array);


try {

	// Get the curl session object
	$session = curl_init($request);
	$putString = stripslashes($body);
	$putData = tmpfile();
	fwrite($putData, $putString);
	fseek($putData, 0);

	// Set the POST options.
	curl_setopt($session, CURLOPT_HEADER, 1);
	curl_setopt($session, CURLOPT_HTTPHEADER,$header_array);
	curl_setopt($session, CURLOPT_PUT, true);
	curl_setopt($session, CURLOPT_HEADER, false);
	curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($session, CURLOPT_POSTFIELDS, $body);
	curl_setopt($session, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($session, CURLOPT_INFILE, $putData);
	curl_setopt($session, CURLOPT_INFILESIZE, strlen($putString));

	// Do the POST and then close the session

	$response = curl_exec($session);
	curl_close($session);
	print $response;


} catch (InvalidArgumentException $e) {
	curl_close($session);
	throw $e;
}
catch (Exception $e)
{
	curl_close($session);
	throw $e;
}