<?php 
function moh_add_custom_metabox(){

	add_meta_box( 
		'moh_meta', 
		'MOH Rooms', 
		'moh_meta_callback', 
		'room', 
		'normal', 
		'high'
		//$callback_args 
		);

}
add_action('add_meta_boxes', 'moh_add_custom_metabox' );
// function moh_does_room_exist($post){
// $moh_stored_meta = get_post_meta( $post->ID, '_room_id', true );
// 	if(!empty ($moh_stored_meta['room_id']) ){
// 		echo $moh_stored_meta['room_id'];
// 		return true;
// 	}
// }

function moh_meta_callback ($post){
	wp_nonce_field(basename(__FILE__), 'moh_custom_rooms_nonce');

	$moh_stored_meta = get_post_meta( $post->ID, '_room_id', true );
	// $moh_stored_meta_room_type = get_post_meta( $post->ID, '_room_type', true );
	// $moh_stored_meta_room_rate = get_post_meta( $post->ID, '_room_rate', true );
	// $moh_stored_meta_room_description = get_post_meta($post->ID, '_room_description',true);
	echo "<h1> post id is: " .  $post->ID . "</h1>";
	 $thePostId = $post->ID;
	 echo $thePostId;
	global $wpdb, $wp_query;
	$roomsTable = $wpdb->prefix.'rooms'; 
	$q = "SELECT * FROM $roomsTable WHERE rm_id = $thePostId";

	$getRoom = $wpdb->get_row($q);
 	
 	if($getRoom){
	 	//echo $moh_stored_meta_room_type;
	 	$moh_stored_meta_rm_id = $getRoom->rm_id;
	 	$moh_stored_meta_room_type = $getRoom->rm_type;
	 	$moh_stored_meta_room_rate = $getRoom->amt_per_night;
	 	$moh_stored_meta_room_description = $getRoom->rm_desc;
	 	$moh_stored_meta_actual_rm_no = $getRoom->actual_rm_no;
	}
 	

	?>

	<div id="moh-meta">

		<div class="meta-row">
			<div class="meta-th">
				<label for="room-id" class="moh-row-title">Room ID</label>
				<small>*This should always match the Post ID (see above).</small>	
			</div>
			<div class="meta-td">
				<input type="text" name="room-id" id="room-id" value="<?php
					echo esc_attr( $moh_stored_meta_rm_id );
			 ?>">
			</div>
		</div>
		<div class="meta-row">
			<div class="meta-th">
				<label for="room-type" class="moh-row-title">Room Type</label>	
			</div>
			<div class="meta-td">
				<input type="text" name="room-type" id="room-type" value="<?php echo esc_attr( $moh_stored_meta_room_type ); ?>">
			</div>
		</div>

		<div class="meta-row">
			<div class="meta-th">
				<label for="actual-rm-no" class="moh-row-title">The Room Number</label>
				<small>*This corresponds to 'actual_rm_no' column in the wp_bookings and wp_rooms table.</small>	
			</div>
			<div class="meta-td">
				<input type="text" name="actual-rm-no" id="actual-rm-no" value="<?php  echo esc_attr( $moh_stored_meta_actual_rm_no ); ?>">
			</div>
		</div>

		<div class="meta-row">
			<div class="meta-th">
				<label for="room-rate" class="moh-row-title">Room Rate</label>	
				<small>enter room rate in cents €100 = 10000</small>
			</div>
			<div class="meta-td">
				<input type="text" name="room-rate" id="room-rate" value="<?php echo esc_attr( $moh_stored_meta_room_rate ); ?>">
			</div>
		</div>
	</div>
	<div class="meta">
		<div class="meta-th">
			<span>Room Description</span>
		</div>
	</div>
	<div class="meta-editor"></div>
	<?php
	$content = $moh_stored_meta_room_description;
	$editor_id = 'room-description';
	$settings = array(
		'textarea_rows'=>5,
		);
	wp_editor($content, $editor_id, $settings);



}
function moh_meta_save($post_id){

	if(! isset($_POST['moh_custom_rooms_nonce'])){
		return;
	}
	if(! wp_verify_nonce( $_POST['moh_custom_rooms_nonce'], basename(__FILE__), 'moh_custom_rooms_nonce') ){
		return;
	}
	if( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE){
		return;
	}

	if(!current_user_can('edit_post', $post_id)){
		return;
	}

	if(!isset($_POST['room-id'])){
		return;
	}
	if(!isset($_POST['room-type'])){
		return;
	}
	if(!isset($_POST['room-rate'])){
		return;
	}
	if(!isset($_POST['room-description'])){
		return;
	}
	$rm_id_data = sanitize_text_field($_POST['room-id'] );
	$rm_type_data = sanitize_text_field($_POST['room-type']);
	$rm_rate_data = sanitize_text_field($_POST['room-rate']);
	$rm_description_data = sanitize_text_field($_POST['room-description']);
	$rm_actual_rm_no_data = sanitize_text_field($_POST['actual-rm-no']);

	update_post_meta($post_id, '_room_id', $rm_id_data);
	update_post_meta($post_id, '_room_type', $rm_type_data);
	update_post_meta($post_id, '_room_rate', $rm_rate_data);
	update_post_meta($post_id, '_room_description', $rm_description_data);
	update_post_meta($post_id, '_actual_rm_no', $rm_actual_rm_no_data);

	//if room doesn't exist insert data else update
	//select rm_id from wp_rooms where id = 
	//add info to wp_rooms table
	global $wpdb, $wp_query;
	$roomsTable = $wpdb->prefix.'rooms'; 
	$q = "SELECT * FROM $roomsTable WHERE rm_id = $post_id";
	$checkIfRoomExists = $wpdb->get_row($q);
	if(count($checkIfRoomExists)==1){
		echo "need to update";
		var_dump($checkIfRoomExists);
		$wpdb->update( 
	$roomsTable, 
	array( 
		'rm_id' => $rm_id_data, 
					'rm_type' => $rm_type_data ,
					'actual_rm_no'=>$rm_actual_rm_no_data,
					//'rm_type_id'
					'amt_per_night' => $rm_rate_data,
					//'max_occup'
					'rm_desc' => $rm_description_data 
	), 
	array( 'rm_id' => $post_id ), 
	array( 
		'%d',
		'%d', 
					'%s',
					'%d',
					'%s' 
	), 
	array( '%d' ) 
);
	}else{
echo "inserting";
var_dump($checkIfRoomExists);
		
		
			$wpdb->insert( 
				$roomsTable, 
				array( 
					'rm_id' => $rm_id_data, 
					'rm_type' => $rm_type_data ,
					'actual_rm_no'=>$rm_actual_rm_no_data,
					//'rm_type_id'
					'amt_per_night' => $rm_rate_data,
					//'max_occup'
					'rm_desc' => $rm_description_data
				), 
				array( 
					'%d', 
					'%s',
					'%d',
					'%d',
					'%s' 
				) 
			);
	}
}
add_action('save_post', 'moh_meta_save' );
?>