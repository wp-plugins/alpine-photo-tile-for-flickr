<?php
/*
Plugin Name: Alpine PhotoTile for Flickr
Plugin URI: http://thealpinepress.com/alpine-phototile-for-flickr/
Description: The Alpine PhotoTile for Flickr is the first plugin in a series intended to create a means of retrieving photos from various popular sites and displaying them in a stylish and uniform way. The plugin is capable of retrieving photos from a particular Flickr user, a group, a set, or the Flickr community. This lightweight but powerful widget takes advantage of WordPress's built in JQuery scripts to create a sleek presentation that I hope you will like.
Version: 1.0.1.1
Author: the Alpine Press
Author URI: http://thealpinepress.com/

*/

/* ******************** DO NOT edit below this line! ******************** */

/* Prevent direct access to the plugin */
if (!defined('ABSPATH')) {
	exit(__( "Sorry, you are not allowed to access this page directly.", PTFFbyTAP_DOMAIN ));
}

/* Pre-2.6 compatibility to find directories */
if ( ! defined( 'WP_CONTENT_URL' ) )
	define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
if ( ! defined( 'WP_CONTENT_DIR' ) )
	define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if ( ! defined( 'WP_PLUGIN_URL' ) )
	define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
if ( ! defined( 'WP_PLUGIN_DIR' ) )
	define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );


/* Set constants for plugin */
define( 'PTFFbyTAP_URL', WP_PLUGIN_URL.'/'. basename(dirname(__FILE__)) . '' );
define( 'PTFFbyTAP_DIR', WP_PLUGIN_DIR.'/'. basename(dirname(__FILE__)) . '' );
define( 'PTFFbyTAP_CACHE', WP_CONTENT_DIR . '/cache/' . basename(dirname(__FILE__)) . '' );
define( 'PTFFbyTAP_VER', '1.0.1' );
define( 'PTFFbyTAP_DOMAIN', 'PTFFbyTAP_Domain' );
define( 'PTFFbyTAP_HOOK', 'PTFFbyTAP_hook' );
define( 'PTFFbyTAP_ID', 'PTFF_by_TAP' );

register_deactivation_hook( __FILE__, 'TAP_PhotoTile_Flickr_remove' );
function TAP_PhotoTile_Flickr_remove(){
  $cache = new theAlpinePressSimpleCache();  
  $cache->clearAll();
}

class TAP_PhotoTile_Flickr extends WP_Widget {


	function TAP_PhotoTile_Flickr() {
		$widget_ops = array('classname' => 'PTFFbyTAP_widget', 'description' => __('Add images from Flickr to your sidebar'));
		$control_ops = array('width' => 550, 'height' => 350);
		$this->WP_Widget(PTFFbyTAP_DOMAIN, __('Alpine PhotoTile for Flickr'), $widget_ops, $control_ops);
	}
  
	function widget( $args, $options ) {
		extract($args);
        
    // Set Important Widget Options    
    $id = $args["widget_id"];
    $defaults = tap_plugin_defaults();
    
    $source_results = theAlpinePress_flickr_photo_retrieval($id, $options, $defaults);
    
    echo $before_widget . $before_title . $options['widget_title'] . $after_title;
    echo $source_results['hidden'];
    if( $source_results['continue'] ){  
      switch ($options['style_option']) {
        case "vertical":
          theAlpinePress_flickr_display_vertical($id, $options, $source_results);
        break;
        case "windows":
         theAlpinePress_flickr_display_hidden($id, $options, $source_results);
        break; 
        case "bookshelf":
          theAlpinePress_flickr_display_hidden($id, $options, $source_results);
        break;
        case "rift":
          theAlpinePress_flickr_display_hidden($id, $options, $source_results);
        break;
        case "floor":
         theAlpinePress_flickr_display_hidden($id, $options, $source_results);
        break;
        case "cascade":
          theAlpinePress_flickr_display_cascade($id, $options, $source_results);
        break;
        case "gallery":
          theAlpinePress_flickr_display_hidden($id, $options, $source_results);
        break;
      }
    }
    // If user does not have necessary extensions 
    // or error occured before content complete, report such...
    else{
      echo 'Sorry:<br>'.$source_results['message'];
    }
    echo $after_widget;
    
  }
    
	function update( $newoptions, $oldoptions ) {
    $optiondetails = tap_plugin_defaults();
    foreach( $newoptions as $id=>$input ){
      $options[$id] = thealpinepress_flickr_options_validate( $input,$oldoptions[$id],$optiondetails[$id] );
    }
    return $options;
	}

	function form( $options ) {
    ?>
  <div class="PTFFbyTAP-flickr">
    <?php
    include( 'admin/widget-menu-form.php'); 
    ?> 
  </div> <?php
	}
}

  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////  Safely Enqueue Scripts  and Register Widget  ////////////////////////////////////
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  
  // Load Admin JS and CSS
	function PTFFbyTAP_admin_head_script(){ 
    // TODO - CREATE SEPERATE FUNCTIONS TO LOAD ADMIN PAGE AND WIDGET PAGE SCRIPTS
    wp_enqueue_script( 'jquery');
    // Replication Error caused by not loading new version of JS and CSS
    // Fix by always changing version number if changes were made
    wp_deregister_script('PTFFbyTAP_widget_menu');
    wp_register_script('PTFFbyTAP_widget_menu',PTFFbyTAP_URL.'/js/ptffbytap_widget_menu.js','',PTFFbyTAP_VER);
    wp_enqueue_script('PTFFbyTAP_widget_menu');
        
    wp_deregister_style('PTFFbyTAP_admin_css');   
    wp_register_style('PTFFbyTAP_admin_css',PTFFbyTAP_URL.'/css/admin_style.css','',PTFFbyTAP_VER);
    wp_enqueue_style('PTFFbyTAP_admin_css');
    
    // Only admin can trigger two week cache cleaning
    $cache = new theAlpinePressSimpleCacheV1();
    $cache->setCacheDir( PTFFbyTAP_CACHE );
    $cache->clean();
	}
  add_action('admin_init', 'PTFFbyTAP_admin_head_script'); // admin_init so that it is ready when page loads
  

  // Load Display JS and CSS
  function PTFFbyTAP_enqueue_display_scripts() {
    wp_enqueue_script( 'jquery' );
    
    wp_deregister_script('PTFFbyTAP_tiles_and_slideshow');
    wp_enqueue_script('PTFFbyTAP_tiles',PTFFbyTAP_URL.'/js/ptffbytap_tiles.js','',PTFFbyTAP_VER);
    
    wp_deregister_style('PTFFbyTAP_widget_css'); // Since I wrote the scripts, deregistering and updating version are redundant in this case
    wp_register_style('PTFFbyTAP_widget_css',PTFFbyTAP_URL.'/css/ptffbytap_widget_style.css','',PTFFbyTAP_VER);
    wp_enqueue_style('PTFFbyTAP_widget_css');
    
  }
  add_action('wp_enqueue_scripts', 'PTFFbyTAP_enqueue_display_scripts');
  
	  
   // Register Widget
	function PTFFbyTAP_widget_register() {register_widget( 'TAP_PhotoTile_Flickr' );}
  add_action('widgets_init','PTFFbyTAP_widget_register');
 
  include_once( 'admin/widget-options.php');
  include_once( 'admin/widget-options-display.php'); 
  include_once( 'admin/widget-options-sanitize.php'); 
  include_once( 'gears/source-flickr.php');
  include_once( 'gears/display-functions.php');
  include_once( 'gears/source-cache.php');
    
?>
