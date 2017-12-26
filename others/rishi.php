	

<?php
//echo phpinfo() ;

class TestArray
{
    public $arr = array();
    public $counter;

    public function __construct ()
    {
        $this->counter = 0;
    }

   public function inc($pret){
   	$this->arr[$this->counter] = $pret;
	$this->counter = $this->counter + 1;
   }
}

$menu = new TestArray();

$menu->inc("paint");
//echo $menu->arr[0];
//echo $menu->counter;

$menu->inc("shirt");
//echo $menu->arr[1];
//echo $menu->counter;


for($i = 0;$i < $menu->counter;$i++){
	echo $menu->arr[$i];
}


?>
