<?php
/***
*This will get the table data
*
*Input is the table name
*Output will be the table data in json format
***/	
function getTable($table){
	include 'config.php';
	$table3 = "users";
	$sql = "SELECT * FROM $table";
	$result = $mysqli->query($sql);
		
	if($result->num_rows > 0){	
		$result_array = array();
		while($row = $result->fetch_assoc()){
			$result_array[] = $row;
		}
		echo json_encode($result_array);
	}
	else
	{
		 echo "Check your input or else it does not exist.";
		 		http_response_code(400);
	}
}	
/***
*This will add a user to the user table
*
*Input is the name, email,username,password,isDisable which is tinybit, status which is tinybit
*Output will be the numeber of successful query
***/	
function postUser($name, $email,$username,$password, $isDisable,$status){
	include 'config.php';
	$sql = "INSERT INTO `user` (`UserID`, `PermitID`, `UserTypeID`, `Name`, `Email`, `Username`, `Token`, `isDisabled`, `Status`) 
	VALUES (NULL, NULL, NULL, '$name', '$email', '$username', '$password', '$isDisable', '$status')";
	if ($mysqli->query($sql)==true){
		echo "Successfully added: " . mysqli_affected_rows($mysqli);
	}
	else{
		
	echo("Error description: " . mysqli_error($mysqli));
	http_response_code(400);
	}
}

/***
*This will get a spefic user from the user table
*
*Input is the userid
*Output will be the user info in a json format
***/	
function getUser($id){
	include 'config.php';
	$sql = "SELECT * FROM user WHERE UserID = $id";
	$result = $mysqli->query($sql);
		
	if($result->num_rows > 0){	
		$result_array = array();
		while($row = $result->fetch_assoc()){
			$result_array[] = $row;
		}
		echo json_encode($result_array);
	}
	else
	{
		echo "Check your input or else it does not exist.";
		http_response_code(400);
	}
}
		
/***
*This will update a spefic users name
*
*Input is the userid and the new name
*Output will be the updated user info in a json format
***/	
function putName($id,$newName){
	include 'config.php';
	$sql = "UPDATE webservice.user SET Name = '$newName' WHERE UserID = $id";
	if ($mysqli->query($sql)==true){
		$sql = "SELECT * FROM user WHERE UserID = $id";
		$result = $mysqli->query($sql);
		
		if($result->num_rows > 0){	
			$result_array = array();
			while($row = $result->fetch_assoc()){
				$result_array[] = $row;
			}
			echo json_encode($result_array);
		}
		else
		{
			   echo "Check your input or else it does not exist.";
			   http_response_code(400);
		}
	}
	else{
	echo("Error description: " . mysqli_error($mysqli));
	}
}

/***
*This will delete a spefic user from the user table
*
*Input is the userid
*Output will be the numeber of successful query
***/
function deleteUser($id){
	include 'config.php';
	$sql = "DELETE FROM user WHERE UserID = $id";
	if ($mysqli->query($sql)==true){
		echo "Successfully removed: " . mysqli_affected_rows($mysqli);
	}
	else{
	echo("Error description: " . mysqli_error($mysqli));
	http_response_code(400);
	}
}


