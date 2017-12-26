<?php

$theArray = array();

class item{
	public function item(){}
	public function AddStringToArray($string) {
		global $theArray;
		array_push($theArray , $string);
	}
}
$a = new item();
$a->AddStringToArray('hello world!');
$b = new item();
$b->AddStringToArray('paan');
print $theArray[0];
print "====================".$theArray[1];
?>
