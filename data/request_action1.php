<?php
	ini_set('display_errors', 1); 
	ini_set('display_startup_errors', 1); 
	error_reporting(E_ALL);
	include("inc/request_config.php");
	include("inc/functions.php");
	$responseData = '';
	$data = json_decode(file_get_contents('php://input'), true);
	if(!empty($data)){
		$action = $data['action'];
	}
	if(!empty($action)){
		switch ($action) {
			case 'all':
				$responseData = $functions->all_product();
			break;
			case 'saverequest':
				$responseData = $functions->saverequest($data);
			break;
			case 'requesteditem':
				$responseData = $functions->all_requested_item($data);
			break;
			case 'fetchrecord':
				$itemId = base64_decode($data['itemid'], true);
				$responseData = $functions->user_requested_item($itemId);
			break;
			case 'requestItemHistory':
				$user_log_id = $data['user_log_id'];
				$responseData = $functions->requestItemHistory($user_log_id);
			break;
			case 'widget':
				$responseData = $functions->widget($data);
			break;
			case 'assigneditem':
				$user_id = $data['user_id'];
				$responseData = $functions->assigneditem($user_id);
			break;
			default:
				
			break;
		}
		echo $responseData;	
	}
	?>