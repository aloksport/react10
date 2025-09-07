<?php
class user_function{
	public function user_list($user_data= array()){
		Global $functions;
		$conn = new Database();
		$message['status'] = 'fail';
		$message['msg']    = 'Something wrong ! try again';
		$status = -1;
		$query= "select id, name, email_address, status, DATE_FORMAT(created, '".$functions->mysql_date_format."') AS Displaydate from inv_users where status != :status order by name ASC";
		$query= $conn->query($query);
		$conn->bind(':status', $status, PDO::PARAM_INT);
		$result= $conn->resultset($query);
		if($conn->rowCount() > 0 ){
			$message['status']      = 'success';
			$message['userData'] = $result;
			$message['msg']         = 'Users found.';
		}else{
			$message['status'] = 'fail';
			$message['msg']    = 'No User found';
		}
		return json_encode($message, true);
	}
	
	public function add_user($user_data= array()){
		Global $functions;
		$conn = new Database();
		$message['status'] = 'fail';
		$message['msg']    = 'Something wrong ! try again';
		if(!empty($user_data)){	
		$name = isset($user_data['name'])?addslashes($user_data['name']):'';
		$email_address = isset($user_data['email_address'])?$user_data['email_address']:'';	
		$userpwd = isset($user_data['userpwd'])?$user_data['userpwd']:'';
		$created  = $updated =date('Y-m-d H:i:s');
		$userstatus = isset($user_data['userstatus'])?addslashes($user_data['userstatus']):'';
		$user_id = isset($user_data['id'])?base64_decode($user_data['id'], true):'';
		$user_role = isset($user_data['user_role'])?$user_data['user_role']:'';
		if($functions->check_user_email_exist($email_address, $user_id)){
			$message['status'] = 'fail';
			$message['msg']    = 'Email address is alreay exist.';	
		} else{
			if(!empty($user_id)){
				$sql = "UPDATE inv_users SET name=:name, email_address=:email_address, status=:status, updated=:updated WHERE id=:userid";
				$stmt = $conn->query($sql);
				$conn->bind(':userid', $user_id, PDO::PARAM_STR);
				$conn->bind(':name', $name, PDO::PARAM_STR);		
				$conn->bind(':email_address', $email_address, PDO::PARAM_STR);					
				$conn->bind(':updated', $updated, PDO::PARAM_STR);
				$conn->bind(':status', $userstatus, PDO::PARAM_INT);
				$conn->execute();
				if(!empty($userpwd)){
					$sql = "UPDATE inv_users SET pwd=:pwd, updated=:updated WHERE id=:userid";
					$stmt = $conn->query($sql);
					$conn->bind(':pwd', md5($userpwd), PDO::PARAM_STR);
					$conn->bind(':userid', $user_id, PDO::PARAM_STR);
					$conn->bind(':updated', $updated, PDO::PARAM_STR);
					$conn->execute();
				}
			} else {
				$sql = "INSERT INTO inv_users (name,email_address,pwd,status,created,updated) VALUES (:name, :email_address, :pwd, :status, :created, :updated)";
				$stmt = $conn->query($sql);
				$conn->bind(':name', $name, PDO::PARAM_STR);		
				$conn->bind(':email_address', $email_address, PDO::PARAM_STR);		
				$conn->bind(':pwd', md5($userpwd), PDO::PARAM_STR);
				$conn->bind(':status', $userstatus, PDO::PARAM_INT);	
				$conn->bind(':created', $created, PDO::PARAM_STR);		
				$conn->bind(':updated', $updated, PDO::PARAM_STR);	
				$conn->execute();
			}
				$message['status'] = 'success';
				if(!empty($user_id)){
					if(!empty($user_role)){
						$functions->assign_user_role($user_id, $user_role, $created, $updated);
					}
					$message['msg']    = 'User details updated  successfully.';
				} else {
					$lastInsertId=$conn->lastInsertId();
					if(!empty($user_role)){
						$functions->assign_user_role($lastInsertId, $user_role, $created, $updated);
					}
					$message['msg']    = 'New user added successfully.';
				}
			}
		}
		return json_encode($message, true); 
	}

	public function user_detail($user_data= array()){
		$conn = new Database();
		$message['status'] = 'fail';
		$message['msg']    = 'Something wrong ! try again';
		$userid = base64_decode($user_data['id'], true);
		$query= "select id, name, email_address, status from inv_users where id = :userid";
		$query= $conn->query($query);
		$conn->bind(':userid', $userid, PDO::PARAM_INT);
		$result= $conn->single($query);
		$resultArr = array();
		if(!empty($result)){
			$query_role= "select role_id from inv_user_roles where user_id = :userid and status=1";
			$query_role= $conn->query($query_role);
			$conn->bind(':userid', $userid, PDO::PARAM_INT);
			$role_result= $conn->single($query_role);
			$user_role_id = '';
			if(!empty($role_result)){
				$user_role_id = isset($role_result['role_id'])?$role_result['role_id']:'';
			}
			$resultArr['id']			= $result['id'];
			$resultArr['name']			= $result['name'];
			$resultArr['email_address']	= $result['email_address'];
			$resultArr['status']		= $result['status'];
			$resultArr['role_id']		= $user_role_id;
		}
		if(!empty($resultArr)){
			$message['status']      = 'success';
			$message['userData'] 	= $resultArr;
			$message['msg']         = 'User found.';
		}else{
			$message['status'] = 'fail';
			$message['msg']    = 'No user found';
		}
		return json_encode($message, true);
	}
	public function user_role(){
		$conn = new Database();
		$message['status'] = 'fail';
		$message['msg']    = 'Something wrong ! try again';
		$status =1;
		$query= "select id, name from inv_roles where status = :status";
		$query= $conn->query($query);
		$conn->bind(':status', $status, PDO::PARAM_INT);
		$result= $conn->resultset($query);
		if($conn->rowCount() > 0 ){
			$message['status']      = 'success';
			$message['roleData']    = $result;
			$message['msg']         = 'Roles found.';
		}else{
			$message['status'] = 'fail';
			$message['msg']    = 'No role found';
		}
		return json_encode($message, true);
	}
	public function assigntouser(){
		$conn = new Database();
		$message['status'] = 'fail';
		$message['msg']    = 'Something wrong ! try again';
		$status =1;
		$query= "select id, name from inv_users where status = :status order by name ASC";
		$query= $conn->query($query);
		$conn->bind(':status', $status, PDO::PARAM_INT);
		$result= $conn->resultset($query);
		if($conn->rowCount() > 0 ){
			$message['status']      = 'success';
			$message['assigntouserData']    = $result;
			$message['msg']         = 'Assign to users found.';
		}else{
			$message['status'] = 'fail';
			$message['msg']    = 'No user found';
		}
		return json_encode($message, true);
	}
	
}
$user_function= new user_function();
?>