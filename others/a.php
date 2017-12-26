<html>
<form action="b.php" method=post>
<?php
	$array_var = array("red" => 1, "green" => 2);
?>
<input type="hidden" id="str_var" name="str_var" value="<?php print base64_encode(serialize($array_var)) ?>" >
<input type="submit" value="Submit" >
</form>
</html>
