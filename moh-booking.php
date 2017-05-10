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
require (plugin_dir_path(__FILE__) . 'admin/moh-manage-bookings.php');
//require (plugin_dir_path(__FILE__) . 'public/testPage.php');
require_once('vendor/autoload.php');

///////////////////////////////
////////Enqueue Scripts///////
//////////////////////////////
$stripe = [
'publish'=>'pk_test_vjCqdUzDseC6Gmko8HO8ZZcA',
'private'=> 'sk_test_ZmA7m9ZpVReJH7yFrlyL4wkL'
];
//echo "<h1>". $stripe['publish']."</h1>";

function moh_admin_enqueue_scripts(){

  wp_enqueue_style( 'moh_enqueue_style', plugins_url('public/css/moh-style.css', __FILE__ ) );
  //admin css is specifically for calendar
  global $pagenow, $typenow;
  if($pagenow == 'edit.php'){
  wp_enqueue_style( 'moh_enqueue_admin_style', plugins_url('admin/css/moh-admin.css', __FILE__ ) );
  }
  wp_enqueue_style('jquery-style', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css' );
  wp_register_script( 'moh_admin_js', plugin_dir_url( __FILE__).'/admin/js/moh-admin.js',  array('jquery', 'jquery-ui-datepicker'), '1', true );   
  wp_register_script( 'moh_main_js', plugin_dir_url( __FILE__).'/public/js/moh-main.js',  array('jquery', 'jquery-ui-datepicker'), '1', true );   
  wp_localize_script('moh_main_js', 'myAjax', array(
      'security' => wp_create_nonce('moh_check_avail'),
      'security_guest'=>wp_create_nonce('moh_guest'),
      'ajaxurl'  => admin_url('admin-ajax.php'),
      'checkAvail_security'=>wp_create_nonce('moh_check_avail_action'),
      'guest_security'=>wp_create_nonce('moh_ajax_action_guest_info')
      ));
  wp_enqueue_script('jquery');
  wp_enqueue_script('moh_main_js');
   wp_enqueue_script('moh_admin_js');
}
add_action('init', 'moh_admin_enqueue_scripts' );

//////////////////////////////////////
//////////   Availabity   ////////////
//////////////////////////////////////
/*
Default Wordpress - no javaScript
*/

function moh_avail_default(){
  echo "<h1>DEFAULT ACTION</h1>";

}
add_action( 'admin_post_nopriv_moh_avail_default', 'moh_avail_default' );
add_action( 'admin_post_moh_avail_default', 'moh_avail_default' );


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
                "data_room_rate"=>$the_room->amt_per_night,
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
//get room rates for summary table on booking page
function moh_summary_table_info(){

   //$nights = $_POST['data']['num_nights'];
    $cArr=strtotime($arr);
    $cDep=strtotime($dep);
    $checkNights = ($cDep-$cArr)/(60*60*24);

  $room_nos = $_POST['data']['selectedRooms'];
  $arr = sanitize_text_field($_POST['data']['arr']);
  $dep = sanitize_text_field($_POST['data']['dep']);
  $cArr=strtotime($arr);
  $cDep=strtotime($dep);
  $checkNights = ($cDep-$cArr)/(60*60*24);
  $grand_total = 0;
  global $wpdb, $wp_query;
    $rooms= $wpdb->prefix.'rooms';
  foreach($room_nos as $room_no){
    $prepare = $wpdb->prepare("SELECT actual_rm_no,amt_per_night from $rooms where rm_id = %d", $room_no);
    $get_rms = $wpdb->get_results($prepare);
      foreach($get_rms as $get_rm){
          $room_cost = ($get_rm->amt_per_night)* $checkNights;
          $grand_total += $room_cost;
          $summaryResponse[] = array(

            "rm_no"=>$get_rm->actual_rm_no,
            "rm_rate"=> $get_rm->amt_per_night,
            "rm_cost"=> $room_cost,
            "num_nights"=>$checkNights,
            
             );
   
       }
   
  }
  //wp_send_json_data($grand_total);
  array_push($summaryResponse, array("grand_total" =>$grand_total)); 
  wp_send_json_success($summaryResponse);
 


}
add_action('wp_ajax_moh_summary_table_info', 'moh_summary_table_info' );
add_action('wp_ajax_nopriv_moh_summary_table_info', 'moh_summary_table_info' );

  //get guest info from form and add to db
  function moh_ajax_guest_info(){
    if(! check_ajax_referer('moh_ajax_action_guest_info', 'security')){
       wp_send_json_error("nonocec" );
    }
    if(!isset($_POST['data']['checkin'])){
      wp_send_json_error( "not set");
    }
    
 
    $arr = sanitize_text_field($_POST['data']['checkin']);
    $dep = sanitize_text_field($_POST['data']['checkout']);
    $nights = $_POST['data']['num_nights'];
    $cArr=strtotime($arr);
    $cDep=strtotime($dep);
    $checkNights = ($cDep-$cArr)/(60*60*24);
  
    $fn=sanitize_text_field($_POST['data']['fname']);
    $ln=sanitize_text_field($_POST['data']['lname']);
    $em=sanitize_text_field($_POST['data']['email']);
    $ad=sanitize_text_field($_POST['data']['address']);
    $country=sanitize_text_field($_POST['data']['country']);
    $phone=sanitize_text_field($_POST['data']['phone']);
    $postcode=sanitize_text_field($_POST['data']['postcode']);
    $adults=sanitize_text_field($_POST['data']['no_adults']);
    $children =sanitize_text_field($_POST['data']['no_children']);
    $arr_time=sanitize_text_field($_POST['data']['arr_time']);
    $room_no = sanitize_text_field($_POST['data']['rm_num']);
    $room_nos = sanitize_text_field($_POST['data']['rm_nums']);

    //GET ACTUAL ROOM NUMBERS (IE NOT WP ROOM POST ID).
    // PUT ACTUAL ROOM NUMBERS IN $actual_rooms_array

    global $wpdb, $wp_query;
    $actual_rooms_array = array();
    foreach($room_nos as $room_no){
           
            $get_rms = $wpdb->get_results("SELECT actual_rm_no, amt_per_night from wp_rooms where rm_id = '$room_no'");
            //$rowCount = mysqli_num_rows(${'r_'.$room_no});
            foreach($get_rms as $get_rm){
              //echo $get_rm->actual_rm_no;
              array_push($actual_rooms_array, $get_rm->actual_rm_no);
              
            }
    }

//calculate cost of stay
 // function calculate_cost(rooms, nights){

 // }

//ADD GUEST INFORMATION TO DB
    global $wpdb, $wp_query;
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
     
     }else{
      wp_send_json_error("<p>Something Went Wrong.</p>" );
     }
    
   
// //////////////////////////
// ///PAYMENT WILL GO HERE////
// //////////////////////////

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
        
        
       }
       else{
      
        wp_send_json_error("<p>Something went wrong with your booking.</p>" );
       }
          
    }
      $bookingResponse[] = array(
        'guest_id'=>"<p>Guest ID: ".$guest. "</p>",
        'arrival_date'=>"<p>Check In Date: ".$arr. "</p>",
        'departure_date'=>"<p>Check Out Date: ".$dep. "</p>",
        'guest_name'=>"<p>Name: ".$fn." ".$ln. "</p>",
        'num_rooms'=>"<p>Number of Rooms: ".count($room_nos). "</p>",
        'arrival_time'=>"<p>Arrival Time: ".$arr_time. "</p>",
        'booking_id'=>"<p>Booking ID: ".$booking_id. "</p>",
        'nights'=>"<p>Nights: ". $nights ."</p>",
        'chnights'=>"<p>Ch Nights: ".$checkNights."</p>",

        );
       
    wp_send_json_success($bookingResponse);
    
   
    //die();

  }
  add_action('wp_ajax_nopriv_moh_ajax_action_guest_info', 'moh_ajax_guest_info');
  add_action('wp_ajax_moh_ajax_action_guest_info', 'moh_ajax_guest_info');
        

