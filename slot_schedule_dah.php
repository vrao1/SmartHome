<?php
	$page = file_get_contents('https://rrtp.comed.com/api?type=pricingtabledaynexttomorrow');
	$pattern = "#<td>[\d.]+\&cent;</td>#";
	//$page = '<tr><td>12:00 <small>AM</small></td><td>2.1&cent;</td></tr><tr><td>1:00 <small>AM</small></td><td>2.0&cent;</td></tr><tr><td>2:00 <small>AM</small></td><td>1.9&cent;</td></tr><tr><td>3:00 <small>AM</small></td><td>1.8&cent;</td></tr><tr><td>4:00 <small>AM</small></td><td>1.7&cent;</td></tr><tr><td>5:00 <small>AM</small></td><td>1.7&cent;</td></tr><tr><td>6:00 <small>AM</small></td><td>1.8&cent;</td></tr><tr><td>7:00 <small>AM</small></td><td>2.0&cent;</td></tr><tr><td>8:00 <small>AM</small></td><td>2.3&cent;</td></tr><tr><td>9:00 <small>AM</small></td><td>2.5&cent;</td></tr><tr><td>10:00 <small>AM</small></td><td>2.7&cent;</td></tr><tr><td>11:00 <small>AM</small></td><td>3.1&cent;</td></tr><tr><td>12:00 <small>PM</small></td><td>3.5&cent;</td></tr><tr><td>1:00 <small>PM</small></td><td>4.0&cent;</td></tr><tr><td>2:00 <small>PM</small></td><td>4.3&cent;</td></tr><tr><td>3:00 <small>PM</small></td><td>4.8&cent;</td></tr><tr><td>4:00 <small>PM</small></td><td>5.2&cent;</td></tr><tr><td>5:00 <small>PM</small></td><td>5.3&cent;</td></tr><tr><td>6:00 <small>PM</small></td><td>4.6&cent;</td></tr><tr><td>7:00 <small>PM</small></td><td>4.0&cent;</td></tr><tr><td>8:00 <small>PM</small></td><td>3.5&cent;</td></tr><tr><td>9:00 <small>PM</small></td><td>3.1&cent;</td></tr><tr><td>10:00 <small>PM</small></td><td>2.6&cent;</td></tr><tr><td>11:00 <small>PM</small></td><td>2.4&cent;</td></tr>';

	$rate="";
	$cent = "&cent;</td>";
	if(preg_match_all($pattern, $page, $matches)){
		foreach ($matches[0] as $key => $value) {
			$value = str_ireplace($cent, "", $value);
			$value = str_ireplace("<td>", "", $value);
			if($rate == ""){
				$rate = $value;
			}else{
				$rate = $rate.":".$value;
			}
		}
	}

	$date = date('Y-m-d');
	$hour = date('H');
	$min = date('i') + ($hour * 60);

	if($min > 990){
		$date = strtotime("+1 day");
		$date = date('Y-m-d',$date);
	}


	class item{
		public $id;
		public $name;
		public $usage;
		public $operating_duration;
		public $priority;
		public $total_consumption;
		public $start_time;
		public $end_time;
		public $dayslot;
		public $total_bill;

		public function item(){

		}
		public function setTotal_consumption(){
			$this->total_consumption = ($this->usage * $this->operating_duration)/60;
		}
	}

	$totalMorningItem = 0;
	$totalDayItem = 0;
	$totalEveningItem = 0;
	$totalNightItem = 0;
	$appliances_morning = array();
	$appliances_day = array();
	$appliances_evening = array();
	$appliances_night = array();

	$mysql_hostname = 'localhost';


    $mysql_username = 'root';

 
    $mysql_password = 'smarthome';


    $mysql_dbname = 'home_appliances';

    $connection = mysqli_connect($mysql_hostname, $mysql_username, $mysql_password);
    
    if (!$connection){
        die("Database Connection Failed" . mysqli_error($connection));
    }

    $select_db = mysqli_select_db($connection, $mysql_dbname);
    
    if (!$select_db){die("Database Selection Failed" . mysqli_error($connection));}

    //3. If the form is submitted or not.
    //3.1 If the form is submitted
    if (isset($rate) and isset($date)){
    
    //3.1.2 Checking the values are existing in the database or not
        $query = "INSERT INTO `day_ahead_hourly_pricing` (`date`,`price`) VALUES ('$date','$rate')";

      //  $result = mysqli_query($connection, $query) or die(mysqli_error($connection));
        
    //3.1.2 If the posted values are equal to the database values, then session will be created for the user.
        if ($result == TRUE){
            $message = "WOW : Records are added successfully into DB";
        }else{
    //3.1.3 If the login credentials doesn't match, he will be shown with an error message.
            $message = "ERROR : Records are not added into DB";
        }

        $query = "SELECT * FROM appliance";

        $result = mysqli_query($connection, $query) or die(mysqli_error($connection));
        $count = mysqli_num_rows($result);


        if ($count > 0) {
        	while($row = $result->fetch_assoc()) {
        		$newAppliance = new item();
        		$newAppliance->id = $row["id"];
                $newAppliance->name = $row["name"];
                $newAppliance->operating_duration = $row["operating_duration"];
                $newAppliance->usage = $row["usage"];
                $newAppliance->priority = $row["priority"];
                $newAppliance->dayslot = $row["dayslot"];
                $newAppliance->setTotal_consumption();
		if($newAppliance->dayslot == 'M'){
                	$appliances_morning[$totalMorningItem] = $newAppliance;
                	$totalMorningItem++;
		}else if($newAppliance->dayslot == 'D'){
                	$appliances_day[$totalDayItem] = $newAppliance;
                	$totalDayItem++;
		}else if($newAppliance->dayslot == 'E'){
                	$appliances_evening[$totalEveningItem] = $newAppliance;
                	$totalEveningItem++;
		}else if($newAppliance->dayslot == 'N'){
                	$appliances_night[$totalNightItem] = $newAppliance;
                	$totalNightItem++;
		}
            }
        }
    }
    else
    {
        /*** if we are here, something has gone wrong with the database ***/
        $message = 'We are unable to process your request. Since Day Ahead Hourly Price is unavailable now.Please try again later.';
    } 

    $all_price = array();
    $accumulated_price = array_fill(0, 288, 0);
    $timings = array();
	$realtime = '00:00';

    for ($i=0; $i < 288; $i++) {
        $time = strtotime($realtime);
    	$timings[$i] = $realtime;	
    	$realtime = date("H:i", strtotime('+5 minutes', $time));
    }
    $all_price_night = array();
    
    $realtime_night = '22:00';
    $accumulated_price_night = array_fill(0, 84, 0);
    $timings_night = array();

    for ($i=0; $i < 84; $i++) {
        $time = strtotime($realtime_night);
    	$timings_night[$i] = $realtime_night;	
    	$realtime_night = date("H:i", strtotime('+5 minutes', $time));
    }

    $token = strtok($rate, ":");
    $index = 0;

    while($token !== false){
    		$j=0;
    		while($j<12){
    			$all_price[$index] = $token;
			if($index >= 264){
				$all_price_night[$index - 264] = $token;	
			}else if($index < 60){
				$all_price_night[24+$index] = $token;	
			}			
    			$j++;
    			$index++;
    		}
    	$token = strtok(":");
    }

     

