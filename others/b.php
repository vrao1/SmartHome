<?php
$str_var = $_POST["str_var"];
$array_var = unserialize(base64_decode($str_var));
foreach ($array_var as $key => $value){
	echo "{$key} => {$value}\n";
}
?>
