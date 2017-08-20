    <?php
    
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
   
    while($flag == TRUE){

        $option_val = "priority_".$num;
	$slot_option = "slot_".$num;

        if (isset($_POST[$option_val])){
            $priority = $_POST[$option_val];
	    $day_slot = $_POST[$slot_option];
            $query = "UPDATE `appliance` SET `priority` = '$priority' , `dayslot` = '$day_slot' WHERE `id` = '$num'";
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
        $num = $num + 1;
    }
    ?>

    <html>
    <head>
        <title>SUBMISSION</title>
    </head>
    <body>
        <center><font size="7" color="red"><?php echo $message; ?></font><hr><hr><br>
            <table><tr><td>
                <font color="blue" size="6"><a href="login_page.php">HOME</a></font></td></tr><tr><td>
            </td></tr>
        </table>   
    </center>

</body>
</html>
