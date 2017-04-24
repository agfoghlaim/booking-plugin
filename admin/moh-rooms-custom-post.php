<?php 
//register post type 'room'
function moh_guesthouse_post_type_room(){
  //BASIC VERSION OF ROOMS
  // $args = array('public'=>true, 'label'=>'Room');
  // register_post_type('room', $args);

  $singular ='Room';
  $plural = 'Rooms';

  $labels = array(
    'name'=>$plural,
    'singular_name'=>$singular,
    'add_name'=>'Add New',
    'add_new_item'=>'Add New ' .$singular,
    'edit'=>'Edit',
    'edit_item'=>'Edit '.$singular,
    'new_item'=>'New '.$singular,
    'view'=>'View '.$singular,
    'view_item'=>'View '.$singular,
    'search_term'=>'Search '.$plural,
    'parent'=>'Parent '.$singular,
    'not_found'=>'No '.$plural.' found',
    'not_found_in_trash'=>'No '.$plural.' in Trash',
    );

  $args = array(
    'labels'              =>$labels,
    'public'              =>true,
    'publicly_queryable'  =>true, 
    'exclude_from_search' =>false,
    'show_in_nav_menus'   =>true,
    'show_ui'             =>true,
    'show_in_menu'         =>true,
    'show_in_admin_bar'   =>true,
    'menu_icon'           =>'dashicons-wordpress-alt',
    'delete_with_user'    =>false,
    'hierarchical'        =>false,
    'has_archive'         =>true,
    'query_var'           =>true,
    'rewrite'             =>array(
      'slug'              =>'rooms',
      'with_front'        =>true,
      'pages'             =>true,
      'feeds'             =>false
      ),
    'supports'=>array(
      'title',
      // 'editor',
      // 'author',
      // 'custom-fields',
      'thumbnail'
      )


    );

register_post_type('room', $args);
}
add_action('init', 'moh_guesthouse_post_type_room' );

//register custom taxonomy room_type
//moh_guesthouse_custom_taxonomy_room_type
function moh_guesthouse_custom_taxonomy_room_type(){
  $singular = 'Room Type';
  $plural = 'Room Types';
  $labels = array(
    'name'          => $plural,
    'singular_name'     => $singular,
    'search_items'      => 'Search' . $plural,
    'popular_items'     => 'Popular' .$plural,
    'all_items'       => 'All'.$plural,
    'parent_item'     => null,
    'parent_item_colon'   => null,
    'edit_item'       => 'Edit'.$singular,
    'update_item'     => 'Update'.$singular,
    'add_new_item'      => 'Add New'.$singular,
    'new_item_name'     => 'New' . $singular.'Name',
    'add_or_remove_items' => 'Add or Remove'.$plural,
    'choose_from_most_used' => 'Choose from most used'.$plural,
    'menu_name'       => $plural,
  );
  $args = array(
    'labels'            => $labels,
    'public'            => true,
    'show_in_nav_menus' => true,
    'show_admin_column' => false,
    'hierarchical'      => true,
    //'show_tagcloud'     => true,
    'show_ui'           => true,
    'query_var'         => true,
    'rewrite'           => array('slug'=>'location'),
    
    //'capabilities'      => array(),
  );
  
register_taxonomy('room_type', 'room', $args );
}

add_action('init', 'moh_guesthouse_custom_taxonomy_room_type' );


?>