/***
*This will find the number available spots for each lot at specfic time and date
*
*Input is the starttime, endtime and the date
*Output will be lotid, lotname, lotstatus, the total which is the number of avaliable spots in json 
*with multiple lists of each lot with those specific information in the json
*
*Furthor notes this function will not work if the starttime is lesser then the endtime
*Also time such as 1 pm is 13:00:00 while 2:00 am is 2:00:00
*
*The subquery clause for total has a combination of comparison to the time
*The Starttime and Endtime for the reservation I used were 8:00 to 12:00
*I needed four cases to test
*The starttime and endtime input is inbetween 8:00 to 12:00
*The starttime before 8:00 and and endtime inbetween 8:00 to 12:00
*The starttime inbetween 8:00 to 12:00 and and after 12:00
*The starttime after 8:00 and before 12:00
*
*
***/
function getAvailable($StartTime,$EndTime,$Date){
    include 'config.php';
    
    $sql = "SELECT LotID,LotName,LotStatus, (ReservationSpots - 
	(SELECT count(ReservationID) 
	FROM parkinglot as p2
	LEFT JOIN reservation as r2 USING (LotID)
	WHERE (((('$StartTime' >= StartTime) AND ('$StartTime' < EndTime)) AND
           (('$EndTime' > StartTime) AND ('$EndTime' <= EndTime))) OR
           (('$StartTime' <= StartTime) AND ('$EndTime' > StartTime)) OR
           (('$StartTime' < EndTime) AND ('$EndTime' >= EndTime))) AND Date = '$Date' AND
	p2.LotID = p.LotID))
	as total 
	FROM parkinglot as p
	LEFT JOIN reservation as r USING (LotID)
	GROUP BY p.LotID";
    $result = $mysqli->query($sql);

    if($result->num_rows > 0){	
		$result_array = array();
		while($row = $result->fetch_assoc()){
			$result_array[] = $row;
		}
		echo json_encode($result_array);
	}
	else
	{
		  echo "Check your input or else it does not exist.";
		  http_response_code(400);
	}
}

/***
*This will display the lots that the user can reserve for, depending on the permitid and the times chosen
*
*Input is the starttime, endtime, date and permitid
*Output will be lotid, lotname, lotstatus, the total which is the number of avaliable spots in json format
*
*Furthor notes this function will not work if the starttime is lesser then the endtime
*Also time such as 1 pm is 13:00:00 while 2:00 am is 2:00:00
*
*The subquery clause for total has a combination of comparison to the time
*The Starttime and Endtime for the reservation I used were 8:00 to 12:00
*I needed four cases to test
*The starttime and endtime input is inbetween 8:00 to 12:00
*The starttime before 8:00 and and endtime inbetween 8:00 to 12:00
*The starttime inbetween 8:00 to 12:00 and and after 12:00
*The starttime after 8:00 and before 12:00
*In the subquery clause in the where clause will list the LotID that are valid with permitid and starttime and endtime
*The cases check when the starttime is less then the endtime I will use 8:00 to 12:00 as the schedule start and end times
*After the THEN it will check the input times with the schedule times, the schedule time being true such as 8:00 to 12:00 
*First it will check if the inputed starttime and endtime is greater then the schedule endtime
*Second it will check if the inputed starttime and endtime is greater then the schedule starttime
*Then it will compare to boolean expression that if either of them is false will return false 
*First will check if the inputed starttime is greater then schedule starttime and that the endtime is greater
*then the endtime, if it is then the schedule times are a subset then the expression returns false
*The last long expression checks to see if the inputed starttime and endtime is in the schedule times
*If it is then the expression return false
*After the THEN it is the ELSE clause which would check if the endtime is greater then the starttime
*Like 6:00 to 2:00
*It mirrors the THEN clause accept the Endtime and Starttime are switched
***/
function getAvailableWithType($StartTime,$EndTime,$Date,$Type){
    include 'config.php';
    
    $sql = "SELECT LotID,LotName,LotStatus, (ReservationSpots - 
	(SELECT count(ReservationID) 
	FROM parkinglot as p2
	LEFT JOIN reservation as r2 USING (LotID)
	WHERE (((('$StartTime' >= StartTime) AND ('$StartTime' < EndTime)) AND
           (('$EndTime' > StartTime) AND ('$EndTime' <= EndTime))) OR
           (('$StartTime' <= StartTime) AND ('$EndTime' > StartTime)) OR
           (('$StartTime' < EndTime) AND ('$EndTime' >= EndTime))) AND Date = '$Date' AND
	p2.LotID = p.LotID AND r2.ReservationStatus = 1))
	as total 
	FROM parkinglot as p
	LEFT JOIN reservation as r USING (LotID)
    WHERE LotID in (SELECT LotID
	FROM schedule as s
	WHERE s.PermitID = '$Type' AND
(CASE WHEN StartTime <  EndTime THEN 
    ((('$StartTime' >= EndTime AND '$EndTime' > EndTime) OR
      ('$StartTime' < StartTime AND '$EndTime' <= StartTime)) AND
      ('$StartTime' > StartTime OR '$EndTime' < EndTime) AND
      ((('$StartTime' < StartTime) OR ('$StartTime' > EndTime) AND ('$EndTime' < StartTime) OR ('$EndTime' > EndTime))))
    ELSE
    ((('$StartTime' >= StartTime AND '$EndTime' > StartTime) OR
      ('$StartTime' < EndTime AND '$EndTime' <= EndTime)) AND
      ('$StartTime' > EndTime OR '$EndTime' < StartTime) AND
      ((('$StartTime' < EndTime) OR ('$StartTime' > StartTime) AND ('$EndTime' < EndTime) OR ('$EndTime' > StartTime))))
 END))
	GROUP BY p.LotID";
    $result = $mysqli->query($sql);

    if($result->num_rows > 0){	
		$result_array = array();
		while($row = $result->fetch_assoc()){
			$result_array[] = $row;
		}
		echo json_encode($result_array);
	}
	else
	{
		  echo "Check your input or else it does not exist.";
		  http_response_code(400);
	}
}

