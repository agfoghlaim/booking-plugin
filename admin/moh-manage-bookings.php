<?php 
//callback function
// global $pagenow;
// var_dump($pagenow);

function manage_bookings_callback(){
	global $pagenow, $typenow;
	var_dump($pagenow);
		var_dump($typenow);

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
		<div id="bookingInformation"></div> 





		<?php
		// function moh_send_admin_data(){
		//  if(isset($_POST['data']['month'])){
		//   $mohTheMonth = $_POST['data']['month'];
		//   wp_send_json_success($mohTheMonth);
		// }
		//  }
		// add_action('wp_ajax_moh_send_admin_data', 'moh_send_admin_data');
		//add_action('wp_ajax_nopriv_moh_send_admin_data', 'moh_send_admin_data');

}//end callback function


?>















