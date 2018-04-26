<?php
include_once 'WebService.php';
include_once 'Color.php';

class POST
{
	public $type = "POST";
	private $webService;
	private $color;
	
	private $table = 'table='; //complaints, notification , parkinglot, permit, reservation, schedule, usertype
	private $id = '&id='; //
     
   public function __construct()
   {	   
		$this->webService = new WebService();
		$this->color = new Color();
   }
   
   public function getUser($id)
   {
	   $fullurl = $this->webService->url . $this->table . "user" . $this->id . $id;
	   echo $fullurl ."<br>";
	   return $fullurl;
   }
}
?>