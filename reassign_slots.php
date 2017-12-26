<?php
	session_start();
?>

       <html>
    <head>
        <title>Appliance Scheduling</title>
    </head>
    <body><center>

    <?php 

	require('item.php');
        echo "<font size=10 color=blue>Please set time slots to all appliances</font><hr><hr><br>";

	$appliances_str = $_POST["appliances_str"];

	$nullSlotAppliance = unserialize(base64_decode($appliances_str));

	$allApplianceIds = array();
	$tashan = 0;

        $message="";

	$user_id = $_SESSION['username'];

 
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


        $query_2 = "SELECT * FROM time_slots where user_id = '$user_id'";

        $result_2 = mysqli_query($connection, $query_2) or die(mysqli_error($connection));
        $count_2 = mysqli_num_rows($result_2);

        if ($count_2 > 0) {

            echo "<form action=\"write_appliance_slots.php\" method=\"POST\"><table BORDER=7 CELLPADDING=7 CELLSPACING=7>";

		$periodLengthVsOption = array();

		$slots_val = "";
            	while($row_2 = $result_2->fetch_assoc()) {
			$name = $row_2["slot_name"];
			$start = $row_2["start"];
			$end = $row_2["end"];
			$opt_str = "<option value=\"".$name."\">".$name." (".$start." - ".$end.")</option>";

			$date1 = "2011-10-10 ".$start;
			$date2 = "2011-10-10 ".$end;

			$diff =  round((strtotime($date2) - strtotime($date1)) /60);
			$diff = $diff < 0 ? -1*$diff : $diff;
			
			$periodLengthVsOption[$diff] = $opt_str;

			#$slots_val = $slots_val.$opt_str;
		}
            
            echo "<tr>";
            echo "<th><font size=5 color=red>Appliance</font></th>";
            echo "<th><font size=5 color=red>Operating Duration</font><font size=3 color=black>(in min)</font></th>";
            echo "<th><font size=5 color=red>Select Day Slot</font></th>";
            echo "</tr>";

            foreach($nullSlotAppliance as $appliance) {

		$new_slots_val = "";

		$time_length = $appliance->operating_duration;
		foreach($periodLengthVsOption as $key => $value){
			if($time_length <= $key){
				$new_slots_val .= $value;
			}
		}
	
		$slots = "<td><select id = \"menu\" name=\"slot_" .$row["id"]. "\" onchange=\"javascript:handleSelect(this," .$appliance->id . ")\"><option value=\"\"></option>".$new_slots_val."<option value=\"customize_timeslot.php\">Assign Customize Time Slot</option></select></td>";

                echo "<tr>";
                echo "<td><font size=4 color=#800080>" . $appliance->name. "</font></td>";
                echo "<td><font size=4 color=#800080>" . $appliance->operating_duration. "</font></td>";

                echo $slots;

		$allApplianceIds[$tashan] = $appliance->id;

                echo "</tr>";
		$tashan++;
            }
            echo "</table>";

            echo "<input type=hidden id=appliance_id name=appliance_id value=". base64_encode(serialize($allApplianceIds)) ." >";
            echo "<input type=\"submit\" name=\"submit\"></form>";
        } else {

            $message = 'We are unable to process your request. Please try again later';
        }
        if(isset($message)){
                echo $message; 
        }
    ?>


        <font color="blue" size="6"><a href="login_page.php">BACK</a></font>
    </center>

<script type="text/javascript">
function handleSelect(elm, apid){
	if(elm.value.match(/\.php$/)){ 
		var url = elm.value + "?id=" +apid;
		window.open(url);
		return true;
	}else{
		return false;
	}
}
// var urlmenu = document.getElementById( 'menu' );
// urlmenu.onchange = function() {
//      window.open( this.options[ this.selectedIndex ].value );
// };
</script>

</body>
</html>
