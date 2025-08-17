<?php
class product_function{
	public function product_list($data=array()){
		Global $functions;
		$conn = new Database();
		$subscriptionType = array('1'=>'Annual', '2'=>'Perpetual');
		$associatedTeamId = array('1'=>'IT','2'=>'Solidworks','3'=>'Graphics','4'=>'HR');		
		$message['status'] = 'fail';
		$message['msg']    = 'Something wrong ! try again';
		$status = -1;
		$product_type_query=' and products.product_type_id IN (1,3)';
		if(!empty($data) && $data['product_type']==2){
			$product_type= isset($data['product_type'])?$data['product_type']:'';
			$product_type_query = ' and products.product_type_id='.$product_type;
		}
		$query="SELECT products.id, products.product_type_id, products.product_name, products.price, products.renewal_cost,products.gst, products.quantity, products.threshold_qty, products.status as pstatus, DATE_FORMAT(products.created, '".$functions->mysql_date_format."') AS Displaydate,product_details.entitlement_end_date, product_details.serial_number, product_details.subscription_type,product_details.associated_team_id,product_details.status as prodetail_status FROM inv_products products LEFT JOIN inv_product_details  product_details ON products.id=product_details.product_id  where products.status != :status".$product_type_query." order by products.status desc,product_details.entitlement_end_date, product_details.associated_team_id ";
		
		$query= $conn->query($query);
		$conn->bind(':status', $status, PDO::PARAM_INT);
		$result= $conn->resultset($query);
		if($conn->rowCount() > 0 ){
			$message['status']      = 'success';
			$message['productData'] = $result;
			$message['subscriptionType'] = $subscriptionType;
			$message['associatedTeamId'] = $associatedTeamId;
			$message['msg']         = 'Product found.';
		}else{
			$message['status'] = 'fail';
			$message['msg']    = 'No Product found';
		}
		return json_encode($message, true);
	}
	function systemlist($data=array()){
		Global $functions;
		$conn = new Database();
		$associatedTeamId = array('1'=>'IT','2'=>'Solidworks','3'=>'Graphics','4'=>'HR');
		$systemType = array('1'=>'Desktop','2'=>'Laptop','3'=>'NAS Drive','4'=>'Other System');
		$message['status'] = 'fail';
		$message['msg']    = 'Something wrong ! try again';
		$status = -1;
		if(!empty($data['id'])){
			$cond='sys.id='.$data['id'];
			//$product_type= isset($data['product_type'])?$data['product_type']:'';
			//$product_type_query = ' and products.product_type_id='.$product_type;
		}elseif(!empty($data['userId'])){
			$cond='sys.assignToUserId='.$data['userId'];
		}else{
			$cond=1;
		}
		$query="SELECT sys.id,sys.systemName,sys.systemType,sys.systemCompanyName,sys.systemSpecification, sys.systemSoftware, sys.haveMonitor, sys.associatedTeamId, sys.purchaseDate, sys.assignToUserId, user.name from inv_system_list  sys left join inv_users  user on  sys.assignToUserId=user.id where ".$cond." order by sys.id";		
		$query= $conn->query($query);
		//$conn->bind(':status', $status, PDO::PARAM_INT);
		$result= $conn->resultset($query);
		//print_r($result);die;
		if($conn->rowCount() > 0 ){
			$message['status']      = 'success';
			$message['systemlist'] = $result;
			$message['associatedTeamId'] = $associatedTeamId;
			$message['systemType'] = $systemType;
			$message['msg']         = 'Product found.';
		}else{
			$message['status'] = 'fail';
			$message['msg']    = 'No Product found';
		}
		return json_encode($message, true);
	}
	
