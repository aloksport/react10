<?php
	include("inc/functions.php");
	$functions->logout();
	//print_r($_POST);die;
	$action=$_POST['action'];
	if($action=="update_notes"){
		if($functions->update_stock_notes()){
			echo "success";
		}else{
			echo "Failed";
		}
	}else if($action=="sell_stock"){
		if($functions->sell_stock_row()){
			echo "success";
		}else{
			echo "Failed";
		}
	}else if($action=="add_watchlist"){
		$functions->add_watchlist();
	}else if($action=="insertNewData"){
		$functions->insertNewDatatoBhavData();
	}else if($action=="delete_watchlist"){
		if($functions->delete_watchlist()){
			echo "success";
		}else{
			echo "Failed";
		}		
	}else if($action=="add_note"){
		if(empty($_POST['note'])){
			echo "empty";
		}else{
			$r=$functions->add_note();
			if($r>0){
				echo $r;
			}else{
				echo "Failed";
			}
		}		
	}else if($action=="add_android_stockDetails"){
		if($functions->add_android_stockDetails()){
			echo 1;
		}else{
			echo 0;
		}	
	}else if($action=="delete_note"){
		if($functions->delete_note()){
			echo "success";
		}else{
			echo "Failed";
		}		
	}else if($action=="update_meta_data"){
		$key=key($_POST);
		$val=$_POST[$key];
		$setdata="$key"."='".$val."'";
		$pageurl="'".$_POST['page_url']."'";
		if($functions->update_meta_data($setdata,$pageurl)){
			echo "success";
		}else{
			echo "Failed";
		}		
	}else if($action=="update_android_data"){
		$key=key($_POST);
		$val=$_POST[$key];
		$setdata="$key"."='".$val."'";
		$pageurl="'".$_POST['page_url']."'";
		if($functions->update_android_data($setdata,$pageurl)){
			echo "success";
		}else{
			echo "Failed";
		}		
	}else if($action=="Add Recommendation"){
		if($functions->add_recommendation()){		
			echo "success";	
		}else{
			echo "failed";
		}		
	}
?>