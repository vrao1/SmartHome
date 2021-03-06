<?php
	session_start();
//	require('item.php');

	$url = 'https://rrtp.comed.com/api?type=pricingtabledaynexttomorrow';
	$pattern = "#<td>[\d.]+\&cent;</td>#";
	$timeThat = "16:30:00";
	$timeNow = date('H:i:s', time()+7200);
	
	if($timeThat > $timeNow){
        	$url = "https://rrtp.comed.com/rrtp/ServletFeed?type=pricingtabledual&date=". date("Ymd", time()+7200);
		$pattern = "#<td>[\d.]+\&cent;</td><td>#";
	}
	$page = file_get_contents($url);

//	$page = '<tr><td>12:00 <small>AM</small></td><td>2.1&cent;</td></tr><tr><td>1:00 <small>AM</small></td><td>2.0&cent;</td></tr><tr><td>2:00 <small>AM</small></td><td>1.9&cent;</td></tr><tr><td>3:00 <small>AM</small></td><td>1.8&cent;</td></tr><tr><td>4:00 <small>AM</small></td><td>1.7&cent;</td></tr><tr><td>5:00 <small>AM</small></td><td>1.7&cent;</td></tr><tr><td>6:00 <small>AM</small></td><td>1.8&cent;</td></tr><tr><td>7:00 <small>AM</small></td><td>2.0&cent;</td></tr><tr><td>8:00 <small>AM</small></td><td>2.3&cent;</td></tr><tr><td>9:00 <small>AM</small></td><td>2.5&cent;</td></tr><tr><td>10:00 <small>AM</small></td><td>2.7&cent;</td></tr><tr><td>11:00 <small>AM</small></td><td>3.1&cent;</td></tr><tr><td>12:00 <small>PM</small></td><td>3.5&cent;</td></tr><tr><td>1:00 <small>PM</small></td><td>4.0&cent;</td></tr><tr><td>2:00 <small>PM</small></td><td>4.3&cent;</td></tr><tr><td>3:00 <small>PM</small></td><td>4.8&cent;</td></tr><tr><td>4:00 <small>PM</small></td><td>5.2&cent;</td></tr><tr><td>5:00 <small>PM</small></td><td>5.3&cent;</td></tr><tr><td>6:00 <small>PM</small></td><td>4.6&cent;</td></tr><tr><td>7:00 <small>PM</small></td><td>4.0&cent;</td></tr><tr><td>8:00 <small>PM</small></td><td>3.5&cent;</td></tr><tr><td>9:00 <small>PM</small></td><td>3.1&cent;</td></tr><tr><td>10:00 <small>PM</small></td><td>2.6&cent;</td></tr><tr><td>11:00 <small>PM</small></td><td>2.4&cent;</td></tr>';

