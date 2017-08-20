<?php
	$page = file_get_contents('https://hourlypricing.comed.com/api?type=5minutefeed&format=text');
	//echo $page;
	$token = strtok($page, ":");

    $all_price = array();
    $accumulated_price = array_fill(0, 288, 0);
    $timings = array_fill(0, 288, "00:00");

    $token = strtok($page, ",");
    $index = 287;

    while($token !== false and $index >= 0){
        $myStr = explode(':', $token);
        $all_price[$index] = $myStr[1];
        //print $myStr[1]."<BR>";
        //print $myStr[0]."<BR>";
        //print $index."-----".date("H:i", $myStr[0]/1000) . "<BR>";

        $timings[$index] = date("Y-m-d H:i", $myStr[0]/1000);
        $token = strtok(",");
        $index--;
    }

	$date = date('Y-m-d H:i');

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

		public function item(){}
	/*	
		public function setId($id){
			$this->$id = $id;
		}

		public function setName($name){
			$this->name = $name;
		}

		public function setUsage($usage){
			$this->usage = $usage;
		}
		
		public function setOp($op){
			$this->operating_duration = $op;
		}

		public function setPriority($priority){
			$this->priority = $priority;
		}
		*/
		public function setTotal_consumption(){
			$this->total_consumption = ($this->usage * $this->operating_duration)/60;
		}
		/*
		public function getId(){
			return $this->id;
		}

		public function getName(){
			return $this->name;
		}

		public function getUsage(){
			return $this->usage;
		}
		
		public function getOp(){
			return $this->operating_duration;
		}

		public function getPriority(){
			return $this->priority;
		}

		public function getTotal_consumption(){
			return $this->total_consumption;
		}*/
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

    /*** mysql username ***/
    $mysql_username = 'root';

    /*** mysql password ***/
    $mysql_password = 'smarthome';

    /*** database name ***/
    $mysql_dbname = 'home_appliances';

    $connection = mysqli_connect($mysql_hostname, $mysql_username, $mysql_password);
    
    if (!$connection){
        die("Database Connection Failed" . mysqli_error($connection));
    }

    $select_db = mysqli_select_db($connection, $mysql_dbname);
    
    if (!$select_db){die("Database Selection Failed" . mysqli_error($connection));}

    
        $query = "SELECT * FROM appliance";

        $result = mysqli_query($connection, $query) or die(mysqli_error($connection));
        $count = mysqli_num_rows($result);

    if ($count > 0) {
        	while($row = $result->fetch_assoc()) {
        		$newAppliance = new item();
        		$newAppliance->id = $row["id"];
                $newAppliance->name = $row["name"];
                //echo $row["name"]."<BR>";
                $newAppliance->operating_duration = $row["operating_duration"];
                //echo $row["operating_duration"]."<BR>";
                $newAppliance->usage = $row["usage"];
                $newAppliance->priority = $row["priority"];
                $newAppliance->setTotal_consumption();
		$newAppliance->dayslot = $row["dayslot"];

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
    else
    {
        /*** if we are here, something has gone wrong with the database ***/
        $message = 'We are unable to process your request. No Data in DB .Please try again later or submit appliances.';
    } 


    for ($i=0; $i < $totalItem-1; $i++) { 
    	for ($j=$i+1; $j < $totalItem; $j++) { 
    		if($appliances[$i]->priority > $appliances[$j]->priority){
    			$tmp = $appliances[$j];
    			$appliances[$j] = $appliances[$i];
    			$appliances[$i] = $tmp;
    		}
    	}
    }
     
    $i=0;
    while($i < $totalItem){

    	$q = ($appliances[$i]->operating_duration)%5;
    	$slots = ($appliances[$i]->operating_duration)/5;

    	if($q > 0){
    		$slots++;
    	}
    	$gSum = 999999;
    	for ($j=0; $j < (288-$slots) ; $j++) { 
    		$current_sum = 0;
    		for ($k=$j; $k < $slots; $k++) { 
    			$current_sum = $current_sum + $all_price[$k] + $accumulated_price[$k];
    		}

    		if ($current_sum < $gSum) {
    			$gSum = $current_sum;
    			$appliances[$i]->start_time = $j;
    			$appliances[$i]->end_time = $j+$slots;
    		}
    	}

    	for ($n=$appliances[$i]->start_time; $n <= $appliances[$i]->end_time; $n++) { 
    		$accumulated_price[$n] = $accumulated_price[$n] + ($all_price[$n] * $appliances[$i]->usage)/12 ;
    	}
    	$i++;
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
    while($i < $totalDayItem){

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
    $nightOffset = 0;
    while($i < $totalDayItem){

    	$q = ($appliances_night[$i]->operating_duration)%5;
    	$slots = ($appliances_night[$i]->operating_duration)/5;

    	if($q > 0){
    		$slots++;
    	}

    	$gSum = 999999;
    	for ($j=0; $j < ($totalNightSlots-$slots) ; $j++) {
		if($j<2){
    			$nightOffset = 0;
		}else{ 
    			$nightOffset = 264;
		}
    		$current_sum = 0;
    		for ($k=$j; $k < $slots; $k++) { 
    			$current_sum = $current_sum + $all_price[$k+$nightOffset] + $accumulated_price[$k+$nightOffset];
    		}

    		if ($current_sum < $gSum) {
    			$gSum = $current_sum;
    			$appliances_night[$i]->start_time = $j+$nightOffset;
    			$appliances_night[$i]->end_time = $j+$slots+$nightOffset;
    		}
    	}

	$last_rate = $all_price[$appliances_night[$i]->end_time];
	$appliances_night[$i]->total_bill = ($q * ($last_rate * $appliances_night[$i]->usage)/60) - ($last_rate * $appliances_night[$i]->usage)/12;

    	for ($n=$appliances_night[$i]->start_time; $n <= $appliances_night[$i]->end_time; $n++) { 
    		$accumulated_price[$n] = $accumulated_price[$n] + ($all_price[$n] * $appliances_night[$i]->usage)/12;
		$appliances_night[$i]->total_bill = $appliances_night[$i]->total_bill + ($all_price[$n] * $appliances_night[$i]->usage)/12;
    	}
    	$i++;
	}
 }
# END Night Appliances
?>

<html>
    <head>
        <title>PHPRO Login</title>
    </head>
    <body>
        <!--<center><?php /*echo "<font size=6 color=blue>Scheduled Appliances taking account of 5 minute Pricing for past 24 hours till ".$date." from Comed</font>"; */?><hr><hr><br>
        <table BORDER=7 CELLPADDING=7 CELLSPACING=7>
            <tr>
            <th><font size=4 color=red>Sl No.</font></th>
            <th><font size=4 color=red>Appliance</font></th>
            <th><font size=4 color=red>Start Time</font></th>
            <th><font size=4 color=red>End Time</font></th>
            </tr>

            <?php
   /*         $i=0;
            while($i < $totalItem)  {
            	$j=$i+1;
                echo "<tr>";
                echo "<td><font size=4 color=#800080>" . $j. "</font></td>";
                echo "<td><font size=4 color=#800080>" . $appliances[$i]->name. "</font></td>";
                echo "<td><font size=4 color=#800080>" . $timings[$appliances[$i]->start_time]. "</font></td>";
                echo "<td><font size=4 color=#800080>" . $timings[$appliances[$i]->end_time]. "</font></td>";
                echo "</tr>";
                $i++;
            }*/
            ?>

        </table>  
        <font color="blue" size="6"><a href="login_page.php">BACK</a></font> 
        </center>
-->
        <center><?php echo "<font size=6 color=blue>Scheduled Appliances taking account of 5 minute Pricing for past 24 hours till ".$date." from Comed</font>"; ?><hr><hr><br>
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
            while($i < $totalMorningItem)  {
                echo "<tr>";
                echo "<td><font size=4 color=#800080>" . $appliances_morning[$i]->priority. "</font></td>";
                echo "<td><font size=4 color=#800080>" .$appliances_morning[$i]->name. "</font></td>";
                echo "<td><font size=4 color=#800080>" .$timings[$appliances_morning[$i]->start_time]. "</font></td>";
                echo "<td><font size=4 color=#800080>" .$timings[$appliances_morning[$i]->end_time]. "</font></td>";
                echo "<td><font size=4 color=#800080>$ " . $appliances_morning[$i]->total_bill/100. "</font></td>";
                echo "<td><input type=\"button\" value=\"YES\"></td>";
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
            while($i < $totalDayItem)  {
            	$j=$i+1;
                echo "<tr>";
                echo "<td><font size=4 color=#800080>" . $appliances_day[$i]->priority. "</font></td>";
                echo "<td><font size=4 color=#800080>" . $appliances_day[$i]->name. "</font></td>";
                echo "<td><font size=4 color=#800080>" .$timings[$appliances_day[$i]->start_time]. "</font></td>";
                echo "<td><font size=4 color=#800080>" .$timings[$appliances_day[$i]->end_time]. "</font></td>";
                echo "<td><font size=4 color=#800080>$ " . $appliances_day[$i]->total_bill/100. "</font></td>";
                echo "<td><input type=\"button\" value=\"YES\"></td>";
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
            while($i < $totalEveningItem)  {
                echo "<tr>";
                echo "<td><font size=4 color=#800080>" . $appliances_evening[$i]->priority. "</font></td>";
                echo "<td><font size=4 color=#800080>" . $appliances_evening[$i]->name. "</font></td>";
                echo "<td><font size=4 color=#800080>" . $timings[$appliances_evening[$i]->start_time]. "</font></td>";
                echo "<td><font size=4 color=#800080>" . $timings[$appliances_evening[$i]->end_time]. "</font></td>";
                echo "<td><font size=4 color=#800080>$ " . $appliances_evening[$i]->total_bill/100. "</font></td>";
                echo "<td><input type=\"button\" value=\"YES\"></td>";
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
            while($i < $totalNightItem)  {
                echo "<tr>";
                echo "<td><font size=4 color=#800080>" . $appliances_night[$i]->priority. "</font></td>";
                echo "<td><font size=4 color=#800080>" . $appliances_night[$i]->name. "</font></td>";
                echo "<td><font size=4 color=#800080>" . $timings[$appliances_night[$i]->start_time]. "</font></td>";
                echo "<td><font size=4 color=#800080>" . $timings[$appliances_night[$i]->end_time]. "</font></td>";
                echo "<td><font size=4 color=#800080>$ " . $appliances_night[$i]->total_bill/100. "</font></td>";
                echo "<td><input type=\"button\" value=\"YES\"></td>";
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
