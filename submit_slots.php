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

    $flag=TRUE;
    $num=1;
    $slotNames = array("morning","day","evening","night");
    $slot_counter = 0;
    while($flag == TRUE){
	$sname = $slotNames[$slot_counter];
        $option_val_start = "ddl".$num;
	$option_val_end = "ddl".($num+1);

        if (isset($_POST[$option_val_start]) and isset($_POST[$option_val_end])){
            $start = $_POST[$option_val_start];
	    $end = $_POST[$option_val_end];
            $query = "insert into time_slots (slot_name,start,end,user_id) values ('$sname', '$start', '$end','$user_id') on duplicate key update slot_name = '$sname' , start = '$start', end='$end';";
            $result = mysqli_query($connection, $query) or die(mysqli_error($connection));
            if ($result == TRUE){
                $message = "WOW : Records are updated successfully into DB";
            }else{
                $message = "ERROR : Records are not updated into DB";
            }
        }
        else
        {
            $flag = FALSE;
        }
        $num = $num + 2;
	$slot_counter = $slot_counter + 1;
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
