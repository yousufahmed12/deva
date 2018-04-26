<?php
include_once 'GET.php';
include_once 'DELETE.php';
include_once 'Color.php';
 
class Test{

public $call;
public $color;
private $webService;
private $type;

	public function __construct($type)
   {
	     $this->webService = new WebService();
		 $this->color = new Color();
		 $this->type = $type;
		 if($type=="GET")
		 {
			$this->call = new GET();
		 }
		 else if($type=="POST")
		 {
			 
		 }
		 else if($type=="PUT")
		 {
			 
		 }
		 else if($type=="DELETE")
		 {
			$this->call = new DEL();
		 }
   }
   
   function get_http_response_code($domain1) {
		$headers = get_headers($domain1);
		return substr($headers[0], 9, 3);
    }	

	//$url is full url for http request
	//$error is whether or not error response is expected
	public function checkOutput($url, bool $error)
	{
		$get_http_response_code = $this->get_http_response_code($url);
		$output = $this->webService->httpSend($this->type, $url);
		
		echo $url . "<br>";
		if($error)
		{
			echo "Expecting error ... RESPONSE: " . $get_http_response_code . "<br>";
			if($get_http_response_code >= 400)
			{
				//echo $this->color->green("Successful error: ") . $output . "\n";
				echo $output . "<br> PASS <br><br><br>";
			}	
			else
			{
				//echo $this->color->red("Unsuccessful error: ") . $output . "\n";	
				echo $output . "<br> FAIL <br><br><br>";	
			}
		}
		else
		{
			echo "Not expecting error ... RESPONSE: " . $get_http_response_code . "<br>";
			if($get_http_response_code >= 400)
			{
				echo  $output . "<br> FAIL <br><br><br>";
			}	
			else
			{
				echo  $output . "<br> PASS <br><br><br>";	
			}
		}
	}
}
//TEST PLAN
/*
GET request to verify current entries 
POST request to create entries 
GET request to verify all POST requests
PUT request to change entries 
GET request to verify all PUT requests
DELETE request to delete entries
GET request to verify all DELETE requests 
*/

//Manual GET tests

$test = new Test("GET");

//Call to non-existing resource 
$test->checkOutput($test->call->getUser(-10000000000000000000000000000000000000000), true);
$test->checkOutput($test->call->getUser(100000000000000000000000000000000000000000), true);
//Valid call
    $test->checkOutput($test->call->getUser(1), false);
//$test->checkOutput($test->call->getUser(1), false);

//Call to non-existing resource 
$test->checkOutput($test->call->getUserStatus(-100000000000000000000000000000000000000000), true);
$test->checkOutput($test->call->getUserStatus(100000000000000000000000000000000000000000), true);
//Valid call
$test->checkOutput($test->call->getUserStatus(1), false);

//Call to non-existing resource 
$test->checkOutput($test->call->getEmail(-100000000000000000000000000000000000000000), true);
$test->checkOutput($test->call->getEmail(100000000000000000000000000000000000000000), true);
//Valid call
$test->checkOutput($test->call->getEmail(1), false);

//Call to non-existing resource 
$test->checkOutput($test->call->getUserReservation(""), true);
$test->checkOutput($test->call->getUserReservation("-100000000"), false);
$test->checkOutput($test->call->getUserReservation(-100000000), false);
//Valid call
$test->checkOutput($test->call->getUserReservation("asdasd@Guy.com"), false);

//Call to non-existing resource 
$test->checkOutput($test->call->getTable("1"), true);
$test->checkOutput($test->call->getTable("complaint"), true);
//Valid call
$test->checkOutput($test->call->getTable("complaints"), false);

$test->checkOutput($test->call->getAvailableWithType("1523602800", "1523620800", "2"), false);
$test->checkOutput($test->call->getAvailableWithType("1523602800", "15236208", "1"), true);

//Valid call
$test->checkOutput($test->call->getAvailable("1523940764", "1524545564"), false);
//Starttime same as Endtime
$test->checkOutput($test->call->getAvailable("1523940764", "1523940764"), false);
//Endtime before Starttime
$test->checkOutput($test->call->getAvailable("1523940764", "1523620"), true);

//Valid call
$test->checkOutput($test->call->getUserInfo("asdasd@Guy.com"), false);
//Call to non-existing resource 
$test->checkOutput($test->call->getUserInfo("@Guy.com"), true);


//Manual DELETE tests

$test = new Test("DELETE");
$testget = new Test("GET");

//remove existing user and verify
$testget->checkOutput($testget->call->getUser(121), false);
$test->checkOutput($test->call->deleteUser(121), false);
$testget->checkOutput($testget->call->getUser(121), true);
//Call to non-existing resource 
$test->checkOutput($test->call->deleteUser(-1000000000), true);


//remove existing complaint and verify
$testget->checkOutput($testget->call->getTable("complaints"), false);
$test->checkOutput($test->call->removeComplaints(1), true);
$testget->checkOutput($testget->call->getTable("complaints"), false);
//Call to non-existing resource 
$test->checkOutput($test->call->removeComplaints(-1000000), true);

//remove existing complaint and verify
$testget->checkOutput($testget->call->getTable("parkinglot"), false);
$test->checkOutput($test->call->removeLot(18), true);
$testget->checkOutput($testget->call->getTable("parkinglot"), false);
//Call to non-existing resource 
$test->checkOutput($test->call->removeLot(10000000000000000), true);


//remove existing complaint and verify
$testget->checkOutput($testget->call->getTable("schedule"), false);
$test->checkOutput($test->call->removeSchedule(27), true);
$testget->checkOutput($testget->call->getTable("schedule"), false);
//Call to non-existing resource 
$test->checkOutput($test->call->removeSchedule(-1), true);
$test->checkOutput($test->call->removeSchedule(1000000000000000000000000), true);


?>