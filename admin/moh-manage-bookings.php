<?php 
//callback function
// global $pagenow;
// var_dump($pagenow);

function manage_bookings_callback(){
?>
<div class="month"> 

  <ul>
    <li class="prev" id="prevMonth">&#10094;</li>
    <li class="next" id="nextMonth">&#10095;</li>
    <li style="text-align:center"id="display-month">
     
      <li style="font-size:18px;text-align:center" id="display-year"></li>
  </ul>
</div>
<div id="marieDiv"></div>  





<?php


}//end callback function
?>
















<!-- 	echo"this is admin pg";
	$date = time();
//$date = strtotime("+1 month");
$day = date('d', $date);
$month = date('m', $date);
$year = date('Y', $date);

$first_day = mktime(0,0,0,$month, 1, $year);
$title = date('F', $first_day);
$day_of_week = date('D', $first_day);

switch($day_of_week){
	case "Sun": $blank = 0; break;
	case "Mon": $blank = 1; break;
	case "Tues": $blank = 2; break;
	case "Wed": $blank = 3; break;
	case "Thurs": $blank = 4; break;
	case "Fri": $blank = 5; break;
	case "Sat": $blank = 6; break;

}


	
$getnext = $date; 
$getnext = strtotime("+1 month");
// $getnextmonth= strtotime("+1 month");
$nextmonth = date('F', $getnext); 

$days_in_month = cal_days_in_month(0, $month, $year);

echo "<table class='myCal' id='moh-cal'>";
echo "<tr><th colspan=500>" . $title . $year . "</th>";
echo "<tr><td>S</td><td>M</td><td>T</td>
<td>W</td><td>T</td><td>F</td>
<td>S</td></tr>";

$day_count = 1;

echo "<tr>";

while( $blank >0){
	echo "<td></td>";
	$blank = $blank-1;
	$day_count++;
}

$day_num = 01;

/* start of queries*/

/* end of queries*/

while($day_num <= $days_in_month){
$arrive = $year."-".$month."-".str_pad($day_num, 2, "0", STR_PAD_LEFT);
$depart = $year."-".$month."-".str_pad($day_num+1, 2, "0", STR_PAD_LEFT);

//echo "<h1> this is date in".$dateIn."</h1>";
//echo "<h1> this is date out".$dateOut."</h1>";
	//find avaibility for each day
	//echo $avail.$day_num
global $wpdb, $wp_query;
$rooms= $wpdb->prefix.'rooms';
$bookings= $wpdb->prefix.'bookings';
  $avail = $wpdb->get_results( $wpdb->prepare(
    "SELECT room_no, booking_id from $bookings 
                      where checkin < %s
                      AND checkout > %s", $depart, $arrive));




	echo "<td class='day-num'>" . "<span class='moh-day-span'>" . $day_num . "</span><br>";
foreach($avail as $available){
	
	echo "<span class='moh-room-span'>Rm No: ".$available->room_no."</span>";
	echo "<span class='moh-room-span'>Bkng Id: " .$available->booking_id."</span><br>";
	
	//echo "<h1>hiya</h1>";
	}
	 
	echo "</td>";
	$day_num++;
	$day_count++;

	if($day_count >7){
		echo "</tr><tr>";
		$day_count = 1;
	}
}

while($day_count > 1 && $day_count <=7){
	$day_count++;
}

echo "</tr></table>"; -->