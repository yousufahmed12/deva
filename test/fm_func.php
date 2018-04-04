<?php
	
//Reservations

/***
*This will cancel a reservation from the reservation table
*
*Input is the reservation id
*Output will boolean of true or false if succesful or not
***/
function cancelReservation($id){
	include 'config.php';
	try {
		$sql = "UPDATE reservation SET ReservationStatus = 0 WHERE ReservationID = $id";
		//include email retrieval and send email code
		if ($mysqli->query($sql)==true){
			echo "true";
		}
		else{
			echo "false";
	    }
	}
    catch(PDOException $e)
    {
        die ('PDO error in cancelReservation()": ' . $e->getMessage() );
    }
}

//Lots

/***
*This will add a lot to the lot table
*
*Input is the schedule id, lot name, max capacity ,disabledspot, reservation spots, lot status which is tinybit, reservability status which is tinybit
*Output will boolean of true or false if succesful or not
***/	

function newLot($sid, $lname, $max, $dspots, $rspots, $lstatus, $reservable){
	include 'config.php';
	$sql = "INSERT INTO `parkinglot`( `ScheduleID`, `LotName`, `MaxCapacity`, `DisabledSpots`, `ReservationSpots`, `LotStatus`, `isReservable`)
	VALUES ($sid', '$lname', '$max', '$dspots', '$rspots', '$lstatus', '$reservable')";
	if ($mysqli->query($sql)==true){
		echo "true";
	}
	else{
	echo "false";
	}
/***
*This will update a lot max capacity , disabledspots , and reservation spots in the lot table
*
*Input is the lot id, max capacity ,disabledspot, reservation spots
*Output will boolean of true or false if succesful or not
***/	
function updateLot($lotid, $max, $dspots, $rspots){
	include 'config.php';
	$sql = "UPDATE `parkinglot` SET `MaxCapacity`= $max,`DisabledSpots`=$dspots,`ReservationSpots`=$rspots WHERE `LotID` = $id";
	if ($mysqli->query($sql)==true){
		echo "true";
	}
	else{
	echo "false";
	}
	/***
*This will change the schedule of a lot to a schedule from the schedule table
*
*Input is the lot id,schedule id
*Output will boolean of true or false if succesful or not
***/	
function changeLotSchedule($lotid, $sid){
	include 'config.php';
	$sql = "UPDATE `parkinglot` SET `ScheduleID`=$sid WHERE `LotID` = $id";
	if ($mysqli->query($sql)==true){
		echo "true";
	}
	else{
	echo "false";
	}
/***
*This will remove a lot from the lot table
*
*Input is the lot id
*Output will boolean of true or false if succesful or not
***/	
function removeLot($id){
	include 'config.php';
	$sql = "DELETE FROM `parkinglot` WHERE `LotID` = $id";
	if ($mysqli->query($sql)==true){
		echo "true";
	}
	else{
	echo "false";
	}
}

/***
*These will change the status of a lot to open or closed
*
*Input is the lot id
*Output will boolean of true or false if succesful or not
***/	
function closeLot($id){
	include 'config.php';
	$sql = "UPDATE reservation SET LotStatus = 0 WHERE LotID = $id";
	if ($mysqli->query($sql)==true){
		echo "true";
	}
	else{
	echo "false";
	}
}
function openLot($id){
	include 'config.php';
	$sql = "UPDATE reservation SET LotStatus = 1 WHERE LotID = $id";
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
***/	
function unreserveLot($id){
	include 'config.php';
	$sql = "UPDATE reservation SET isReservable = 0 WHERE LotID = $id";
	if ($mysqli->query($sql)==true){
		echo "true";
	}
	else{
	echo "false";
	}
}
function reserveLot($id){
	include 'config.php';
	$sql = "UPDATE reservation SET isReservable = 1 WHERE LotID = $id";
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
***/	
function lockUser($id){
	include 'config.php';
	$sql = "UPDATE user SET Status = 0 WHERE LotID = $id";
	if ($mysqli->query($sql)==true){
		echo "true";
	}
	else{
	echo "false";
	}
}
function unlockUser($id){
	include 'config.php';
	$sql = "UPDATE user SET Status = 1 WHERE LotID = $id";
	if ($mysqli->query($sql)==true){
		echo "true";
	}
	else{
	echo "false";
	}
}


?>
