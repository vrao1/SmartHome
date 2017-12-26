<?php

$GLOBALS = array(
		'aw' => array()
);

class item{

	protected $glob;
	public function item(){
		global $GLOBALS;
		$this->glob = & $GLOBALS;
	}

	public function mata(index,val){
		$this->glob['aw'][index] = val;
	}
}

$a = new item();
$a->mata(0,"ladki");

$b = new item();
$b->mata(1,"chata");

echo $GLOBALS['aw'];
?>
