    <?php

    /*** begin our session ***/
    session_start();
   
    $message="hi";
    
    /*** if we are here the data is valid and we can insert it into database ***/
    $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
    $password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);

    /*** now we can encrypt the password ***/
    $password = sha1( $password );

    /*** connect to database ***/
    /*** mysql hostname ***/
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
    if (isset($_POST['username']) and isset($_POST['password'])){
    //3.1.1 Assigning posted values to variables.
        $username = $_POST['username'];
        $password = $_POST['password'];
    //3.1.2 Checking the values are existing in the database or not
        $query = "SELECT * FROM `login` WHERE id='$username' and password='$password'";

        $result = mysqli_query($connection, $query) or die(mysqli_error($connection));
        $count = mysqli_num_rows($result);
    //3.1.2 If the posted values are equal to the database values, then session will be created for the user.
        if ($count == 1){
            $_SESSION['username'] = $username;
        }else{
    //3.1.3 If the login credentials doesn't match, he will be shown with an error message.
            $fmsg = "Invalid Login Credentials.";
        }
    }
    //3.1.4 if the user is logged in Greets the user with message
    if (isset($_SESSION['username'])){
        $username = $_SESSION['username'];
        $message = "<font size=7 color=red> Hi " . $username . "! Welcome to your home";

    }
    else
    {
        /*** if we are here, something has gone wrong with the database ***/
        $message = 'We are unable to process your request. Please try again later"';
    }
    
    ?>

    <html>
    <head>
        <title>PHPRO Login</title>
    </head>
    <body>
        <center><?php echo $message; ?><hr><hr><br>
        <table><tr><td>
        <font color="blue" size="6"><a href="add_appliance.html">Add New Home Appliances</a></font></td></tr><tr><td>
        <font color="blue" size="6"><a href="set_priority.php">Set Day Slot and priority to schedule all appliances</a></font></td></tr><tr><td>
        <font color="blue" size="6"><a href="show_db.php">Show priorities and day slots for each appliances</a></font></td></tr><tr><td>
        <font color="blue" size="6">Day Ahead Hourly Pricing -> </font></td></tr>
	<tr><td><font color="red" size="5"><a href="schedule_dah.php">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Get Schedule without accounting day slots</a></font></td></tr><tr><td>
	<tr><td><font color="red" size="5"><a href="slot_schedule_dah.php">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Get Schedule for each four slots of the day</a></font></td></tr><tr><td>
        <font color="blue" size="6">Last 24 hours Pricing -> </font></td></tr>
        <tr><td><font color="red" size="5"><a href="schedule_last24h.php">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Get Schedule without accounting day slots</a></font></td></tr>
        <!--<tr><td><font color="red" size="5"><a href="slot_schedule_last24h.php">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Get Schedule for each four slots of the day</a></font></td></tr>-->
        </table>   
        </center>

    </body>
    </html>
