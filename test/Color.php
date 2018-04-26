<?php

class Color
{
	public function red($string)
   {
	   return "\033[31m " . $string . " \033[0m";
   }
   public function green($string)
   {
	   return "\033[32m " . $string . " \033[0m";
   }
   public function blue($string)
   {
	   return "\033[34m " . $string . " \033[0m";
   }
   public function cyan($string)
   {
	   return "\033[36m " . $string . " \033[0m";
   }
   public function purple($string)
   {
	   return "\033[35m " . $string . " \033[0m";
   }
   public function brown($string)
   {
	   return "\033[33m " . $string . " \033[0m";
   }
}
?>