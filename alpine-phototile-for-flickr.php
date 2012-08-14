<?php
/*
Plugin Name: Alpine PhotoTile for Flickr
Plugin URI: http://thealpinepress.com/alpine-phototile-for-flickr/
Description: The Alpine PhotoTile for Flickr is the first plugin in a series intended to create a means of retrieving photos from various popular sites and displaying them in a stylish and uniform way. The plugin is capable of retrieving photos from a particular Flickr user, a group, a set, or the Flickr community. This lightweight but powerful widget takes advantage of WordPress's built in JQuery scripts to create a sleek presentation that I hope you will like.
Version: 1.0.2.1
Author: the Alpine Press
Author URI: http://thealpinepress.com/

*/

/* ******************** DO NOT edit below this line! ******************** */

/* Prevent direct access to the plugin */
if (!defined('ABSPATH')) {
	exit(__( "Sorry, you are not allowed to access this page directly.", APTFFbyTAP_DOMAIN ));
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
define( 'APTFFbyTAP_URL', WP_PLUGIN_URL.'/'. basename(dirname(__FILE__)) . '' );
define( 'APTFFbyTAP_DIR', WP_PLUGIN_DIR.'/'. basename(dirname(__FILE__)) . '' );
define( 'APTFFbyTAP_CACHE', WP_CONTENT_DIR . '/cache/' . basename(dirname(__FILE__)) . '' );
define( 'APTFFbyTAP_VER', '1.0.2.1' );
define( 'APTFFbyTAP_DOMAIN', 'APTFFbyTAP_Domain' );
define( 'APTFFbyTAP_HOOK', 'APTFFbyTAP_hook' );
define( 'APTFFbyTAP_ID', 'PTFF_by_TAP' );

register_deactivation_hook( __FILE__, 'TAP_PhotoTile_Flickr_remove' );
function TAP_PhotoTile_Flickr_remove(){
  $cache = new theAlpinePressSimpleCache();  
  $cache->clearAll();
}


class Alpine_PhotoTile_for_Flickr extends WP_Widget {

	function Alpine_PhotoTile_for_Flickr() {
		$widget_ops = array('classname' => 'APTFFbyTAP_widget', 'description' => __('Add images from Flickr to your sidebar'));
		$control_ops = array('width' => 550, 'height' => 350);
		$this->WP_Widget(APTFFbyTAP_DOMAIN, __('Alpine PhotoTile for Flickr'), $widget_ops, $control_ops);
	}
  
	function widget( $args, $options ) {
		extract($args);
        
    // Set Important Widget Options    
    $id = $args["widget_id"];
    $defaults = APTFFbyTAP_option_defaults();
    
    $source_results = APTFFbyTAP_photo_retrieval($id, $options, $defaults);
    
    echo $before_widget . $before_title . $options['widget_title'] . $after_title;
    echo $source_results['hidden'];
    if( $source_results['continue'] ){  
      switch ($options['style_option']) {
        case "vertical":
          APTFFbyTAP_display_vertical($id, $options, $source_results);
        break;
        case "windows":
         APTFFbyTAP_display_hidden($id, $options, $source_results);
        break; 
        case "bookshelf":
          APTFFbyTAP_display_hidden($id, $options, $source_results);
        break;
        case "rift":
          APTFFbyTAP_display_hidden($id, $options, $source_results);
        break;
        case "floor":
         APTFFbyTAP_display_hidden($id, $options, $source_results);
        break;
        case "cascade":
          APTFFbyTAP_display_cascade($id, $options, $source_results);
        break;
        case "gallery":
          APTFFbyTAP_display_hidden($id, $options, $source_results);
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
    $optiondetails = APTFFbyTAP_option_defaults();
    foreach( $newoptions as $id=>$input ){
      $options[$id] = theAlpinePressMenuOptionsValidateV1( $input,$oldoptions[$id],$optiondetails[$id] );
    }
    return $options;
	}

	function form( $options ) {

    include( 'admin/widget-menu-form.php'); 

	}
}

  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////  Safely Enqueue Scripts  and Register Widget  ////////////////////////////////////
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  
  // Load Admin JS and CSS
	function APTFFbyTAP_admin_head_script(){ 
    // TODO - CREATE SEPERATE FUNCTIONS TO LOAD ADMIN PAGE AND WIDGET PAGE SCRIPTS
    wp_enqueue_script( 'jquery');
    // Replication Error caused by not loading new version of JS and CSS
    // Fix by always changing version number if changes were made
    wp_deregister_script('APTFFbyTAP_widget_menu');
    wp_register_script('APTFFbyTAP_widget_menu',APTFFbyTAP_URL.'/js/aptffbytap_widget_menu.js','',APTFFbyTAP_VER);
    wp_enqueue_script('APTFFbyTAP_widget_menu');
        
    wp_deregister_style('APTFFbyTAP_admin_css');   
    wp_register_style('APTFFbyTAP_admin_css',APTFFbyTAP_URL.'/css/aptffbytap_admin_style.css','',APTFFbyTAP_VER);
    wp_enqueue_style('APTFFbyTAP_admin_css');
    
    add_action('admin_print_footer_scripts', 'APTFFbyTAP_menu_toggles');
    
    // Only admin can trigger two week cache cleaning
    $cache = new theAlpinePressSimpleCacheV1();
    $cache->setCacheDir( APTFFbyTAP_CACHE );
    $cache->clean();
	}
  add_action('admin_enqueue_scripts', 'APTFFbyTAP_admin_head_script'); // admin_init so that it is ready when page loads
  
  function APTFFbyTAP_menu_toggles(){
    ?>
    <script type="text/javascript">
    if( jQuery().theAlpinePressWidgetMenuPlugin  ){
      jQuery(document).ready(function(){
        jQuery('.APTFFbyTAP-flickr .APTFFbyTAP-parent').theAlpinePressWidgetMenuPlugin();
        
        jQuery(document).ajaxComplete(function() {
          jQuery('.APTFFbyTAP-flickr .APTFFbyTAP-parent').theAlpinePressWidgetMenuPlugin();
        });
      });
    }
    </script>  
    <?php   
  }
  
  // Load Display JS and CSS
  function APTFFbyTAP_enqueue_display_scripts() {
    wp_enqueue_script( 'jquery' );
    
    wp_deregister_script('APTFFbyTAP_tiles_and_slideshow');
    wp_enqueue_script('APTFFbyTAP_tiles',APTFFbyTAP_URL.'/js/aptffbytap_tiles.js','',APTFFbyTAP_VER);
    
    wp_deregister_style('APTFFbyTAP_widget_css'); // Since I wrote the scripts, deregistering and updating version are redundant in this case
    wp_register_style('APTFFbyTAP_widget_css',APTFFbyTAP_URL.'/css/aptffbytap_widget_style.css','',APTFFbyTAP_VER);
    wp_enqueue_style('APTFFbyTAP_widget_css');
    
  }
  add_action('wp_enqueue_scripts', 'APTFFbyTAP_enqueue_display_scripts');
  
	  
   // Register Widget
	function APTFFbyTAP_widget_register() {register_widget( 'Alpine_PhotoTile_for_Flickr' );}
  add_action('widgets_init','APTFFbyTAP_widget_register');
 
  include_once( 'admin/widget-options.php');
  include_once( 'admin/function-options-display.php'); 
  include_once( 'admin/function-options-sanitize.php'); 
  include_once( 'gears/source-flickr.php');
  include_once( 'gears/display-functions.php');
  include_once( 'gears/function-cache.php');
    
?>