//Lots

/***
*This will add a lot to the lot table
*
*Input is the schedule id, lot name, max capacity ,disabledspot, reservation spots, lot status which is tinybit, reservability status which is tinybit
*Output will be the numeber of successful query
***/	

function newLot($lotname, $max, $dspots, $rspots, $lstatus, $reservable){
	include 'config.php';
	$sql = "INSERT INTO `parkinglot`(`LotID`, `LotName`, `MaxCapacity`, `DisabledSpots`, `ReservationSpots`, `LotStatus`, `isReservable`) 
	VALUES (NULL , '$lotname', '$max', '$dspots', '$rspots', '$lstatus', '$reservable')";
	if ($mysqli->query($sql)==true){
		echo "Successfully added: " . mysqli_affected_rows($mysqli);
	}
	else{
	echo("Error description: " . mysqli_error($mysqli));
	http_response_code(400);
	}
}
/***
*This will update a lot max capacity , disabledspots , and reservation spots in the lot table
*
*Input is the lot id, max capacity ,disabledspot, reservation spots
*Output will be the numeber of successful query
***/	
function updateLot($id, $max, $dspots, $rspots){
	include 'config.php';
	$sql = "UPDATE `parkinglot` SET `MaxCapacity`='$max',`DisabledSpots`='$dspots',`ReservationSpots`='$rspots' WHERE `LotID`='$id'";
	if ($mysqli->query($sql)==true){
		echo "Successful updates: " . mysqli_affected_rows($mysqli)." \n If 0 then it has already been updated or does not exist.";
	}
	else{
	echo("Error description: " . mysqli_error($mysqli));
	http_response_code(400);
	}
}
/***
*This will update a lot disabledspots in the lot table
*
*Input is the lot id, disabledspot
*Output will be the numeber of successful query
***/	
function updateLotDSpots($id, $dspots){
	include 'config.php';
	$sql = "UPDATE `parkinglot` SET `DisabledSpots`='$dspots' WHERE `LotID`='$id'";
	if ($mysqli->query($sql)==true){
		echo "Successful updates: " . mysqli_affected_rows($mysqli)." \n If 0 then it has already been updated or does not exist.";
	}
	else{
	echo("Error description: " . mysqli_error($mysqli));
	http_response_code(400);
	}
}
/***
*This will update a lot  reservation spots in the lot table
*
*Input is the lot id, reservation spots
*Output will be the numeber of successful query
***/	
function updateLotRSpots($id, $rspots){
	include 'config.php';
	$sql = "UPDATE `parkinglot` SET `ReservationSpots`='$rspots' WHERE `LotID`='$id'";
	if ($mysqli->query($sql)==true){
		echo "Successful updates: " . mysqli_affected_rows($mysqli)." \n If 0 then it has already been updated or does not exist.";
	}
	else{
	echo("Error description: " . mysqli_error($mysqli));
	http_response_code(400);
	}
}
/***
*This will add a schedule to the schedule table
*
*Input is the lot id, permit id, startime ,endtime
*Permit id: 1 Commuter, 2 Resident 3 Employee 
*Output will be the numeber of successful query
***/	

