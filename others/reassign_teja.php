<?php

require('itemClass.php');

/*
$object = $_SESSION['obj'];

echo "<BR>";
echo $object->priority;
echo "<BR>";

*/

$appliances_str = $_POST["appliances_str"];

echo "<BR>";
echo strlen($appliances_str);
echo "<BR>";
echo $appliances_str;

$nullSlotAppliance = unserialize(base64_decode($appliances_str));

echo "<BR> Dekho Dekho <BR>";
echo sizeof($nullSlotAppliance);
echo "<BR> Dekho Dekho <BR>";
print_r($nullSlotAppliance[0]->priority);
print_r($nullSlotAppliance[0]->name);
print_r($nullSlotAppliance[0]->usage);
print_r($nullSlotAppliance[0]->operating_duration);
echo "<BR>";
echo strlen($nullSlotAppliance);
echo "<BR>";

?>
