
<?php
#	$page = '<tr><td>12:00 <small>AM</small></td><td>1.8&cent;</td><td>2.0&cent;</td></tr><tr><td>1:00 <small>AM</small></td><td>1.7&cent;</td><td>1.7&cent;</td></tr><tr><td>2:00 <small>AM</small></td><td>1.6&cent;</td><td>1.9&cent;</td></tr><tr><td>3:00 <small>AM</small></td><td>1.4&cent;</td><td>1.7&cent;</td></tr><tr><td>4:00 <small>AM</small></td><td>1.4&cent;</td><td>2.0&cent;</td></tr><tr><td>5:00 <small>AM</small></td><td>1.6&cent;</td><td>1.7&cent;</td></tr><tr><td>6:00 <small>AM</small></td><td>2.2&cent;</td><td>2.4&cent;</td></tr><tr><td>7:00 <small>AM</small></td><td>2.6&cent;</td><td>2.8&cent;</td></tr><tr><td>8:00 <small>AM</small></td><td>2.5&cent;</td><td>2.3&cent;</td></tr><tr><td>9:00 <small>AM</small></td><td>2.5&cent;</td><td>2.2&cent;</td></tr><tr><td>10:00 <small>AM</small></td><td>2.5&cent;</td><td>2.2&cent;</td></tr><tr><td>11:00 <small>AM</small></td><td>2.4&cent;</td><td>2.2&cent;</td></tr><tr><td>12:00 <small>PM</small></td><td>2.3&cent;</td><td>2.2&cent;</td></tr><tr><td>1:00 <small>PM</small></td><td>2.3&cent;</td><td>2.1&cent;</td></tr><tr><td>2:00 <small>PM</small></td><td>2.3&cent;</td><td>2.1&cent;</td></tr><tr><td>3:00 <small>PM</small></td><td>2.3&cent;</td><td>2.1&cent;</td></tr><tr><td>4:00 <small>PM</small></td><td>2.5&cent;</td><td>2.3&cent;</td></tr><tr><td>5:00 <small>PM</small></td><td>3.3&cent;</td><td>3.1&cent;</td></tr><tr><td>6:00 <small>PM</small></td><td>3.2&cent;</td><td>2.5&cent;</td></tr><tr><td>7:00 <small>PM</small></td><td>3.1&cent;</td><td>2.5&cent;</td></tr><tr><td>8:00 <small>PM</small></td><td>3.0&cent;</td><td>2.5&cent;</td></tr><tr><td>9:00 <small>PM</small></td><td>2.7&cent;</td><td>2.3&cent;</td></tr><tr><td>10:00 <small>PM</small></td><td>2.3&cent;</td><td>n/a </td></tr><tr><td>11:00 <small>PM</small></td><td>2.0&cent;</td><td>n/a </td></tr>';

      $page = '<tr><td>12:00 <small>AM</small></td><td>2.1&cent;</td></tr><tr><td>1:00 <small>AM</small></td><td>2.0&cent;</td></tr><tr><td>2:00 <small>AM</small></td><td>1.9&cent;</td></tr><tr><td>3:00 <small>AM</small></td><td>1.8&cent;</td></tr><tr><td>4:00 <small>AM</small></td><td>1.7&cent;</td></tr><tr><td>5:00 <small>AM</small></td><td>1.7&cent;</td></tr><tr><td>6:00 <small>AM</small></td><td>1.8&cent;</td></tr><tr><td>7:00 <small>AM</small></td><td>2.0&cent;</td></tr><tr><td>8:00 <small>AM</small></td><td>2.3&cent;</td></tr><tr><td>9:00 <small>AM</small></td><td>2.5&cent;</td></tr><tr><td>10:00 <small>AM</small></td><td>2.7&cent;</td></tr><tr><td>11:00 <small>AM</small></td><td>3.1&cent;</td></tr><tr><td>12:00 <small>PM</small></td><td>3.5&cent;</td></tr><tr><td>1:00 <small>PM</small></td><td>4.0&cent;</td></tr><tr><td>2:00 <small>PM</small></td><td>4.3&cent;</td></tr><tr><td>3:00 <small>PM</small></td><td>4.8&cent;</td></tr><tr><td>4:00 <small>PM</small></td><td>5.2&cent;</td></tr><tr><td>5:00 <small>PM</small></td><td>5.3&cent;</td></tr><tr><td>6:00 <small>PM</small></td><td>4.6&cent;</td></tr><tr><td>7:00 <small>PM</small></td><td>4.0&cent;</td></tr><tr><td>8:00 <small>PM</small></td><td>3.5&cent;</td></tr><tr><td>9:00 <small>PM</small></td><td>3.1&cent;</td></tr><tr><td>10:00 <small>PM</small></td><td>2.6&cent;</td></tr><tr><td>11:00 <small>PM</small></td><td>2.4&cent;</td></tr>';

	$rate="";
	//$pattern = "#<td>[\d.]+\&cent;</td><td>#";
	$pattern = "#<td>[\d.]+\&cent;</td>#";
	$cent = "&cent;</td>";

	if(preg_match_all($pattern, $page, $matches)){
		foreach ($matches[0] as $key => $value) {
			$value = str_ireplace($cent, "", $value);
			$value = str_ireplace("<td>", "", $value);

			if($rate == ""){
				$rate = $value;
			}else{
				$rate = $rate.":".$value;
			}
		}
	}

	echo $rate."<BR>";
?>
