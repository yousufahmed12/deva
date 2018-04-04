<?php
include 'config.php';
include 'func.php';

if ($_SERVER['REQUEST_METHOD'] == "GET"){
	
	if(isset($_GET['table']) && isset($_GET['id'])){
		/***
		*This will get all the a specfic user info
		*
		*The url will be (table = user& id = 'the users id')
		*
		*Input is the user table and the user id
		*Output is a json with the user information
		***/
		if($_GET['table'] == "user"){
			$table = $_GET['table'];
			$id = $_GET['id'];
			echo getUser($id);
			http_response_code(200);
		}
		
	}
	else if(isset($_GET['table'])){
		/***
		*This will get all the data inside the table that is chosen
		*
		*The url will be (table = 'name of the table')
		*
		*Input is the name of the table
		*Output is a json of all the data inside the table
		***/
		if($_GET['table']){
			
			$table = $_GET['table'];
			echo getTable($table);
			http_response_code(200);
		}
	}
	else if(isset($_GET['starttime']) && isset($_GET['endtime']) && isset($_GET['type'])){
		/***
		*This will get all the lots that a user is allowed to reserve
		*
		*The url will be (starttime ='epoch input'&endtime='epoch input'&type='type of user)
		*
		*Input two epochs for the starttime and endtime and the user type
		*Output is a json with the list of parking lot that the user can reserve
		***/
		$starttime = $_GET['starttime'];
		$endtime = $_GET['endtime'];
		$type = $_GET['type'];
		
		$StartTime = date(" H:i:s", substr($starttime, 0, 10));
		$EndTime = date(" H:i:s", substr($endtime, 0, 10));
		$Date = date("Y-m-d", substr($starttime, 0, 10));
		
		echo getAvailableWithType($StartTime,$EndTime,$Date,$type);
		http_response_code(200);
	}
	else if(isset($_GET['starttime']) && isset($_GET['endtime'])){
		/***
		*This will get the lots and their availability
		*
		*The url will be (starttime ='epoch input'&endtime='epoch input')
		*
		*Input two epochs for the starttime and endtime
		*Output is a json with the list of parking lot with the available spots
		***/
		$starttime = $_GET['starttime'];
		$endtime = $_GET['endtime'];
		
		$StartTime = date(" H:i:s", substr($starttime, 0, 10));
		$EndTime = date(" H:i:s", substr($endtime, 0, 10));
		$Date = date("Y-m-d", substr($starttime, 0, 10));
		
		echo getAvailable($StartTime,$EndTime,$Date);
		http_response_code(200);
	}
		
	

}
else if ($_SERVER['REQUEST_METHOD'] == "POST"){
	if(isset($_GET['table'])){
		/***
		*This will get the add a new user to the database
		*
		*The url will be (table = user)
		*
		*Input will be a post body with the user info
		*Output will be a boolean of true or false
		***/
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
			
			echo postUser($name, $email, $username, $password, $isDisable, $status);
			http_response_code(200);
		}
	} 
}
else if ($_SERVER['REQUEST_METHOD'] == "PUT"){
	if(isset($_GET['table']) && isset($_GET['id']) && isset($_GET['newName'])){
		/***
		*This will update the name of a specfic user to a new name
		*
		*The url will be (table = user & id = 'specfic user id' & newName = 'the new name you want to add')
		*
		*Input will be the user id and the new name.
		*Output will be a boolean of true or false
		***/
		if($_GET['table'] == "user"){
			$table = $_GET['table'];
			$id = $_GET['id'];
			$newName = $_GET['newName'];
			echo putName($id,$newName);
			http_response_code(200);
		}
		
	}
}
else if ($_SERVER['REQUEST_METHOD'] == "DELETE"){
	if(isset($_GET['table']) && isset($_GET['id'])){
		/***
		*This will get the delete a user in the database
		*
		*The url will be (table = user & id = 'specfic user id')
		*
		*Input will the user id
		*Output will be a boolean of true or false
		***/
		if($_GET['table'] == "user"){
			$id = $_GET['id'];
			echo deleteUser($id);
			http_response_code(200);
		}
		
	}
}
else if($_SERVER['REQUEST_METHOD'] == "OPTIONS") {
        //added an options verb
        //this allows the server to complete its steps for CORS
        // previously 405 occured because it first looks for OPTIONS then compl$
        echo 'options here';
  }
else {
         http_response_code(405);
}


?>