	// Add System   Added By Alok 15-May-2025
	function addSystem($data=array()){
		Global $functions;
		$conn = new Database();		
		$message['status'] = 'fail';
		$message['msg']    = 'Something wrong ! try again';
		$status = -1;
		if(!empty($data)){
			//print_r($data);die;
			$sid = isset($data['sid'])?$data['sid']:'';
			$purchaseDate=$data['purchaseDate']?$data['purchaseDate']:date("y-m-d");
			if($sid ==""){				
				$sql = "INSERT INTO inv_system_list (userName, systemName, systemType, systemCompanyName,  systemSpecification, systemSoftware, haveMonitor, associatedTeamId, purchaseDate,assignToUserId) VALUES (:userName, :systemName, :systemType, :systemCompanyName, :systemSpecification,  :systemSoftware, :haveMonitor, :associatedTeamId, :purchaseDate, :assignToUserId)";
				$stmt = $conn->query($sql);
				$conn->bind(':userName', $data['userName'], PDO::PARAM_STR);		
				$conn->bind(':systemName', $data['systemName'], PDO::PARAM_STR);
				$conn->bind(':systemType', $data['systemType'], PDO::PARAM_STR);
				$conn->bind(':systemCompanyName', $data['systemCompanyName'], PDO::PARAM_STR);
				$conn->bind(':systemSpecification', $data['systemSpecification'], PDO::PARAM_STR);
				$conn->bind(':systemSoftware', $data['systemSoftware'], PDO::PARAM_STR);
				$conn->bind(':haveMonitor', $data['haveMonitor'], PDO::PARAM_STR);	
				$conn->bind(':associatedTeamId', $data['associatedTeamId'], PDO::PARAM_STR);
				$conn->bind(':purchaseDate', $purchaseDate, PDO::PARAM_STR);
				$conn->bind(':assignToUserId', $data['userName'], PDO::PARAM_STR);
				$conn->execute();
				$lastInsertId=$conn->lastInsertId();
				if($lastInsertId){
					$message['status']      = 'success';
					$message['msg']         = 'System has been added successfully.';
				}
			}else{
				$sql = "update inv_system_list set userName=:userName, systemName=:systemName, systemType=:systemType, systemCompanyName=:systemCompanyName, systemSpecification=:systemSpecification, systemSoftware=:systemSoftware, haveMonitor=:haveMonitor, associatedTeamId=:associatedTeamId, purchaseDate=:purchaseDate, assignToUserId=:assignToUserId WHERE id=:sid";
				$stmt = $conn->query($sql);
				$conn->bind(':sid', $data['sid'], PDO::PARAM_INT);		
				$conn->bind(':userName', $data['userName'], PDO::PARAM_STR);		
				$conn->bind(':systemName', $data['systemName'], PDO::PARAM_STR);
				$conn->bind(':systemType', $data['systemType'], PDO::PARAM_STR);
				$conn->bind(':systemCompanyName', $data['systemCompanyName'], PDO::PARAM_STR);
				$conn->bind(':systemSpecification', $data['systemSpecification'], PDO::PARAM_STR);
				$conn->bind(':systemSoftware', $data['systemSoftware'], PDO::PARAM_STR);
				$conn->bind(':haveMonitor', $data['haveMonitor'], PDO::PARAM_STR);	
				$conn->bind(':associatedTeamId', $data['associatedTeamId'], PDO::PARAM_STR);
				$conn->bind(':purchaseDate', $purchaseDate, PDO::PARAM_STR);
				$conn->bind(':assignToUserId', $data['userName'], PDO::PARAM_STR);
				$conn->execute();
				$message['status']      = 'success';
				$message['msg']         = 'System has been updated successfully.';
			}
		}
		return json_encode($message, true);
	}
	
	// Add Product   Added By Alok 25-March-2025
	function addProduct($data=array()){
		Global $functions;
		$conn = new Database();		
		$message['status'] = 'fail';
		$message['msg']    = 'Something wrong ! try again';
		$status = -1;
		if(!empty($data)){
			//print_r($data);die;
			$created=$updated=date("Y-m-d h:i:s");
			if($data['pid']==""){				
				$sql = "INSERT INTO inv_products (product_type_id, product_name,price,quantity,threshold_qty,gst,status,created,updated) VALUES (:product_type_id, :product_name, :price,:quantity, :threshold_qty, :gst, :status,:created,:updated)";
				$stmt = $conn->query($sql);
				$conn->bind(':product_type_id', $data['product_type'], PDO::PARAM_STR);		
				$conn->bind(':product_name', $data['pname'], PDO::PARAM_STR);
				$conn->bind(':price', $data['product_price'], PDO::PARAM_STR);
				$conn->bind(':quantity', $data['product_qty'], PDO::PARAM_STR);
				$conn->bind(':threshold_qty', $data['product_thres_qty'], PDO::PARAM_STR);
				$conn->bind(':gst', $data['product_gst'], PDO::PARAM_STR);
				$conn->bind(':status', $data['product_status'], PDO::PARAM_STR);	
				$conn->bind(':created', $created, PDO::PARAM_STR);		
				$conn->bind(':updated', $updated, PDO::PARAM_STR);
				$conn->execute();
				$lastInsertId=$conn->lastInsertId();
				if($lastInsertId){
					$message['status']      = 'success';
					$message['msg']         = 'Product added successfully.';
				}
			}else{
				$sql = "update inv_products set product_type_id=:product_type_id, product_name=:product_name, price=:price, quantity=:quantity, threshold_qty=:threshold_qty, gst=:gst, status=:status, updated=:updated WHERE id=:pid";
				$stmt = $conn->query($sql);
				$conn->bind(':pid', $data['pid'], PDO::PARAM_INT);		
				$conn->bind(':product_type_id', $data['product_type'], PDO::PARAM_STR);		
				$conn->bind(':product_name', $data['pname'], PDO::PARAM_STR);
				$conn->bind(':price', $data['product_price'], PDO::PARAM_STR);
				$conn->bind(':quantity', $data['product_qty'], PDO::PARAM_STR);
				$conn->bind(':threshold_qty', $data['product_thres_qty'], PDO::PARAM_STR);
				$conn->bind(':gst', $data['product_gst'], PDO::PARAM_STR);
				$conn->bind(':status', $data['product_status'], PDO::PARAM_STR);	
				$conn->bind(':updated', $updated, PDO::PARAM_STR);
				$conn->execute();
				$message['status']      = 'success';
				$message['msg']         = 'Product updated successfully.';
			}
		}
		return json_encode($message, true);
	}
	
