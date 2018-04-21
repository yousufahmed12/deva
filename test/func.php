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
	}
}	
/***
*This will add a user to the user table
*
*Input is the name, email,username,password,isDisable which is tinybit, status which is tinybit
*Output will boolean of true or false if succesful or not
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
*Output will boolean of true or false if succesful or not
***/
function deleteUser($id){
	include 'config.php';
	$sql = "DELETE FROM user WHERE UserID = $id";
	if ($mysqli->query($sql)==true){
		echo "Successfully removed: " . mysqli_affected_rows($mysqli);
	}
	else{
	echo("Error description: " . mysqli_error($mysqli));
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
	}
}

//Lots

/***
*This will add a lot to the lot table
*
*Input is the schedule id, lot name, max capacity ,disabledspot, reservation spots, lot status which is tinybit, reservability status which is tinybit
*Output will boolean of true or false if succesful or not
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
	}
}
/***
*This will update a lot max capacity , disabledspots , and reservation spots in the lot table
*
*Input is the lot id, max capacity ,disabledspot, reservation spots
*Output will boolean of true or false if succesful or not
***/	
function updateLot($id, $max, $dspots, $rspots){
	include 'config.php';
	$sql = "UPDATE `parkinglot` SET `MaxCapacity`='$max',`DisabledSpots`='$dspots',`ReservationSpots`='$rspots' WHERE `LotID`='$id'";
	if ($mysqli->query($sql)==true){
		echo "Successful updates: " . mysqli_affected_rows($mysqli);
	}
	else{
	echo("Error description: " . mysqli_error($mysqli));
	}
}
/***
*This will update a lot disabledspots in the lot table
*
*Input is the lot id, disabledspot
*Output will boolean of true or false if succesful or not
***/	
function updateLotDSpots($id, $dspots){
	include 'config.php';
	$sql = "UPDATE `parkinglot` SET `DisabledSpots`='$dspots' WHERE `LotID`='$id'";
	if ($mysqli->query($sql)==true){
		echo "Successful updates: " . mysqli_affected_rows($mysqli);
	}
	else{
	echo("Error description: " . mysqli_error($mysqli));
	}
}
/***
*This will update a lot  reservation spots in the lot table
*
*Input is the lot id, reservation spots
*Output will boolean of true or false if succesful or not
***/	
function updateLotRSpots($id, $rspots){
	include 'config.php';
	$sql = "UPDATE `parkinglot` SET `ReservationSpots`='$rspots' WHERE `LotID`='$id'";
	if ($mysqli->query($sql)==true){
		echo "Successful updates: " . mysqli_affected_rows($mysqli);
	}
	else{
	echo("Error description: " . mysqli_error($mysqli));
	}
}
/***
*This will add a schedule to the schedule table
*
*Input is the lot id, permit id, startime ,endtime
*Permit id: 1 Commuter, 2 Resident 3 Employee 
*Output will boolean of true or false if succesful or not
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
	}
}
/***
*This will remove a schedule from the schedule table
*
*Input is the schedule id
*Output will boolean of true or false if succesful or not
***/	
function removeSchedule($id){
	include 'config.php';
	$sql = "DELETE FROM `schedule` WHERE `ScheduleID`= '$id'";
	if ($mysqli->query($sql)==true){
		echo "Successfully removed: " . mysqli_affected_rows($mysqli);
	}
	else{
	echo("Error description: " . mysqli_error($mysqli));
	}
}
/***
*This will change the schedule of a lot to a schedule from the schedule table
*
*Input is the lot id,schedule id
*Output will boolean of true or false if succesful or not
***/	
function changeLotSchedule($lotid, $id){
	include 'config.php';
	$sql = "UPDATE `schedule` SET `LotID`=$lotid WHERE `ScheduleID` = '$id'";
	if ($mysqli->query($sql)==true){
		echo "Successful updates: " . mysqli_affected_rows($mysqli);
	}
	else{
	echo("Error description: " . mysqli_error($mysqli));
	}
}
/***
*This will remove a lot from the lot table
*
*Input is the lot id
*Output will boolean of true or false if succesful or not
***/	
function removeLot($id){
	include 'config.php';
	$sql = "DELETE FROM `parkinglot` WHERE `LotID`= '$id'";
	if ($mysqli->query($sql)==true){
		echo "Successfully removed: " . mysqli_affected_rows($mysqli);
	}
	else{
	echo("Error description: " . mysqli_error($mysqli));
	}
}
/***
*These will change the status of a lot to open or closed
*
*Input is the lot id
*Output will boolean of true or false if succesful or not
*NEEDS FIXING BOOLEAN
***/	
function closeLot($id){
	include 'config.php';
	$sql = "UPDATE parkinglot SET LotStatus = 0 WHERE LotID = $id";
	if ($mysqli->query($sql)==true){
		echo "Successful updates: " . mysqli_affected_rows($mysqli);
	}
	else{
	echo("Error description: " . mysqli_error($mysqli));
	}
}
function openLot($id){
	include 'config.php';
	$sql = "UPDATE parkinglot SET LotStatus = 1 WHERE LotID = $id";
	if ($mysqli->query($sql)==true){
		echo "Successful updates: " . mysqli_affected_rows($mysqli);
	}
	else{
	echo("Error description: " . mysqli_error($mysqli));
	}
}

