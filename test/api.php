<?php
include 'config.php';
include 'func.php';

if ($_SERVER['REQUEST_METHOD'] == "GET"){
	
	if(isset($_GET['table']) && isset($_GET['id'])){
		if($_GET['table'] == "user"){
			$table = $_GET['table'];
			$id = $_GET['id'];
			echo func9($id);
			http_response_code(200);
		}
		
	}
	else if(isset($_GET['table'])){
		if($_GET['table']){
			
			$table = $_GET['table'];
			echo func5($table);
			http_response_code(200);
		}
	}

}
else if ($_SERVER['REQUEST_METHOD'] == "POST"){
	if(isset($_GET['table'])){
		if($_GET['table'] == "user"){
			$postBody = file_get_contents("php://input");
			$postBody = json_decode($postBody);
			
			$name = "name";
			$email = "email";
			$username = "username";
			$password = "password";
			$isDisable = "isDisable";
			$status = "status";
			
			$name = $postBody->$name;
			$email = $postBody->$email;
			$username = $postBody->$username;
			$password = $postBody->$password;
			$isDisable = $postBody->$isDisable;
			$status = $postBody->$status;
			
			echo func8($name, $email, $username, $password, $isDisable, $status);
			http_response_code(200);
		}
	} 
}
else if ($_SERVER['REQUEST_METHOD'] == "PUT"){
	if(isset($_GET['table']) && isset($_GET['id']) && isset($_GET['newName'])){
		if($_GET['table'] == "user"){
			$table = $_GET['table'];
			$id = $_GET['id'];
			$newName = $_GET['newName'];
			echo func10($id,$newName);
			http_response_code(200);
		}
		
	}
}
else if ($_SERVER['REQUEST_METHOD'] == "DELETE"){
	if(isset($_GET['table']) && isset($_GET['id'])){
		if($_GET['table'] == "user"){
			$id = $_GET['id'];
			echo func11($id);
			http_response_code(200);
		}
		
	}
}
else {
        http_response_code(405);
}
?>