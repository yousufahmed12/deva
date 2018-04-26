<?php
include_once 'WebService.php';
include_once 'Color.php';

class DEL
{
	public $type = "DELETE";
	private $webService;
	private $color;
	 
	private $remove = 'remove=';
	private $user = 'user&id=';
	private $complaints = 'complaints&id=';
	private $parkinglot = 'parkinglot&id=';
	private $schedule = 'schedule&id=';
	
     
   public function __construct()
   {	   
		$this->webService = new WebService();
		$this->color = new Color();
   }
   
   
   public function deleteUser($id)
   {
	   return $this->webService->url . $this->remove . $this->user . $id;
   }
   
   public function removeComplaints($id)
   {
	   return $this->webService->url . $this->remove . $this->complaints . $id;
   }
   
   public function removeLot($id)
   {
	   return $this->webService->url . $this->remove . $this->parkinglot . $id;
   }
   
   public function removeSchedule($id)
   {
	   return $this->webService->url . $this->remove . $this->schedule . $id;;
   }
}
?>