function newSchedule($lotid, $pid, $starttime, $endtime){
	include 'config.php';
	$sql = "INSERT INTO `schedule`(`ScheduleID`, `LotID`, `PermitID`, `StartTime`, `EndTime`) 
	VALUES (NULL ,'$lotid', '$pid', '$starttime', '$endtime')";
	if ($mysqli->query($sql)==true){
		echo "Successfully added: " . mysqli_affected_rows($mysqli);
	}
	else{
	echo("Error description: " . mysqli_error($mysqli));
	http_response_code(400);
	}
}
/***
*This will remove a schedule from the schedule table
*
*Input is the schedule id
*Output will be the numeber of successful query
***/	
function removeSchedule($id){
	include 'config.php';
	$sql = "DELETE FROM `schedule` WHERE `ScheduleID`= '$id'";
	if ($mysqli->query($sql)==true){
		echo "Successfully removed: " . mysqli_affected_rows($mysqli);
	}
	else{
	echo("Error description: " . mysqli_error($mysqli));
	http_response_code(400);
	}
}
/***
*This will change the schedule of a lot to a schedule from the schedule table
*
*Input is the lot id,schedule id
*Output will be the numeber of successful query
***/	
function changeLotSchedule($lotid, $id){
	include 'config.php';
	$sql = "UPDATE `schedule` SET `LotID`=$lotid WHERE `ScheduleID` = '$id'";
	if ($mysqli->query($sql)==true){
		echo "Successful updates: " . mysqli_affected_rows($mysqli)." \n If 0 then it has already been updated or does not exist.";
	}
	else{
	echo("Error description: " . mysqli_error($mysqli));
	http_response_code(400);
	}
}
/***
*This will remove a lot from the lot table
*
*Input is the lot id
*Output will be the numeber of successful query
***/	
function removeLot($id){
	include 'config.php';
	$sql = "DELETE FROM `parkinglot` WHERE `LotID`= '$id'";
	if ($mysqli->query($sql)==true){
		echo "Successfully removed: " . mysqli_affected_rows($mysqli);
	}
	else{
	echo("Error description: " . mysqli_error($mysqli));
	http_response_code(400);
	}
}
/***
*These will change the status of a lot to open or closed
*
*Input is the lot id
*Output will be the numeber of successful query
*NEEDS FIXING BOOLEAN
***/	
function closeLot($id){
	include 'config.php';
	$sql = "UPDATE parkinglot SET LotStatus = 0 WHERE LotID = $id";
	if ($mysqli->query($sql)==true){
		$success =  mysqli_affected_rows($mysqli);
		echo "Successful updates: " . $success." \n If 0 then it has already been updated or does not exist.";
		if($success > 0){
			cancelLotReservation($id);
		}
	}
	else{
	echo("Error description: " . mysqli_error($mysqli));
	http_response_code(400);
	}
}
function openLot($id){
	include 'config.php';
	$sql = "UPDATE parkinglot SET LotStatus = 1 WHERE LotID = $id";
	if ($mysqli->query($sql)==true){
		echo "Successful updates: " . mysqli_affected_rows($mysqli)." \n If 0 then it has already been updated or does not exist.";
	}
	else{
	echo("Error description: " . mysqli_error($mysqli));
	http_response_code(400);
	}
}

