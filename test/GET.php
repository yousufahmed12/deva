<?php
include_once 'WebService.php';
include_once 'Color.php';

class GET
{
	public $type = "GET";
	
	private $color;
	
	private $table = 'table='; //"complaints", "notification", "parkinglot", "reservation", "schedule", "user"
	private $id = '&id='; //
	private $userStatus= 'get=userStatus';
	private $email = 'get=Email'; 
	private $userinfo = 'get=userInfo'; 
	private $uemail = '&email='; 
	private $userReservation = 'get=userReservation';
	private $starttime = 'starttime=';
	private $endtime = '&endtime=';
	private $utype = '&type=';
	private $webService;
     
   public function __construct()
   {	   
		$this->webService = new WebService();
		$this->color = new Color();
   }
   
   
   
   public function getUser($id)
   {
	   return $this->webService->url . $this->table . "user" . $this->id . $id;
   }
   
   public function getUserStatus($id)
   {
	   return $this->webService->url . $this->userStatus . $this->id . $id;
   }
   
   public function getEmail($id)
   {
	   return $this->webService->url . $this->email . $this->id . $id;
   }
   
   public function getUserReservation($email)
   {
	   return $this->webService->url . $this->userReservation . $this->uemail . $email;
   }
   
   //complaints, notification , parkinglot, permit, reservation, schedule, usertype
   public function getTable($table)
   {
	   return $this->webService->url . $this->table . $table;
   }
   
   public function getAvailableWithType($starttime, $endtime, $type)
   {
	   return $this->webService->url . $this->starttime . $starttime . $this->endtime . $endtime . $this->utype . $type;
   }
   
   public function getAvailable($starttime, $endtime)
   {
	   return $this->webService->url . $this->starttime . $starttime . $this->endtime . $endtime;
   } 
   
   public function getUserInfo($email)
   {
	   return $this->webService->url . $this->userinfo . $this->uemail . $email;
   }
}
?>