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
<BR>
<BR>
<BR>
<BR>
<BR>
<BR>
<BR>
<BR>
<center>
<?php
	$_SESSION = array();
	session_destroy();

	echo "<font color=black size=10>You are successfully logged out !</font>";
	echo "</BR><font color=black size=10>Please Re-Login <h2><a href=index.html>here</a></h2></font>";
?>
</center>
</body>
</html>
