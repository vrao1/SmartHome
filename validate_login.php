<html>
        <style>
        body {
        background-image: url("./images/cat.jpg");
        background-repeat: no-repeat;
        background-size: cover;
        background-position: center;
        }
        </style>
<body>    
<?php

    session_start();
   
    $message="hi";
 
    $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
    $password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);

    $password = sha1( $password );

    $mysql_hostname = 'localhost';

    $mysql_username = 'root';

    $mysql_password = 'smarthome';

    $mysql_dbname = 'home_appliances';
    $goAhead = True;

    $connection = mysqli_connect($mysql_hostname, $mysql_username, $mysql_password);
    
    if (!$connection){
        die("Database Connection Failed" . mysqli_error($connection));
    }

    $select_db = mysqli_select_db($connection, $mysql_dbname);
    
    if (!$select_db){die("Database Selection Failed" . mysqli_error($connection));}

    if (isset($_POST['username']) and isset($_POST['password'])){
        $username = $_POST['username'];
        $password = $_POST['password'];
        $query = "SELECT * FROM `login` WHERE id='$username' and password='$password'";

        $result = mysqli_query($connection, $query) or die(mysqli_error($connection));
	$count = mysqli_num_rows($result);
       
        if ($count == 1){
		$goAhead = true;
        }else{
		$goAhead = false;
        }
    }
    if ($goAhead == True){
        $_SESSION['username'] = $username;
	header('Location: login_page.php');
    }
    else
    {
	echo "<center><font color=black size=7>Login Cblackentials are not valid. Please try again.</font></center><BR><BR>";
	echo "<center><font color=blue size=5><a href=\"index.html\">BACK</a></font></center>";
    }
    
    ?>
</body>
</html>