#	$page = '<tr><td>12:00 <small>AM</small></td><td>1.8&cent;</td><td>2.0&cent;</td></tr><tr><td>1:00 <small>AM</small></td><td>1.7&cent;</td><td>1.7&cent;</td></tr><tr><td>2:00 <small>AM</small></td><td>1.6&cent;</td><td>1.9&cent;</td></tr><tr><td>3:00 <small>AM</small></td><td>1.4&cent;</td><td>1.7&cent;</td></tr><tr><td>4:00 <small>AM</small></td><td>1.4&cent;</td><td>2.0&cent;</td></tr><tr><td>5:00 <small>AM</small></td><td>1.6&cent;</td><td>1.7&cent;</td></tr><tr><td>6:00 <small>AM</small></td><td>2.2&cent;</td><td>2.4&cent;</td></tr><tr><td>7:00 <small>AM</small></td><td>2.6&cent;</td><td>2.8&cent;</td></tr><tr><td>8:00 <small>AM</small></td><td>2.5&cent;</td><td>2.3&cent;</td></tr><tr><td>9:00 <small>AM</small></td><td>2.5&cent;</td><td>2.2&cent;</td></tr><tr><td>10:00 <small>AM</small></td><td>2.5&cent;</td><td>2.2&cent;</td></tr><tr><td>11:00 <small>AM</small></td><td>2.4&cent;</td><td>2.2&cent;</td></tr><tr><td>12:00 <small>PM</small></td><td>2.3&cent;</td><td>2.2&cent;</td></tr><tr><td>1:00 <small>PM</small></td><td>2.3&cent;</td><td>2.1&cent;</td></tr><tr><td>2:00 <small>PM</small></td><td>2.3&cent;</td><td>2.1&cent;</td></tr><tr><td>3:00 <small>PM</small></td><td>2.3&cent;</td><td>2.1&cent;</td></tr><tr><td>4:00 <small>PM</small></td><td>2.5&cent;</td><td>2.3&cent;</td></tr><tr><td>5:00 <small>PM</small></td><td>3.3&cent;</td><td>3.1&cent;</td></tr><tr><td>6:00 <small>PM</small></td><td>3.2&cent;</td><td>2.5&cent;</td></tr><tr><td>7:00 <small>PM</small></td><td>3.1&cent;</td><td>2.5&cent;</td></tr><tr><td>8:00 <small>PM</small></td><td>3.0&cent;</td><td>2.5&cent;</td></tr><tr><td>9:00 <small>PM</small></td><td>2.7&cent;</td><td>2.3&cent;</td></tr><tr><td>10:00 <small>PM</small></td><td>2.3&cent;</td><td>n/a </td></tr><tr><td>11:00 <small>PM</small></td><td>2.0&cent;</td><td>n/a </td></tr>';

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


	$user_id = $_SESSION['username'];

	$date = date('Y-m-d');
	$hour = date('H');
	$min = date('i') + ($hour * 60);
	
	# Total Displayed Items Ids
	$totalItemId = array();
	$totalItem=0;

	# Total Cost
	$totalCosts = 0;

	# All Time Slots need to be stoblack in following array

	$allTimeSlots = array();
	$allTimesSlotsLength = 0;

	# Hash Table to store position of each slot in above array
	$timeSlotPosition = array(); #( 'morning' => 0, 'day' => 1, 'evening' => 2, 'night' => 3);

	# Appliances without any slot
	$nullSlotAppliance = array();
	$totalNullSlotAppliance = 0;

	if($min > 990){
		$date = strtotime("+1 day");
		$date = date('Y-m-d',$date);
	}

	// Put each object in a hash table for each (entire timeSlots) object array

	class timeSlots{
		public $name;
		public $start_time;
		public $end_time;
		public $slotLengthInMin;
		public $allotedAppliances = array();
		public $totalAllotedAppliances;
		public $totalCosts;

		public function __construct(){
			//$this->allotedAppliances = array();
			$this->totalAllotedAppliances = 0;
			$this->totalCosts = 0;
		}

		public function addCost($bill){
			$this->totalCosts = $this->totalCosts + $bill;
		}

		public function storeNewAppliance($newAppliance){
			//if($newAppliance == null){
			//	return;
			//}
			$this->allotedAppliances[ ] = $newAppliance;
			$this->totalAllotedAppliances = $this->totalAllotedAppliances + 1;	
		}
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
//		public $ignoreFlag;

		public function __construct(){
//			$this->ignoreFlag = false;
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


    if (isset($rate) and isset($date)){

        $query = "SELECT * FROM time_slots where user_id = '$user_id'";

        $result = mysqli_query($connection, $query) or die(mysqli_error($connection));
        $count_times = mysqli_num_rows($result);

	// Assign all appliance to each slot's object's instance variable known as array

	$allTimeSlotsLength = 0;

        if ($count_times > 0) {
        	while($row = $result->fetch_assoc()) {
        		$newTimeSlot = new timeSlots();
			$newTimeSlot->name = $row["slot_name"];
			$newTimeSlot->start_time = $row["start"];
			$newTimeSlot->end_time = $row["end"];

                        $date1 = "2011-10-10 ".$row["start"];
                        $date2 = "2011-10-10 ".$row["end"];

                        $diff =  round((strtotime($date2) - strtotime($date1)) /60);
                        $diff = $diff < 0 ? 1440 + $diff : $diff;

			$newTimeSlot->slotLengthInMin = $diff ;  
			$timeSlotPosition[$newTimeSlot->name] = $allTimeSlotsLength;
			$allTimeSlots[$allTimeSlotsLength] = $newTimeSlot;
			$allTimeSlotsLength = $allTimeSlotsLength + 1;
		}
	}
    	else
    	{
        	$message = 'We are unable to process your request. Since timeSlots table has no data.Please try again later.';
    	}

        $query = "SELECT * FROM appliance where user_id = '$user_id'";

        $result = mysqli_query($connection, $query) or die(mysqli_error($connection));
        $count = mysqli_num_rows($result);

	// Assign all appliance to each slot's object's instance variable known as array

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

		try{
			if (!array_key_exists($newAppliance->dayslot , $timeSlotPosition)){
				$nullSlotAppliance[$totalNullSlotAppliance] = $newAppliance;
				$totalNullSlotAppliance = $totalNullSlotAppliance + 1;
			}else{
		
				//$allTimeSlots[$timeSlotPosition[$newAppliance->dayslot]]->storeNewAppliance($newAppliance);
				$midIndex = $timeSlotPosition[$newAppliance->dayslot];
				$allTimeSlots[$midIndex]->allotedAppliances[ ] = $newAppliance;

				//$allTimeSlots[$midIndex]->totalAllotedAppliances = sizeof($allTimeSlots[$midIndex]->allotedAppliances);
			}
		}catch(Exception $e){
			echo 'Caught Exception: '. $e->getMessage(). "<BR>";
		}
            }
        }
    }
    else
    {
        $message = 'We are unable to process your request. Since Day Ahead Hourly Price is unavailable now.Please try again later.';
    } 