	// Fetch Product Details Added By Alok 25-March-2025
	function productDetails($data=array()){
		Global $functions;
		$conn = new Database();		
		$message['status'] = 'fail';
		$message['msg']    = 'Something wrong ! try again';
		$status = -1;
		if(!empty($data)){			
			$query="select * from inv_products where id=:id";		
			$query= $conn->query($query);
			$conn->bind(':id', $data['id'], PDO::PARAM_INT);
			$result= $conn->resultset($query);
			if($conn->rowCount() > 0 ){
				$message['status']      = 'success';
				$message['productDetails'] = $result;
				$message['msg']         = 'Product found.';
			}else{
				$message['status'] = 'fail';
				$message['msg']    = 'No Product found';
			}
		}		
		return json_encode($message, true);
	}
	
	// Fetch Item Assigned By User Added By Alok 27-March-2025
	function fetchItemByUser($data=array()){
		//print_r($data);die;
		Global $functions;
		$conn = new Database();		
		$message['status'] = 'fail';
		$message['msg']    = 'Something wrong ! try again';
		$status = -1;
		if(!empty($data)){			
			$query="select userlog.id, product.product_type_id, product.product_name,product.price, DATE_FORMAT(userlog.created, '".$functions->mysql_date_format."') AS createdDate, DATE_FORMAT(userlog.closed_date, '".$functions->mysql_date_format."') AS issuedDate, userlog.current_status  from inv_products product left join inv_user_log userlog on product.id=userlog.product_id where userlog.user_id=:user_id and product.id!=26 and userlog.current_status in (7,11) order by userlog.id desc";		
			$query= $conn->query($query);
			$conn->bind(':user_id', $data['userId'], PDO::PARAM_INT);
			$result= $conn->resultset($query);
			if($conn->rowCount() > 0 ){
				$message['status']      = 'success';
				$message['itemDetails'] = $result;
				$message['msg']         = 'Product found.';
			}else{
				$message['status'] = 'fail';
				$message['msg']    = 'No Product found';
			}
		}		
		return json_encode($message, true);
	}
	
	// Fetch Software Details Added By Alok 10-July-2025
	function softwareDetails($data=array()){
		Global $functions;
		$conn = new Database();		
		$message['status'] = 'fail';
		$message['msg']    = 'Something wrong ! try again';
		$status = -1;
		if(!empty($data)){			
			$query="select p.id, p.product_type_id,p.product_name,p.price,p.quantity,p.threshold_qty, p.renewal_cost,p.gst,p.status, d.entitlement_end_date,d.serial_number,d.subscription_type,d.associated_team_id from inv_products p left join inv_product_details d on p.id=d.product_id where p.id=:id";
			$query= $conn->query($query);
			$conn->bind(':id', $data['id'], PDO::PARAM_INT);
			$result= $conn->resultset($query);
			if($conn->rowCount() > 0 ){
				$message['status']      = 'success';
				$message['softwareDetails'] = $result;
				$message['msg']         = 'Product found.';
			}else{
				$message['status'] = 'fail';
				$message['msg']    = 'No Product found';
			}
		}		
		return json_encode($message, true);
	}
	
