<?php
$utc_date = DateTime::createFromFormat(
    'Y-m-d G:i',
    '2011-04-27 02:45',
    new DateTimeZone('UTC')
);

$acst_date = clone $utc_date; // we don't want PHP's default pass object by reference here
$acst_date->setTimeZone(new DateTimeZone('Australia/Yancowinna'));

echo 'UTC:  ' . $utc_date->format('Y-m-d g:i A');  // UTC:  2011-04-27 2:45 AM
echo 'ACST: ' . $acst_date->format('Y-m-d g:i A'); // ACST: 2011-04-27 12:15 PM
?>
