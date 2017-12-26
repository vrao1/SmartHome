<?php
	session_start();
?>

       <html>
    <head>
        <title>Appliance Scheduling</title>
    </head>
        <style>
        body {
        background-image: url("./images/blueLight.jpeg");
        background-repeat: no-repeat;
        background-size: cover;
        background-position: center;
        }
        </style>
    <body><center>

    <?php 

        echo "<font size=10 color=blue>Please set time slots to all appliances</font><hr><hr><br>";

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


        $query = "SELECT * FROM appliance where user_id = '$user_id'";

        $result = mysqli_query($connection, $query) or die(mysqli_error($connection));
        $count = mysqli_num_rows($result);

        $query_2 = "SELECT * FROM time_slots where user_id = '$user_id'";

        $result_2 = mysqli_query($connection, $query_2) or die(mysqli_error($connection));
        $count_2 = mysqli_num_rows($result_2);

        if ($count_2 > 0 && $count > 0) {

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
            echo "<th><font size=5 color=black>Sl No.</font></th>";
            echo "<th><font size=5 color=black>Appliance</font></th>";
            echo "<th><font size=5 color=black>Operating Duration</font><font size=3 color=black>(in min)</font></th>";
            echo "<th><font size=5 color=black>Select Day Slot</font></th>";
            echo "</tr>";

	    $appliance_IDs = array();
	    $record_counter = 1;
            while($row = $result->fetch_assoc()) {

		$new_slots_val = "";

		$time_length = $row["operating_duration"];
		foreach($periodLengthVsOption as $key => $value){
			if($time_length <= $key){
				$new_slots_val .= $value;
			}
		}
	
		$slots = "<td><select id = \"menu\" name=\"slot_" .$row["id"]. "\" onchange=\"javascript:handleSelect(this)\"><option value=\"\"></option>".$new_slots_val."<option value=\"customize_timeslot.php\">Assign Customize Time Slot</option></select></td>";

                echo "<tr>";
                echo "<td><font size=4 color=#800080>" . $record_counter. "</font></td>";
		$appliance_IDs[$record_counter-1] = $row["id"];
                echo "<td><font size=4 color=#800080>" . $row["name"]. "</font></td>";
                echo "<td><font size=4 color=#800080>" . $row["operating_duration"]. "</font></td>";
                echo $slots;
                echo "</tr>";
		$record_counter++;
            }
            echo "</table>";
	    echo "<input type=hidden id=appliance_ids name=appliance_ids value=". base64_encode(serialize($appliance_IDs)) ." >";
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
function handleSelect(elm){
	if(elm.value.match(/\.php$/)){ 
		window.open(elm.value);
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
