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
		$newAppliance->dayslot = $row["dayslot"];
                $newAppliance->setTotal_consumption();
                $appliances[$totalItem] = $newAppliance;
                $totalItem++;
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

	$last_rate = $all_price[$appliances[$i]->end_time];
	$appliances[$i]->total_bill = ($q * ($last_rate * $appliances[$i]->usage)/60) - ($last_rate * $appliances[$i]->usage)/12;

    	for ($n=$appliances[$i]->start_time; $n <= $appliances[$i]->end_time; $n++) { 
    		$accumulated_price[$n] = $accumulated_price[$n] + ($all_price[$n] * $appliances[$i]->usage)/12;
		$appliances[$i]->total_bill = $appliances[$i]->total_bill + ($all_price[$n] * $appliances[$i]->usage)/12;
    	}
	$appliances[$i]->total_bill = round(($appliances[$i]->total_bill / 100), 2);
    	$i++;
	}

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
        <center><?php echo "<font size=6 color=blue>Scheduled Appliances taking account of 5 minute Pricing for past 24 hours till ".$date." from Comed</font>"; ?><hr><hr><br>
        <table BORDER=7 CELLPADDING=7 CELLSPACING=7>
            <tr>
            <th><font size=4 color=red>Sl No.</font></th>
            <th><font size=4 color=red>Appliance</font></th>
            <th><font size=4 color=red>Start Time</font></th>
	    <th><font size=4 color=red>End Time</font></th>
	    <th><font size=4 color=red>Total Bill Amount</font><font color=black size=3> (To be paid) </font></th>
	    <th><font size=3 color=blue>Is it OKAY to you to schedule it?</font></th>
	    </tr>

            <?php
            $i=0;
            while($i < $totalItem)  {
                echo "<tr>";
                echo "<td><font size=4 color=#800080>" . $appliances[$i]->priority. "</font></td>";
                echo "<td><font size=4 color=#800080>" . $appliances[$i]->name. "</font></td>";
                echo "<td><font size=4 color=#800080>" . $timings[$appliances[$i]->start_time]. "</font></td>";
                echo "<td><font size=4 color=#800080>" . $timings[$appliances[$i]->end_time]. "</font></td>";
		echo "<td><font size=4 color=#800080>$ " .$appliances[$i]->total_bill. "</font></td>";
		echo "<td><input type=\"button\" value=\"YES\" id=\"button".$i."\" style= \"color:#2F4F4F\" onclick=\"setColor('button".$i."')\";/></td>";
		#echo "<td><input type=\"button\" value=\"YES\"></td>";
                echo "</tr>";
                $i++;
            }
            ?>

        </table>  
        <font color="blue" size="6"><a href="login_page.php">BACK</a></font> 
        </center>

    </body>
</html>