# START Morning Appliances

 if($totalMorningItem > 0){   

    for ($i=0; $i < $totalMorningItem-1; $i++) { 
    	for ($j=$i+1; $j < $totalMorningItem; $j++) { 
    		if($appliances_morning[$i]->priority > $appliances_morning[$j]->priority){
    			$tmp = $appliances_morning[$j];
    			$appliances_morning[$j] = $appliances_morning[$i];
    			$appliances_morning[$i] = $tmp;
    		}
    	}
    }

    $i=0;
    $totalMorningSlots = 48;
    $morningOffset = 60;
    while($i < $totalMorningItem){

    	$q = ($appliances_morning[$i]->operating_duration)%5;
    	$slots = ($appliances_morning[$i]->operating_duration)/5;

    	if($q > 0){
    		$slots++;
    	}

    	$gSum = 999999;
    	for ($j=0; $j < ($totalMorningSlots-$slots) ; $j++) { 
    		$current_sum = 0;
    		for ($k=$j; $k < $slots; $k++) { 
    			$current_sum = $current_sum + $all_price[$k+$morningOffset] + $accumulated_price[$k+$morningOffset];
    		}

    		if ($current_sum < $gSum) {
    			$gSum = $current_sum;
    			$appliances_morning[$i]->start_time = $j+$morningOffset;
    			$appliances_morning[$i]->end_time = $j+$slots+$morningOffset;
    		}
    	}

	$last_rate = $all_price[$appliances_morning[$i]->end_time];
	$appliances_morning[$i]->total_bill = ($q * ($last_rate * $appliances_morning[$i]->usage)/60) - ($last_rate * $appliances_morning[$i]->usage)/12;

    	for ($n=$appliances_morning[$i]->start_time; $n <= $appliances_morning[$i]->end_time; $n++) { 
    		$accumulated_price[$n] = $accumulated_price[$n] + ($all_price[$n] * $appliances_morning[$i]->usage)/12;
		$appliances_morning[$i]->total_bill = $appliances_morning[$i]->total_bill + ($all_price[$n] * $appliances_morning[$i]->usage)/12;
    	}
	$appliances_morning[$i]->total_bill = round(($appliances_morning[$i]->total_bill/100),2);
    	$i++;
	}
 }

