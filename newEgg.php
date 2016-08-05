<?php

require("xmlProdutos.php");
require("clientNewEgg.php");

/**
* Insere Produtos
*/
class NewEgg
{
	public function insereProdutos()
	{
		$getProducts = new XMLNewegg();
		$produtos = $getProducts->xmlProdutos();

		if ($produtos != '') {
			$enviaNewEgg = new ClientNewEgg();
			echo $enviaNewEgg->insertNew($produtos);
		}
	}

	/**
	 * Description
	 * @param type $sellerId
	 * @param type $requestId
	 * @return type
	 */
	public function requestResponse($sellerId, $requestId)
	{
		$getProducts = new XMLNewegg();
		$bodyResp = $getProducts->xmlBodyResponse($sellerId, $requestId);

		if ($bodyResp != '') {
			$enviaNewEgg = new ClientNewEgg();
			$resposta = $enviaNewEgg->responseFeed($bodyResp);
			foreach ($resposta as $key => $value) {
				echo $key . " : " . $value . "<br>";
			}
		}
	}

	public function processingReport($requestId)
	{
		$enviaNewEgg = new ClientNewEgg();
		$resposta = $enviaNewEgg->processingReport($requestId);
		echo "[ ".$resposta["Message"] . " ]" . "<br>";

		//var_dump($resposta);
		foreach ($resposta["Result"] as $value) {
			echo "[ AdditionalInfo ]" . "<br>";
			echo "[ SubCategoryID ] => " . $value->AdditionalInfo->SubCategoryID . "<br>";
			echo "[ SKU ] => " . $value->AdditionalInfo->SellerPartNumber . "<br>";
			echo "[ MnfPartNumber ] => " . $value->AdditionalInfo->ManufacturerPartNumberOrISBN. "<br>";
			echo "[ UPC ] => " . $value->AdditionalInfo->UPC . "<br>";
			echo "<br>";
			echo "[ ErrorList ]" . "<br>";
			echo "[ ErrorDescription ] => " . $value->ErrorList->ErrorDescription . "<br>";
		}

	}
}
$sellerId = SellerID;
$requestId = '26XLNN1RD5I4R';

$inserirNovo = new NewEgg();
//$inserirNovo->insereProdutos();
//$inserirNovo->requestResponse($sellerId,$requestId);
//$inserirNovo->processingReport($requestId);