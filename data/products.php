<?php
	include("inc/request_config.php");
	include("inc/functions.php");
	include("inc/product_function.php");
	$responseData = '';	
	$data = json_decode(file_get_contents('php://input'), true);
	if(!empty($data)){
		$action = $data['action'];
	}
	if(!empty($action)){
		switch ($action) {
			case 'all':
				$responseData = $product_function->product_list($data);
			break;
			case 'systemlist':
				$responseData = $product_function->systemlist($data);
			break;
			case 'addSystem':
				$responseData = $product_function->addSystem($data);
			break;
			case 'systemDetails':
				$responseData = $product_function->systemDetails($data);
			break;
			case 'add':
				$responseData = $product_function->addProduct($data);
			break;
			case 'productDetails':
				$responseData = $product_function->productDetails($data);
			break;
			case 'fetchItemByUser':
				$responseData = $product_function->fetchItemByUser($data);
			break;
			case 'softwareDetails':
				$responseData = $product_function->softwareDetails($data);
			break;
			case 'addsoftware':
				$responseData = $product_function->addSoftware($data);
			break;
			default:
				
			break;
		}
	echo $responseData;
  }
?>