# END Morning Appliances
# START Day Appliances
 if($totalDayItem > 0){   

    for ($i=0; $i < $totalDayItem-1; $i++) { 
    	for ($j=$i+1; $j < $totalDayItem; $j++) { 
    		if($appliances_day[$i]->priority > $appliances_day[$j]->priority){
    			$tmp = $appliances_day[$j];
    			$appliances_day[$j] = $appliances_day[$i];
    			$appliances_day[$i] = $tmp;
    		}
    	}
    }

    $i=0;
    $totalDaySlots = 108;
    $dayOffset = 108;
    while($i < $totalDayItem){

    	$q = ($appliances_day[$i]->operating_duration)%5;
    	$slots = ($appliances_day[$i]->operating_duration)/5;

    	if($q > 0){
    		$slots++;
    	}

    	$gSum = 999999;
    	for ($j=0; $j < ($totalDaySlots-$slots) ; $j++) { 
    		$current_sum = 0;
    		for ($k=$j; $k < $slots; $k++) { 
    			$current_sum = $current_sum + $all_price[$k+$dayOffset] + $accumulated_price[$k+$dayOffset];
    		}

    		if ($current_sum < $gSum) {
    			$gSum = $current_sum;
    			$appliances_day[$i]->start_time = $j+$dayOffset;
    			$appliances_day[$i]->end_time = $j+$slots+$dayOffset;
    		}
    	}

	$last_rate = $all_price[$appliances_day[$i]->end_time];
	$appliances_day[$i]->total_bill = ($q * ($last_rate * $appliances_day[$i]->usage)/60) - ($last_rate * $appliances_day[$i]->usage)/12;

    	for ($n=$appliances_day[$i]->start_time; $n <= $appliances_day[$i]->end_time; $n++) { 
    		$accumulated_price[$n] = $accumulated_price[$n] + ($all_price[$n] * $appliances_day[$i]->usage)/12;
		$appliances_day[$i]->total_bill = $appliances_day[$i]->total_bill + ($all_price[$n] * $appliances_day[$i]->usage)/12;
    	}
	$appliances_day[$i]->total_bill = round(($appliances_day[$i]->total_bill/100),2);
    	$i++;
	}
 }