/***
*These will update the status of a lot to allow a lot to be reservable or not
*
*Input is the lot id
*Output will be the numeber of successful query
*NEEDS FIXING BOOLEAN
***/	
function unreserveLot($id){
	include 'config.php';
	$sql = "UPDATE parkinglot SET isReservable = 0 WHERE LotID = $id";
	if ($mysqli->query($sql)==true){
		echo "Successful updates: " . mysqli_affected_rows($mysqli)." \n If 0 then it has already been updated or does not exist.";
	}
	else{
	echo("Error description: " . mysqli_error($mysqli));
	http_response_code(400);
	}
}
function reserveLot($id){
	include 'config.php';
	$sql = "UPDATE parkinglot SET isReservable = 1 WHERE LotID = $id";
	if ($mysqli->query($sql)==true){
		echo "Successful updates: " . mysqli_affected_rows($mysqli)." \n If 0 then it has already been updated or does not exist.";
	}
	else{
	echo("Error description: " . mysqli_error($mysqli));
	http_response_code(400);
	}
}

//Users

/***
*These will update the User status to prevent user from making reservation
*
*Input is the user id
*Output will be the numeber of successful query
*NEEDS FIXING BOOLEAN
***/	
function lockUser($id){
	include 'config.php';
	$sql = "UPDATE user SET Status = 0 WHERE UserID = '$id'";
	if ($mysqli->query($sql)==true){
		echo "Successful updates: " . mysqli_affected_rows($mysqli)." \n If 0 then it has already been updated or does not exist.";
	}
	else{
	echo("Error description: " . mysqli_error($mysqli));
	http_response_code(400);
	}
}
function unlockUser($id){
	include 'config.php';
	$sql = "UPDATE user SET Status = 1 WHERE UserID = '$id'";
	if ($mysqli->query($sql)==true){
		echo "Successful updates: " . mysqli_affected_rows($mysqli)." \n If 0 then it has already been updated or does not exist.";
	}
	else{
	echo("Error description: " . mysqli_error($mysqli));
	http_response_code(400);
	}
}
/***
*This will get a specific user email from the user table
*
*Input is the userid
*Output will be the user info in a json format
***/	
function getEmail($id){
	include 'config.php';
	$sql = "SELECT `Email` FROM `user` WHERE `UserID` = '$id'";
	$result = $mysqli->query($sql);
		
	if($result->num_rows > 0){	
		$result_array = array();
		while($row = $result->fetch_assoc()){
			$result_array[] = $row;
		}
		echo json_encode($result_array);
	}
	else
	{
		  echo "Check your input or else it does not exist.";
		  		http_response_code(400);
	}
}
/*This will get a specific user status from the user table
*
*Input is the userid
*Output will be the user info in a json format
***/	
function getUserStatus($id){
	include 'config.php';
	$sql = "SELECT `Status` FROM `user` WHERE `UserID` = '$id'";
	$result = $mysqli->query($sql);
	
	if($result->num_rows > 0){	
		$result_array = array();
		while($row = $result->fetch_assoc()){
			$result_array[] = $row;
		}
		echo json_encode($result_array);
	}
	else
	{
		 echo "Check your input or else it does not exist.";
		 		http_response_code(400);
	}
}

//Complaints


/***
*This will add a complaint to the complaints table
*
*Input is the user id, report
*Output will be the numeber of successful query
***/	
function newComplaint($uid, $report){
	include 'config.php';
	$sql = "INSERT INTO `complaints` (`ComplaintID`, `UserID`, `Report`, `TimestampComplaint`) VALUES (NULL,'$uid', '$report',CURRENT_TIMESTAMP)";
	if ($mysqli->query($sql)==true){
		echo "Successfully added: " . mysqli_affected_rows($mysqli);
	}
	else{
	echo("Error description: " . mysqli_error($mysqli));
	http_response_code(400);
	}
}
/***
*This will remove a complaint from the commplaints table
*
*Input is the complaint id
*Output will be the numeber of successful query
*
*NEEDS FIXING BOOLEAN
***/	
function removeComplaint($id){
	include 'config.php';
	$sql = "DELETE FROM `complaints` WHERE `ComplaintID`= $id";
	if ($mysqli->query($sql)==true){
		echo "Successfully removed: " . mysqli_affected_rows($mysqli);
	}
	else{
	echo("Error description: " . mysqli_error($mysqli));
	http_response_code(400);
	}
	
}

