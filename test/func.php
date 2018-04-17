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
		
	$result_array = array();
		while($row = $result->fetch_assoc()){
			$result_array[] = $row;
		}
	echo json_encode($result_array);
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
		echo "true";
	}
	else{
	echo "false";
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
		
	$result_array = array();
		while($row = $result->fetch_assoc()){
			$result_array[] = $row;
		}
		echo json_encode($result_array);
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
		echo "true";
	}
	else{
	echo "false";
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
     try {
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

    $result_array = array();
        while($row = $result->fetch_assoc()){
            $result_array[] = $row;
        }
        echo json_encode($result_array);
         }
    catch(PDOException $e)
    {
        die ('PDO error in getReservation()": ' . $e->getMessage() );
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
     try {
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

    $result_array = array();
        while($row = $result->fetch_assoc()){
            $result_array[] = $row;
        }
        echo json_encode($result_array);
         }
    catch(PDOException $e)
    {
        die ('PDO error in getReservation()": ' . $e->getMessage() );
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
		echo "true";
	}
	else{
	echo "false";
	}
}
function openLot($id){
	include 'config.php';
	$sql = "UPDATE parkinglot SET LotStatus = 1 WHERE LotID = $id";
	if ($mysqli->query($sql)==true){
		echo "true";
	}
	else{
	echo "false";
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
		echo "true";
	}
	else{
	echo "false";
	}
}
function reserveLot($id){
	include 'config.php';
	$sql = "UPDATE parkinglot SET isReservable = 1 WHERE LotID = $id";
	if ($mysqli->query($sql)==true){
		echo "true";
	}
	else{
	echo "false";
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
		echo "true";
	}
	else{
	echo "false";
	}
}
function unlockUser($id){
	include 'config.php';
	$sql = "UPDATE user SET Status = 1 WHERE UserID = '$id'";
	if ($mysqli->query($sql)==true){
		echo "true";
	}
	else{
	echo "false";
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
		echo "true";
	}
	else{
	echo "false";
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
		echo "true";
	}
	else{
	echo "false";
	}
	
}
?>