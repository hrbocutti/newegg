<?php

require("config.php");
//require("xmlProdutos.php");

/**
* Classe to create new product on Newegg
*/
class ClientNewEgg
{
	public function insertNew($bodyXml)
	{

		// Request Newegg API! REST Web Service using
		// HTTP POST with curl. PHP4/PHP5
		// Allows retrieval of HTTP status code for error reporting

		define('ITEM_DATA','ITEM_DATA');
		define('INVENTORY_AND_PRICE_DATA','INVENTORY_AND_PRICE_DATA');



		error_reporting(E_ALL);

		$request = 'https://api.newegg.com/marketplace/datafeedmgmt/feeds/submitfeed?sellerid='.SellerID.'&requesttype='.ITEM_DATA;



		$header_array =array('Content-Type:application/xml',
			                 'Accept:application/xml',
			                 'Authorization:' . Authorization,
			                 'SecretKey:' . SecretKey);


		//var_dump($header_array);


		try {

			// Get the curl session object
			$session = curl_init($request);
			$putString = stripslashes($bodyXml);
			$putData = tmpfile();
			fwrite($putData, $putString);
			fseek($putData, 0);

			// Set the POST options.
			curl_setopt($session, CURLOPT_HEADER, 1);
			curl_setopt($session, CURLOPT_HTTPHEADER,$header_array);
			//curl_setopt($session, CURLOPT_PUT, false);
			curl_setopt($session, CURLOPT_POST, true);
			curl_setopt($session, CURLOPT_HEADER, false);
			curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($session, CURLOPT_POSTFIELDS, $bodyXml);
			curl_setopt($session, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($session, CURLOPT_INFILE, $putData);
			curl_setopt($session, CURLOPT_INFILESIZE, strlen($putString));

			// Do the POST and then close the session

			$response = curl_exec($session);
			$xmlReady = simplexml_load_string($response);
			curl_close($session);


			$requestId = $xmlReady->ResponseBody->ResponseList->ResponseInfo->RequestId;
			if ($xmlReady->IsSuccess == true) {
				return $requestId;
			}

		} catch (InvalidArgumentException $e) {
			curl_close($session);
			throw $e;
		}
		catch (Exception $e)
		{
			curl_close($session);
			throw $e;
		}
	}

	/**
	 * Resposta do feed
	 * @param $bodyXml
	 * @return String
	 */
	public function responseFeed($bodyXml)
	{

		$url = 'https://api.newegg.com/marketplace/datafeedmgmt/feeds/status?sellerid='.SellerID;

		$header_array =array('Content-Type:application/xml',
			                 'Accept:application/xml',
			                 'Authorization:' . Authorization,
			                 'SecretKey:' . SecretKey);


		try {

			$session = curl_init($url);
			$putString = stripslashes($bodyXml);
			$putData = tmpfile();
			fwrite($putData, $putString);
			fseek($putData, 0);

			// Set the POST options.
			curl_setopt($session, CURLOPT_HEADER, 1);
			curl_setopt($session, CURLOPT_HTTPHEADER,$header_array);
			curl_setopt($session, CURLOPT_PUT, true);
			curl_setopt($session, CURLOPT_HEADER, false);
			curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($session, CURLOPT_POSTFIELDS, $bodyXml);
			curl_setopt($session, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($session, CURLOPT_INFILE, $putData);
			curl_setopt($session, CURLOPT_INFILESIZE, strlen($putString));

			// Do the POST and then close the session

			$response = curl_exec($session);
			$xmlReady = simplexml_load_string($response);
			curl_close($session);
			$RequestStatus = $xmlReady->ResponseBody->ResponseList->ResponseInfo->RequestStatus;
			$RequestType = $xmlReady->ResponseBody->ResponseList->ResponseInfo->RequestType;
			return array("RequestStatus" => $RequestStatus, "RequestType" => $RequestType);

		} catch (InvalidArgumentException $e) {
			curl_close($session);
			throw $e;
		}
		catch (Exception $e)
		{
			curl_close($session);
			throw $e;
		}
	}

	public function processingReport($requestId)
	{
		$url = 'https://api.newegg.com/marketplace/datafeedmgmt/feeds/result/'.$requestId.'?sellerid='.SellerID;

		$header_array =array('Content-Type:application/xml',
			                 'Accept:application/xml',
			                 'Authorization:' . Authorization,
			                 'SecretKey:' . SecretKey);


		try {

			$session = curl_init($url);

			// Set the POST options.
			curl_setopt($session, CURLOPT_HTTPHEADER,$header_array);
			curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($session, CURLOPT_SSL_VERIFYPEER, 0);

			// Do the POST and then close the session

			$response = curl_exec($session);
			curl_close($session);

			$xml = simplexml_load_string($response);

			$Message = $xml->MessageType;
			$Result = $xml->Message->ProcessingReport->Result;

			return array("Message" => $Message,
				         "Result" => $Result);



		} catch (InvalidArgumentException $e) {
		curl_close($session);
		throw $e;
		}
		catch (Exception $e)
		{
			curl_close($session);
			throw $e;
		}
	}
}