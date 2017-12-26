<html>
    <head>
    <title> Example of the radio checked property</title>
    </head>
    <body>
    <form name="form1" id="form1" method="post" onSubmit="return checkButton()" >
<table>
           <?php
	    $totalItem=1;
            $i = 0;
            $j = 0;
            
	   //while($i < $totalItem)  {
           //     $j = $i + 1;
                echo "<tr>";
                echo "<td><input name = \"button".$i."\" type=radio id=\"button_yes".$i."\" value=\"YES\"><label>YES</label></td>";
                echo "<td><input name = \"button".$i."\" type=radio id=\"button_no".$i."\" value=\"NO\"><label>NO</label></td>";
                echo "</tr>";
            //    $i++;
            //}
            ?>

</table>
    <!--<INPUT type="button" value="Get Checked" onClick='checkButton()'>-->
    <!--<INPUT type="submit" value="Get Checked" onClick='checkButton()'>-->

    <INPUT type="submit" value="Get Checked">

    </form>
    </body>

<script src="hadbud.js"></script>

    </html>