	// Add Software  Details Added By Alok 10-July-2025
	function addSoftware($data=array()){
		Global $functions;
		$conn = new Database();		
		$message['status'] = 'fail';
		$message['msg']    = 'Something wrong ! try again';
		$status = -1;
		if(!empty($data)){
			//print_r($data);die;
			$created=$updated=date("Y-m-d h:i:s");
			if($data['sid']==""){				
				$sql = "INSERT INTO inv_products (product_type_id, product_name,quantity,renewal_cost, gst,status,created,updated) VALUES (:product_type_id, :product_name,:quantity, :renewal_cost, :gst, :status,:created,:updated)";
				$stmt = $conn->query($sql);
				$conn->bind(':product_type_id', 2, PDO::PARAM_STR);		
				$conn->bind(':product_name', $data['softwarename'], PDO::PARAM_STR);
				$conn->bind(':quantity', $data['softwareQty'], PDO::PARAM_STR);
				$conn->bind(':renewal_cost', $data['renewalCost'], PDO::PARAM_STR);
				$conn->bind(':gst', $data['gstRate'], PDO::PARAM_STR);
				$conn->bind(':status', $data['softwareStatus'], PDO::PARAM_STR);	
				$conn->bind(':created', $created, PDO::PARAM_STR);		
				$conn->bind(':updated', $updated, PDO::PARAM_STR);
				$conn->execute();
				$lastInsertId=$conn->lastInsertId();
				if($lastInsertId){
					$sql = "INSERT INTO inv_product_details (product_id, entitlement_end_date,serial_number,subscription_type, associated_team_id,status,created,updated) VALUES (:product_id, :entitlement_end_date,:serial_number, :subscription_type,:associated_team_id, :status,:created,:updated)";
					$stmt = $conn->query($sql);
					$conn->bind(':product_id', $lastInsertId, PDO::PARAM_STR);		
					$conn->bind(':entitlement_end_date', $data['entitlementDate'], PDO::PARAM_STR);		
					$conn->bind(':serial_number', $data['serialNumber'], PDO::PARAM_STR);
					$conn->bind(':subscription_type', $data['subscriptionType'], PDO::PARAM_STR);
					$conn->bind(':associated_team_id', $data['associatedTeamId'], PDO::PARAM_STR);
					$conn->bind(':status', 1, PDO::PARAM_STR);	
					$conn->bind(':created', $created, PDO::PARAM_STR);		
					$conn->bind(':updated', $updated, PDO::PARAM_STR);
					$conn->execute();
					$message['status']      = 'success';
					$message['msg']         = 'Software added successfully.';
				}
			}else{
				$sql = "update inv_products set  product_name=:product_name, quantity=:quantity, renewal_cost=:renewal_cost, gst=:gst, status=:status, updated=:updated WHERE id=:pid";
				$stmt = $conn->query($sql);
				$conn->bind(':pid', $data['sid'], PDO::PARAM_INT);		
				$conn->bind(':product_name', $data['softwarename'], PDO::PARAM_STR);
				$conn->bind(':quantity', $data['softwareQty'], PDO::PARAM_STR);
				$conn->bind(':renewal_cost', $data['renewalCost'], PDO::PARAM_STR);
				$conn->bind(':gst', $data['gstRate'], PDO::PARAM_STR);
				$conn->bind(':status', $data['softwareStatus'], PDO::PARAM_STR);	
				$conn->bind(':updated', $updated, PDO::PARAM_STR);
				$conn->execute();
				$sql = "update inv_product_details set entitlement_end_date=:EED, serial_number=:serial_number, subscription_type=:subsType, associated_team_id=:assocTeamID, updated=:updated WHERE product_id=:pid";
				$stmt = $conn->query($sql);
				$conn->bind(':pid', $data['sid'], PDO::PARAM_INT);		
				$conn->bind(':EED', $data['entitlementDate'], PDO::PARAM_STR);
				$conn->bind(':serial_number', $data['serialNumber'], PDO::PARAM_STR);
				$conn->bind(':subsType', $data['subscriptionType'], PDO::PARAM_STR);
				$conn->bind(':assocTeamID', $data['associatedTeamId'], PDO::PARAM_STR);
				$conn->bind(':updated', $updated, PDO::PARAM_STR);
				$conn->execute();
				$message['status']      = 'success';
				$message['msg']         = 'Software updated successfully.';
			}
		}
		return json_encode($message, true);
	}
}
$product_function= new product_function();
?>