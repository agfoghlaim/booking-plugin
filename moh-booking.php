<?php 
/*
Plugin Name: Moh Booking
Plugin URI: localhost
Description: description
Author: Marie
Version: 1.0

*/
if( ! defined( 'ABSPATH')){
  exit;
}
require (plugin_dir_path(__FILE__) . 'admin/moh-room-fields.php');
require (plugin_dir_path(__FILE__) . 'admin/moh-rooms-custom-post.php');

///////////////////////////////
////////Enqueue Scripts///////
//////////////////////////////

function moh_admin_enqueue_scripts(){

  wp_enqueue_style( 'moh_enqueue_style', plugins_url('public/css/moh-style.css', __FILE__ ) );
  wp_enqueue_style('jquery-style', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css' );
  wp_register_script( 'moh_main_js', plugin_dir_url( __FILE__).'/public/js/moh-main.js',  array('jquery', 'jquery-ui-datepicker'), '1', true );   
  wp_localize_script('moh_main_js', 'myAjax', array(
      'security' => wp_create_nonce('moh_check_avail'),
      'ajaxurl'  => admin_url('admin-ajax.php'),
      'checkAvail_security'=>wp_create_nonce('moh_check_avail_action'),
      'getDetails'=>wp_create_nonce('marie_is_confused')
      ));
  wp_enqueue_script('jquery');
  wp_enqueue_script('moh_main_js');
}
add_action('init', 'moh_admin_enqueue_scripts' );

//////////////////////////////////////
//////////   Availabity   ////////////
//////////////////////////////////////
/*
For Availabity Form Validation
*/
function moh_check_date_format($the_date){
    $yr = substr($the_date,0,4);
    $mt = substr($the_date,5,2);
    $dt = substr($the_date,8,2);
    if (is_numeric($yr) && is_numeric($mt) && is_numeric($dt)){
      return checkdate($mt,$dt,$yr);
    }
  }

/*
Availabity Ajax Response
*/
function moh_check_avail_action(){
  //check honey

  if(!empty($_POST['submission'])){
    wp_send_json_error('Nice try.');
  }
  if(! check_ajax_referer('moh_check_avail_action', 'security')){
  wp_send_json_error('Stop messing.');
  }



      if (isset($_POST['data']['arrive']) && isset($_POST['data']['depart'])){
          $arrive = sanitize_text_field($_POST['data']['arrive'] );
          $depart = sanitize_text_field($_POST['data']['depart'] );
          

            if(!moh_check_date_format($arrive) || !moh_check_date_format($depart)){
               wp_send_json_error("Please enter Arrival and Departure dates in format yyyy-mm-dd.");
              }

          $sixMonths = new dateTime('+6 months');
          $twoWeeks = new dateTime('+2 weeks');
          $now = date('Y-m-d');
          $checkArr = date_create_from_format ( 'Y-m-d' , $arrive);
          $checkDep = date_create_from_format ( 'Y-m-d' , $depart);
       
           if(strlen($arrive) !==10 || strlen($depart) !==10){
             wp_send_json_error('Server Says: Incorrect date format');
           }
           if($arrive < $now){
             wp_send_json_error('Check your Arrival is in the future.</br>');
           }
           if($arrive !== date_format($checkArr, 'Y-m-d') || $depart !== date_format($checkDep, 'Y-m-d')){
             wp_send_json_error('Server Says: Oops, check all dates are in the format');
           }
           if($arrive>=$depart){
             wp_send_json_error('Check your arrival date is before departure</br>');
           }
            if($arrive > date_format($sixMonths, 'Y-m-d')){
             wp_send_json_error('Sorry, we only accept bookings up to 6 months in advance');
           }
      ////////////////////////
      //FIX CHECK 2 WEEKS here ////
      ///////////////////////
      

        global $wpdb, $wp_query;
          $bookings = $wpdb->prefix . 'bookings'; 
          $rooms = $wpdb->prefix.'rooms';
          $the_rooms = $wpdb->get_results( $wpdb->prepare(
            "SELECT distinct actual_rm_no, rm_type, amt_per_night, rm_id, rm_desc
             FROM $bookings, $rooms
              where $bookings.room_no = $rooms.actual_rm_no 
               and room_no not in(
                              select room_no from $bookings 
                              where checkin < %s
                              AND checkout > %s)", $depart, $arrive));
          //echo ($the_rooms) ? "rooms! ": "query fail";
          $no_rooms = count($the_rooms);
          
          //echo ($no_rooms > 0) ? "Rooms are available" : "No Rooms Available";
        if($no_rooms > 0) {
         // wp_send_json_success($no_rooms );
            foreach($the_rooms as $the_room){
              $rm_id = $the_room->rm_id;
              $room_pic = get_the_post_thumbnail($rm_id,'thumbnail');
              $actual_rm_no = $the_room->actual_rm_no;
              $rm_desc = $the_room->rm_desc;
              $rm_rate = $the_room->amt_per_night;
              
              $availResponse[] = array(

                "room_type" => "<h3>" . $the_room->rm_type . "</h3>",
                "room_number"=>"<p>Room Number: ".$the_room->actual_rm_no."</p>",
                "room_description"=>"<p>Description: ".$the_room->rm_desc."<p>",
                "room_rate"=>"<h5>Nightly Rate ".$the_room->amt_per_night."</h5>",
                "room_id"=>$the_room->rm_id,
                "room_thumbnail"=>$room_pic, //sending the whole image tag
                "room_book_button"=> "<button class='get-the-room' name='add-".$the_room->rm_id . "'  id='add-".$the_room->rm_id . "' value='".$the_room->rm_id . "'>select room</button>",
                "room_remove_button"=> "<button class='remove-the-room' id='remove-".$the_room->rm_id . "' style='display:none;'  value='".$the_room->rm_id . "'>remove room</button>",
               "room_show_booking_form"=>"<form action='book-room-101'><input id='book-".$the_room->rm_id . "'  class='show-booking-button' type='submit' style='display:none;' value='Book Now' /></form><hr>"
                //"room_show_booking_form"=>"<button id='book-".$the_room->rm_id . "'  class='show-booking-button'  style='display:none;' >press this</button><hr>"
              );
        }
        wp_send_json_success($availResponse );
      }
  
  }
}
add_action('wp_ajax_moh_check_avail_action', 'moh_check_avail_action'  );
add_action('wp_ajax_nopriv_moh_check_avail_action', 'moh_check_avail_action'  );

////////////////////////////////////////
//////////Select Rooms/////////////////
/////////////////////////////////////////


/*===================for book individual room page================ */

  //get guest info from form and add to db
  function moh_ajax_guest_info(){
    if(! check_ajax_referer('wp_rooms_action', 'security_info')){
      echo "info nonce notok";
    }else{
      echo "info nonce ok";
    }
   // echo $arr;
   // echo $_POST['dep'];
    $arr = $_POST['checkin'];
    $dep = $_POST['checkout'];
    $fn=$_POST['fname'];
    $ln=$_POST['lname'];
    $em=$_POST['email'];
    $ad=$_POST['address'];
    $country=$_POST['country'];
    $phone=$_POST['phone'];
    $postcode=$_POST['postcode'];
    $adults=$_POST['no_adults'];
    $children =$_POST['no_children'];
    $arr_time=$_POST['arr_time'];
    $room_no = $_POST['rm_num'];

    $room_nos = $_POST['rm_nums'];

    //GET ACTUAL ROOM NUMBERS (IE NOT WP ROOM POST ID).
    // PUT ACTUAL ROOM NUMBERS IN $actual_rooms_array

    global $wpdb, $wp_query;
    $actual_rooms_array = array();
    foreach($room_nos as $room_no){
           
            $get_rms = $wpdb->get_results("SELECT actual_rm_no from wp_rooms where rm_id = '$room_no'");
            //$rowCount = mysqli_num_rows(${'r_'.$room_no});
            foreach($get_rms as $get_rm){
              echo $get_rm->actual_rm_no;
              array_push($actual_rooms_array, $get_rm->actual_rm_no);
            }
    }
/////////////////////////////////////////////////////////
///////////   GET INFO AND REDIRECT TO BOOKING PAGE   ///
/////////////////////////////////////////////////////////


function moh_booking_data_action(){


}
add_action('wp_ajax_moh_booking_data_action', 'moh_booking_data_action'  );
add_action('wp_ajax_nopriv_moh_booking_data_action', 'moh_booking_data_action'  );


////////////////////////////////////////////////////////////////
///////////////////// BOOKING PAGE   ///////////////////////////
////////////////////////////////////////////////////////////////

//ADD GUEST INFORMATION TO DB
    global $wpdb;
     $add_guest = $wpdb->insert('wp_guests', array(
    'fname' => $fn,
    'lname' => $ln,
    'email' => $em,
    'address' => $ad,
    'country' => $country,
    'postcode' => $postcode,
    'phone' => $phone,
    'no_adults' => $adults,
    'no_children' => $children,
    'arrival' => $arr_time
    ));
     $guest = $wpdb->insert_id;

     if($add_guest){
      echo $fn . " added, guest id is (secret) " . $guest;
      echo "<p>arr: " . $arr . "</p>";
      echo "<p>dep: ".$dep."</p>";
      echo "<p>guest: ".$guest."</p>";
     }else{
      echo $fn . " not added";
     }
    
   
//////////////////////////
///PAYMENT WILL GO HERE////
//////////////////////////

// BOOKING QUERY 
     //array to hold booking ids
     //$confirm_booking = array();
    foreach($actual_rooms_array as $actual_room_to_book){
       $book_query = $wpdb->insert('wp_bookings', array(
        'guest_id' => $guest,
        'checkin' => $arr,
        'checkout'=> $dep,
        'room_no'=> $actual_room_to_book
      ));
         if($book_query){
        $booking_id = $wpdb->insert_id;
        //array_push($confirm_booking, $wpdb->insert_id)
        echo "booking id is: " . $booking_id . "<br>";
        echo "booked";
       }
       else{
        echo "not booked";
       }
          
    }
    
   
    die();

  }
  add_action('wp_ajax_nopriv_moh_ajax_action_guest_info', 'moh_ajax_guest_info');
  add_action('wp_ajax_moh_ajax_action_guest_info', 'moh_ajax_guest_info');









/////////////////////////////////////
////////       Widget       /////////
/////////////////////////////////////

//register widget area in divi-child theme functions.php
//body of widget in moh-booking plugin index.php
//add widget to page in wp admin widget area

class moh_guesthouse extends WP_Widget{
  function __construct(){
    parent::__construct(false, $name = __('MOH Guesthouse'));
}
function form(){

}
function update(){

}
//output widget info
function widget($args, $instance){
   ?>
    <div class="widget check-avail">
      <h4>Marie Book Now</h4>
      <?php include 'moh-index.php';?>
       <input type="hidden" name="action" value="moh_ajax_action" />


    </div>
    <?php

  }

}

//initialise widget
add_action('widgets_init', function(){
  register_widget('moh_guesthouse');
});




/////////////////////////////////////
////////Add Tables To Database///////
/////////////////////////////////////
register_activation_hook( __FILE__, 'moh_install' );
//register_activation_hook( __FILE__, 'moh_install_data' );

function moh_install () {
   global $wpdb;

   $charset_collate = $wpdb->get_charset_collate();

  $table_name = $wpdb->prefix . 'bookings';

  $charset_collate = $wpdb->get_charset_collate();

  $sql = "CREATE TABLE $table_name (
  booking_id mediumint(9) NOT NULL AUTO_INCREMENT,
  guest_id mediumint(9) NOT NULL,
  checkin datetime DEFAULT '0000-00-00' NOT NULL,
  checkout datetime DEFAULT '0000-00-00' NOT NULL,
  room_no mediumint(9),
  no_nights mediumint(9),
  room_type_requested varchar(55) DEFAULT '' NOT NULL,
  no_adults mediumint(9),
  no_children mediumint(9),
  PRIMARY KEY  (booking_id),
  KEY guest_id (guest_id),
  KEY room_no (room_no)
  ) $charset_collate;

  CREATE TABLE wp_guests (
  guest_id mediumint(9) NOT NULL AUTO_INCREMENT,
  fname varchar(55) NOT NULL,
  lname varchar(55) NOT NULL,
  email varchar(55) NOT NULL,
  address varchar(255) NOT NULL,
  country varchar(55),
  postcode varchar(55),
  phone varchar(55),
  no_adults mediumint(9),
  no_children mediumint(9),
  arrival varchar(9),
  PRIMARY KEY  (guest_id)
) $charset_collate;

  CREATE TABLE wp_rooms (
  rm_id mediumint(9) NOT NULL,
  rm_type varchar(55) NOT NULL,
  rm_type_id mediumint(9) NOT NULL,
  amt_per_night varchar(55),
  max_occup mediumint(9),
  rm_desc longtext
  PRIMARY KEY  (rm_id),
  KEY rm_type_id (rm_type_id)
) $charset_collate;

  CREATE TABLE wp_room_type (
  room_type_id mediumint(9) NOT NULL,
  description varchar(55) NOT NULL,
  post_id_wp mediumint(9),
  PRIMARY KEY  (room_type_id)
) $charset_collate;";

  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
  dbDelta( $sql );
}
?>