//////////////////////////////////////////////////////
////////      Add Bookings Submenu Page      /////////
//////////////////////////////////////////////////////
function moh_add_submenu_bookings(){

  add_submenu_page( 
                    'edit.php?post_type=room',
                    'Manage Bookings',//$page_title, 
                    'Manage Bookings',//$menu_title, 
                    'manage_options',//$capability, 
                    'manage_bookings',//$menu_slug, 
                    'manage_bookings_callback'//$function see admin/moh-manage-bookings.php
    );

}
add_action('admin_menu', 'moh_add_submenu_bookings' );


///////////////////////////////////////////////////////////////////////
/////////////////                  ADMIN          /////////////////////
///////////////////////////////////////////////////////////////////////
//THE FOLLOWING ID TO DISPLAY BOOKING INFO AUTOMATICALLY ON ADMIN CALENDAR

//for calendar in moh-manage-bookings.php, see moh-admin.js-getAdminData();
//get bookings' details



function moh_send_admin_data(){
 if(!isset($_POST['data']['month']) || !isset($_POST['data']['year']) || !isset($_POST['data']['days_in_month'])){
  //$mohTheMonth = $_POST['data']['month'];
  wp_send_json_error("Sorry could not retrieve requested data");
  }

 // more checks here

  $day_num = 01;
  $days_in_month = $_POST['data']['days_in_month'];
  $year = $_POST['data']['year'];
  $month = $_POST['data']['month'];

  //vars for query
  global $wpdb;
  $rooms= $wpdb->prefix.'rooms';
  $bookings= $wpdb->prefix.'bookings';
   $guests= $wpdb->prefix.'guests';
   

    $get_total_number_of_rooms = $wpdb->get_results("SELECT actual_rm_no FROM $rooms;");
    $total_number_of_rooms = $wpdb->num_rows;
    foreach($get_total_number_of_rooms as $get_names_of_all_rooms){
      $names_of_all_rooms[] = $get_names_of_all_rooms->actual_rm_no;
    }

   

  while($day_num <= $days_in_month){
      $date= date_create($year."-".$month."-".$day_num);
      $arrive = date_format($date,"Y-m-d");
      $date1 = str_replace('-', '/', $arrive);
      $depart = date('Y-m-d',strtotime($date1 . "+1 days"));

      ///
      $prepared_query = $wpdb->prepare(
        "SELECT checkin, checkout, room_no , fname, lname, address, email, country, $guests.no_adults, $guests.no_children, arrival,  booking_id, $bookings.guest_id, checkin
        FROM $bookings, $guests 
          WHERE $bookings.guest_id = $guests.guest_id 
          AND checkin < %s
           AND checkout > %s" , $depart, $arrive);

      $all_rooms_query = $wpdb->prepare(
           "SELECT room_no 
        FROM $bookings
          WHERE checkin < %s
           AND checkout > %s" , $depart, $arrive);

       
      
      $avail = $wpdb->get_results($prepared_query);
      unset($all_rooms_booked_today);
      foreach($avail as $get_the_rooms){
      $all_rooms_booked_today[] = $get_the_rooms->room_no;
    }

//$get_all_rooms_booked_each_day = $wpdb->get_results($all_rooms_query);
      $number_of_bookings = $wpdb->num_rows;
        foreach($avail as $available){
         // $all_rooms_booked_today[] = $get_booked_rooms->room_no;
          //$get_room_no = $available->room_no;
          // foreach($get_room_no as $get_booked_rooms){
          //     $get_booked_rooms['room_no'];
          //   }
          $adminResponse[] = array(
           "all_rooms_booked_today"=>$all_rooms_booked_today,
           "checkin"=>$available->checkin,
           "checkout"=>$available->checkout,
            "number_of_bookings"=> $number_of_bookings,
            "names_of_all_rooms" => $names_of_all_rooms,
            "total_number_of_rooms" =>$total_number_of_rooms,
            "data_arr_date" => $arrive,
            "data_dep_date"=>$depart,
            "data_room_num" =>  $available->room_no,
            "data_guest_id" => $available->guest_id,
             "data_guest_name" =>  $available->fname." ".$available->lname ,
             "data_booking_id"=>$available->booking_id,
             "data_arrival_time"=>$available->arrival,
             "arr_date" => "<p>Check In: ".$arrive."</p>",
            "dep_date"=>"<p>Check Out: ".$depart."</p>",
            "room_num" =>"<p>".$available->room_no."</p>",
            "guest_id" =>"<p>Guest Id: ". $available->guest_id."</p>",
             "guest_name" =>"<p><b>Name: </b>" .$available->fname." ".$available->lname. "</p>",
             "booking_id"=>"<p>Booking ID: " . $available->booking_id . "</p>"

          );
        }
      $day_num ++;

  }

  wp_send_json_success($adminResponse);


}
add_action('wp_ajax_moh_send_admin_data', 'moh_send_admin_data');
add_action('wp_ajax_nopriv_moh_send_admin_data', 'moh_send_admin_data');





function moh_send_admin_data_ids(){
  global $wpdb;
   $rooms = $wpdb->prefix.'rooms';
          $get_rooms_in_db = $wpdb->get_results( $wpdb->prepare(
            "SELECT * from $rooms"));
    foreach($get_rooms_in_db as $rooms_in_db){
      $all_rooms_in_db[] = $rooms_in_db->actual_rm_no;
    }
    wp_send_json_success($all_rooms_in_db );

}


add_action('wp_ajax_moh_send_admin_data_ids', 'moh_send_admin_data_ids');
add_action('wp_ajax_nopriv_moh_send_admin_data_ids', 'moh_send_admin_data_ids');






















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
      <h4>Booking Widget</h4>
      <?php include 'moh-index.php';?>
      <!--
       <input type="hidden" name="action" value="moh_ajax_action" />
     -->


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