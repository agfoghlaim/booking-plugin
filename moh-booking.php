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
      //'security' => wp_create_nonce('wp_rooms_action'),
      'ajaxurl'  => admin_url('admin-ajax.php'),
      //'checkAvail_security'=>wp_create_nonce('moh_check_availabity')
      ));
  wp_enqueue_script('jquery');
  wp_enqueue_script('moh_main_js');
}
add_action('init', 'moh_admin_enqueue_scripts' );

/////////////////////////////////////
//////////   Availabity   ////////////
//////////////////////////////////////

/*default action for check availabity form, ie if javaScript disabled
is in moh-index.php
Ideally 1. find out how to stop admin-post.php redirect
        2. write moh_avail_default function and move default action here

add_action('admin_post_nopriv_moh_avail_default', 'moh_avail_default');
add_action('admin_post_moh_avail_default', 'moh_avail_default');

function moh_avail_default(){
  //
}
*/





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