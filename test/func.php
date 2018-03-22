<?php
	
function func5($table){
	include 'config.php';
	$table3 = "users";
	$sql = "SELECT * FROM $table";
	$result = $mysqli->query($sql);
		
	$result_array = array();
		while($row = $result->fetch_assoc()){
			$result_array[] = $row;
		}
	echo json_encode($result_array);
	}
	
		
function func8($name, $email,$username,$password, $isDisable,$status){
	include 'config.php';
	$sql = "INSERT INTO `user` (`UserID`, `PermitID`, `UserTypeID`, `Name`, `Email`, `Username`, `Password`, `isDisabled`, `Status`) 
	VALUES (NULL, NULL, NULL, '$name', '$email', '$username', '$password', '$isDisable', '$status')";
	if ($mysqli->query($sql)==true){
		echo "true";
	}
	else{
	echo "false";
	}
}
		
function func9($id){
	include 'config.php';
	$sql = "SELECT * FROM user WHERE UserID = $id";
	$result = $mysqli->query($sql);
		
	$result_array = array();
		while($row = $result->fetch_assoc()){
			$result_array[] = $row;
		}
		echo json_encode($result_array);
}
		
		
function func10($id,$newName){
	include 'config.php';
	$sql = "UPDATE webservice.user SET Name = '$newName' WHERE UserID = $id";
		if ($mysqli->query($sql)==true){
		$sql = "SELECT * FROM user WHERE UserID = $id";
		$result = $mysqli->query($sql);
		
		$result_array = array();
		while($row = $result->fetch_assoc()){
		$result_array[] = $row;
	}
	echo json_encode($result_array);
	}
	else{
	echo "false";
	}
}
	
function func11($id){
	include 'config.php';
	$sql = "DELETE FROM user WHERE UserID = $id";
	if ($mysqli->query($sql)==true){
		echo "true";
	}
	else{
	echo "false";
	}
}
?>