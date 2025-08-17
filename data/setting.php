<?php
	include("inc/request_config.php");
	include("inc/functions.php");
	$responseData = '';
	$data = json_decode(file_get_contents('php://input'), true);
	if(!empty($data)){
		$action = $data['action'];
	}
	if(!empty($action)){
		switch ($action) {
			case 'allstatus':
				$responseData = all_status($data);
			break;
			default:
				
			break;
		}
	echo $responseData;
  }
	
	function all_status($array_data=array()){
		$conn = new Database();
		$message['status'] = 'fail';
		$message['msg']    = 'Something wrong ! try again';
		$status = -1;
		$role_condition= '';
		if(!empty($array_data)){
			if(isset($array_data['role_id']) && ($array_data['role_id']==3)){
				$role_condition = " and show_to_role_id=".$array_data['role_id'];
			} elseif(isset($array_data['role_id'])&& ($array_data['role_id']==2)){
				$role_condition = " and show_to_role_id IN (2,3)";
			}else{
				$role_condition= '';
			}
		}
		$query= "select id, name, css_class from inv_status where status != :status $role_condition order by name ASC";
		$query= $conn->query($query);
		$conn->bind(':status', $status, PDO::PARAM_INT);
		$result= $conn->resultset($query);
		if(!empty($result)){
			$statusDataArr = array();
			$statusClassArr   = array();
			foreach($result as $resultData){
				$statusDataArr[$resultData['id']] = $resultData['name'];
				$statusClassArr[$resultData['id']]   = $resultData['css_class'];
			}
		}
		if($conn->rowCount() > 0 ){
			$message['status']      = 'success';
			$message['statusData']  = $statusDataArr;
			$message['statusClassData']  = $statusClassArr;
			$message['current_status']  = $result;
			$message['msg']         = 'Status found.';
		}else{
			$message['status'] = 'fail';
			$message['msg']    = 'No Status found';
		}
		return json_encode($message, true);
	}
?>