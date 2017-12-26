<?php
$date1 = "2011-10-10 01:00:00";
$date2 = "2011-10-10 10:11:00";
echo round((strtotime($date2) - strtotime($date1)) /60);
?>
