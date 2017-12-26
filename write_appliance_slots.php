    <?php

    session_start(); 

    $appliance_ids = $_POST['appliance_ids'];
    $appliance_IDs = unserialize(base64_decode($appliance_ids));

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

    foreach($appliance_IDs as $ap_id){

	$slot_option = "slot_".$ap_id;

        if (isset($_POST[$slot_option])){
	    $day_slot = $_POST[$slot_option];

	    if(strlen($day_slot) > 0){

            	$query = "UPDATE `appliance` SET `dayslot` = '$day_slot' WHERE `id` = '$ap_id'";
            	$result = mysqli_query($connection, $query) or die(mysqli_error($connection));
            	if ($result == TRUE){
                	$message = "WOW : Records are updated successfully into DB";
            	}else{
                	$message = "ERROR : Records are not updated into DB";
            	}
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
                <font color="blue" size="6"><a href="login_page.php">HOME</a></font></td></tr><tr><td>
            </td></tr>
        </table>   
    </center>

</body>
</html>