/***
*These will update the status of a lot to allow a lot to be reservable or not
*
*Input is the lot id
*Output will boolean of true or false if succesful or not
*NEEDS FIXING BOOLEAN
***/	
function unreserveLot($id){
	include 'config.php';
	$sql = "UPDATE parkinglot SET isReservable = 0 WHERE LotID = $id";
	if ($mysqli->query($sql)==true){
		echo "Successful updates: " . mysqli_affected_rows($mysqli);
	}
	else{
	echo("Error description: " . mysqli_error($mysqli));
	}
}
function reserveLot($id){
	include 'config.php';
	$sql = "UPDATE parkinglot SET isReservable = 1 WHERE LotID = $id";
	if ($mysqli->query($sql)==true){
		echo "Successful updates: " . mysqli_affected_rows($mysqli);
	}
	else{
	echo("Error description: " . mysqli_error($mysqli));
	}
}

//Users

/***
*These will update the User status to prevent user from making reservation
*
*Input is the user id
*Output will boolean of true or false if succesful or not
*NEEDS FIXING BOOLEAN
***/	
function lockUser($id){
	include 'config.php';
	$sql = "UPDATE user SET Status = 0 WHERE UserID = '$id'";
	if ($mysqli->query($sql)==true){
		echo "Successful updates: " . mysqli_affected_rows($mysqli);
	}
	else{
	echo("Error description: " . mysqli_error($mysqli));
	}
}
function unlockUser($id){
	include 'config.php';
	$sql = "UPDATE user SET Status = 1 WHERE UserID = '$id'";
	if ($mysqli->query($sql)==true){
		echo "Successful updates: " . mysqli_affected_rows($mysqli);
	}
	else{
	echo("Error description: " . mysqli_error($mysqli));
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
	}
}

//Complaints


/***
*This will add a complaint to the complaints table
*
*Input is the user id, report
*Output will boolean of true or false if succesful or not
***/	
function newComplaint($uid, $report){
	include 'config.php';
	$sql = "INSERT INTO `complaints` (`ComplaintID`, `UserID`, `Report`, `TimestampComplaint`) VALUES (NULL,'$uid', '$report',CURRENT_TIMESTAMP)";
	if ($mysqli->query($sql)==true){
		echo "Successfully added: " . mysqli_affected_rows($mysqli);
	}
	else{
	echo("Error description: " . mysqli_error($mysqli));
	}
}
/***
*This will remove a complaint from the commplaints table
*
*Input is the complaint id
*Output will boolean of true or false if succesful or not
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
	}
	
}

//Reservations

/***
*This will add a reservation to the reservation table
*
*Input is the lot id, user id, date, startime ,endtime
*Output will boolean of true or false if succesful or not
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
	}
}
/***
*This will cancel a reservation from the reservation table
*
*Input is the reservation id
*Output will boolean of true or false if succesful or not
***/
function cancelReservation($id){
	include 'config.php';
	$sql = "UPDATE `reservation` SET `ReservationStatus` = 0 WHERE `ReservationID` = '$id'";
	if ($mysqli->query($sql)==true){
		echo "Successful updates: " . mysqli_affected_rows($mysqli);
	}
	else{
	echo("Error description: " . mysqli_error($mysqli));
	}
}

?>
