<?php
require_once('../../polyhousestore/app/Mage.php');
umask(0);
Mage::app();

/**
* Classe para gerar Xml de Produtos
*/
class XMLNewegg
{

	public function xmlProdutos()
	{
		$collection = Mage::getModel('catalog/product')
		->getCollection()
		->addAttributeToSort('created_at', 'DESC')
		->addAttributeToFilter('newegg_feed' , '1')
		->addAttributeToFilter('status' , '1')
		->addAttributeToSelect('*')->setPageSize(10000);
		$collection->getSelect();

		$doc = new DomDocument('1.0' , 'utf-8');
		$doc->formatOutput = true;

		$root = $doc->appendChild($doc->createElement('NeweggEnvelope'));

		$root->appendChild($doc->createAttribute('xmlns:xs'))
		->appendChild($doc->createTextNode('http://www.w3.org/2001/XMLSchema'));
		$root->appendChild($doc->createAttribute('elementFormDefault'))
		->appendChild($doc->createTextNode('qualified'));
		$root->appendChild($doc->createAttribute('attributeFormDefault'))
		->appendChild($doc->createTextNode('unqualified'));


		$head = new DOMElement('Header');
		$root->appendChild($head);

		$DocumentVersion = new DOMElement('DocumentVersion','1.01');
		$head->appendChild($DocumentVersion);

		$MessageType = new DOMElement('MessageType','BatchItemCreation');
		$root->appendChild($MessageType);

		$Overwrite = new DOMElement('Overwrite');
		//$root->appendChild($Overwrite);

		$Message = new DOMElement('Message');
		$root->appendChild($Message);

		$Itemfeed = new DOMElement('Itemfeed');
		$Message->appendChild($Itemfeed);

		$SummaryInfo = new DOMElement('SummaryInfo','');
		$Itemfeed->appendChild($SummaryInfo);

		//$SubCategoryID = new DOMElement('SubCategoryID','2809');
		//$SummaryInfo->appendChild($SubCategoryID);

		for ($i=1; $i <= $collection->getLastPageNumber(); $i++) {
			if ($collection->isLoaded()) {
				$collection->clear();
				$collection->setPage($i);
				$collection->setPageSize(10000);
			}

			foreach ($collection as $product) {


				$sku           = $product->getSku();
				$upc           = $product->getUpc();
				$name          = $product->getName();
				$manufacture   = $product->getData('manufacture');
				$mnfPartNumber = $product->getData('part_number');
				$shortDesc     = preg_replace("/&#?[a-z0-9]{2,8};/i","",strip_tags($product->getShort_description()));
				$productDesc   = preg_replace("/&#?[a-z0-9]{2,8};/i","",strip_tags($product->getDescription()));


				$weight = $product->getWeight();
				$length = $product->getLength();
				$height = $product->getHeight();
     			$width  = $product->getWidth();

     			$itemCondition = $product->getAttributeText('condition');

     			$msrp  = str_replace(',', '',number_format($product->getMsrp(),2));
     			$map   = str_replace(',', '',number_format($product->getMap(),2));
     			$price = str_replace(',', '', number_format($product->getData('newegg_price'),2));

     			$qtyPackage = $product->getAttributeText('quantity_package');

     			if ($product->getAttributeText('free_shipping_newegg') == 1) {

     				$shipping = "Free";

     			}else{

     				$shipping = "Default";
     			}

				$Item = new DOMElement('Item');
				$Itemfeed->appendChild($Item);

				$Action = new DOMElement('Action','Create Item');
				$Item->appendChild($Action);

				$BasicInfo = new DOMElement('BasicInfo');
				$Item->appendChild($BasicInfo);

				$SellerPartNumber = new DOMElement('SellerPartNumber' , $sku);
				$BasicInfo->appendChild($SellerPartNumber);

				$Manufacturer = new DOMElement('Manufacturer' , $manufacture);
				$BasicInfo->appendChild($Manufacturer);

				$ManufacturerPartNumberOrISBN = new DOMElement('ManufacturerPartNumberOrISBN' , $mnfPartNumber);
				$BasicInfo->appendChild($ManufacturerPartNumberOrISBN);

				$UPC = new DOMElement('UPC' , $upc);
				$BasicInfo->appendChild($UPC);

				$RelatedSellerPartNumber = new DOMElement('RelatedSellerPartNumber' , '');
				$BasicInfo->appendChild($RelatedSellerPartNumber);

				$WebsiteShortTitle = new DOMElement('WebsiteShortTitle' , $name);
				$BasicInfo->appendChild($WebsiteShortTitle);

				$BulletDescription = new DOMElement('BulletDescription' , $name);
				$BasicInfo->appendChild($BulletDescription);

				$ProductDescription = new DOMElement('ProductDescription' , $productDesc);
				$BasicInfo->appendChild($ProductDescription);

				$ItemDimension = new DOMElement('ItemDimension');
				$BasicInfo->appendChild($ItemDimension);

				$ItemLength = new DOMElement('ItemLength', $length);
				$ItemDimension->appendChild($ItemLength);

				$ItemWidth = new DOMElement('ItemWidth', $width);
				$ItemDimension->appendChild($ItemWidth);

				$ItemHeight = new DOMElement('ItemHeight', $height);
				$ItemDimension->appendChild($ItemHeight);

				$ItemWeight = new DOMElement('ItemWeight', $weight);
				$BasicInfo->appendChild($ItemWeight);

				$PacksOrSets = new DOMElement('PacksOrSets', $qtyPackage);
				$BasicInfo->appendChild($PacksOrSets);

				$ItemCondition = new DOMElement('ItemCondition', $itemCondition);
				$BasicInfo->appendChild($ItemCondition);

				$ItemPackage = new DOMElement('ItemPackage','');
				$BasicInfo->appendChild($ItemPackage);

				$ShippingRestriction = new DOMElement('ShippingRestriction','No');
				$BasicInfo->appendChild($ShippingRestriction);

				$Currency = new DOMElement('Currency','USD');
				$BasicInfo->appendChild($Currency);

				$MSRP = new DOMElement('MSRP',$msrp);
				$BasicInfo->appendChild($MSRP);

				$MAP = new DOMElement('MAP', $map);
				$BasicInfo->appendChild($MAP);

				$CheckoutMAP = new DOMElement('CheckoutMAP','No');
				$BasicInfo->appendChild($CheckoutMAP);

				$SellingPrice = new DOMElement('SellingPrice', $price);
				$BasicInfo->appendChild($SellingPrice);

				$Shipping = new DOMElement('Shipping', $shipping);
				$BasicInfo->appendChild($Shipping);

				$Inventory = new DOMElement('Inventory','Inventory');
				$BasicInfo->appendChild($Inventory);

				$ActivationMark = new DOMElement('ActivationMark','ActivationMark');
				$BasicInfo->appendChild($ActivationMark);

				$ItemImages = new DOMElement('ItemImages');
				$BasicInfo->appendChild($ItemImages);

				$Image = new DOMElement('Image');
				$ItemImages->appendChild($Image);

				$ImageUrl = new DOMElement('ImageUrl','ImageUrl');
				$Image->appendChild($ImageUrl);

				$IsPrimary = new DOMElement('IsPrimary','IsPrimary');
				$Image->appendChild($IsPrimary);

			}
		}

		$dateSave = date("Y-m-d H:i:s");
		$doc->save("c:/tmp/tmp.xml");

		return $doc->saveXML();
	}
}