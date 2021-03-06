<?php
	session_start();
?>
       <html>
    <head>
        <title>Appliance Scheduling</title>
    </head>
        <style>
        body {
        background-image: url("./images/blueLight.jpeg");
        background-repeat: no-repeat;
        background-size: cover;
        background-position: center;
        }
        </style>
    <body><center>

    <?php 
	$allOptions = array(); 

	class groupedObjects{

		public $allRows;
		public $tableHeaderStr;

		public function groupedObjects(){

			$this->allRows = "";
			$this->tableHeaderStr = "<table BORDER=7 CELLPADDING=7 CELLSPACING=7>";
            		$this->tableHeaderStr .= "<tr>";
            		$this->tableHeaderStr .= "<th><font size=5 color=black>Sl No.</font></th>";
            		$this->tableHeaderStr .= "<th><font size=5 color=black>Appliance</font></th>";
            		$this->tableHeaderStr .= "<th><font size=5 color=black>Operating Duration</font><font size=3 color=black>(in min)</font></th>";
            		$this->tableHeaderStr .= "<th><font size=5 color=black>Select Priority</font></th>";
            		$this->tableHeaderStr .= "</tr>";
		}

		public function addRow($rowNum, $appliance, $operatingDuration, $totalAssignedAppliances, $whichSlot, $key){
			global $allOptions;

			$this->allRows .= "<tr>";
			$this->allRows .= "<td><font size=4 color=#800080>";
			$this->allRows .= $rowNum;
			$this->allRows .= "</font></td>";
			$this->allRows .= "<td><font size=4 color=#800080>";
			$this->allRows .= $appliance;
			$this->allRows .= "</font></td>";
			$this->allRows .= "<td><font size=4 color=#800080>";
			$this->allRows .= $operatingDuration;
			$this->allRows .= "</font></td>";
			$this->allRows .= "<td>";

			$onChangeStr = "onchange=\"configureDropDownLists( ";

			$allIds = array();

			array_push($allIds , "this");

			for($i = $rowNum+1; $i <= $totalAssignedAppliances; $i++){
				array_push($allIds, "document.getElementById('ddl".$i."_".$whichSlot."')");
			}
			
			$onChangeStr .= " ".$allIds." )\">";
			$newWhichSlot = str_replace(" ", "_", $whichSlot);
			$this->allRows .= "<select name=\"ddl".$rowNum."_".$newWhichSlot."\" id=\"ddl".$rowNum."_".$whichSlot."\" ".$onChangeStr;
			
			array_push($allOptions, "document.getElementById('ddl".$rowNum."_".$whichSlot."')");
			
			//if($rowNum == 1){
				$this->allRows .= "<option value=\"\"></option>";

				for($i = 1; $i <= $totalAssignedAppliances; $i++){
					$this->allRows .= "<option value=\"".$i.":".$key."\">".$i."</option>";
				}			
			//}

			$this->allRows .= "</select>";				

			$this->allRows .= "</td>";
			$this->allRows .= "</tr>";
		}

		public function showOnPage(){
			echo $this->tableHeaderStr;
			echo $this->allRows;
			echo "</table>";
		}
	}

        echo "<font size=10 color=blue>Please set priority to all appliances</font><hr><hr><br>";

        $message="";

	$user_id = $_SESSION['username'];

 
        $mysql_hostname = 'localhost';


        $mysql_username = 'root';


        $mysql_password = 'smarthome';


        $mysql_dbname = 'home_appliances';

	$slotNames = array('morning' => 0 ,'day' => 0 ,'evening' => 0 , 'night' => 0);
	$slotNamesCounter = array();

        $connection = mysqli_connect($mysql_hostname, $mysql_username, $mysql_password);

        if (!$connection){
            die("Database Connection Failed" . mysqli_error($connection));
        }

        $select_db = mysqli_select_db($connection, $mysql_dbname);

        if (!$select_db){die("Database Selection Failed" . mysqli_error($connection));}


        $queryAll = "SELECT * FROM appliance where user_id = '$user_id'";

        $resultAll = mysqli_query($connection, $queryAll) or die(mysqli_error($connection));
        $countAll = mysqli_num_rows($resultAll);

        $queryCount = "select count(name) as total, dayslot from appliance where user_id = '$user_id' group by (dayslot)";

        $resultCount = mysqli_query($connection, $queryCount) or die(mysqli_error($connection));
        $countCount = mysqli_num_rows($resultCount);

	if($countCount > 0){
       		while($rowCount = $resultCount->fetch_assoc()) {
			$count = $rowCount["total"];
			$dayslot = $rowCount["dayslot"];
			$slotNames[$dayslot] = $count;
			$slotNamesCounter[$dayslot] = 0;
		}
	}

	$queryDistinct = "select distinct(dayslot) as slots from appliance where user_id = '$user_id';";

        $resultDistinct = mysqli_query($connection, $queryDistinct) or die(mysqli_error($connection));
        $countDistinct = mysqli_num_rows($resultDistinct);
	
        if ($countCount > 0 && $countAll > 0 && $countDistinct > 0) {

	    $allAppliancesObjs = array();
            $mappingSlotToObject = array();

	    $j = 0;
	    while($rowDistinct = $resultDistinct->fetch_assoc()){
		$slot_name = $rowDistinct["slots"];
	    	$allAppliancesObjs[$j] = new groupedObjects();
		$mappingSlotToObject[$slot_name] = $j;
		$j++;
	    }


            while($rowAll = $resultAll->fetch_assoc()) {

		# Data Structures available:
		# $mappingSlotToObject "morning" => 0
		# $allAppliancesObjs , An array of classes
		# $slotNames, "morning" => count
		# $slotNamesCounter[$dayslot] = 0;
		# public function addRow($rowNum, $appliance, $operatingDuration, $totalAssignedAppliances, $whichSlot){

                $id = $rowAll["id"];
                $applianceName = $rowAll["name"];
                $operationHour = $rowAll["operating_duration"];
                $whichSlot = $rowAll["dayslot"];

		$index = $mappingSlotToObject[$whichSlot];
		$slotNamesCounter[$whichSlot] = $slotNamesCounter[$whichSlot] + 1;

		$allAppliancesObjs[$index]->addRow($slotNamesCounter[$whichSlot], $applianceName, $operationHour, $slotNames[$whichSlot], $whichSlot, $id);
            }

            #echo "<form name=\"schedule\" method=\"POST\" onsubmit=\"return myFunction(".$allOptions.");\">";
            echo "<form action=\"submit_set_priority.php\" method=\"POST\">";

            # Print Morning

	    if(array_key_exists("morning", $slotNames) && $slotNames["morning"] > 0){
		echo "<center><h1>These Appliances are scheduled for <font color=green size=5><b>\"Morning slot\"</b> </font>, You must set priority of each by selecting drop down menu</h1></center>";
		$allAppliancesObjs[$mappingSlotToObject["morning"]]->showOnPage();
		echo "</BR>";
	    } 

	    # Print Day

	    if(array_key_exists("day", $slotNames) && $slotNames["day"] > 0){
		echo "<center><h1>These Appliances are scheduled for <font color=green size=5><b>\"Day slot\"</b></font> , You must set priority of each by selecting drop down menu</h1></center>";
		$allAppliancesObjs[$mappingSlotToObject["day"]]->showOnPage();
		echo "</BR>";
	    } 

	    # Print Evening

	    if(array_key_exists("evening", $slotNames) && $slotNames["evening"] > 0){
		echo "<center><h1>These Appliances are scheduled for <font color=green size=5><b>\"Evening slot\"</b></font> , You must set priority of each by selecting drop down menu</h1></center>";
		$allAppliancesObjs[$mappingSlotToObject["evening"]]->showOnPage();
		echo "</BR>";
	    } 

	    # Print Night

	    if(array_key_exists("night", $slotNames) && $slotNames["night"] > 0){
		echo "<center><h1>These Appliances are scheduled for <font color=green size=5><b>\"Night slot\"</b></font> , You must set priority of each by selecting drop down menu</h1></center>";
		$allAppliancesObjs[$mappingSlotToObject["night"]]->showOnPage();
		echo "</BR>";
	    }

	    # Print Remaining All 
	    foreach($mappingSlotToObject as $key => $value){
		if($key != "morning" && $key != "day" && $key != "evening" && $key != "night"){
			echo "<center><h1>These Appliances are scheduled for <font color=green size=5><b>\"".$key." slot\"</b></font> , You must set priority of each by selecting drop down menu</h1></center>";
			$allAppliancesObjs[$value]->showOnPage();
			echo "</BR>";
		}
	    }	    
            echo "<input type=\"submit\" name=\"submit\"></form>";
        } else {

            $message = 'We are unable to process your request. Please try again later';
        }
        if(isset($message)){
                echo $message; 
        }
    ?>


        <font color="blue" size="6"><a href="login_page.php">BACK</a></font>
    </center>

<script type="text/javascript">

     function configureDropDownLists(allIds) {
//	document.write(allIds);
	
	var totalIds = allIds.length;
//	document.write(totalIds);

	if(totalIds > 1){
		var allNum = new Array(totalIds);

		for(i=0;i< allIds[0].length; i++){
			allNums[i] = allIds[0].options[i].value;
		}

		var selectedIndex = allNums.indexOf(allIds[0].value);
		totalOptions = allNums.length;
		for(i = 1;i < totalIds; i++){
			allIds[i].options.length = 0;
			for(j = 0;j < totalOptions && j != selectedIndex;j++){
        			createOption(allIds[i], allNums[j]);
			}
		}
	}
    }

    function createOption(ddl, val) {
        var opt = document.createElement('option');
        opt.value = val;
        opt.text = val;
        ddl.options.add(opt);
    }

    function myFunction(allIds) {
	var totalIds = allIds.length;
	document.write(typeof allIds);
	for(i=0;i<totalIds;i++){
		if(allIds[i].options.length == 0){ 
			alert("Please select options from drop down menu");
			document.schedule.action = "set_priority.php";
			return false;
		}
	}
		
	document.schedule.action = "submit_set_priority.php";
	return true;
    }
</script>

</body>
</html>
