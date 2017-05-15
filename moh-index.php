<!DOCTYPE html>
<html>
	<head>
		<title>ajaxing</title>
	</head>
	<body><!--<?php//echo esc_url( admin_url('admin-post.php') );?>-->
		<form action="" method="post">
		<!--
		<input type="hidden" value="moh_avail_default" name="action">
	-->
		<label>Arrive:</label> 
		<input class="datepicker" type="date" id="arrive" name="arrive">
		<label>Depart:</label> 
		<input class="datepicker" type="date" id="depart" name="depart">
		<label>Nights:</label> 
		<input type="number" min="1" max="14" id="num-nights" name="num-nights">
		<input type="submit" id="date-submit" name="avail-submit" value="Check Dates">
		<input type="text" id="xyz" style="display:none" name="<?php echo apply_filters( 'honey', 'date-submitted'); ?>" value="">
		<input type="submit" id="test-submit"  value="Test Check Dates">
		<!--start of switch to dropdown selection 
		<label>Day</label>
		<input type="number" id="day" name="day" min="1" max="31">
		<label>Month</label>
		<input type="number" id="day" name="day">
		<label>Year</label>
		<input type="number" id="day" name="day">
		 of switch to dropdown selection  -->
		</form>
		<div id="date-data"></div>
		<p id="arr-err"></p>
		<p id="dep-err"></p>
		<p id="nights-err"></p>	
		<p id="show-nights"></p>
		
		<!--<script type="text/javascript" src="http://localhost/designassociates/marie_plugin/wp-content/plugins/moh_guesthouse/js/global.js"></script>-->
		<div id="show-rooms-info"></div>
        <div id="moh-booking-div" ></div>
        <div class="room-booking-button"></div>

	<?php 
	
	//default action for AVAILABITY
	//ideally use admin_post_action_name and admin_post_nopriv_action_name and change action in above form
	// function moh_check_date_format($the_date){
	//   $yr = substr($the_date,0,4);
	//   $mt = substr($the_date,5,2);
	//   $dt = substr($the_date,8,2);
	//   if (is_numeric($yr) && is_numeric($mt) && is_numeric($dt)){
	//     return checkdate($mt,$dt,$yr);
	//   }
	// }

	// if (isset($_POST['arrive']) && isset($_POST['depart'])){

 //    	$arrive = sanitize_text_field($_POST['arrive']);
 //    	$depart = sanitize_text_field($_POST['depart']);
	// 	    	if(!moh_check_date_format($arrive) || !moh_check_date_format($depart)){
	// 	       	 die("Please enter Arrival and Departure dates in format yyyy-mm-dd.");
	// 	      	}
	// 	   	$sixMonths = new dateTime('+6 months');
	// 		$twoWeeks = new dateTime('+2 weeks');
	// 		$now = date('Y-m-d');
	// 		$checkArr = date_create_from_format ( 'Y-m-d' , $arrive);
	// 	    $checkDep = date_create_from_format ( 'Y-m-d' , $depart);
		   
	// 		if(strlen($arrive) !==10 || strlen($depart) !==10){
	// 			die('Server Says: Incorrect date format');
	// 		}
	// 		if($arrive < $now){
	// 			die('Check your Arrival is in the future.</br>');
	// 		}
	// 		if($arrive !== date_format($checkArr, 'Y-m-d') || $depart !== date_format($checkDep, 'Y-m-d')){
	// 			die('Server Says: Oops, check all dates are in the format');
	// 		}
	// 		if($arrive>=$depart){
	// 			die('Check your arrival date is before departure</br>');
	// 		}
	// 		 if($arrive > date_format($sixMonths, 'Y-m-d')){
	// 			die('Sorry, we only accept bookings up to 6 months in advance');
	// 		}
	// 		////////////////////////
	// 		//FIX CHECK 2 WEEKS here ////
	// 		///////////////////////
			

	// 			global $wpdb, $wp_query;
	// 			  $bookings = $wpdb->prefix . 'bookings'; 
	// 			  $rooms = $wpdb->prefix.'rooms';
	// 			  $the_rooms = $wpdb->get_results( $wpdb->prepare(
	// 			    "SELECT distinct actual_rm_no, rm_type, amt_per_night, rm_id, rm_desc
	// 			     FROM $bookings, $rooms
	// 			      where $bookings.room_no = $rooms.actual_rm_no 
	// 			       and room_no not in(
	// 			                      select room_no from $bookings 
	// 			                      where checkin < %s
	// 			                      AND checkout > %s)", $depart, $arrive));
	// 			  //echo ($the_rooms) ? "rooms! ": "query fail";
	// 			  $no_rooms = count($the_rooms);
	// 			  //echo ($no_rooms > 0) ? "Rooms are available" : "No Rooms Available";
	// 			 if($no_rooms > 0) {
	// 			 	foreach($the_rooms as $the_room){
	// 	        	$rm_id = $the_room->rm_id;
	// 	          	$room_pic = get_the_post_thumbnail($rm_id,'thumbnail');
	// 	          	$actual_rm_no = $the_room->actual_rm_no;
	// 	          	$rm_desc = $the_room->rm_desc;
	// 	          	$rm_rate = $the_room->amt_per_night;
	// 	             //if ajax
	// 	          	//send json
	// 	          	//else echo
	// 	            //echo "<h3>" .$the_room->rm_type. "</h3>";
	// 	            echo"<p>".$actual_rm_no."</p>";
	// 	            echo"<p>".$rm_desc."<p>";
	// 	            echo"<h5>".$rm_rate."</h5>";
	// 	            //"room_id"=>$the_room->rm_id,
	// 	            echo $room_pic; //sending the whole image tag
	// 	            echo"<button class='get-the-room'  id='add-".$the_room->rm_id . "' value='".$the_room->rm_id . "'>select room</button>";
	// 	            echo "<button class='remove-the-room' id='remove-".$the_room->rm_id . "' style='display:none;'  value='".$the_room->rm_id . "'>remove room</button>";
	// 	            echo "<form action=''><input class='show-booking-button' type='submit' style='display:none;' value='Book Now' /></form>";
		            
		           
		          

	// 	        }
	// 			 }
	
	// }

	?>
	</body>

</html>