# END Day Appliances
# START Evening Appliances
 if($totalEveningItem > 0){   

    for ($i=0; $i < $totalEveningItem-1; $i++) { 
    	for ($j=$i+1; $j < $totalEveningItem; $j++) { 
    		if($appliances_evening[$i]->priority > $appliances_evening[$j]->priority){
    			$tmp = $appliances_evening[$j];
    			$appliances_evening[$j] = $appliances_evening[$i];
    			$appliances_evening[$i] = $tmp;
    		}
    	}
    }

    $i=0;
    $totalEveningSlots = 60;
    $eveningOffset = 204;
    while($i < $totalEveningItem){

    	$q = ($appliances_evening[$i]->operating_duration)%5;
    	$slots = ($appliances_evening[$i]->operating_duration)/5;

    	if($q > 0){
    		$slots++;
    	}

    	$gSum = 999999;
    	for ($j=0; $j < ($totalEveningSlots-$slots) ; $j++) { 
    		$current_sum = 0;
    		for ($k=$j; $k < $slots; $k++) { 
    			$current_sum = $current_sum + $all_price[$k+$eveningOffset] + $accumulated_price[$k+$eveningOffset];
    		}

    		if ($current_sum < $gSum) {
    			$gSum = $current_sum;
    			$appliances_evening[$i]->start_time = $j+$eveningOffset;
    			$appliances_evening[$i]->end_time = $j+$slots+$eveningOffset;
    		}
    	}

	$last_rate = $all_price[$appliances_evening[$i]->end_time];
	$appliances_evening[$i]->total_bill = ($q * ($last_rate * $appliances_evening[$i]->usage)/60) - ($last_rate * $appliances_evening[$i]->usage)/12;

    	for ($n=$appliances_evening[$i]->start_time; $n <= $appliances_evening[$i]->end_time; $n++) { 
    		$accumulated_price[$n] = $accumulated_price[$n] + ($all_price[$n] * $appliances_evening[$i]->usage)/12;
		$appliances_evening[$i]->total_bill = $appliances_evening[$i]->total_bill + ($all_price[$n] * $appliances_evening[$i]->usage)/12;
    	}
	$appliances_evening[$i]->total_bill = round(($appliances_evening[$i]->total_bill/100),2);
    	$i++;
	}
 }
# END Evening Appliances
# START Night Appliances

 if($totalNightItem > 0){   

    for ($i=0; $i < $totalNightItem-1; $i++) { 
    	for ($j=$i+1; $j < $totalNightItem; $j++) { 
    		if($appliances_night[$i]->priority > $appliances_night[$j]->priority){
    			$tmp = $appliances_night[$j];
    			$appliances_night[$j] = $appliances_night[$i];
    			$appliances_night[$i] = $tmp;
    		}
    	}
    }

    $i=0;
    $totalNightSlots = 84;
    while($i < $totalNightItem){

    	$q = ($appliances_night[$i]->operating_duration)%5;
    	$slots = ($appliances_night[$i]->operating_duration)/5;

    	if($q > 0){
    		$slots++;
    	}

    	$gSum = 999999;
	$actual_slots = $slots;

    	for ($j=0; $j < ($totalNightSlots-$slots) ; $j++) { 
		
    		$current_sum = 0;
    		for ($k=$j; $k < $slots; $k++) {
    			$current_sum = $current_sum + $all_price_price[$k] + $accumulated_price_night[$k];
    		}

    		if ($current_sum < $gSum) {
    			$gSum = $current_sum;
			$appliances_night[$i]->start_time = $j;
    			$appliances_night[$i]->end_time = $j+$slots;
    		}
    	}

	$last_rate = $all_price_night[$appliances_night[$i]->end_time];
	$appliances_night[$i]->total_bill = ($q * ($last_rate * $appliances_night[$i]->usage)/60) - ($last_rate * $appliances_night[$i]->usage)/12;

    	for ($n=$appliances_night[$i]->start_time; $n <= $appliances_night[$i]->end_time; $n++) { 
    		$accumulated_price_night[$n] = $accumulated_price_night[$n] + ($all_price_night[$n] * $appliances_night[$i]->usage)/12;
		$appliances_night[$i]->total_bill = $appliances_night[$i]->total_bill + ($all_price_night[$n] * $appliances_night[$i]->usage)/12;
    	}
	$appliances_night[$i]->total_bill = round(($appliances_night[$i]->total_bill/100),2);
    	$i++;
	}
 }
# END Night Appliances
?>

<html>
    <head>
        <title>PHPRO Login</title>

