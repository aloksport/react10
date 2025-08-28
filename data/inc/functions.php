<?php
include('conn.php');
class functions{
	public $base_url;
	function __construct(){
		ini_set('session.gc_maxlifetime', 10);
		session_start();
		$base_url;
		//date_default_timezone_set('Asia/Kolkata');
		$hostname=$_SERVER['SERVER_NAME'];
		if($hostname == "localhost"){
			$this->base_url="http://".$hostname."/my-extra-mile/";
		}else{
			$this->base_url="https://".$hostname."/my-extra-mile/";
		}
    }	
	function fetch_all_stock(){
		$conn = new Database();
		$query= $conn->query("select * from tbl_stock_buy");
		return $result= $conn->resultset($query);
	}
	function login(){
		$conn = new Database();
		if(!isset($_POST['user_email']) && !isset($_POST['user_pass'])){
			return false;
		}
		$query= $conn->query("select * from users where user_email='".$_POST['user_email']."' && user_pass='".md5($_POST['user_pass'])."'");
		$result= $conn->resultset($query);
		//echo '<pre>';print_r($result);echo '<br/>';echo $result[0]['user_status'];die;
		if(($conn->rowCount() >0) and ($result[0]['user_status']==1) ){
			$time=time();
			$_SESSION['login']=true;
			$_SESSION['user_email']=$_POST['user_email'];
			$_SESSION['user_time_in']=$time;
			$sql = "INSERT INTO tbl_user_log (user_id,user_time_in,user_ip) VALUES (:user_id, :user_time_in, :user_ip)";
			$stmt = $conn->query($sql);
			$conn->bind(':user_id', $result[0]['ID'], PDO::PARAM_STR);		
			$conn->bind(':user_time_in',$time, PDO::PARAM_STR);		
			$conn->bind(':user_ip', $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);		
			$conn->execute();
			return 1;
		}elseif(($conn->rowCount() >0) and ($result[0]['user_status']==0) ){
			return 2;
		}else{
			return false;
		}
	}
	function forgot_password(){
		$email_encode= base64_encode($_POST['recover-passwrd']);
		$username=$this->get_user_details($_POST['recover-passwrd']);
		if(empty($username)){
			return 2;
		}else{
			$username=$username[0]['user_nickname'];
			$subject="Recover your password";
			$msg="Hi ".$username.",<br/>
			Please click below link to set your new password.<br/>
			<a href='".$this->base_url."new-password.php?pr=".$email_encode."'>Click Here</a> to set your new password.<br/><br/>";				
			$send_email=$this->send_email($_POST['recover-passwrd'],$subject,$username,$msg);
			if($send_email){
				return 1;
			}else{
				return 0;
			}
		}
	}
	function setup_new_password(){
		//echo $_POST['password1'];die;
		$conn = new Database();
		$user_email=str_replace("pr=","","pr=YWxva0BzcG9ydGFpbm1lbnRkZXNpZ24uY29t");
		$user_email=base64_decode($user_email);
		$sql = "UPDATE users SET user_pass = :user_pass WHERE user_email = :user_email";
		$stmt = $conn->query($sql);									 
		$conn->bind(':user_pass', md5($_POST['password1']), PDO::PARAM_STR);		
		$conn->bind(':user_email', $user_email, PDO::PARAM_STR);		
		$result=$conn->execute(); 
		if($result){
			return  true;
		}else{
			return false;
		}		
	}
	function logout(){
		$conn = new Database();
		$username=$this->get_user_details($_SESSION['user_email']);//die;
		$sql = "UPDATE tbl_user_log SET user_time_out = :user_time_out WHERE user_id = :user_id and user_time_in=:user_time_in";
		$stmt = $conn->query($sql);									 
		$conn->bind(':user_time_out',time(), PDO::PARAM_STR);		
		$conn->bind(':user_time_in',$_SESSION['user_time_in'], PDO::PARAM_STR);		
		$conn->bind(':user_id', $username[0]['ID'], PDO::PARAM_STR);		
		$result=$conn->execute();
		if(!isset($_SESSION['login'])) {
			header('Location: '.$this->base_url.'login.php?status=logout');
		}
	}
	function get_customers_log($id){
		$conn = new Database();
		$query= $conn->query("select count(user_id) as num, user_time_in, user_time_out, user_ip,ID, user_fullname, user_email from tbl_user_log left join users on tbl_user_log.user_id=users.ID where tbl_user_log.user_id=:user_id");
		$conn->bind(':user_id', $id, PDO::PARAM_STR);	
		return $result= $conn->resultset($query);
	}
	function login_status_message($status){
		if($status=="success"){return "Your account has been successfully activated. Please log in to access.";}
		if($status=="logout"){return "You have been successfully logout of your account. Please log in to access.";}
		
	}
	function get_user_details($user_email){
		$conn = new Database();
		$query= "select * from users where user_email= :user_email";
		$query= $conn->query($query);
		$conn->bind(':user_email', $user_email, PDO::PARAM_STR);
		$result= $conn->resultset($query);
		return $result;
	}
	function register(){
		$conn = new Database();
		if(!isset($_POST['user_email'])){
			return false;
		}else if($this->check_user_email_exist($_POST['user_email'])){
			return "use_exists";
		}else{
			$user_activation_key= rand(100,100000);
			$random_number1= 5000000-$user_activation_key;
			$sql = "INSERT INTO users (user_fullname,user_email,user_phone,user_nickname,user_pass,user_activation_key,user_status, user_reg_ip_add, user_registered) VALUES (:user_fullname, :user_email,:user_phone, :user_nickname, :user_pass, :user_activation_key,:user_status, :user_reg_ip_add, :user_registered)";
			$stmt = $conn->query($sql);
			$nickname= explode(" ",$_POST['user_fullname']);
			$conn->bind(':user_fullname', $_POST['user_fullname'], PDO::PARAM_STR);		
			$conn->bind(':user_email', $_POST['user_email'], PDO::PARAM_STR);		
			$conn->bind(':user_phone', $_POST['user_phone'], PDO::PARAM_STR);		
			$conn->bind(':user_nickname', $nickname[0], PDO::PARAM_STR);		
			$conn->bind(':user_pass', md5($_POST['user_pass']), PDO::PARAM_STR);		
			$conn->bind(':user_activation_key', $user_activation_key, PDO::PARAM_STR);		
			$conn->bind(':user_reg_ip_add', $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);		
			$conn->bind(':user_status', 0, PDO::PARAM_INT);		
			$conn->bind(':user_registered', date('Y-m-d H:i:s'), PDO::PARAM_STR);		
			$conn->execute();
			$lastInsertId=$conn->lastInsertId();
			if($lastInsertId){
				$email_encode= base64_encode($_POST['user_email'])."AVIORS".$random_number1;
				$subject="Activate your Cflick Account";
				$msg="Hi ".$_POST['user_fullname'].",<br/>
				Thank you for registering with <a href='https://www.cflick.com'>cflick.com.</a> We have processed your request.<br/>
				<a href='".$this->base_url."activate.php?pr=".$email_encode."'>Click Here</a> to activate your account.<br/><br/>";				
				//$this->send_email($_POST['user_email'],$subject,$_POST['user_fullname'],$msg);
				return "success1";				
			}
			
		}
	}
	function activate_user($user_email,$number){
		$conn = new Database();
		$user_activation_key= 5000000-$number;
		$sql = "UPDATE users SET user_status = :user_status WHERE user_email = :user_email && user_activation_key= :user_activation_key";
		$stmt = $conn->query($sql);									 
		$conn->bind(':user_status', 1, PDO::PARAM_INT);		
		$conn->bind(':user_email', $user_email, PDO::PARAM_STR);		
		$conn->bind(':user_activation_key', $user_activation_key, PDO::PARAM_STR);		
		$result=$conn->execute(); 
		if($conn->rowCount()>0){
			header('Location: '.$this->base_url.'login.php?status=success');
		}else{
			header('Location: '.$this->base_url.'register.php?status=success');
		}
	}
	public function check_user_email_exist($user_email){
		$conn = new Database();
		$query= "select * from users where user_email= :user_email";
		$query= $conn->query($query);
		$conn->bind(':user_email', $user_email, PDO::PARAM_STR);
		$result= $conn->resultset($query);
		if($conn->rowCount() > 0 ){
			return true;
		}else{
			return false;
		}
	}
	function send_email($to,$subject,$rec_name,$msg){
		require 'PHPMailer/class.phpmailer.php';
		require_once('PHPMailer/PHPMailerAutoload.php');
		$mail = new PHPMailer(); // the true param means it will throw exceptions on errors, which we need to catch
		$mail->IsSMTP();
		$name  = "Cflick";
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= "From:amy@cflick.com\r\n";
		$setfrom ="amy@cflick.com";
		$mail->SMTPAuth   = true;                  // enable SMTP authentication
		$mail->Host       = "mail.cflick.com"; // sets the SMTP server
		$mail->Port       = 465;		
		$mail->AddAddress($to, $rec_name);
		$mail->Username = 'amy@cflick.com';
		$mail->Password = 'yca66}h4wTyJ';
		$mail->AddReplyTo($setfrom, $name);
		$mail->Subject = $subject;
		$mail->SetFrom($setfrom, $name);
		$htmlContent = $msg;
		$mail->MsgHTML($htmlContent);
		$x=$mail->Send();
		if($x==false){
			return false;
		}else{	
			return true;
		}
	}
	function create_select_box_for_nifty($selectname,$default_selected_val){
		$conn = new Database();
		$query= "select stk_nifty_status from  tbl_isin_code group by stk_nifty_status order by stk_nifty_status";
		$query= $conn->query($query);
		$result= $conn->resultset($query);
		$select="";
		if($conn->rowCount() > 0 ){
			$select.="<select name='".$selectname."' class='".$selectname."'><option value='0'>Select Nifty </option>";
			foreach($result as $key=>$val){
				//echo "<br/>".$default_selected_val."==".$val['stk_nifty_status'];
				if($default_selected_val==$val['stk_nifty_status']){$selected= "selected='selected'";}else{ $selected="";}
				$select.="<option ".$selected." value='".$val['stk_nifty_status']."'>Nifty ".$val['stk_nifty_status']."</option>";
			}
			$select.="</select>";
		return $select;
		}else{
			return false;
		}
	}
	function create_select_box_for_nifty_indices($selectname,$default_selected_val){
		$conn = new Database();
		$query= "select count(*) as num ,stk_industry  from tbl_isin_code group by stk_industry";
		$query= $conn->query($query);
		$result= $conn->resultset($query);
		$select="";
		if($conn->rowCount() > 0 ){
			$select.="<select name='".$selectname."' class='".$selectname."'><option value='0'>Select Nifty </option>";
			foreach($result as $key=>$val){
				//echo "<br/>".$default_selected_val."==".$val['stk_nifty_status'];
				if($default_selected_val==$val['stk_industry']){$selected= "selected='selected'";}else{ $selected="";}
				$select.="<option ".$selected." value='".$val['stk_nifty_status']."'>Nifty ".$val['stk_industry']."</option>";
			}
			$select.="</select>";
		return $select;
		}else{
			return false;
		}
	}
	function create_select_box_for_all_stock($selectname,$default_selected_val,$limit){
		$conn = new Database();
		$query= "select id,stk_symbol from  tbl_isin_code order by id limit 0,$limit";
		$query= $conn->query($query);
		$result= $conn->resultset($query);
		$select="";
		if($conn->rowCount() > 0 ){
			$select.="<select id='stk_name' name='".$selectname."' class='".$selectname."'><option value='0'>Select Stock Name </option>";
			foreach($result as $key=>$val){
				//echo "<br/>".$default_selected_val."==".$val['stk_nifty_status'];
				if($default_selected_val==$val['id']){$selected= "selected='selected'";}else{ $selected="";}
				$select.="<option ".$selected." value='".$val['id']."'>".$val['stk_symbol']."</option>";
			}
			$select.="</select>";
		return $select;
		}else{
			return false;
		}
	}
	function trade_book(){
		$conn = new Database();
		$user_id=$this->get_user_details($_SESSION['user_email']);
		$userid=$user_id[0]['ID'];
		$csvfile=$_FILES['fileToUpload']['tmp_name'];
		$row = 1;
		if (($handle = fopen($csvfile, "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
				$num = count($data);
				if($row != 1){
					if($data[2]=="Buy"){ 
						$table="tbl_stock_buy";$buy_sell_date="stk_buy_date";
						$buy_sell_quantity="stk_buy_quantity";
					}else{
						$table="tbl_stock_sell";$buy_sell_date="stk_sell_date";
						$buy_sell_quantity="stk_sell_quantity";
					}
					$query="insert into $table(stk_stock_name,$buy_sell_date,$buy_sell_quantity,stk_price,
					stk_trade_value,stk_order_ref,stk_exchange,stk_stt,stk_trans_and_sebi_turnover_charges,
					stk_stamp_duty,stk_brokrage_and_servicetax,stk_brokragewith_alltaxes,user_id) values(:stk_stock_name,:stk_buy_date,:stk_buy_quantity,:stk_price,:stk_trade_value, :stk_order_ref, :stk_exchange, :stk_stt, :stk_trans_and_sebi_turnover_charges, :stk_stamp_duty, :stk_brokrage_and_servicetax, :stk_brokragewith_alltaxes,:user_id)";
					$stmt = $conn->query($query);
					$conn->bind(':stk_stock_name', $data[1], PDO::PARAM_STR);		
					$conn->bind(':stk_buy_date', date('Y-m-d', strtotime($data[0])), PDO::PARAM_STR);		
					$conn->bind(':stk_buy_quantity', $data[3], PDO::PARAM_STR);		
					$conn->bind(':stk_price', $data[4], PDO::PARAM_STR);		
					$conn->bind(':stk_trade_value', $data[5], PDO::PARAM_INT);		
					$conn->bind(':stk_order_ref', $data[6], PDO::PARAM_STR);		
					$conn->bind(':stk_exchange', $data[10], PDO::PARAM_STR);		
					$conn->bind(':stk_stt', $data[11], PDO::PARAM_STR);		
					$conn->bind(':stk_trans_and_sebi_turnover_charges', $data[12], PDO::PARAM_STR);		
					$conn->bind(':stk_stamp_duty', $data[13], PDO::PARAM_STR);		
					$conn->bind(':stk_brokrage_and_servicetax', $data[14], PDO::PARAM_STR);		
					$conn->bind(':stk_brokragewith_alltaxes', $data[15], PDO::PARAM_STR);		
					$conn->bind(':user_id', $userid, PDO::PARAM_STR);		
					$conn->execute();
					$lastInsertId=$conn->lastInsertId();
					if($lastInsertId){
						header('Location: '.$this->base_url.'purchased.php');
					}
				}
				$row++;	
			}
			fclose($handle);
		}
	}
	function upload_current_price(){
		$conn = new Database();
		$csvfile=$_FILES['file_current_price']['tmp_name'];
		$row = 1;
		if (($handle = fopen($csvfile, "r")) !== FALSE) {    
			while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {        
				$num = count($data);		
				if($row != 1){			
					$sql = "UPDATE tbl_stock_buy SET stk_current_price = :stk_price WHERE stk_stock_name = :stk_stock_name";
					$stmt = $conn->query($sql);									 
					$conn->bind(':stk_price', $data[5], PDO::PARAM_STR);		
					$conn->bind(':stk_stock_name', $data[0], PDO::PARAM_STR);		
					$result=$conn->execute(); 			
					if($result){				
						header('Location: '.$this->base_url.'purchased.php');			
					}
				}
				$row++;	
			}
			fclose($handle);
		}
	}
	function portfolio_stock($stock_status){
		$conn = new Database();
		$sql="select * from tbl_stock_buy where stk_stock_status=:stk_stock_status order by stk_buy_date desc";
		$query= $conn->query($sql);
		$conn->bind(':stk_stock_status', $stock_status, PDO::PARAM_STR);
		return $result= $conn->resultset($query);
	}
	function purchased_stock($stock_status){
		$conn = new Database();
		$sql="select * from tbl_stock_buy order by stk_buy_date desc";
		$query= $conn->query($sql);
		//$conn->bind(':stk_stock_status', $stock_status, PDO::PARAM_STR);
		return $result= $conn->resultset($query);
	}
	function stock_sold_status($stock_id){
		$conn = new Database();
		$sql="select * from tbl_stock_sell where stk_purchase_id=:stk_purchase_id";
		$query= $conn->query($sql);
		$conn->bind(':stk_purchase_id', $stock_id, PDO::PARAM_INT);
		$result= $conn->execute();
		if($conn->rowCount() > 0 ){
			return true;
		}else{
			return false;
		}
	}
	function sell_stock(){
		$conn = new Database();
		$query= $conn->query("select * from tbl_stock_sell");
		return $result= $conn->resultset($query);
	}
	function stock_recommendation(){
		$conn = new Database();
		$sql="select * from recommendation order by recom_date desc";
		$query= $conn->query($sql);
		return $result= $conn->resultset($query);
	}
	function stock_all_android_stock(){
		$conn = new Database();
		$today_date=date("Y-m-d");
		$sql="select * from tbl_android_stockDetails  where stockExdate>='".$today_date."' order by stockExdate";
		$query= $conn->query($sql);
		return $result= $conn->resultset($query);
	}
	function update_current_price(){
		$conn = new Database();
		$sql = "UPDATE tbl_stock_buy SET stk_current_price = :stk_price WHERE stk_stock_name = :stk_stock_name";
		$stmt = $conn->query($sql);									 
		$conn->bind(':stk_price', $_POST['current_value'], PDO::PARAM_STR);		
		$conn->bind(':stk_stock_name', $_POST['stock_name'], PDO::PARAM_STR);		
		$result=$conn->execute(); 
		if($result){
			return  true;
		}
	}
	function update_stock_notes(){
		$conn = new Database();
		$sql = "UPDATE tbl_stock_buy SET stk_notes = :stk_notes WHERE stk_stock_name = :stk_stock_name";
		$stmt = $conn->query($sql);									 
		$conn->bind(':stk_notes', $_POST['stcok_notes'], PDO::PARAM_STR);		
		$conn->bind(':stk_stock_name', $_POST['stock_name'], PDO::PARAM_STR);		
		$result=$conn->execute(); 
		if($conn->rowCount()>0){
			return true;
		}else{
			return false;
		}
	}
	function update_stk_sell_notes(){
		$conn = new Database();
		$sql = "UPDATE tbl_stock_sell SET stk_notes = :stk_notes WHERE stk_stock_name = :stk_stock_name";
		$stmt = $conn->query($sql);									 
		$conn->bind(':stk_notes', $_POST['stcok_notes'], PDO::PARAM_STR);		
		$conn->bind(':stk_stock_name', $_POST['stock_name'], PDO::PARAM_STR);		
		$result=$conn->execute(); 
		if($conn->rowCount()>0){
			return true;
		}else{
			return false;
		}
	}
	function sell_stock_row(){
		$conn = new Database();
		$user_id=$this->get_user_details($_SESSION['user_email']);
		$userid=$user_id[0]['ID'];
		$query="insert into tbl_stock_sell(stk_purchase_id, stk_stock_name,stk_sell_date ,stk_sell_quantity, stk_price, stk_trade_value, stk_order_ref, stk_exchange, stk_stt, stk_trans_and_sebi_turnover_charges,stk_stamp_duty, stk_brokrage_and_servicetax, stk_brokragewith_alltaxes,user_id,stk_notes)values (:stk_purchase_id, :stk_stock_name, :stk_sell_date,:stk_sell_quantity,:stk_price,:stk_trade_value, :stk_order_ref, :stk_exchange, :stk_stt, :stk_trans_and_sebi_turnover_charges, :stk_stamp_duty, :stk_brokrage_and_servicetax, :stk_brokragewith_alltaxes,:user_id, :stk_notes)";
		$stmt = $conn->query($query);
		$conn->bind(':stk_purchase_id', $_POST['stock_id'], PDO::PARAM_STR);		
		$conn->bind(':stk_stock_name', $_POST['stock_name'], PDO::PARAM_STR);		
		$conn->bind(':stk_sell_date', date('Y-m-d', strtotime($_POST['stock_sell_date'])), PDO::PARAM_STR);		
		$conn->bind(':stk_sell_quantity', $_POST['stock_sell_qty'], PDO::PARAM_STR);		
		$conn->bind(':stk_price', $_POST['stock_sell_price'], PDO::PARAM_STR);		
		$conn->bind(':stk_trade_value', $_POST['stock_sell_price']*$_POST['stock_sell_qty'], PDO::PARAM_INT);	
		$conn->bind(':stk_order_ref', "", PDO::PARAM_STR);		
		$conn->bind(':stk_exchange', "NSE", PDO::PARAM_STR);		
		$conn->bind(':stk_stt', 0, PDO::PARAM_STR);		
		$conn->bind(':stk_trans_and_sebi_turnover_charges', 0, PDO::PARAM_STR);		
		$conn->bind(':stk_stamp_duty', 0, PDO::PARAM_STR);		
		$conn->bind(':stk_brokrage_and_servicetax', 0, PDO::PARAM_STR);		
		$conn->bind(':stk_brokragewith_alltaxes', 0, PDO::PARAM_STR);		
		$conn->bind(':user_id', $userid, PDO::PARAM_STR);
		$conn->bind(':stk_notes', $_POST['stock_sell_notes'], PDO::PARAM_STR);		
		$conn->execute();
		$lastInsertId=$conn->lastInsertId();
		if($lastInsertId){
			return true;
		}else{
			return false;
		}
	}
	function add_recommendation(){
		$conn = new Database();
		$query="insert into recommendation(recom_date, recom_reseach_pro, recom_stock_name, recom_current_price, recom_recommend_price, recom_recommend_buyorsell, recom_invst_duration, recom_target1, recom_target2, recom_stoploss,recom_potential_upside,recom_sources,recom_notes) values(:recom_date,:recom_reseach_pro,:recom_stock_name,:recom_current_price,:recom_recommend_price, :recom_recommend_buyorsell, :recom_invst_duration, :recom_target1, :recom_target2, :recom_stoploss, :recom_potential_upside, :recom_sources,:recom_notes)";
		$stmt = $conn->query($query);
		$conn->bind(':recom_date', date('Y-m-d', strtotime($_POST['recom_date'])), PDO::PARAM_STR);		
		$conn->bind(':recom_reseach_pro', $_POST['recom_reseach_product'], PDO::PARAM_STR);		
		$conn->bind(':recom_stock_name', $_POST['recom_stock_name'], PDO::PARAM_STR);		
		$conn->bind(':recom_current_price', $_POST['recom_current_price'], PDO::PARAM_STR);		
		$conn->bind(':recom_recommend_price', $_POST['recom_price'], PDO::PARAM_INT);		
		$conn->bind(':recom_recommend_buyorsell', $_POST['recom_type'], PDO::PARAM_STR);		
		$conn->bind(':recom_invst_duration', $_POST['recom_duration'], PDO::PARAM_STR);		
		$conn->bind(':recom_target1', $_POST['recom_target1'], PDO::PARAM_STR);		
		$conn->bind(':recom_target2', $_POST['recom_target2'], PDO::PARAM_STR);		
		$conn->bind(':recom_stoploss', $_POST['recom_stoploss'], PDO::PARAM_STR);		
		$conn->bind(':recom_potential_upside', $_POST['recom_potential_upside'], PDO::PARAM_STR);		
		$conn->bind(':recom_sources', $_POST['recom_source'], PDO::PARAM_STR);		
		$conn->bind(':recom_notes', $_POST['recom_notes'], PDO::PARAM_STR);		
		$conn->execute();
		$lastInsertId=$conn->lastInsertId();
		if($lastInsertId){
			header('Location: '.$this->base_url.'recommendation.php');
		}
	}
	function check_file_for_include(){
		$req_file_name=explode("/",$_SERVER['PHP_SELF']);
		$r_f_name =end($req_file_name);
		if( ($r_f_name=="purchased.php") or ($r_f_name=="transaction-history.php") or ($r_f_name=="sold.php") or ($r_f_name=="recommendation.php") or ($r_f_name=="moneycontrol_recommendation.php") or ($r_f_name=="others_recommendation.php") or ($r_f_name=="portfolio.php")){
			return true;
		}	
	}
	function num_frmt($number,$todecimal){
		return number_format( $number, $todecimal, '.', '' );
	}
	function font_color($number){
		if($number>=0){
			return "<span style='color:green;'>$number</span>";
		}else{
			return "<span style='color:red;'>$number</span>";
		}
	}
	function check_super_admin(){
		$user_id=$this->get_user_details($_SESSION['user_email']);
		$userid=$user_id[0]['ID'];
		if($userid==1){
			return true;
		}else{
			return false;
		}
	}
	function get_customers($condition,$args){
		if($condition==1){			
			$condition=" 1 order by user_registered desc";
		}elseif($condition==2){
			//$condition="stk_date='$args' group by tbl_isin_code.id order by tbl_isin_code.id limit 0,501";
		}
		//echo $condition;//die;
		$conn = new Database();
		$sql="select * from users where $condition ";
		$query= $conn->query($sql);
		return $result= $conn->resultset($query);
	}
	function transaction_history(){
		$conn = new Database();
		$sql="SELECT tbl_stock_buy.stk_stock_name as stk_name, 
		tbl_stock_buy.stk_buy_date as stk_buy_date,
		tbl_stock_buy.stk_buy_quantity as stk_buy_qty,
		tbl_stock_buy.stk_price as stk_buy_unit_price,
		tbl_stock_buy.stk_brokragewith_alltaxes as stk_buy_ttl_brkrge_withtax,
		tbl_stock_sell.stk_sell_date as stk_sell_date,
		tbl_stock_sell.stk_price as stk_sell_unit_price,
		tbl_stock_sell.stk_brokragewith_alltaxes as stk_sell_ttl_brkrge_withtax
		
		FROM tbl_stock_sell left JOIN tbl_stock_buy ON tbl_stock_buy.id = tbl_stock_sell.stk_purchase_id order by tbl_stock_sell.stk_sell_date desc";
		$query= $conn->query($sql);
		//$conn->bind(':recom_sources', $recom, PDO::PARAM_STR);
		return $result= $conn->resultset($query);
	}
	function isin_details($stk_symbol){
		$conn = new Database();
		$sql="select * from tbl_isin_code where stk_symbol=:stk_symbol";
		$query= $conn->query($sql);
		$conn->bind(':stk_symbol', $stk_symbol, PDO::PARAM_STR);
		return $result= $conn->resultset($query);
	}
	function copy_data_to_tbl_daily_bhav_data3(){
		$conn = new Database();
		$last_traded_date=$this->get_last_traded_date();
		$query= "insert into tbl_daily_bhav_data3(stk_isin_no, stk_symbol, stk_series,stk_date,stk_timestamp, stk_prev_close, stk_open_price, stk_high_price, stk_low_price, stk_last_price, stk_close_price, stk_avg_price, stk_ttl_trd_qnty, stk_turnover_lacs, stk_no_of_trades, stk_deliv_qty, stk_deliv_per, stk_last_modified_time, stk_user_id) SELECT stk_isin_no, stk_symbol, stk_series,stk_date,stk_timestamp, stk_prev_close, stk_open_price, stk_high_price, stk_low_price, stk_last_price, stk_close_price, stk_avg_price, stk_ttl_trd_qnty, stk_turnover_lacs, stk_no_of_trades, stk_deliv_qty, stk_deliv_per, stk_last_modified_time, stk_user_id FROM tbl_daily_bhav_data where stk_date='".$last_traded_date."' and stk_isin_no <51";
		$stmt = $conn->query($query);
		$conn->execute();
	}
	function upload_daily_bhav_data(){
		$conn = new Database();
		$user_id=$this->get_user_details($_SESSION['user_email']);
		$userid=$user_id[0]['ID'];
		$csvfile=$_FILES['file_nse_daily_data']['tmp_name'];
		$row = 1;$querystatus=0;$abc=0;
		if (($handle = fopen($csvfile, "r")) !== FALSE) {			
			while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
				$num = count($data);
				if($row != 1){
					$stk_isin_exits=$this->isin_details(trim($data[0]));
					if( (trim($data[0])==$stk_isin_exits[0]['stk_symbol']) ){
						$query="insert into tbl_daily_bhav_data( stk_symbol, stk_series,stk_date,stk_prev_close, stk_open_price, stk_high_price, stk_low_price,  stk_close_price,  stk_ttl_trd_qnty, stk_no_of_trades, stk_deliv_per) values(:stk_symbol, :stk_series,:stk_date,:stk_prev_close, :stk_open_price, :stk_high_price, :stk_low_price, :stk_close_price, :stk_ttl_trd_qnty,  :stk_no_of_trades,  :stk_deliv_per)";
						$stmt = $conn->query($query);
						$conn->bind(':stk_symbol', trim($data[0]), PDO::PARAM_STR);		
						$conn->bind(':stk_series', trim($data[1]), PDO::PARAM_STR);		
						$conn->bind(':stk_date', date('Y-m-d', strtotime(trim($data[2]))), PDO::PARAM_STR);
						$conn->bind(':stk_prev_close', trim($data[3]), PDO::PARAM_STR);		
						$conn->bind(':stk_open_price', trim($data[4]), PDO::PARAM_STR);		
						$conn->bind(':stk_high_price', trim($data[5]), PDO::PARAM_STR);		
						$conn->bind(':stk_low_price', trim($data[6]), PDO::PARAM_STR);		
						$conn->bind(':stk_close_price', trim($data[8]), PDO::PARAM_STR);	
						$conn->bind(':stk_ttl_trd_qnty', trim($data[10]), PDO::PARAM_STR);		
						$conn->bind(':stk_no_of_trades', trim($data[12]), PDO::PARAM_STR);	
						$conn->bind(':stk_deliv_per', trim($data[14]), PDO::PARAM_STR);	
						$querystatus=$conn->execute();
					}
				}
				$row++;	
			}
			fclose($handle);
		}
		if($querystatus>0){
			//$this->copy_data_to_tbl_daily_bhav_data3();
			return true;
		}else{
			return false;
		}
	}
	function upload_daily_bhav_data3(){
		$conn = new Database();
		$user_id=$this->get_user_details($_SESSION['user_email']);
		$userid=$user_id[0]['ID'];
		$csvfile=$_FILES['file_nse_daily_data_test']['tmp_name'];
		$row = 1;$querystatus=0;$abc=0;
		if (($handle = fopen($csvfile, "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
				$num = count($data);
				if($row != 1){
					$stk_isin_exits=$this->isin_details(trim($data[0]));
					//if( (trim($data[0])==$stk_isin_exits[0]['stk_symbol']) and (trim($data[1])==$stk_isin_exits[0]['stk_series'])){echo trim($data[0]);die;
						//echo $row."===".$data[6]."======".$abc."<br/>";$abc++;
						//print_r($stk_isin_exits[0]['stk_symbol']);
						$query="insert into tbl_daily_bhav_datasingle( stk_symbol,stk_date, stk_series,stk_prev_close,stk_open_price, stk_high_price,stk_low_price,stk_close_price,stk_ttl_trd_qnty,stk_no_of_trades) values(:stk_symbol, :stk_date,:stk_series,:stk_prev_close,:stk_open_price, :stk_high_price, :stk_low_price, :stk_close_price,:stk_ttl_trd_qnty,:stk_no_of_trades)";
						$stmt = $conn->query($query);
						//$conn->bind(':stk_isin_no', $stk_isin_exits[0]['id'], PDO::PARAM_STR);		
						$conn->bind(':stk_symbol', trim($data[0]), PDO::PARAM_STR);		
								
						$conn->bind(':stk_date', date('Y-m-d', strtotime(trim($data[1]))), PDO::PARAM_STR);	$conn->bind(':stk_series', trim($data[2]), PDO::PARAM_STR);
						//$conn->bind(':stk_timestamp', strtotime($data[2]), PDO::PARAM_STR);		
						$conn->bind(':stk_prev_close', trim($data[6]), PDO::PARAM_STR);		
						$conn->bind(':stk_open_price', trim($data[3]), PDO::PARAM_STR);		
						$conn->bind(':stk_high_price', trim($data[4]), PDO::PARAM_STR);		
						$conn->bind(':stk_low_price', trim($data[5]), PDO::PARAM_STR);		
						//$conn->bind(':stk_last_price', trim($data[7]), PDO::PARAM_STR);		
						$conn->bind(':stk_close_price', trim($data[8]), PDO::PARAM_STR);		
						//$conn->bind(':stk_avg_price', trim($data[9]), PDO::PARAM_STR);		
						$conn->bind(':stk_ttl_trd_qnty', trim($data[12]), PDO::PARAM_STR);		
						/*$conn->bind(':stk_turnover_lacs', trim($data[11]), PDO::PARAM_STR);*/		
						$conn->bind(':stk_no_of_trades', trim($data[14]), PDO::PARAM_STR);		
						/*$conn->bind(':stk_deliv_qty', trim($data[13]), PDO::PARAM_STR);		
						$conn->bind(':stk_deliv_per', trim($data[14]), PDO::PARAM_STR);		
						$conn->bind(':stk_last_modified_time', time(), PDO::PARAM_STR);		
						$conn->bind(':stk_user_id', $userid, PDO::PARAM_INT);*/				
						$querystatus=$conn->execute();
					//}
				}
				$row++;	
			}
			fclose($handle);
		}
		if($querystatus>0){
			//$this->copy_data_to_tbl_daily_bhav_data3();
			return true;
		}else{
			return false;
		}
	}
	function upload_daily_bhav_dataSingleStock(){
		$conn = new Database();
		$user_id=$this->get_user_details($_SESSION['user_email']);
		$userid=$user_id[0]['ID'];
		$csvfile=$_FILES['file_singleStockDataToBhavTable']['tmp_name'];
		$row = 1;$querystatus=0;$abc=0;
		if (($handle = fopen($csvfile, "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
				$num = count($data);
				if($row != 1){
					$stk_isin_exits=$this->isin_details(trim($data[0]));
					//if( (trim($data[0])==$stk_isin_exits[0]['stk_symbol']) and (trim($data[1])==$stk_isin_exits[0]['stk_series'])){echo trim($data[0]);die;
						//echo $row."===".$data[0]."======".$abc."<br/>";$abc++;
						//print_r($stk_isin_exits[0]['stk_symbol']);
						$query="insert into tbl_daily_bhav_data3( stk_symbol,stk_date,stk_prev_close, stk_open_price, stk_high_price,stk_low_price,stk_close_price,stk_ttl_trd_qnty) values(:stk_symbol, :stk_date,:stk_prev_close,:stk_open_price, :stk_high_price, :stk_low_price, :stk_close_price,:stk_ttl_trd_qnty)";
						$stmt = $conn->query($query);
						//$conn->bind(':stk_isin_no', $stk_isin_exits[0]['id'], PDO::PARAM_STR);		
						$conn->bind(':stk_symbol', trim($data[0]), PDO::PARAM_STR);		
						//$conn->bind(':stk_series', trim($data[1]), PDO::PARAM_STR);		
						$conn->bind(':stk_date', date('Y-m-d', strtotime(trim($data[1]))), PDO::PARAM_STR);		
						//$conn->bind(':stk_timestamp', strtotime($data[2]), PDO::PARAM_STR);		
						$conn->bind(':stk_prev_close', trim($data[3]), PDO::PARAM_STR);		
						$conn->bind(':stk_open_price', trim($data[3]), PDO::PARAM_STR);		
						$conn->bind(':stk_high_price', trim($data[4]), PDO::PARAM_STR);		
						$conn->bind(':stk_low_price', trim($data[5]), PDO::PARAM_STR);		
						//$conn->bind(':stk_last_price', trim($data[7]), PDO::PARAM_STR);		
						$conn->bind(':stk_close_price', trim($data[8]), PDO::PARAM_STR);		
						/*$conn->bind(':stk_avg_price', trim($data[9]), PDO::PARAM_STR);*/		
						$conn->bind(':stk_ttl_trd_qnty', trim($data[10]), PDO::PARAM_STR);		
						/*$conn->bind(':stk_turnover_lacs', trim($data[11]), PDO::PARAM_STR);		
						$conn->bind(':stk_no_of_trades', trim($data[12]), PDO::PARAM_STR);		
						$conn->bind(':stk_deliv_qty', trim($data[13]), PDO::PARAM_STR);		
						$conn->bind(':stk_deliv_per', trim($data[14]), PDO::PARAM_STR);		
						$conn->bind(':stk_last_modified_time', time(), PDO::PARAM_STR);		
						$conn->bind(':stk_user_id', $userid, PDO::PARAM_INT);*/				
						$querystatus=$conn->execute();
					//}
				}
				$row++;	
			}
			fclose($handle);
		}
		if($querystatus>0){
			//$this->copy_data_to_tbl_daily_bhav_data3();
			return true;
		}else{
			return false;
		}
	}
	function cron_upload_daily_bhav_data(){
		$conn = new Database();
		ini_set("user_agent","any");
		$csv= $this->get_csv_data_using_curl();
		$row = str_getcsv($csv, "\n");
		$length = count($row);
		$nse_traded = str_getcsv($row[1], ",");
		$nse_traded_date=$nse_traded[2];
		$conn = new Database();
		$last_traded_date=$this->get_last_traded_date();
		$querystatus=0;
		if(strtotime($nse_traded_date) > strtotime($last_traded_date)){
			for($i=1;$i<$length;$i++) {
				$data = str_getcsv($row[$i], ",");
				$stk_isin_exits=$this->isin_details(trim($data[0]));				
				if( (trim($data[0])==$stk_isin_exits[0]['stk_symbol']) and (trim($data[1])==$stk_isin_exits[0]['stk_series'])){
					//echo trim($data[0])."==".$stk_isin_exits[0]['stk_symbol']."<br/>";
					$query="insert into tbl_daily_bhav_data(stk_isin_no, stk_symbol, stk_series,stk_date,stk_timestamp, stk_prev_close, stk_open_price, stk_high_price, stk_low_price, stk_last_price, stk_close_price, stk_avg_price, stk_ttl_trd_qnty, stk_turnover_lacs, stk_no_of_trades, stk_deliv_qty, stk_deliv_per,
					stk_last_modified_time, stk_user_id ) values(:stk_isin_no,:stk_symbol, :stk_series,:stk_date,:stk_timestamp,:stk_prev_close, :stk_open_price, :stk_high_price, :stk_low_price, :stk_last_price, :stk_close_price, :stk_avg_price, :stk_ttl_trd_qnty, :stk_turnover_lacs, :stk_no_of_trades, :stk_deliv_qty, :stk_deliv_per, :stk_last_modified_time, :stk_user_id)";
					$stmt = $conn->query($query);
					$conn->bind(':stk_isin_no', $stk_isin_exits[0]['id'], PDO::PARAM_STR);		
					$conn->bind(':stk_symbol', trim($data[0]), PDO::PARAM_STR);		
					$conn->bind(':stk_series', trim($data[1]), PDO::PARAM_STR);		
					$conn->bind(':stk_date', date('Y-m-d', strtotime(trim($data[2]))), PDO::PARAM_STR);		
					$conn->bind(':stk_timestamp', strtotime($data[2]), PDO::PARAM_STR);		
					$conn->bind(':stk_prev_close', trim($data[3]), PDO::PARAM_STR);		
					$conn->bind(':stk_open_price', trim($data[4]), PDO::PARAM_STR);		
					$conn->bind(':stk_high_price', trim($data[5]), PDO::PARAM_STR);		
					$conn->bind(':stk_low_price', trim($data[6]), PDO::PARAM_STR);		
					$conn->bind(':stk_last_price', trim($data[7]), PDO::PARAM_STR);		
					$conn->bind(':stk_close_price', trim($data[8]), PDO::PARAM_STR);		
					$conn->bind(':stk_avg_price', trim($data[9]), PDO::PARAM_STR);		
					$conn->bind(':stk_ttl_trd_qnty', trim($data[10]), PDO::PARAM_STR);		
					$conn->bind(':stk_turnover_lacs', trim($data[11]), PDO::PARAM_STR);		
					$conn->bind(':stk_no_of_trades', trim($data[12]), PDO::PARAM_STR);		
					$conn->bind(':stk_deliv_qty', trim($data[13]), PDO::PARAM_STR);		
					$conn->bind(':stk_deliv_per', trim($data[14]), PDO::PARAM_STR);		
					$conn->bind(':stk_last_modified_time', time(), PDO::PARAM_STR);		
					$conn->bind(':stk_user_id', 0, PDO::PARAM_INT);				
					$querystatus=$conn->execute();
				}				
			}
			//$this->copy_data_to_tbl_daily_bhav_data3();
		}		
	}
	function get_csv_data_using_curl(){
		$ch = curl_init();
		$url="https://nsearchives.nseindia.com/products/content/sec_bhavdata_full_".date('dmY').".csv"; 
		curl_setopt($ch, CURLOPT_URL, $url); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
		curl_setopt($ch, CURLOPT_VERBOSE, true); 
		//curl_setopt($ch, CURLOPT_USERAGENT, $agent); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15); //time out of 15 seconds
		$output = curl_exec($ch); 
		curl_close($ch);
		$filename=date("h:i A d-M-Y").".txt";
		touch("$filename");
		$myfile = fopen($filename, "w") or die("Unable to open file!");
		$txt = "Cron is working and NSE is not accessible\n";
		fwrite($myfile, $output);
		fclose($myfile);
		return $output;
	}
	function cron_test_upload_daily_bhav_data2(){
		$conn = new Database();
		$sql = "UPDATE users SET user_status = 1 WHERE ID = 4";
		$stmt = $conn->query($sql);	
		$result=$conn->execute(); 
	}
	function get_last_traded_date(){
		$conn = new Database();
		$sql="select max(stk_date) as last_traded_date from tbl_daily_bhav_data";
		$query= $conn->query($sql);
		$result= $conn->resultset($query);
		return $last_tr_dt=date('Y-m-d',strtotime($result[0]['last_traded_date']));
	}
	function get_traded_date($date){
		$conn = new Database();
		//echo $date;
		$sql="select stk_date as last_traded_date from tbl_daily_bhav_data where stk_date='".$date."'limit 1 ";
		$query= $conn->query($sql);
		$conn->bind(':stk_date', $date, PDO::PARAM_STR);
		$conn->execute();
		//$result= $conn->resultset($query);
		if($conn->rowCount()>0){
			//echo "in if ";die;
			return true;
		}else{
			//echo "in else ";die;
			return false;
		}
	}
	function daily_stock_data($condition,$args,$nifty_status){		
		if($condition==1){
			$last_traded_date= $this->get_last_traded_date();
			$condition="stk_date='$last_traded_date' and stk_nifty_status='$nifty_status'order by tbl_isin_code.id asc";
			//$condition="stk_date='$last_traded_date' order by tbl_isin_code.id asc";
		}elseif($condition==2){
			if($nifty_status==0){$nifty_con='';}else{$nifty_con= " and stk_nifty_status='$nifty_status'";}
			$condition="stk_date='$args' $nifty_con order by tbl_isin_code.id";
		}elseif($condition==3){
			$condition="tbl_daily_bhav_data.stk_symbol='$args' order by tbl_daily_bhav_data.stk_date desc";
		}elseif($condition==4){
			$condition="tbl_daily_bhav_data.stk_symbol='$args' order by tbl_daily_bhav_data.stk_date desc ";
		}
		//echo $condition;//die;
		$conn = new Database();
		$sql="select distinct tbl_daily_bhav_data.stk_symbol,tbl_isin_code.id,tbl_daily_bhav_data.stk_series,tbl_daily_bhav_data.stk_open_price,tbl_daily_bhav_data.stk_high_price, tbl_daily_bhav_data.stk_low_price,tbl_isin_code.stk_nifty_status,tbl_daily_bhav_data.stk_ttl_trd_qnty,tbl_daily_bhav_data.stk_deliv_per,tbl_daily_bhav_data.stk_date, tbl_daily_bhav_data.stk_prev_close,tbl_daily_bhav_data.stk_close_price from tbl_daily_bhav_data left join tbl_isin_code on tbl_daily_bhav_data.stk_symbol= tbl_isin_code.stk_symbol where $condition ";//die;
		$query= $conn->query($sql);
		//$conn->bind(':stk_date', $stk_date, PDO::PARAM_STR);
		return $result= $conn->resultset($query);
	}
	function get_stock_volume($stk_isin,$days){
		$conn = new Database();
		$sql="select stk_ttl_trd_qnty from tbl_daily_bhav_data where stk_isin_no=:stk_isin_no order by stk_date desc limit 0,$days";
		$query= $conn->query($sql);
		$conn->bind(':stk_isin_no', $stk_isin, PDO::PARAM_STR);				
		$result= $conn->resultset($query);
		return $result=array_reverse($result);
	}
	function stock_volume_increase($multiplier,$last_traded_date,$days=2){
		$conn = new Database();
		$sql="select id from tbl_isin_code order by id limit 0,500";
		$query= $conn->query($sql);
		$result = $conn->resultset($query);
		$formated_date=date("yy-m-d",strtotime($last_traded_date));
		foreach($result as $key=>$val){
			$sql="select id,stk_isin_no,stk_symbol,stk_date,stk_close_price,stk_ttl_trd_qnty from tbl_daily_bhav_data where stk_date<='$formated_date' and stk_isin_no='".$val['id']."' order by stk_date desc limit 0,$days";
			$query= $conn->query($sql);				
			$result= $conn->resultset($query);
			//echo '<pre>';print_r($result);
			if(($result[0]['stk_ttl_trd_qnty']) >= ($result[1]['stk_ttl_trd_qnty'])*$multiplier){
				//echo $result[0]['stk_symbol']."==".($result[0]['stk_ttl_trd_qnty'])."====>".($result[1]['stk_ttl_trd_qnty']);echo "<br/>";
				$volume_increased=number_format($result[0]['stk_ttl_trd_qnty']/$result[1]['stk_ttl_trd_qnty']);
				$price_increased=number_format($result[0]['stk_close_price']-$result[1]['stk_close_price']);
				$price_increased_percentage=number_format(($price_increased*100)/$result[1]['stk_close_price'],2);
				$vol_price_increased[]=array('stk_symbol'=>$result[0]['stk_symbol'],
				'stk_date'=>$result[0]['stk_date'], 'volume_increased'=>$volume_increased,
				'price_inc'=>$price_increased,	'price_inc_per'=>$price_increased_percentage,
				'stk_isin_no'=>$result[0]['stk_isin_no']);
			}
		}	
		return $vol_price_increased;
	}
	
	function upload_android_data_nse(){
		$conn = new Database();
		$csvfile=$_FILES['file_android']['tmp_name'];
		$row = 1;
		if (($handle = fopen($csvfile, "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
				$num = count($data);
				if($row != 1){
					$query= "select * from tbl_android_stockDetails where stockSymbol= :stockSymbol and stockExdate=:stockExdate";
					$query= $conn->query($query);
					$conn->bind(':stockSymbol', $data[0], PDO::PARAM_STR);
					$conn->bind(':stockExdate', date('Y-m-d', strtotime(trim($data[5]))), PDO::PARAM_STR);	
					$result= $conn->resultset($query);
					if($conn->rowCount()>0){
						
					}else{
						
						$query="insert into tbl_android_stockDetails(stockSymbol, stockName, stockDesc, stockExdate, stockRecordDate, stockPurpose,stockEntryType,stockStatus,stockEntryDate) values(:stockSymbol,:stockName, :stockDesc,:stockExdate,:stockRecordDate, :stockPurpose,:stockEntryType,:stockStatus,:stockEntryDate)";
						$stmt = $conn->query($query);
						$conn->bind(':stockSymbol', trim($data[0]), PDO::PARAM_STR);		
						$conn->bind(':stockName', trim($data[1]), PDO::PARAM_STR);		
						$conn->bind(':stockDesc', trim($data[1]), PDO::PARAM_STR);				
						$conn->bind(':stockExdate', date('Y-m-d', strtotime(trim($data[5]))), PDO::PARAM_STR);		
						$conn->bind(':stockRecordDate', date('Y-m-d', strtotime(trim($data[6]))), PDO::PARAM_STR);		
						$conn->bind(':stockPurpose', trim($data[3]), PDO::PARAM_STR);		
						$conn->bind(':stockEntryType', 1, PDO::PARAM_INT);		
						$conn->bind(':stockStatus', 1, PDO::PARAM_INT);	
						$conn->bind(':stockEntryDate', date('Y-m-d'), PDO::PARAM_STR);	
						$conn->execute();
						$lastInsertId=$conn->lastInsertId();							
					}
				}
				$row++;	
			}
			fclose($handle);
		}
		return true;
	}
	
	
	function upload_android_data_bse(){
		$conn = new Database();
		$csvfile=$_FILES['file_android']['tmp_name'];
		$row = 1;
		if (($handle = fopen($csvfile, "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
				$num = count($data);
				if($row != 1){
					$query= "select * from tbl_android_stockDetails where stockSymbol= :stockSymbol and stockExdate=:stockExdate";
					$query= $conn->query($query);
					$conn->bind(':stockSymbol', $data[1], PDO::PARAM_STR);
					$conn->bind(':stockExdate', date('Y-m-d', strtotime(trim($data[3]))), PDO::PARAM_STR);	
					$result= $conn->resultset($query);
					if($conn->rowCount()>0){
						
					}else{
						
						$query="insert into tbl_android_stockDetails(stockSymbol, stockName, stockDesc, stockExdate, stockRecordDate, stockPurpose,stockEntryType,stockStatus,stockEntryDate) values(:stockSymbol,:stockName, :stockDesc,:stockExdate,:stockRecordDate, :stockPurpose,:stockEntryType,:stockStatus,:stockEntryDate)";
						$stmt = $conn->query($query);
						$conn->bind(':stockSymbol', trim($data[1]), PDO::PARAM_STR);		
						$conn->bind(':stockName', trim($data[2]), PDO::PARAM_STR);		
						$conn->bind(':stockDesc', trim($data[2]), PDO::PARAM_STR);				
						$conn->bind(':stockExdate', date('Y-m-d', strtotime(trim($data[3]))), PDO::PARAM_STR);		
						$conn->bind(':stockRecordDate', date('Y-m-d', strtotime(trim($data[5]))), PDO::PARAM_STR);		
						$conn->bind(':stockPurpose', trim($data[4]), PDO::PARAM_STR);		
						$conn->bind(':stockEntryType', 1, PDO::PARAM_INT);		
						$conn->bind(':stockStatus', 1, PDO::PARAM_INT);
						$conn->bind(':stockEntryDate', date('Y-m-d'), PDO::PARAM_STR);
						$conn->execute();
						$lastInsertId=$conn->lastInsertId();								
					}
				}
				$row++;	
			}
			fclose($handle);
		}
		return true;
	}
	function validateNseData(){
		$conn = new Database();
		$sql="select stk_symbol,stk_nifty_status from tbl_isin_code order by stk_nifty_status asc";
		/*SELECT tbl_daily_bhav_data.stk_symbol, COUNT(*) AS countstkSymbol,tbl_isin_code.stk_nifty_status
    FROM tbl_daily_bhav_data   INNER JOIN tbl_isin_code
    ON tbl_daily_bhav_data.stk_symbol=tbl_isin_code.stk_symbol
    GROUP BY tbl_isin_code.stk_symbol,tbl_isin_code.stk_nifty_status
    ORDER BY tbl_isin_code.	stk_nifty_status,tbl_isin_code.stk_symbol asc;*/
	/*sql="SELECT `stk_symbol`, `stk_date`, COUNT(*) FROM tbl_daily_bhav_data GROUP BY `stk_symbol`, `stk_date` HAVING COUNT(*) > 1";*/
		$query= $conn->query($sql);
		$result = $conn->resultset($query);
		//echo '<pre>';
		//print_r($result);
		//$data=array(Null);
		foreach($result as $key=>$value){
			$sql1="SELECT COUNT('".$value['stk_symbol']."') as tradingDay FROM `tbl_daily_bhav_data` WHERE stk_symbol='".$value['stk_symbol']."'";
			$query1= $conn->query($sql1);
			$result1 = $conn->resultset($query1);
			$data[]=array($value['stk_symbol'],$result1[0]['tradingDay'],$value['stk_nifty_status']);
		}
		return $data;
	}
	function insertNewDatatoBhavData(){
		//echo '<pre>';echo "alok";print_r($_POST['stk_isin']);
		$conn = new Database();
		$query= "delete from tbl_daily_bhav_data where stk_symbol= :stk_symbol";
		$query= $conn->query($query);
		$conn->bind(':stk_symbol', $_POST['stk_isin'], PDO::PARAM_STR);
		$conn->execute();
		
		$query= "insert into tbl_daily_bhav_data(stk_symbol, stk_date,  stk_open_price, stk_high_price, stk_low_price,  stk_close_price,  stk_ttl_trd_qnty ) SELECT stk_symbol,stk_date, stk_open_price, stk_high_price, stk_low_price, stk_close_price,  stk_ttl_trd_qnty FROM tbl_daily_bhav_data3 where stk_symbol='".$_POST['stk_isin']."'";		
		$stmt = $conn->query($query);		
		$r=$conn->execute();
		if($r){echo "1";}else{echo "0";}
	}
	function upload_nse_isin_details(){
		$conn = new Database();
		$csvfile=$_FILES['fileNSE_ISIN']['tmp_name'];
		$row = 1;
		if (($handle = fopen($csvfile, "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
				$num = count($data);
				if($row != 1){
					$query= "select * from tbl_isin_code1 where stk_symbol= :stk_symbol";
					$query= $conn->query($query);
					$conn->bind(':stk_symbol', $data[0], PDO::PARAM_STR);
					$result= $conn->resultset($query);
					if($result[0]['stk_symbol']== $data[0]){
						$query="UPDATE tbl_isin_code1 set stk_nifty_status= :stk_nifty_status where stk_symbol=:stk_symbol";
						$stmt = $conn->query($query);	
						$conn->bind(':stk_symbol', trim($data[0]), PDO::PARAM_STR);		
						$conn->bind(':stk_nifty_status', 10, PDO::PARAM_STR);	
						$conn->execute();
						//$lastInsertId=$conn->lastInsertId();
						//if($lastInsertId){
							//header('Location: '.$this->base_url.'nse_data.php');
						//}
					}
				}
				$row++;	
			}
			fclose($handle);
		}
		return true;
	}
	
	function rsi($niftyStatus){
		$conn = new Database();
		$ma_status=array();
		$sql="select stk_symbol from tbl_isin_code  where stk_nifty_status='".$niftyStatus."'";
		//$sql="select stk_symbol from tbl_isin_code  where stk_nifty_status in (2) order by stk_nifty_status asc limit 3";
		$query= $conn->query($sql);
		$result = $conn->resultset($query);//echo '<pre>';print_r($result);//die;
		$last120days=strtotime("-300 days");
		$from_date=date("Y-m-d",$last120days);
		foreach($result as $key=>$val){
			$sql="select id,stk_symbol,stk_date,stk_close_price from tbl_daily_bhav_data where stk_symbol='".$val['stk_symbol']."' and stk_date>='".$from_date."'"; 
			$query= $conn->query($sql);
			$result1 = $conn->resultset($query);
			$array_close=(array) null;$rsi= null;$rsi_end=null;
			foreach($result1 as $key1=>$val1){
				$stk_symbol[]=$val1['stk_symbol'];
				$array_close[]=$val1['stk_close_price'];
				$array_date=$val1['stk_date'];
			}
			$rsi=$this->rsiwith($array_close);
			$rsi_end=end($rsi);
			$rsi_stk_end=end($stk_symbol);
			$ma_status[]=array(	"stk_symbol"=>$rsi_stk_end,
							"stk_date"=>$array_date,
							"rsi"=>$rsi_end);
		}
		return $ma_status;
	}
	function rsiwith ($closingValue){
		$rsi=array();
		foreach($closingValue as $key=>$val){
			$close=$val;
			$key_1=$key-1;
			if($key==0){
				$change=0;
			}else{
				$change=$closingValue[$key]-$closingValue[$key-1];
			}
			if($change>0){$gain=$change;}else{$gain=0;}
			if($change<0){$loss=abs($change);}else{$loss=0;}		
			if($key==0){
				$avgGain=$avgLoss=0;
			}else{
				$avgGain=((($avgGain*13)+$gain)/14);
				$avgLoss=((($avgLoss*13)+$loss)/14);
			}
			if($avgGain==0 or $avgLoss==0){
				$rs=0;
			}else{
				$rs=$avgGain/$avgLoss;
			}
			if($key>12){
				$rsi[]=$this->num_frmt(100-(100/(1+$rs)),2);
			}else{
				$rsi[]=0;
			}
		}
		return $rsi;
	}
	function divergence($days=10,$nifty_status=1){		
		//echo $days."  ".$nifty_status."  ";die;
		$conn = new Database();
		$result1=array();
		$sql="select id,stk_symbol from tbl_isin_code where stk_nifty_status=:nifty_status order by stk_symbol";
		$query= $conn->query($sql);
		$conn->bind(':nifty_status', $nifty_status, PDO::PARAM_STR);	
		$result= $conn->resultset($query);
		//print_r($result);die;
		foreach($result as $key =>$val){
			//echo $nifty_status;
			$sql1="select stk_symbol,stk_date,stk_open_price,stk_high_price,stk_low_price,stk_close_price from tbl_daily_bhav_data where stk_symbol='".$val['stk_symbol']."' order by stk_date asc limit ".$days."";
			$query1= $conn->query($sql1);
			$resultset=$conn->resultset($query1);
			//echo '<pre>';print_r(array_reverse($resultset));die;
			$result1[$val['stk_symbol']]= $resultset;
			
		}
		//echo '<pre>';print_r($result1);
		return $result1;
	}
	function divergence2($stk_name,$days,$period=14){		
		//echo $stk_name."  ".$period."  ".$days;die;
		$conn = new Database();
		//$sql="select * from tbl_daily_bhav_data4 ";//where id<178";//178-214order by stk_date desc limit 10";
		$sql="select id,stk_symbol,stk_open_price,stk_high_price,stk_low_price,stk_close_price,stk_date from tbl_daily_bhav_data where stk_symbol='".$stk_name."' order by stk_date asc"; //die;working;
		$query= $conn->query($sql);
		$result1 = $conn->resultset($query);
		//echo '<pre>';print_r($result1);//die;
		$last120days=strtotime("-158 days");
		$from_date=date("Y-m-d",$last120days);		
		$arrayOpenPrice=$arrayHighPrice=$arrayLowPrice=$arrayClosePrice= null;$rsi= null;$rsi_end=null;
		$arrayDate=null;$arrayKey=null;
		foreach($result1 as $key1=>$val1){
			$arrayOpenPrice[]=$val1['stk_open_price'];
			$arrayHighPrice[]=$val1['stk_high_price'];
			$arrayLowPrice[]=$val1['stk_low_price'];
			$arrayClosePrice[]=$val1['stk_close_price'];		
			$arrayDate[]=$val1['stk_date'];
			$arrayKey[]=$key1;
		}
		$rsi=$this->rsiwith($arrayClosePrice);
		//echo '<pre>';print_r($rsi);die;
		$rsi=array_reverse($rsi);
		$arrayDate=array_reverse($arrayDate);
		$arrayOpenPrice=array_reverse($arrayOpenPrice);
		$arrayHighPrice=array_reverse($arrayHighPrice);
		$arrayLowPrice=array_reverse($arrayLowPrice);
		$arrayClosePrice=array_reverse($arrayClosePrice);
		$arrayKey=array_reverse($arrayKey);
		$ma_status=array("stk_date"=>$arrayDate,"array_key"=>$arrayKey,
		"arrayOpenPrice"=>$arrayOpenPrice,"arrayHighPrice"=>$arrayHighPrice,"array_low_price"=>$arrayLowPrice,"arrayClosePrice"=>$arrayClosePrice,"ma_periods"=>$period,"rsi"=>$rsi);
		//return $ma_status;
		//echo '<pre>';print_r($ma_status);die;
		$max=null;$max_value_date=null;$min_value=null;$min_value_date=null;
		$min_rsi=$rsi[0];$max_rsi=$rsi[0];//die;
		$allMaxHighprice=null;$allMinLowPrice=null;
		for($i=0;$i<$days;$i++){
			//echo $i;
			if($max_rsi<$rsi[$i]){
				$max=$arrayHighPrice[$i];
				$max_value_date=$arrayDate[$i];
				$max_rsi=$rsi[$i];
				$allMaxHighprice[]=$arrayClosePrice[$i];
				
			}
			if($min_rsi>$rsi[$i]){
				//echo $min_value." ".$min_value_date;echo '<br>';
				$min_value=$arrayLowPrice[$i];
				$min_value_date=$arrayDate[$i];
				$min_rsi=$rsi[$i];
				$allMinLowPrice[]=$arrayClosePrice[$i];
			}
		}
		$current_rsi=$rsi[0];
		$current_low=$arrayLowPrice[0];
		$current_high=$arrayHighPrice[0];
		$current_date=$arrayDate[0];
		/*echo "<br><b>Test For Bearish Diversion</b></br>";
		echo "Current High==>".$current_high." Date==>".$current_date." RSI===>".$current_rsi;echo '<br>';
		echo "High in Row==>".$max." Max Date==>".$max_value_date."Max RSI===>".$max_rsi;echo '<br>';
		echo "<br><b>Test For Bullish Diversion</b></br>";
		echo "Current Low==>".$current_low." Date==>".$current_date." RSI===>".$current_rsi;echo '<br>';
		echo "Lowest in Row==>".$min_value." Min Date==>".$min_value_date." RSI===>".$min_rsi;echo '<br>';*/
		if(($current_rsi>$min_rsi )  and ($current_low<$min_value)){
			//echo "current_low  ". $current_low. "  ====   ".min($allMinLowPrice);echo "<br>";//die;
			//print_r($allMinLowPrice);echo "<br>";
			if($current_low > min($allMinLowPrice)){
				//return $rsi[0];
			}else{
				$date1 =strtotime($current_date); $date2= strtotime($min_value_date);
				$daysDifference = ($date1 - $date2) / (60 * 60 * 24);
				if($daysDifference > 4){
					$diffInPrice=$current_low-$min_value;
					$percentDiffInPrice=(($diffInPrice*100)/$min_value);
					if( (($current_rsi-$min_rsi)>1) /*and ($percentDiffInPrice>1)*/){
						return "<table border=1 width=50%><tr><td colspan='3' align='middle'>Bull</td></tr><tr><td>Minimun RSI ".$min_rsi."</td><td>Lowest in Row ".$min_value."</td><td> Min Date ".date('d-M-Y',strtotime($min_value_date))."</td></tr><tr><td>Current RSI ".$current_rsi."</td><td>Current Low ".$current_low."</td><td>Today Date  ".date('d-M-Y',strtotime($current_date))."</td></tr></table>";
					}
				}
			}
		}
		if(($current_rsi<$max_rsi )  and ($current_high>$max)){
			//echo "max  ". $max. "  ====   ".print_r($allMaxHighprice);//die;
			if($max < max($allMaxHighprice)){ 
				//return	$rsi[0];
			}else{
				$date1 =strtotime($current_date); $date2= strtotime($max_value_date);
				$daysDifference = ($date1 - $date2) / (60 * 60 * 24);
				if($daysDifference> 4){
					$diffInPrice=$current_high-$max;
					$percentDiffInPrice=(($diffInPrice*100)/$max);
					if( (($max_rsi-$current_rsi)>1) /*and ($percentDiffInPrice>1)*/){
						return "<table border=1 width=50%><tr><td colspan='3' align='middle'>Bear</td></tr><tr><td>Maximun RSI ".$max_rsi."</td><td>High in Row ".$max."</td><td> Max Date ".date('d-M-Y',strtotime($max_value_date))."</td></tr><tr><td>Current RSI ".$current_rsi."</td><td>Current High ".$current_high."</td><td>Today Date  ".date('d-M-Y',strtotime($current_date))."</td></tr></table>";
					}
				}
			}
		}else{
			//return $rsi[0];
		}
	}
	function divergence1($stk_name,$days,$period=14){		
		//echo $stk_name."  ".$period."  ".$days;die;
		$conn = new Database();
		//$sql="select * from tbl_daily_bhav_data4 ";//where id<178";//178-214order by stk_date desc limit 10";
		$sql="select id,stk_high_price,stk_low_price,stk_close_price,stk_date from tbl_daily_bhav_data where stk_symbol='".$stk_name."' order by stk_date asc"; //die;working;
		$query= $conn->query($sql);
		$result1 = $conn->resultset($query);
		//echo '<pre>';print_r($result1);//die;
		$last120days=strtotime("-158 days");
		$from_date=date("Y-m-d",$last120days);		
		$array_close= null;$rsi= null;$rsi_end=null;$array_low_price=null;$array_high_price=null;
		$array_date=null;$array_key=null;
		foreach($result1 as $key1=>$val1){
			$array_close[]=$val1['stk_close_price'];
			$array_low_price[]=$val1['stk_low_price'];
			$array_high_price[]=$val1['stk_high_price'];
			$array_date[]=$val1['stk_date'];
			$array_key[]=$key1;
		}
		$rsi=trader_rsi($array_close,$period);
		//echo '<pre>';print_r($rsi);//die;
		$rsi=array_reverse($rsi);
		$array_date=array_reverse($array_date);
		$array_low_price=array_reverse($array_low_price);
		$array_high_price=array_reverse($array_high_price);
		$array_close_price=array_reverse($array_close);
		$array_key=array_reverse($array_key);
		$ma_status=array("stk_date"=>$array_date,
						"array_key"=>$array_key,"array_low_price"=>$array_low_price,"array_high_price"=>$array_high_price,"ma_periods"=>$period,"rsi"=>$rsi);
		//return $ma_status;
		//echo '<pre>';print_r($ma_status);die;
		$max=null;$max_value_date=null;$min_value=null;$min_value_date=null;
		$min_rsi=$rsi[0];$max_rsi=$rsi[0];//die;
		$allMaxHighprice=null;$allMinLowPrice=null;
		for($i=0;$i<$days;$i++){
			//echo $i;
			if($max_rsi<$rsi[$i]){
				$max=$array_high_price[$i];
				$max_value_date=$array_date[$i];
				$max_rsi=$rsi[$i];
				$allMaxHighprice[]=$array_close_price[$i];
				
			}
			if($min_rsi>$rsi[$i]){
				//echo $min_value." ".$min_value_date;echo '<br>';
				$min_value=$array_low_price[$i];
				$min_value_date=$array_date[$i];
				$min_rsi=$rsi[$i];
				$allMinLowPrice[]=$array_close_price[$i];
			}
		}
		$current_rsi=$rsi[0];
		$current_low=$array_low_price[0];
		$current_high=$array_high_price[0];
		$current_date=$array_date[0];
		/*echo "<br><b>Test For Bearish Diversion</b></br>";
		echo "Current High==>".$current_high." Date==>".$current_date." RSI===>".$current_rsi;echo '<br>';
		echo "High in Row==>".$max." Max Date==>".$max_value_date."Max RSI===>".$max_rsi;echo '<br>';
		echo "<br><b>Test For Bullish Diversion</b></br>";
		echo "Current Low==>".$current_low." Date==>".$current_date." RSI===>".$current_rsi;echo '<br>';
		echo "Lowest in Row==>".$min_value." Min Date==>".$min_value_date." RSI===>".$min_rsi;echo '<br>';*/
		if(($current_rsi>$min_rsi )  and ($current_low<$min_value)){
			//echo "current_low  ". $current_low. "  ====   ".min($allMinLowPrice);echo "<br>";//die;
			//print_r($allMinLowPrice);echo "<br>";
			if($current_low > min($allMinLowPrice)){
				return $rsi[0];
			}else{
				return "bull<br>Minimun RSI ===> ".$min_rsi."&nbsp;&nbsp; Lowest in Row ==> ".$min_value." Min Date ==> ".date('d-M-Y',strtotime($min_value_date))."<br>Current RSI ===> ".$current_rsi."  Current Low&nbsp;&nbsp;&nbsp;&nbsp;==> ".$current_low." Today Date ==> ".date('d-M-Y',strtotime($current_date)) ;
			}
		}
		if(($current_rsi<$max_rsi )  and ($current_high>$max)){
			//echo "max  ". $max. "  ====   ".print_r($allMaxHighprice);//die;
			if($max < max($allMaxHighprice)){ 
				return	$rsi[0];
			}else{
				return "bear<br>Maximun RSI &nbsp;===> ".$max_rsi." High in Row ==> ".$max ."&nbsp;&nbsp; &nbsp;Max Date ==>&nbsp;&nbsp; ".date('d-M-Y',strtotime($max_value_date))."<br>
				Current RSI&nbsp;&nbsp;&nbsp;&nbsp; ===> ".$current_rsi." Current High ==> ".$current_high." &nbsp;&nbsp;Today Date ==>&nbsp;&nbsp;".date('d-M-Y',strtotime($current_date));
			}
		}else{
			return $rsi[0];
		}
	}
	function trader_cdlmarubozu(){
		$conn = new Database();
		$sql="select stk_symbol from tbl_isin_code  order by id limit 0,500";
		$query= $conn->query($sql);
		$result = $conn->resultset($query);
		$last120days=strtotime("-158 days");
		$from_date=date("Y-m-d",$last120days);
		foreach($result as $key=>$val){
			$sql = "select * from tbl_daily_bhav_data where stk_symbol='".$val['stk_symbol']."' and stk_date>='".$from_date."'";
			$query= $conn->query($sql);
			$result = $conn->resultset($query);
			$array_open=(array) null;$array_high=(array) null;$array_low=(array) null;$array_close=(array) null;
				foreach($result as $key =>$val){
					//echo $a." ".date('d-M-Y',strtotime($val['stk_date']))."==".$val['stk_close_price'];
					$array_open[]=$val['stk_open_price'];
					$array_high[]=$val['stk_high_price'];
					$array_low[]=$val['stk_low_price'];
					$array_close[]=$val['stk_close_price'];
					$array_date=$val['stk_date'];
					$a++;
				}
			$sma=trader_cdlmarubozu($array_open,$array_high,$array_low,$array_close);
			if((end($sma)==100) or (end($sma)==-100)) {
				$ma_status[]=array("stk_symbol"=>$val['stk_symbol'],"stk_date"=>$val['stk_date']);
			}						
		}
		return $ma_status;
	}
	function trader_cdlclosingmarubozu(){
		$conn = new Database();
		$sql="select stk_symbol from tbl_isin_code  order by id limit 0,500";
		$query= $conn->query($sql);
		$result = $conn->resultset($query);
		$last120days=strtotime("-158 days");
		$from_date=date("Y-m-d",$last120days);
		foreach($result as $key=>$val){
			$sql = "select * from tbl_daily_bhav_data where stk_symbol='".$val['stk_symbol']."' and stk_date>='".$from_date."'";
			$query= $conn->query($sql);
			$result = $conn->resultset($query);
			$array_open=(array) null;$array_high=(array) null;$array_low=(array) null;$array_close=(array) null;
				foreach($result as $key =>$val){
					//echo $a." ".date('d-M-Y',strtotime($val['stk_date']))."==".$val['stk_close_price'];
					$array_open[]=$val['stk_open_price'];
					$array_high[]=$val['stk_high_price'];
					$array_low[]=$val['stk_low_price'];
					$array_close[]=$val['stk_close_price'];
					$array_date=$val['stk_date'];
					$a++;
				}
			$sma=trader_cdlclosingmarubozu($array_open,$array_high,$array_low,$array_close);
			if((end($sma)==100) or (end($sma)==-100)) {
				$ma_status[]=array("stk_symbol"=>$val['stk_symbol'],"stk_date"=>$val['stk_date']);
			}						
		}
		return $ma_status;
	}
	function moving_average_calculation($days=50, $matype=1){
		//echo $days."  ".$matype;die;
		$conn = new Database();
		$sql="select id from tbl_isin_code order by id limit 0,500";
		$query= $conn->query($sql);
		$result = $conn->resultset($query);
		foreach($result as $key=>$val){
			$sql="select id,stk_symbol,stk_date,stk_close_price,stk_high_price,stk_low_price, stk_isin_no from tbl_daily_bhav_data where stk_isin_no='".$val['id']."' order by stk_date";
			$query= $conn->query($sql);
			$result = $conn->resultset($query);
			//echo '<pre>';
			$a=1;
			$array_sma=(array) null;
			$sma_days=$days;
			if($matype==1){
				$TRADER_MA_TYPE=TRADER_MA_TYPE_SMA;
			}elseif($matype==2){
				$TRADER_MA_TYPE=TRADER_MA_TYPE_EMA;
			}elseif($matype==3){
				$TRADER_MA_TYPE=TRADER_MA_TYPE_WMA;
			}elseif($matype==4){
				$TRADER_MA_TYPE=TRADER_MA_TYPE_DEMA;
			}elseif($matype==5){
				$TRADER_MA_TYPE=TRADER_MA_TYPE_TEMA;
			}
			foreach($result as $key =>$val){
				//echo $a."  ".date('d-M-Y',strtotime($val['stk_date']))."==".$val['stk_symbol']."===".$val['stk_close_price'];echo "<br/>";
				$array_sma[]=$val['stk_close_price'];
				$a++;
			}
			//print_r($array_sma);
			$sma=(array) null;
			//echo $array_sma."==".$sma_days."====".$TRADER_MA_TYPE;
			$sma=trader_ma($array_sma,$sma_days,$TRADER_MA_TYPE);
			//print_r($sma);
// for(;$sma_days>0;$sma_days--){
	// array_unshift($sma,"0");
// }
			//echo '<br/>';print_r($sma);
//$row=1;$simple_moving_avg="";$above="";
			$res_end=end($result);
			$sma_end=end($sma);
			//end($sma);end($result);
			$res_second_last=prev($result);
			$sma_second_last=prev($sma);
			//print_r($res_second_last);print_r($sma_second_last);//die;
			$ma_status[]=array("stk_isin_no"=>$res_end['stk_isin_no'],
								"stk_symbol"=>$res_end['stk_symbol'],
								"stk_date"=>$res_end['stk_date'],
								"stk_high_price"=>$res_end['stk_high_price'],
								"stk_low_price"=>$res_end['stk_low_price'],
								"stk_close_price"=>$res_end['stk_close_price'],
								"stk_sma"=>$sma_end,
								"stk_pre_date"=>$res_second_last['stk_date'],
								"stk_pre_high_price"=>$res_second_last['stk_high_price'],
								"stk_pre_low_price"=>$res_second_last['stk_low_price'],
								"stk_pre_close_price"=>$res_second_last['stk_close_price'],
								"stk_pre_sma"=>$sma_second_last,
								"ma_days"=>$days,
								"ma_type"=>$matype);
		}
		return $ma_status;
	}
	function macd(){
		$conn = new Database();
		$sql="select id from tbl_isin_code order by id limit 0,50";
		$query= $conn->query($sql);
		$result = $conn->resultset($query);
		foreach($result as $key=>$val){
			$sql="select stk_isin_no,id,stk_symbol,stk_date,stk_close_price from tbl_daily_bhav_data where stk_isin_no='".$val['id']."' order by stk_date";
			$query= $conn->query($sql);
			$result_bhav = $conn->resultset($query);
			$array_sma=(array) null;			
			foreach($result_bhav as $key =>$val){
				$array_sma[]=$val['stk_close_price'];
			}
			//print_r($array_sma);die;
			$sma=(array) null;
			$sma=trader_macd($array_sma,12,26,9);
			//echo '<br/>';
			//print_r($sma);die;
			//echo '<br/>';
			$res_end=array_slice($result_bhav,-10);
			$last_seven_rec1 = array_slice($sma[0], -10);
			$last_seven_rec2 = array_slice($sma[1], -10);
			$last_seven_rec3 = array_slice($sma[2], -10);
			/*print_r($res_end);
			print_r($last_seven_rec1);
			print_r($last_seven_rec2);
			print_r($last_seven_rec3);die;*/
			$ma_status[]=array("macd_values"=>$last_seven_rec1,
								"signal_values"=>$last_seven_rec2,
								"divergence_values"=>$last_seven_rec3,
								"stk_result"=>$res_end);
		}
		return $ma_status;
	}
	function watchlist(){
		$conn = new Database();
		$user_id=$this->get_user_details($_SESSION['user_email']);
		$userid=$user_id[0]['ID'];
		$sql="select a.id, a.stk_added_date, a.stk_traded_date, a.stk_symbol, a.comment, b.stk_symbol from tbl_watchlist a left join tbl_isin_code b  on b.stk_symbol=a.stk_symbol where a.user_id=:user_id order by a.stk_added_date desc";
		$query= $conn->query($sql);
		$conn->bind(':user_id', $userid, PDO::PARAM_STR);		
		$result = $conn->resultset($query);
		return $result;
	}
	function get_close_price_by_date($date,$stk_symbol){
		$conn = new Database();
		$sql="select stk_close_price from tbl_daily_bhav_data where stk_date=:stk_date and stk_symbol=:stk_symbol";
		$query= $conn->query($sql);
		$conn->bind(':stk_date', $date, PDO::PARAM_STR);		
		$conn->bind(':stk_symbol', $stk_symbol, PDO::PARAM_STR);		
		$result = $conn->resultset($query);
		return $result;
	}
	function  add_watchlist(){
		//echo $_POST['stk_isin'];die;
		$conn = new Database();
		$user_id=$this->get_user_details($_SESSION['user_email']);
		$userid=$user_id[0]['ID'];
		$query= "select * from tbl_watchlist where stk_symbol= :stk_symbol and stk_traded_date=:stk_traded_date and user_id=:user_id";
		$query= $conn->query($query);
		$conn->bind(':stk_symbol', $_POST['stk_isin'], PDO::PARAM_STR);
		$conn->bind(':stk_traded_date', date('Y-m-d', strtotime(trim($_POST['stk_traded_date']))), PDO::PARAM_STR);
		$conn->bind(':user_id', $userid, PDO::PARAM_STR);
		$result= $conn->resultset($query);
		//print_r($result);die;
		if($conn->rowCount()>0){
			echo 1;
		}else{
			//echo '<pre>';print_r($_POST);
			$sql = "INSERT INTO tbl_watchlist (user_id,stk_symbol,stk_added_date,stk_traded_date,comment) VALUES (:user_id,:stk_symbol,:stk_added_date,:stk_traded_date,:comment)";
			$stmt = $conn->query($sql);
			$conn->bind(':user_id', $userid, PDO::PARAM_INT);		
			$conn->bind(':stk_symbol',$_POST['stk_isin'], PDO::PARAM_STR);		
			$conn->bind(':stk_added_date', date('Y-m-d'), PDO::PARAM_STR);		
			$conn->bind(':stk_traded_date',date('Y-m-d', strtotime(trim($_POST['stk_traded_date']))), PDO::PARAM_STR);		
			$conn->bind(':comment', $_POST['stock_notes'], PDO::PARAM_STR);		
			$conn->execute();
			if($conn->lastInsertId()){
				echo 2;
			}else{
				echo 3;
			}		
		}
	}
	function  delete_watchlist(){
		$conn = new Database();
		$user_id=$this->get_user_details($_SESSION['user_email']);
		$userid=$user_id[0]['ID'];
		$query= "delete from tbl_watchlist where id= :id and user_id=:user_id ";
		$query= $conn->query($query);
		$conn->bind(':id', $_POST['watchlist_id'], PDO::PARAM_INT);
		$conn->bind(':user_id', $userid, PDO::PARAM_INT);
		$conn->execute();
		if($conn->rowCount()>0){
			return true;
		}else{
			return false;
		}
	}
	function  delete_note(){
		$conn = new Database();
		$user_id=$this->get_user_details($_SESSION['user_email']);
		$userid=$user_id[0]['ID'];
		$query= "delete from tbl_note where id= :id and user_id=:user_id ";
		$query= $conn->query($query);
		$conn->bind(':id', $_POST['note_id'], PDO::PARAM_INT);
		$conn->bind(':user_id', $userid, PDO::PARAM_INT);
		$conn->execute();
		if($conn->rowCount()>0){
			return true;
		}else{
			return false;
		}
	}
	function add_note(){
		$conn = new Database();
		$user_id=$this->get_user_details($_SESSION['user_email']);
		$userid=$user_id[0]['ID'];
		$sql = "INSERT INTO tbl_note (user_id,added_date,note) VALUES (:user_id,:added_date,:note)";
		$stmt = $conn->query($sql);
		$conn->bind(':user_id', $userid, PDO::PARAM_INT);		
		$conn->bind(':added_date',date('Y-m-d H:i'), PDO::PARAM_STR);		
		$conn->bind(':note', $_POST['note'], PDO::PARAM_STR);
		$conn->execute();
		if($conn->lastInsertId()){
			return $conn->lastInsertId();
		}else{
			return false;
		}
	}function add_android_stockDetails(){
		$conn = new Database();
		$user_id=$this->get_user_details($_SESSION['user_email']);
		$userid=$user_id[0]['ID'];
		$sql = "INSERT INTO  tbl_android_stockDetails (stockSymbol,stockName,stockDesc,stockExdate,stockRecordDate,stockPurpose,	stockEntryType,stockStatus,stockEntryDate) VALUES (:stockSymbol,:stockName,:stockDesc,:stockExdate,:stockRecordDate,:stockPurpose,:stockEntryType,:stockStatus,:stockEntryDate)";
		$stmt = $conn->query($sql);
		//$conn->bind(':user_id', $userid, PDO::PARAM_INT);		
		$conn->bind(':stockSymbol',$_POST['stockSymbol'], PDO::PARAM_STR);		
		$conn->bind(':stockName',$_POST['stockName'], PDO::PARAM_STR);		
		$conn->bind(':stockDesc',$_POST['stockName'], PDO::PARAM_STR);		
		$conn->bind(':stockExdate',$_POST['stockExdate'], PDO::PARAM_STR);		
		$conn->bind(':stockRecordDate',$_POST['stockRecordDate'], PDO::PARAM_STR);		
		$conn->bind(':stockPurpose',$_POST['stockPurpose'], PDO::PARAM_STR);
		$conn->bind(':stockEntryType',$_POST['stockEntryType'], PDO::PARAM_INT);		
		$conn->bind(':stockStatus',$_POST['stockStatus'], PDO::PARAM_INT);		
		$conn->bind(':stockEntryDate',date('Y-m-d'), PDO::PARAM_STR);
		$conn->execute();
		if($conn->lastInsertId()){
			return $conn->lastInsertId();
		}else{
			return false;
		}
	}
	function notes(){
		$conn = new Database();
		$user_id=$this->get_user_details($_SESSION['user_email']);
		$userid=$user_id[0]['ID'];
		$sql="select * from tbl_note where user_id=:user_id order by added_date desc";
		$query= $conn->query($sql);
		$conn->bind(':user_id', $userid, PDO::PARAM_STR);		
		$result = $conn->resultset($query);
		return $result;
	}
	function page_details($page_url){
		$conn = new Database();
		$sql="select * from tbl_page_detail";
		$query= $conn->query($sql);	
		return $result= $conn->resultset($query);
	}
	function meta_data($page_url){
		$conn = new Database();
		$sql="select * from tbl_page_detail where page_url=:page_url";
		$query= $conn->query($sql);
		$conn->bind(':page_url', $page_url, PDO::PARAM_STR);	
		$result= $conn->resultset($query);
		if(count($result)>0){
			$title=$result[0]["title"];$desc=$result[0]["description"];
		}else{
			$title="Cflick ";$desc="Nifty Data";
		}
		$html="";
		$html.="<title>".$title." | cflick.com</title>\n";
		$html.='<meta name="description" content="'.$desc.'" />';
		$html.="\n";
		return $html;
	}
	function update_meta_data($setdata,$page_url){
		$conn = new Database();
		$sql = "UPDATE tbl_page_detail SET $setdata WHERE page_url = $page_url";
		$stmt = $conn->query($sql);		
		//$conn->bind(':page_url', $page_url, PDO::PARAM_STR);		
		$result=$conn->execute(); 
		if($result){
			return  true;
		}else{
			return false;
		}
	}
	function update_android_data($setdata,$stockid){
		$conn = new Database();
		$sql = "UPDATE tbl_android_stockDetails SET $setdata WHERE stockId = $stockid";
		$stmt = $conn->query($sql);		
		//$conn->bind(':page_url', $page_url, PDO::PARAM_STR);		
		$result=$conn->execute(); 
		if($result){
			return  true;
		}else{
			return false;
		}
	}
	function company_info($symbol){
		$conn = new Database();
		$sql="select * from tbl_isin_code where stk_symbol=:stk_symbol";
		$query= $conn->query($sql);
		$conn->bind(':stk_symbol', $symbol, PDO::PARAM_STR);	
		return $result= $conn->resultset($query);
	}
	function fetch_scanned_candlestick_pattern(){
		$conn = new Database();
		$sql="select * from tbl_candle_pattern order by stk_date desc";
		$query= $conn->query($sql);	
		return $result= $conn->resultset($query);
	}
	
	function double_top($stk_name,$days){		
		//echo $stk_name."   .$days;die;
		$conn = new Database();
		$percentDiff=0.5;
		$sql="select id,stk_high_price,stk_close_price,stk_date from tbl_daily_bhav_data where stk_symbol='".$stk_name."' order by stk_date desc limit ".$days.""; //die;working;
		$query= $conn->query($sql);
		$result = $conn->resultset($query);
		$highestPrice=0;$todayHigh=0;
		$todayHigh= $result[0]['stk_high_price']; //echo '<br>';
		$todayDate=$result[0]['stk_date'];//echo '<br>';
		foreach($result as $key => $value){
			if($value['stk_high_price'] > $highestPrice){
				$highestPrice=$value['stk_high_price'];
				$highDate=$value['stk_date'];
				$highClose=$value['stk_close_price'];
			}
		}
		//echo "highestPrice=>".$highestPrice." === highDate=>".$highDate;
		if($todayHigh<$highestPrice){
			$priceDiff=$highestPrice-$todayHigh;
			$percentInPriceDiff=($priceDiff*100)/$highestPrice;
			if($percentInPriceDiff<$percentDiff){
				return "highestPrice=>".$highestPrice." === highDate=>".date('d-M-Y',strtotime($highDate))." todayHigh ".$todayHigh."Near Breakout";
			}
		}
	}
	function double_bottom($stk_name,$days){		
		//echo $stk_name."   .$days;die;
		$conn = new Database();
		$percentDiff=0.5;
		$sql="select id,stk_low_price,stk_close_price,stk_date from tbl_daily_bhav_data where stk_symbol='".$stk_name."' order by stk_date desc limit ".$days."";
		$query= $conn->query($sql);
		$result = $conn->resultset($query);
		$todayLow=$result[0]['stk_low_price']; ;
		$lowestPrice= $result[0]['stk_low_price']; //echo '<br>';
		$todayDate=$result[0]['stk_date'];//echo '<br>';
		foreach($result as $key => $value){
			if($lowestPrice >= $value['stk_low_price']){
				$lowestPrice=$value['stk_low_price'];
				$lowDate=$value['stk_date'];
				$lowClose=$value['stk_close_price'];
			}
		}
		//echo "lowestPrice=>".$lowestPrice." === lowDate=>".$lowDate."=== lowestPrice=>".$todayLow;echo '<br>';
		if($todayLow>$lowestPrice){
			$priceDiff=$todayLow-$lowestPrice;
			$percentInPriceDiff=($priceDiff*100)/$lowestPrice;//echo "<br>";
			if($percentInPriceDiff<$percentDiff){
				//echo  "hello=lowestPrice=>".$lowestPrice." === LowDate=>".$lowDate." todayLow ".$todayLow."Near Support";echo "<br>";
				return "lowestPrice=>".$lowestPrice." === LowDate=>".date('d-M-Y',strtotime($lowDate))." todayLow ".$todayLow."Near Support";
			}
		}
	}

	function trading_date($date){
		$date=date("Y-m-d",strtotime($date));
		$conn = new Database();
		$query= "select * from tbl_trading_date where stk_trading_date= :stk_trading_date";
		$query= $conn->query($query);
		$conn->bind(':stk_trading_date', $date, PDO::PARAM_STR);
		$result= $conn->resultset($query);
		//echo '<pre>';print_r($result);die;
		if($conn->rowCount() >0) {
			return true;
		}else{
			$sql = "INSERT INTO tbl_trading_date (stk_trading_date) VALUES (:stk_trading_date)";
			$stmt = $conn->query($sql);
			$conn->bind(':stk_trading_date', date('Y-m-d', strtotime(trim($date))), PDO::PARAM_STR);
			$conn->execute();
		}
	}
	
	function ohlcData($data){
		//echo '<pre>';print_r($niftyStatus=$data['niftyStatus']);die;
		$niftyStatus=$data['niftyStatus'];
		$duration=$data['getDataFromDate'];
		$conn = new Database();
		$ma_status=array();
		$sql="select stk_symbol from tbl_isin_code  where stk_nifty_status='".$niftyStatus."' limit 10,10";		
		$query= $conn->query($sql);
		$result = $conn->resultset($query);//echo '<pre>';print_r($result);die;
		$last120days=strtotime("-$duration days");
		$from_date=date("Y-m-d",$last120days);$result1=(array) null;
		foreach($result as $key=>$val){
			$sql="select id,stk_symbol,stk_date,stk_open_price,stk_high_price,stk_low_price, stk_close_price   from tbl_daily_bhav_data where stk_symbol='".$val['stk_symbol']."' and stk_date>='".$from_date."' order by stk_date asc"; 
			$query= $conn->query($sql);
			$result1[] = $conn->resultset($query);
			//print_r($result1);
			
		}
		/*	$array_close=(array) null;$array_date=(array) null;$array_open=(array) null;
			$array_high=(array) null;$array_low=(array) null;
			foreach($result1 as $key1=>$val1){
				$stk_symbol=$val1['stk_symbol'];
				$array_date[]=date("Y-m-d",strtotime($val1['stk_date']));
				$array_open[]=$val1['stk_open_price'];
				$array_high[]=$val1['stk_high_price'];
				$array_low[]=$val1['stk_low_price'];
				$array_close[]=$val1['stk_close_price'];
				
			}
			$ma_status[]=array(	"stk_symbol"=>$stk_symbol,"stk_date"=>$array_date,"stk_open"=>$array_open,
			"array_high"=>$array_high,"stk_low"=>$array_low,"array_close"=>$array_close);
		}*/
		$ma_status=json_encode($result1,true);
		//print_r( $ma_status);
		return $ma_status;
	}
}
$functions= new functions();

?>