 <?php
    $servername = "localhost";
    $user = "phpmyadmin";
    $passwd = "Aswe2018*";
    $dbname ="webservice";

     $mysqli = mysqli_connect($servername,$user,$passwd,$dbname);//login to database
	 
	// Check connection
	if (!$mysqli)
	  {
	  	die("Connection error: " . mysqli_connect_errno());
	  }
?>