<script>
function setColor(btn){
    var property = document.getElementById(btn);
    property.style.backgroundColor = "#00FF00";
}
</script>

    </head>
    <body>
        <center><?php echo "<font size=6 color=blue>Scheduled Appliances taking account of Day Ahead Pricing from Comed for the date ".$date."</font>"; ?><hr><hr><br>
	<?php
		if($totalMorningItem>0){
	?>
        <center><font size=5 color=purple> Economical Schedule of appliances in Morning from 5 AM till 8:59 AM on the date mentioned above</font></center>
        <table BORDER=7 CELLPADDING=7 CELLSPACING=7>
            <tr>
            <th><font size=4 color=red>Priority No.</font></th>
            <th><font size=4 color=red>Appliance</font></th>
            <th><font size=4 color=red>Start Time</font></th>
            <th><font size=4 color=red>End Time</font></th>
            <th><font size=4 color=red>Total Bill Amount</font><font color=black size=3> (To be paid) </font></th>
            <th><font size=3 color=blue>Is it OKAY to you to schedule it?</font></th>
            </tr>

            <?php
            $i=0;
	    $suffix = "1";	
            while($i < $totalMorningItem){
                echo "<tr>";
                echo "<td><font size=4 color=#800080>" . $appliances_morning[$i]->priority. "</font></td>";
                echo "<td><font size=4 color=#800080>" . $appliances_morning[$i]->name. "</font></td>";
                echo "<td><font size=4 color=#800080>" . $timings[$appliances_morning[$i]->start_time]. "</font></td>";
                echo "<td><font size=4 color=#800080>" . $timings[$appliances_morning[$i]->end_time]. "</font></td>";
                echo "<td><font size=4 color=#800080>$ " . $appliances_morning[$i]->total_bill. "</font></td>";
                echo "<td><input type=\"button\" value=\"YES\" id=\"button".$suffix.$i."\" style= \"color:#2F4F4F\" onclick=\"setColor('button".$suffix.$i."')\";/></td>";
                echo "</tr>";
                $i++;
            }
            ?>

        </table>  
	<?php
	}else{
                echo "<center><font size=4 color=red> Sorry ! You don't have any appliance to be scheduled in</center>";
	}
	if($totalDayItem>0){
	?>
        <center><font size=5 color=purple> Economical Schedule of appliances in Daytime from 9 AM till 4:59 PM on the date mentioned above</font></center>
        <table BORDER=7 CELLPADDING=7 CELLSPACING=7>
            <tr>
            <th><font size=4 color=red>Priority No.</font></th>
            <th><font size=4 color=red>Appliance</font></th>
            <th><font size=4 color=red>Start Time</font></th>
            <th><font size=4 color=red>End Time</font></th>
            <th><font size=4 color=red>Total Bill Amount</font><font color=black size=3> (To be paid) </font></th>
            <th><font size=3 color=blue>Is it OKAY to you to schedule it?</font></th>
            </tr>

            <?php
            $i=0;
	    $suffix = "2";	
            while($i < $totalDayItem)  {
                echo "<tr>";
                echo "<td><font size=4 color=#800080>" . $appliances_day[$i]->priority. "</font></td>";
                echo "<td><font size=4 color=#800080>" . $appliances_day[$i]->name. "</font></td>";
                echo "<td><font size=4 color=#800080>" . $timings[$appliances_day[$i]->start_time]. "</font></td>";
                echo "<td><font size=4 color=#800080>" . $timings[$appliances_day[$i]->end_time]. "</font></td>";
                echo "<td><font size=4 color=#800080>$ " . $appliances_day[$i]->total_bill. "</font></td>";
                echo "<td><input type=\"button\" value=\"YES\" id=\"button".$suffix.$i."\" style= \"color:#2F4F4F\" onclick=\"setColor('button".$suffix.$i."')\";/></td>";
                #echo "<td><input type=\"button\" value=\"YES\"></td>";
                echo "</tr>";
                $i++;
            }
            ?>

        </table>  
	<?php
	}else{
                echo "<center><font size=4 color=red> Sorry ! You don't have any appliance to be scheduled in Day Hours for the date mentioned above</center>";
	}
	if($totalEveningItem>0){
	?>
        <center><font size=5 color=purple> Economical Schedule of appliances in Evening time from 5 PM till 9:59 PM on the date mentioned above</font></center>
        <table BORDER=7 CELLPADDING=7 CELLSPACING=7>
            <tr>
            <th><font size=4 color=red>Priority No.</font></th>
            <th><font size=4 color=red>Appliance</font></th>
            <th><font size=4 color=red>Start Time</font></th>
            <th><font size=4 color=red>End Time</font></th>
            <th><font size=4 color=red>Total Bill Amount</font><font color=black size=3> (To be paid) </font></th>
            <th><font size=3 color=blue>Is it OKAY to you to schedule it?</font></th>
            </tr>

            <?php
            $i=0;
	    $suffix = "3";	
            while($i < $totalEveningItem)  {
                echo "<tr>";
                echo "<td><font size=4 color=#800080>" . $appliances_evening[$i]->priority. "</font></td>";
                echo "<td><font size=4 color=#800080>" . $appliances_evening[$i]->name. "</font></td>";
                echo "<td><font size=4 color=#800080>" . $timings[$appliances_evening[$i]->start_time]. "</font></td>";
                echo "<td><font size=4 color=#800080>" . $timings[$appliances_evening[$i]->end_time]. "</font></td>";
                echo "<td><font size=4 color=#800080>$ " . $appliances_evening[$i]->total_bill. "</font></td>";
                echo "<td><input type=\"button\" value=\"YES\" id=\"button".$suffix.$i."\" style= \"color:#2F4F4F\" onclick=\"setColor('button".$suffix.$i."')\";/></td>";
                #echo "<td><input type=\"button\" value=\"YES\"></td>";
                echo "</tr>";
                $i++;
            }
            ?>

        </table>  
	<?php
	}else{
                echo "<center><font size=4 color=red> Sorry ! You don't have any appliance to be scheduled in Evening Hours for the date mentioned above</center>";
	}
	if($totalNightItem>0){
	?>
        <center><font size=5 color=purple> Economical Schedule of appliances in Night time from 10 PM till 4:59 AM on the date mentioned above and next day</font></center>
        <table BORDER=7 CELLPADDING=7 CELLSPACING=7>
            <tr>
            <th><font size=4 color=red>Priority No.</font></th>
            <th><font size=4 color=red>Appliance</font></th>
            <th><font size=4 color=red>Start Time</font></th>
            <th><font size=4 color=red>End Time</font></th>
            <th><font size=4 color=red>Total Bill Amount</font><font color=black size=3> (To be paid) </font></th>
            <th><font size=3 color=blue>Is it OKAY to you to schedule it?</font></th>
            </tr>

            <?php
            $i=0;
	    $suffix = "4";	
            while($i < $totalNightItem)  {
                echo "<tr>";
                echo "<td><font size=4 color=#800080>" . $appliances_night[$i]->priority. "</font></td>";
                echo "<td><font size=4 color=#800080>" . $appliances_night[$i]->name. "</font></td>";
             	echo "<td><font size=4 color=#800080>" . $timings_night[$appliances_night[$i]->start_time]. "</font></td>";
                echo "<td><font size=4 color=#800080>" . $timings_night[$appliances_night[$i]->end_time]. "</font></td>";
                echo "<td><font size=4 color=#800080>$ " . $appliances_night[$i]->total_bill."</font></td>";
                echo "<td><input type=\"button\" value=\"YES\" id=\"button".$suffix.$i."\" style= \"color:#2F4F4F\" onclick=\"setColor('button".$suffix.$i."')\";/></td>";
                #echo "<td><input type=\"button\" value=\"YES\"></td>";
                echo "</tr>";
                $i++;
            }
            ?>

        </table>  
	<?php
	}else{
                echo "<center><font size=4 color=red> Sorry ! You don't have any appliance to be scheduled in Night Hours for the date mentioned above and next day</center>";
	}
	?>
        <font color="blue" size="6"><a href="login_page.php">BACK</a></font> 
        </center>
    </body>
</html>
