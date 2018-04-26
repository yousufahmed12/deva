<?php

class WebService
{
	public $tables = array("complaints", "notification", "parkinglot", "permit", "reservation", "schedule", "user", "usertype");
	public $url = 'http://ec2-34-229-81-168.compute-1.amazonaws.com/deva/api.php?';	
   
	//send http request with json 
	//requesttype(GET, POST, PUT, DELETE)
	//fullurl(http request url)
	//json(post body)
	function httpSendJ($requestType, $fullurl, $json){
        $headers = array('Accept: application/json','Content-Type: application/json');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $fullurl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $requestType);
		curl_setopt($ch, CURLOPT_POSTFIELDS,$json);
		
		//check if post body exists
		if($requestType === "POST"){
			if($data == NULL){
				echo "Attempted to POST request without post body <br>";
			}
			else{
				
			}
		}
		
        return curl_exec($ch);
	}
	
	function httpSend($requestType, $fullurl){
        $headers = array('Accept: application/json','Content-Type: application/json');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $fullurl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $requestType);	
	
        return curl_exec($ch);
	}
}
?>