//Reservations

/***
*This will add a reservation to the reservation table
*
*Input is the lot id, user id, date, startime ,endtime
*Output will be the numeber of successful query
***/	
function newReservation($lotid, $uid, $date,$starttime, $endtime){
	include 'config.php';
	$sql = "INSERT INTO `reservation`(`ReservationID`, `LotID`, `UserID`, `NotifyID`, `Date`, `StartTime`, `EndTime`, `CheckInTime`, `CheckOutTime`, `ReservationStatus`, `ReservationTimestamp`) 
	VALUES (NULL,'$lotid','$uid',NULL,'$date','$starttime','$endtime', NULL, NULL, 1,CURRENT_TIMESTAMP)";
	if ($mysqli->query($sql)==true){
		echo "Successfully added: " . mysqli_affected_rows($mysqli);
	}
	else{
	echo("Error description: " . mysqli_error($mysqli));
	http_response_code(400);
	}
}
/***
*This will cancel a reservation from the reservation table
*
*Input is the reservation id
*Output will be the numeber of successful query
***/
function cancelReservation($id){
	include 'config.php';
	$sql = "UPDATE `reservation` SET `ReservationStatus` = 0 WHERE `ReservationID` = '$id'";
	if ($mysqli->query($sql)==true){
		echo "Successful updates: " . mysqli_affected_rows($mysqli)." \n If 0 then it has already been updated or does not exist.";
	}
	else{
	echo("Error description: " . mysqli_error($mysqli));
	http_response_code(400);
	}
}
/***
*This will cancel reservations from the reservation table for a lot for all reservations that are the current date 
*and starttimes that have yet to start
*
*Input is the lot id
*Output will be the numeber of successful query
***/
function cancelLotReservation($id){
	include 'config.php';
	$sql = "UPDATE `reservation` SET `ReservationStatus` = 0 WHERE `LotID` = $id AND  CURRENT_TIME >= `StartTime` AND `Date` = CURRENT_DATE";
	if ($mysqli->query($sql)==true){
		echo "\nCancelled reservations: " . mysqli_affected_rows($mysqli)." \nIf 0 then it has already been updated or does not exist.";
	}
	else{
	echo("Error description: " . mysqli_error($mysqli));
	http_response_code(400);
	}
}
/***
*This will get the all reservation for a specific user 
*
*Input is the userid
*Output will be the table data in json format
***/	
function getUserReservations($id){
	include 'config.php';
	$table3 = "users";
	$sql = "SELECT * FROM `reservation` WHERE `UserID` = $id";
	$result = $mysqli->query($sql);
		
	if($result->num_rows > 0){	
		$result_array = array();
		while($row = $result->fetch_assoc()){
			$result_array[] = $row;
		}
		echo json_encode($result_array);
	}
	else
	{
		 echo "Check your input or else it does not exist.";
		 		http_response_code(400);
	}
}	
/***
*This will get a user id from the user table with an email
*
*Input is user email
*Output will be the numeber of successful query
***/
function getUserIdEmail($userEmail){

	include 'config.php';
	$sql = "SELECT `UserID` FROM `user` WHERE `Email` = '$userEmail'";
	$result = $mysqli->query($sql);		
		
	if($result->num_rows > 0){	
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		return $row["UserID"];
	}
	else
	{
		echo "Check your input email or else it does not exist.";
		http_response_code(400);
	}
	/* free result set */
		$result->close();
}

/*
	Kevin - 4/25/18
	Attempt to get userid from a token
*/

