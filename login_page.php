    <?php

    session_start();
   
    $user_id = $_SESSION['username'];
    $message = "Hi " . $user_id . " ! Welcome to your home";
    ?>

    <html>
    <head>
        <title>PHPRO Login</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    </head>
	<style>
	body {
    	background-image: url("./images/makan.png");
    	background-repeat: no-repeat;
	background-size: cover;
	background-position: center;
	}
	</style>
    <body>
        <center>
	<?php echo "<h1><font color=\"black\">$message</font></h1>"; 
	?>
	<HR>
	<div style="text-align:right"><h3><a href="logout.php">Logout</a></h3></div>
	<HR><br>
        <table><tr><td>
        <h2><font color="purple"><a href="add_appliance.html"> Add New Home Appliances</a></font></h2></td></tr>
	<tr><td>
        <h2><font color="purple"><a href="set_slots.html"> Set Slots for 24 hours</a></font></h2></td></tr>
	<tr><td>
        <h2><font color="purple"><a href="assign_slots.php"> Assign Day Slots to all appliances</a></font></h2></td></tr>
	<tr><td>
        <h2><font color="purple"><a href="set_priority.php"> Assign priority to schedule all appliances</a></font></h2></td></tr>
	<tr><td>
        <h2><font color="purple"><a href="show_db.php"> Show priorities & day slots for appliances</a></font></h2></td></tr>
	<tr><td>
<div class="container">
    <h2><a href="schedule_dah.php" data-toggle="tooltip" data-placement="right" title="Get the most economic schedule across 24 hours for each appliance irrespective to the slots they are supposed to operate within !"><font color="purple"> Get Schedule beyond assigned slots</font></a></h2>
</div>
</td></tr>
	<tr><td>
<div class="container">
    <h2><a href="slot_schedule_dah.php" data-toggle="tooltip" data-placement="right" title="Get the most economic schedule for each appliance within respective assigned slots !"><font color="purple"> Get Schedule within assigned slots</font></a></h2>
</div>
</td></tr>
        </table>   
        </center>

<script>
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();   
});
</script>
    </body>
    </html>
