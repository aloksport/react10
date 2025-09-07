<?php
	include("inc/request_config.php");
	include("inc/functions.php");
	$data = json_decode(file_get_contents('php://input'), true);
	$responseData = '';
	if(!empty($data)){
		$action = $data['action'];
		switch ($action) {
			case 'register':
				$responseData = $functions->user_register($data);
			break;
			case 'login':
				$responseData = $functions->user_login($data);
			break;
			case 'forgetpassword':
				$responseData = $functions->user_forgetpassword($data);
			break;
			case 'resetpassword':
				$responseData = $functions->user_resetpassword($data);
			break;
			default:
				
			break;
		}		
	}
	echo $responseData;
	?>