    <?php
    session_start();
    $user_id = $_SESSION['username']; 

    $message="hi";
    
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

	$query_2 = "select count(name) as total, dayslot from appliance where user_id = '$user_id' group by dayslot;";

#       echo $query_2;

        $result_2 = mysqli_query($connection, $query_2) or die(mysqli_error($connection));
        $count_2 = mysqli_num_rows($result_2);
	$slotNames = array();

	if($count_2 > 0){
                while($row_2 = $result_2->fetch_assoc()) {
                        $count = $row_2["total"];
                        $dayslot = $row_2["dayslot"];
                        $slotNames[$dayslot] = $count;
                }
        }

    $FLAG = false;
    $findDuplicateElem = array();

    if($count_2>0){
	foreach($slotNames as $key => $value){
		$slot_counter=1;
		while($slot_counter <= $value){
			$newKey = str_replace(" ","_",$key);
			$option_val = "ddl".$slot_counter."_".$newKey;
        		if (isset($_POST[$option_val])){
            			$elem = $_POST[$option_val];
				$pieces = explode(":", $elem);

				if(array_key_exists($key, $findDuplicateElem) && $findDuplicateElem[$key] == $pieces[0]){
            				$message = "Please select different priority for ".$key." slot by going back to set priority page";
					$FLAG = TRUE;
					break;
				}else{
					$findDuplicateElem[$key] = $pieces[0];
				}

            			$query = "update appliance set priority = '$pieces[0]' where id = '$pieces[1]';";
            			$result = mysqli_query($connection, $query) or die(mysqli_error($connection));
            			if ($result == TRUE){
                			$message = "WOW : Records are updated successfully into DB";
            			}else{
                			$message = "ERROR : Records are not updated into DB";
					break;
					$FLAG = TRUE;
            			}
        		}
        		else
        		{
            			$message = "Please select priority again by going back to set priority page";
				$FLAG = TRUE;
				break;
        		}
			$slot_counter = $slot_counter + 1;
		}
		if($FLAG == TRUE){
			break;
		}
	}
    }
    ?>

    <html>
    <head>
        <title>SUBMISSION</title>
    </head>
        <style>
        body {
        background-image: url("./images/cat.jpg");
        background-repeat: no-repeat;
        background-size: cover;
        background-position: center;
        }
        </style>
    <body>
        <center><font size="7" color="red"><?php echo $message; ?></font><hr><hr><br>
            <table><tr><td>
		<?php
			if($FLAG == TRUE){ 
		echo "<font color=\"blue\" size=\"6\"><a href=\"set_priority.php\">BACK</a></font></td></tr><tr><td>";
			}else{
		echo "<font color=\"blue\" size=\"6\"><a href=\"login_page.php\">HOME</a></font></td></tr><tr><td>";
			}
                ?>
            </td></tr>
        </table>   
    </center>

</body>
</html>
