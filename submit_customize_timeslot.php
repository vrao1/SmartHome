    <?php

    session_start();

    require('item.php');
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
    $slot_counter = 0;
    while($flag == TRUE){
        $option_val_start = "ddl".$num;
	$option_val_end = "ddl".($num+1);

        if (isset($_POST['appliance_id']) and isset($_POST[$option_val_start]) and isset($_POST[$option_val_end])){

	    $appliance_id = $_POST["appliance_id"];

            $start = $_POST[$option_val_start];
	    $end = $_POST[$option_val_end];

	    $sname = $_POST["customized_name"];
            $query_1 = "insert into time_slots (slot_name,start,end,user_id) values ('$sname', '$start', '$end','$user_id') on duplicate key update slot_name = '$sname' , start = '$start', end='$end';";
            $result_1 = mysqli_query($connection, $query_1) or die(mysqli_error($connection));

            $query_2 = "update appliance set priority='1' , dayslot = '$sname' where id = '$appliance_id';";
            $result_2 = mysqli_query($connection, $query_2) or die(mysqli_error($connection));

            if ($result_1 == TRUE and $result_2 == TRUE){
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
