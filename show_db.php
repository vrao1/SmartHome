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
	$user_id = $_SESSION['username'];

        echo "<font size=10 color=blue>List of priority and assigned dayslots to all appliances</font><hr><hr><br>";

        $message="";

 
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


        $query = "select A.name,A.priority,A.operating_duration,B.start,B.end , A.dayslot from appliance A, time_slots B where A.dayslot = B.slot_name and A.user_id = B.user_id and A.user_id = '$user_id';";

        $result = mysqli_query($connection, $query) or die(mysqli_error($connection));
        $count = mysqli_num_rows($result);

        if ($count > 0) {
            echo "<table BORDER=7 CELLPADDING=7 CELLSPACING=7>";	 
            echo "<tr>";
            echo "<th><font size=5 color=black>Priority No.</font></th>";
            echo "<th><font size=5 color=black>Appliance</font></th>";
            echo "<th><font size=5 color=black>Operating Duration</font><font size=3 color=black>(in min)</font></th>";
            echo "<th><font size=5 color=black>Assigned Day Slot</font></th>";
            echo "<th><font size=5 color=black>Start Timing</font></th>";
            echo "<th><font size=5 color=black>End Timing</font></th>";
            echo "</tr>";

            while($row = $result->fetch_assoc()) {
		$timeslot="";
		if($row["dayslot"] == 'M'){
			$timeslot="Morning (5AM - 9AM)";
		}else if($row["dayslot"] == 'D'){
			$timeslot="Day (9AM - 5PM)";
		}else if($row["dayslot"] == 'E'){
			$timeslot="Evening (5PM - 10PM)";
		}else if($row["dayslot"] == 'N'){
			$timeslot="Night (10PM - 5AM)";
		}
 
                echo "<tr>";
                echo "<td><font size=4 color=#800080>" . $row["priority"]. "</font></td>";
                echo "<td><font size=4 color=#800080>" . $row["name"]. "</font></td>";
                echo "<td><font size=4 color=#800080>" . $row["operating_duration"]. "</font></td>";
                echo "<td><font size=4 color=#800080>" . $row["dayslot"]. "</font></td>";
                echo "<td><font size=4 color=#800080>" . $row["start"]. "</font></td>";
                echo "<td><font size=4 color=#800080>" . $row["end"]. "</font></td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {

            $message = 'We are unable to process your request. Please try again later';
        }
        if(isset($message)){
                echo "<font color=black size=6>".$message."</font><BR><BR>"; 
        }
    ?>


        <font color="blue" size="6"><a href="login_page.php">BACK</a></font>
    </center>

</body>
</html>
