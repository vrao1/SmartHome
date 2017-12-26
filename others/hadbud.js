
function checkButton() {
	var items = "<?php echo $totalItem ?>";
	var i = 0;
	var chanda = "button";
	var papa = chanda + i.toString();
	var allChecked = document.getElementsByName(papa);
	var number = allChecked.length;
	var isChecked = false;

	for (var j=0;j<number;j++){
		if ( allChecked[j].checked == true){
			isChecked = true;
			break;
		}	
	}

	if (isChecked == true){
		document.form1.action = "bhalo.php";
	}else{
		document.form1.action = "kemon_achen.php";
		return false;
	}

	return true;
}