function getUserFromToken($userToken){
	//Kevin - 4/25/18 Include the MySQL Config
	include 'config.php';
	/*4/25/18 Kevin - Added reference to gplus-config.php and the autoload file from Google*/
	include ('gplus/vendor/autoload.php');
	include ('gplus/gplus-config.php');

	/*Kevin - 4/25/18 Access Token and get payload*/
	try {
		$accessToken = $g_client->fetchAccessTokenWithAuthCode($userToken);
		$g_client->setAccessToken($accessToken);
	}catch (Exception $e){
		echo "Error on Access Token: $e";
		return null; 
	}

	try {
		$pay_load = $g_client->verifyIdToken();
	}catch (Exception $e) {
		echo "Error on Verify Token: $e";
		return null;
	}

	/*Kevin - 4/25/18 Get User Email From Payload and Select the UserID from the user*/
	if(isset($pay_load)){
		$userEmail = $pay_load["email"];
		$sql = "SELECT * FROM user WHERE Email = '$userEmail'";
		$result = $mysqli->query($sql);
		$rowcount=mysqli_num_rows($result);
		if ($rowcount == 1) {
			$userID = 0;
			while ($row = $result->fetch_object()){
				$userID = $row->UserID;
			}
			return $userID;
		} 
		else if($rowcount == 0) {
			return null;
		} 
		else {
			return null;
		}
		/* free result set */
		$result->close();
		
	}
	else{
		echo "Error payload not set";
		return null;
	}	

	
}

/***
*This will get the user info
*
*Input email
*Output will be user info in json format
***/
function getUserInfo($userEmail){

	include 'config.php';
	$sql = "SELECT UserID, Name, Token FROM user WHERE Email = '$userEmail'";
	$result = $mysqli->query($sql);		
	if($result->num_rows > 0){	
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		echo json_encode($row);
	}
	else
	{
		echo "Check your input email or else it does not exist.";
	}
	/* free result set */
		$result->close();
}

/***
*This will add a new user to the user table for the faculty team
*
*Input PermitID, UserTypeID, Name, Email, Username, Token, isDisabled, Status
*Output will be success or not
***/
function postUserWithPermit($permit, $type, $name, $email,$username,$password, $isDisable,$status){
	include 'config.php';
	$sql = "INSERT INTO `user` (`UserID`, `PermitID`, `UserTypeID`, `Name`, `Email`, `Username`, `Token`, `isDisabled`, `Status`) VALUES
	(NULL, '$permit', '$type', '$name', '$email', '$username', '$password', '$isDisable', '$status')";
	if ($mysqli->query($sql)==true){
		echo "Successfully added: " . mysqli_affected_rows($mysqli);
	}
	else{
	echo("Error description: " . mysqli_error($mysqli));
	http_response_code(400);
	}
}

/***
*This will add a new notification to the notification table
*
*Input UserID, Timestamp, Message, NotificationTypeID
*Output will be success or not
***/	
function newNotification($UserID, $Message, $NotificationTypeID){
	include 'config.php';
	$sql = "INSERT INTO `notification` (`NotifyID`, `UserID`, `Timestamp`, `Message`, `NotificationTypeID`) VALUES 
	(NULL, '$UserID', CURRENT_TIMESTAMP, '$Message', '$NotificationTypeID')";
	if ($mysqli->query($sql)==true){
		echo "Successfully added: " . mysqli_affected_rows($mysqli);
	}
	else{
	echo("Error description: " . mysqli_error($mysqli));
	http_response_code(400);
	}
}

/***
*This will update parkinglot with new isReservable or ReservationSpots
*
*Input LotID, isReservable, ReservationSpots
*Output will be success or not
***/	
function updateParkinglot($LotID, $isReservable, $ReservationSpots){
	include 'config.php';
	$sql = "UPDATE `parkinglot` SET `isReservable` = '$isReservable' WHERE `parkinglot`.`LotID` = '$LotID'";
	$sql2 = "UPDATE `parkinglot` SET `ReservationSpots` = '$ReservationSpots' WHERE `parkinglot`.`LotID` = '$LotID'";
	$mysqli->query($sql);
	if ($mysqli->query($sql2)==true){
		echo "Successfully updated ";
	}
	else{
	echo("Error description: " . mysqli_error($mysqli));
	http_response_code(400);
	}
}



?>