<html>
        <style>
        body {
        background-image: url(“./images/blue.gif");
        background-repeat: no-repeat;
        background-size: cover;
        background-position: center;
        }
        </style>
<body>
<center>
<?php
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

    if (isset($_POST['username']) and isset($_POST['password'])){

        $username = $_POST['username'];
        $password = $_POST['password'];
        $query = "INSERT INTO login (id,password) VALUES('$username' ,'$password')";

        $result = mysqli_query($connection, $query) or die(mysqli_error($connection));

        if ($result){
	    $fmsg = "You are successfully signed up !";

        }else{
            $fmsg = "Invalid Username please try again.";
        }
    }

	echo "<font color=red size=5>$fmsg</font>";
	echo "</BR><font color=green size=5>Please Re-Login <a href=index.html>here</a></font>";
?>
</center>
</body>
</html>
