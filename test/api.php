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
		
		
		$StartTime = date("H:i:s", substr($starttime, 0, 10));
		$EndTime = date("H:i:s", substr($endtime, 0, 10));
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
		/***
		*This will get the add a new complaint to the database
		*
		*The url will be (table = complaints)
		*
		*Input will be a post body with the complaint info
		*Output will be a boolean of true or false
		***/
		if($_GET['table'] == "complaints"){
			$postBody = file_get_contents("php://input");
			$postBody = json_decode($postBody);
			
			$id = "id";
			$report = "report";
			
			$id = $postBody->$id;
			$report = $postBody->$report;
			
			echo newComplaint($id, $report);
			http_response_code(200);
		}
		/***
		*This will get the add a new reservation to the database
		*
		*The url will be (table = reservation)
		*
		*Input will be a post body with the reservation info
		*Output will be a boolean of true or false
		***/
		if($_GET['table'] == "reservation"){
			$postBody = file_get_contents("php://input");
			$postBody = json_decode($postBody);
			
			$lotid = "LotID";
			$userid = "UserID";
			$starttime = "StartTime";
			$endtime = "EndTime";
			
			$lotid = $postBody->$lotid;
			$userid = $postBody->$userid;
			$starttime = $postBody->$starttime;
			$endtime = $postBody->$endtime;

			$StartTime = date(" H:i:s", substr($starttime, 0, 10));
			$EndTime = date(" H:i:s", substr($endtime, 0, 10));
			$Date = date("Y-m-d", substr($starttime, 0, 10));

			echo newReservation($lotid, $userid, $Date,$StartTime, $EndTime);
			http_response_code(200);
		}
		/***
		*This will get the add a new lot to the database
		*
		*The url will be (table = parkinglot)
		*
		*Input will be a post body with the lot info
		*Output will be a boolean of true or false
		***/
		if($_GET['table'] == "parkinglot"){
			$postBody = file_get_contents("php://input");
			$postBody = json_decode($postBody);
			
			$lotname= "LotName";
			$max = "MaxCapacity";
			$dspots = "DisabledSpots";
			$rspots = "ReservationSpots";
			$lstatus = "LotStatus";
			$reservable = "isReservable";
			
			$lotname = $postBody->$lotname;
			$max = $postBody->$max;
			$dspots = $postBody->$dspots;
			$rspots = $postBody->$rspots;
			$lstatus = $postBody->$lstatus;
			$reservable = $postBody->$reservable;

			echo newLot($lotname, $max, $dspots, $rspots, $lstatus, $reservable);
			http_response_code(200);
		}
		/***
		*This will get the add a new schedule to the database
		*
		*The url will be (table = schedule)
		*
		*Input will be a post body with the schedule info
		*Permit id: 1 Commuter, 2 Resident 3 Employee 
		*Output will be a boolean of true or false
		***/
		if($_GET['table'] == "schedule"){
			$postBody = file_get_contents("php://input");
			$postBody = json_decode($postBody);
			
			$lotid = "LotID";
			$pid = "PermitID";
			$starttime = "StartTime";
			$endtime = "EndTime";
			
			$lotid = $postBody->$lotid;
			$pid = $postBody->$pid;
			$starttime = $postBody->$starttime;
			$endtime = $postBody->$endtime;

			$StartTime = date(" H:i:s", substr($starttime, 0, 10));
			$EndTime = date(" H:i:s", substr($endtime, 0, 10));

			echo newSchedule($lotid, $pid, $StartTime, $EndTime);
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
	else if(isset($_GET['function'])&& isset($_GET['id']) && isset($_GET['m']) && isset($_GET['d']) && isset($_GET['r'])) {
		/***
		*This will update a lot  max capacity , number of disabled spots, and number of reservation spots
		*
		*The url will be (function = updateLot & id = 'specfic lot id' & m = 'new max capacicity' & d = 'new # of disabled spots' & r = 'new # of reservation spots' )
		*
		*Input will be the lot id,max capacity , number of disabled spots, and number of reservation spots
		*Output will be a boolean of true or false
		***/
		if($_GET['function'] == "updateLot"){
			$id = $_GET['id'];
			$max = $_GET['m'];
			$dspots = $_GET['d'];
			$rspots = $_GET['r'];
	
			echo updateLot($id, $max, $dspots, $rspots);
			http_response_code(200);
		}
	}
	else if(isset($_GET['function']) && isset($_GET['id']) && isset($_GET['lotid'])){
		/***
		*This will set a schedule to a specfic lot
		*
		*The url will be (function = changeLotSchedule & id = 'specfic schedule id' & lotid = 'the new lot you want to add the schedule to')
		*
		*Input will be the schedule id and the lot id.
		*Output will be a boolean of true or false
		***/
		if($_GET['function'] == "changeLotSchedule"){
			$lotid = $_GET['lotid'];
			$id = $_GET['id'];
			echo changeLotSchedule($lotid, $id);
			http_response_code(200);
		}
		
	}
	else if(isset($_GET['function'])&& isset($_GET['id'])){
		/***
		*This will update the user status to be 1 or in boolean true
		*
		*The url will be (function = unlockUser & id = 'specfic user id' )
		*
		*Input will be the user id
		*Output will be a boolean of true or false
		***/
		if($_GET['function'] == "unlockUser"){
			$id = $_GET['id'];
			echo unlockUser($id);
			http_response_code(200);
		}
		/***
		*This will update the user status to be 0 or in boolean false
		*
		*The url will be (function = lockUser & id = 'specfic user id' )
		*
		*Input will be the user id
		*Output will be a boolean of true or false
		***/
		else if($_GET['function'] == "lockUser"){
			$id = $_GET['id'];
			echo lockUser($id);
			http_response_code(200);
		}
		/***
		*This will update the parkinglot isReservable to be 1 or in boolean true
		*
		*The url will be (function = reserveLot & id = 'specfic parkinglot id' )
		*
		*Input will be the parkinglot id
		*Output will be a boolean of true or false
		***/
		else if($_GET['function'] == "reserveLot"){
			$id = $_GET['id'];
			echo reserveLot($id);
			http_response_code(200);
		}
		/***
		*This will update the parkinglot isReservable to be 0 or in boolean false
		*
		*The url will be (function = unreserveLot & id = 'specfic parkinglot id' )
		*
		*Input will be the parkinglot id
		*Output will be a boolean of true or false
		***/
		else if($_GET['function'] == "unreserveLot"){
			$id = $_GET['id'];
			echo unreserveLot($id);
			http_response_code(200);
		}
		/***
		*This will update the parkinglot LotStatus to be 1 or in boolean true
		*
		*The url will be (function = openLot & id = 'specfic parkinglot id' )
		*
		*Input will be the parkinglot id
		*Output will be a boolean of true or false
		***/
		else if($_GET['function'] == "openLot"){
			$id = $_GET['id'];
			echo openLot($id);
			http_response_code(200);
		}
		/***
		*This will update the parkinglot LotStatus to be 0 or in boolean false
		*
		*The url will be (function = closeLot & id = 'specfic parkinglot id' )
		*
		*Input will be the parkinglot id
		*Output will be a boolean of true or false
		***/
		else if($_GET['function'] == "closeLot"){
			$id = $_GET['id'];
			echo closeLot($id);
			http_response_code(200);
		}
		/***
		*This will update the ReservationStatus to be 0 or in boolean false
		*
		*The url will be (function = cancelReservation & id = 'specfic reservation id' )
		*
		*Input will be the Reservation id
		*Output will be a boolean of true or false
		***/
		else if($_GET['function'] == "cancelReservation"){
			$id = $_GET['id'];
			echo cancelReservation($id);
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
		/***
		*This will get the delete a complaint in the database
		*
		*The url will be (table = complaints & id = 'specfic complaint id')
		*
		*Input will the complaints id
		*Output will be a boolean of true or false
		***/
		if($_GET['table'] == "complaints"){
			
			$id = $_GET['id'];
			echo removeComplaint($id);
			http_response_code(200);
			
			
		} 
		/***
		*This will get the delete a lot in the database
		*
		*The url will be (table = parkinglot & id = 'specfic lot id')
		*
		*Input will the complaints id
		*Output will be a boolean of true or false
		***/
		if($_GET['table'] == "parkinglot"){
			
			$id = $_GET['id'];
			echo removeLot($id);
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
