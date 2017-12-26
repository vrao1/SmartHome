    <?php

    session_start();
    $message="hi";
    $user_id = $_SESSION['username']; 

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
    if (isset($_POST['appliancename']) and isset($_POST['watt']) and isset($_POST['hours']) and isset($_POST['minutes'])){
    //3.1.1 Assigning posted values to variables.
        $appliancename = filter_var($_POST['appliancename'], FILTER_SANITIZE_STRING);
        $watt = filter_var($_POST['watt'], FILTER_SANITIZE_STRING);
        $hours = filter_var($_POST['hours'], FILTER_SANITIZE_STRING);
        $minutes = filter_var($_POST['minutes'], FILTER_SANITIZE_STRING);
        $minutes = $minutes + ($hours*60);
    
    //3.1.2 Checking the values are existing in the database or not
        $query = "INSERT INTO `appliance` (`name`,`usage`,`operating_duration`, `user_id`) VALUES ('$appliancename','$watt','$minutes','$user_id')";

        $result = mysqli_query($connection, $query) or die(mysqli_error($connection));
        
    //3.1.2 If the posted values are equal to the database values, then session will be created for the user.
        if ($result == TRUE){
            $message = "WOW : Records are added successfully into DB";
        }else{
    //3.1.3 If the login credentials doesn't match, he will be shown with an error message.
            $message = "ERROR : Records are not added into DB";
        }
    }
    else
    {
        /*** if we are here, something has gone wrong with the database ***/
        $message = 'We are unable to process your request. Please modify the entered records and try again';
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
        <center><?php echo $message; ?><hr><hr><br>
        <table><tr><td>
        <font color="blue" size="6"><a href="add_appliance.html">BACK</a></font></td></tr><tr><td>
        </td></tr>
        </table>   
        </center>

    </body>
    </html>
