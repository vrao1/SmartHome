
	<?php

	require('itemClass.php');
//	session_start();

	  $nullSlotAppliance = [];
	   $nullSlotAppliance[0] = new itemClass();
	 //  $nullSlotAppliance[1] = new itemClass();

	   $totalNullSlotAppliance = 1;

	    $nullSlotAppliance[0]->priority = 2;

//	$_SESSION['obj'] = $nullSlotAppliance;

   	    $nullSlotAppliance[0]->name = "Aloo Chat";
	    $nullSlotAppliance[0]->usage = 567;
	    $nullSlotAppliance[0]->operating_duration = 123;

/*
	    $nullSlotAppliance[1]->priority = 1;
	    $nullSlotAppliance[1]->name = "Samosa Chat";
	    $nullSlotAppliance[1]->usage = 5643;
	    $nullSlotAppliance[1]->operating_duration = 349;

*/
	    $i = 0;
	    echo "<table>";
            while($i < $totalNullSlotAppliance){
                echo "<tr>";
                echo "<td><font size=4 color=#800080>" . $nullSlotAppliance->priority. "</font></td>";
                
		echo "<td><font size=4 color=#800080>" . $nullSlotAppliance[$i]->name. "</font></td>";
                echo "<td><font size=4 color=#800080>" . $nullSlotAppliance[$i]->usage. "</font></td>";
                echo "<td><font size=4 color=#800080>" . $nullSlotAppliance[$i]->operating_duration. "</font></td>";
  

              echo "</tr>";
                $i++;
            }
            echo "</table>";
	    echo "<form id=\"reassign\" name=\"reassign\" action=\"reassign_teja.php\" method=\"POST\">";
            echo "<font size=4 color=green>To Re-Assign slots please click this button </font>";
            echo "<input type=hidden id=appliances_str name=appliances_str value=". base64_encode(serialize($nullSlotAppliance)) ." >";
	    echo "<input type=submit value=Re-Assign >";
	    echo "</form>";
	
	?>