# start New
 if($allTimeSlotsLength > 0){

	$morningStart = $allTimeSlots[$timeSlotPosition["morning"]]->start_time;	

        $date1 = "2011-10-10 00:00:00";
        $date2 = "2011-10-10 ".$morningStart;

        $slotsInNightFromMidNight =  round((strtotime($date2) - strtotime($date1)) /300);

    	#prices storage in realtime

    	$all_price = array();
    	$accumulated_price = array_fill(0, 288+$slotsInNightFromMidNight, 0);

    	$timings = array();
    	$realtime = '00:00';

    	# Putting Across all 288 timings

    	for ($i=0; $i < 288; $i++) {
        	$time = strtotime($realtime);
    		$timings[$i] = $realtime; # Store All Times incremented by 5 minutes	
    		$realtime = date("H:i", strtotime('+5 minutes', $time));
    	}

	# Putting extra for night duration

	$MAX_INDEX = 288 + $slotsInNightFromMidNight;
    	$realtime = '00:00';

    	for ($i=288; $i < $MAX_INDEX; $i++) {
        	$time = strtotime($realtime);
    		$timings[$i] = $realtime; # Store All Times incremented by 5 minutes	
    		$realtime = date("H:i", strtotime('+5 minutes', $time));
    	}
	
	# Parsing rates from the web page
    	$token = strtok($rate, ":");
    	$index = 0;

    	while($token !== false){
    		$j=0;
    		while($j<12){
    			$all_price[$index] = $token;
    			$j++;
    			$index++;
    		}
    		$token = strtok(":");
    	}

	# Store next from midNight till Morning
    	for ($i=288; $i < $MAX_INDEX; $i++) {
		$all_price[$i] = $all_price[$i-288];
	}

	$allTimeSlotsCounter = 0;
	# Iterating through the array containing each time slot along with corresponding allocated appliances
 	foreach($allTimeSlots as $slot){

		$howManyAppliances = sizeof($slot->allotedAppliances);

		if($howManyAppliances == 0){
			continue;
		}
	
		#Sort Based on Priority
    		for ($i=0; $i < $howManyAppliances-1; $i++) { 
    			for ($j=$i+1; $j < $howManyAppliances; $j++) { 
    				if($slot->allotedAppliances[$i]->priority > $slot->allotedAppliances[$j]->priority){
    					$tmp = $slot->allotedAppliances[$j];
    					$slot->allotedAppliances[$j] = $slot->allotedAppliances[$i];
    					$slot->allotedAppliances[$i] = $tmp;
    				}
    			}
    		}

		# Calculate total cost using most economic slots for appliance usage		
  
  		$totalSlots = ($slot->slotLengthInMin)/5;
                $date1 = "2011-10-10 00:00:00";
                $date2 = "2011-10-10 ".$slot->start_time;

                $diff =  round((strtotime($date2) - strtotime($date1)) /300);


                $slotOffset = $diff < 0 ? -1*$diff : $diff;
		$itemCount = sizeof($slot->allotedAppliances);

    		$i = 0;
		
   	 	while($i < $howManyAppliances){

    			$q = ($slot->allotedAppliances[$i]->operating_duration)%5;
    			$slots_num = ($slot->allotedAppliances[$i]->operating_duration)/5;

    			if($q > 0){
    				$slots_num++;
    			}

    			$gSum = 999999;

			# If Available Slot length is insufficient to operate this appliance then 
			if($totalSlots < $slots_num){
				$nullSlotAppliance[$totalNullSlotAppliance] = $slot->allotedAppliances[$i];
				$totalNullSlotAppliance = $totalNullSlotAppliance + 1;

				//$slot->allotedAppliances[$i]->ignoreFlag = true;
				$i++;
				continue;
			}else if($totalSlots == $slots_num){
    				$slot->allotedAppliances[$i]->start_time = $slotOffset;
    				$slot->allotedAppliances[$i]->end_time = $slots_num-1;
			}else{
				$upperLimit = $slotOffset + ($totalSlots - $slots_num);
    				for ($j = $slotOffset; $j < $upperLimit ; $j++) { 
    					$current_sum = 0;

					$tillWhatIndex = $j + $slots_num;

    					for ($k=$j; $k < $tillWhatIndex; $k++) { 
						$indx = $k;
    						$current_sum = $current_sum + $all_price[$indx] + $accumulated_price[$indx];
    					}

    					if ($current_sum < $gSum) {
    						$gSum = $current_sum;
						$start_indx = $j;
						$end_indx = $j+$slots_num-1;
					
    						$slot->allotedAppliances[$i]->start_time = $start_indx;
    						$slot->allotedAppliances[$i]->end_time = $end_indx;
    					}
    				}
			}
			# In case if operating duration doesn't fit multiple of 5 min slots, then (1-fraction) duration needs to be calculated in advance to subtract from the total, where fraction = $q
 
			$last_rate = $all_price[$slot->allotedAppliances[$i]->end_time];
			if ($q > 0){
			$slot->allotedAppliances[$i]->total_bill = ($q * ($last_rate * $slot->allotedAppliances[$i]->usage)/60) - ($last_rate * $slot->allotedAppliances[$i]->usage)/12;
    			$accumulated_price[$slot->allotedAppliances[$i]->end_time] = $accumulated_price[$slot->allotedAppliances[$i]->end_time] + $slot->allotedAppliances[$i]->total_bill;
			}

    			for ($n=$slot->allotedAppliances[$i]->start_time; $n <= $slot->allotedAppliances[$i]->end_time; $n++) { 
    				$accumulated_price[$n] = $accumulated_price[$n] + ($all_price[$n] * $slot->allotedAppliances[$i]->usage)/12;
				$slot->allotedAppliances[$i]->total_bill = $slot->allotedAppliances[$i]->total_bill + ($all_price[$n] * $slot->allotedAppliances[$i]->usage)/12;
			}
			$slot->allotedAppliances[$i]->total_bill = round(($slot->allotedAppliances[$i]->total_bill/100),2);

			$slot->addCost($slot->allotedAppliances[$i]->total_bill);
    			$i++;
		} // end of while

		$allTimeSlotsCounter = $allTimeSlotsCounter + 1;

	} // end of foreach

 } // end of if 

