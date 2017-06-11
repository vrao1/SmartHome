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

	$totalItem = 0;
	$appliances = array();

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
                $newAppliance->setTotal_consumption();
                $appliances[$totalItem] = $newAppliance;
                $totalItem++;
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

    for ($i=0; $i < $totalItem-1; $i++) { 
    	for ($j=$i+1; $j < $totalItem; $j++) { 
    		if($appliances[$i]->priority < $appliances[$j]->priority){
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
?>

<html>
    <head>
        <title>PHPRO Login</title>
    </head>
    <body>
        <center><?php echo "<font size=6 color=blue>Scheduled Appliances taking account of Day Ahead Pricing from Comed for the date ".$date."</font>"; ?><hr><hr><br>
        <table BORDER=7 CELLPADDING=7 CELLSPACING=7>
            <tr>
            <th><font size=4 color=red>Sl No.</font></th>
            <th><font size=4 color=red>Appliance</font></th>
            <th><font size=4 color=red>Start Time</font></th>
            <th><font size=4 color=red>End Time</font></th>
            </tr>

            <?php
            $i=0;
            while($i < $totalItem)  {
            	$j=$i+1;
                echo "<tr>";
                echo "<td><font size=4 color=#800080>" . $j. "</font></td>";
                echo "<td><font size=4 color=#800080>" . $appliances[$i]->name. "</font></td>";
                echo "<td><font size=4 color=#800080>" . $date." ".$timings[$appliances[$i]->start_time]. "</font></td>";
                echo "<td><font size=4 color=#800080>" . $date." ".$timings[$appliances[$i]->end_time]. "</font></td>";
                echo "</tr>";
                $i++;
            }
            ?>

        </table>  
        <font color="blue" size="6"><a href="login_page.php">BACK</a></font> 
        </center>

    </body>
</html>