# end New     
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
        <style>
        body {
        background-image: url("./images/blueLight.jpeg");
        background-repeat: no-repeat;
        background-size: cover;
        background-position: center;
        }
        </style>
    <body>
        <center><?php echo "<font size=6 color=blue>The most economic schedule for each appliance within respective assigned slots for the date ".$date."</font>"; ?><hr><hr><br>
	<?php
	if($allTimeSlotsLength>0){
	       # Iterating through the array containing each time slot along with corresponding allocated appliances
		
        	foreach($allTimeSlots as $slot){

			if (sizeof($slot->allotedAppliances) > 0){
	?>
        <center><font size=5 color=purple> <HR><?php echo "Economical Schedule of appliances in \"<font color=green><b>".$slot->name."</b></font>\" from ".$slot->start_time." till ".$slot->end_time." on the date mentioned above"; ?></font></center>

	<form id="schedule" name="schedule" method="POST" onsubmit="return myFunction();">
        <table BORDER=7 CELLPADDING=7 CELLSPACING=7>
            <tr>
            <th><font size=4 color=black>Priority No.</font></th>
            <th><font size=4 color=black>Appliance</font></th>
            <th><font size=4 color=black>Start Time</font></th>
            <th><font size=4 color=black>End Time</font></th>
            <th><font size=4 color=black>Total Bill Amount (In Cents)</font><font color=black size=3> (To be paid) </font></th>
            <th colspan="2"><font size=3 color=blue>Is it OKAY to you to schedule it?</font></th>
            </tr>

            <?php
            $i=0;
            while($i < sizeof($slot->allotedAppliances)){
	//	if ( $slot->allotedAppliances[$i]->ignoreFlag == true ){
	//		continue;
	//		$i++;
	//	}

	    	$suffix = $slot->allotedAppliances[$i]->id;	

                echo "<tr>";
                echo "<td><font size=4 color=#800080>" . $slot->allotedAppliances[$i]->priority. "</font></td>";
                echo "<td><font size=4 color=#800080>" . $slot->allotedAppliances[$i]->name. "</font></td>";
                echo "<td><font size=4 color=#800080>" . $timings[$slot->allotedAppliances[$i]->start_time]. "</font></td>";
                echo "<td><font size=4 color=#800080>" . $timings[$slot->allotedAppliances[$i]->end_time]. "</font></td>";
                echo "<td><font size=4 color=#800080>&cent; " . ($slot->allotedAppliances[$i]->total_bill/10). "</font></td>";
               	echo "<td><input name = \"button".$suffix."\" type=radio id=\"button_yes".$suffix."\" value=\"YES\"><label>YES</label></td>";
                echo "<td><input name = \"button".$suffix."\" type=radio id=\"button_no".$suffix."\" value=\"NO\"><label>NO</label></td>";
                echo "</tr>";
                $i++;
		$totalItemId[$totalItem] = $suffix;
		$totalItem++;
            }
            ?>

            <tr>
            <td><font size=4 color=black>Total</font></td>
            <td></td>
            <td></td>
            <td></td>
            <td><font size=4 color=black>&cent; <?php $totalCosts = $slot->totalCosts/10; echo $totalCosts;?></font><font color=black size=3> (To be paid) </font></td>
            <td colspan="2"></td>
            </tr>

        </table> 
	<HR></BR> 
	<?php
		}
		else
		{
                	echo "<center><HR><font size=5 color=black> Sorry ! You don't have any appliance to be scheduled in <font color=green><b>\"".$slot->name."\"</b></font> Hours for the date mentioned above</center><HR></BR>";
		}
	      } // End of Foreach
	}else{
                echo "<center><font size=4 color=black> Sorry ! You don't have any appliance to be scheduled in</center>";
	}

	?>

	<input type="submit" value="Re-Execute" id="button_re"/>
	</form>
	<HR>
	<?php
	if ($totalNullSlotAppliance > 0){
	?>

         <center><HR><font size=5 color=black> Following are appliances which either hasn't been assigned slot or the length of the assigned slot does not accommodate it's operating duration</center><HR></BR>

	<form id="reassign" name="reassign" action="reassign_slots.php" method="POST">
        <table BORDER=7 CELLPADDING=7 CELLSPACING=7>
            <tr>
            <th><font size=4 color=black>Priority No.</font></th>
            <th><font size=4 color=black>Appliance</font></th>
            <th><font size=4 color=black>Usage</font></th>
            <th><font size=4 color=black>Operating Duration</font></th>
	<?php
	    $i = 0;
            while($i < $totalNullSlotAppliance){
                echo "<tr>";
                echo "<td><font size=4 color=#800080>" . $nullSlotAppliance[$i]->priority. "</font></td>";
                echo "<td><font size=4 color=#800080>" . $nullSlotAppliance[$i]->name. "</font></td>";
                echo "<td><font size=4 color=#800080>" . $nullSlotAppliance[$i]->usage. "</font></td>";
                echo "<td><font size=4 color=#800080>" . $nullSlotAppliance[$i]->operating_duration. "</font></td>";
                echo "</tr>";
                $i++;
            }
            echo "</table>";
            echo "<font size=4 color=green>To Re-Assign slots please click this button </font>";
            echo "<input type=hidden id=appliances_str name=appliances_str value=". base64_encode(serialize($nullSlotAppliance)) ." >";
	    echo "<input type=submit value=Re-Assign >";
	    echo "</form>";
	}
	?>
	<HR>
	<BR>
        <font color="blue" size="6"><a href="login_page.php">BACK</a></font> 
        </center>
    	<script>
		function myFunction() {
			var button = "button";

			var totalItem = "<?php echo $totalItem ?>";
			var i = 0;
			var totalItemId = new Array();
   				<?php foreach ($totalItemId as $item) : ?>
   					totalItemId[i]= <?php echo $item ?>;
					i++;
   				<?php endforeach; ?>
			var k = 0;

			while (k<totalItem){
				var key = button + totalItemId[k].toString();
				var radios = document.getElementsByName(key);

				var howManyOptions = radios.length;
				var isChecked = false;

				for (var j=0;j < howManyOptions;j++){
					if(radios[j].checked == true){
						isChecked = true;
						break;
					}
				}

				if (isChecked == false){
					alert("Kindly Click \"YES\" or \"NO\" radio button of each row");
					document.schedule.action = "slot_schedule_dah.php";
					return false;
				}
				k++;
			}

			document.schedule.action = "final_slot_schedule_dah.php";
			return true;
		}
	</script>
	</body>
</html>
