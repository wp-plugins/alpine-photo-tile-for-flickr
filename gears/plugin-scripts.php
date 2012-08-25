<?php
/**
 * Alpine PhotoTile for Flickr: Styles and Scripts
 *
 * @since 1.1.1
 *
 */
 
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////  Safely Enqueue Scripts  and Register Widget  ////////////////////////////////////
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  
  // Load Admin JS and CSS
	function APTFFbyTAP_admin_widget_script($hook){ 
    wp_deregister_style('upwbyeth_tabs_ui');     
    wp_register_style('upwbyeth_tabs_ui',APTFFbyTAP_URL.'/css/upwbyeth_tabs_ui.css','',APTFFbyTAP_VER);
    // upwbyeth_tabs_ui.css is registered by after options page is created

    wp_deregister_script('APTFFbyTAP_widget_menu');
    wp_register_script('APTFFbyTAP_widget_menu',APTFFbyTAP_URL.'/js/aptffbytap_widget_menu.js','',APTFFbyTAP_VER);

    wp_deregister_style('APTFFbyTAP_admin_css');   
    wp_register_style('APTFFbyTAP_admin_css',APTFFbyTAP_URL.'/css/aptffbytap_admin_style.css','',APTFFbyTAP_VER);
    
    wp_enqueue_style( 'farbtastic' );
    wp_enqueue_script( 'farbtastic' );
    
    if( 'widgets.php' != $hook )
      return;
      
    wp_enqueue_script( 'jquery');
  
    wp_enqueue_script('APTFFbyTAP_widget_menu');
        
    wp_enqueue_style('APTFFbyTAP_admin_css');
    
    add_action('admin_print_footer_scripts', 'APTFFbyTAP_menu_toggles');
    
    // Only admin can trigger two week cache cleaning by visiting widgets.php
    $disablecache = APTFFbyTAP_get_option( 'cache_disable' );
    if ( class_exists( 'theAlpinePressSimpleCacheV2' ) && APTFFbyTAP_CACHE && !$disablecache ) {
      $cache = new theAlpinePressSimpleCacheV2();
      $cache->setCacheDir( APTFFbyTAP_CACHE );
      $cache->clean();
    }
	}
  add_action('admin_enqueue_scripts', 'APTFFbyTAP_admin_widget_script'); 
  
  function APTFFbyTAP_menu_toggles(){
      
    ?>
    <script type="text/javascript">
    if( jQuery().APTFFbyTAPWidgetMenuPlugin  ){
      jQuery(document).ready(function(){
        jQuery('.APTFFbyTAP-flickr .APTFFbyTAP-parent').APTFFbyTAPWidgetMenuPlugin();
        
        jQuery(document).ajaxComplete(function() {
          jQuery('.APTFFbyTAP-flickr .APTFFbyTAP-parent').APTFFbyTAPWidgetMenuPlugin();
        });
      });
    }
    </script>  
    <?php   
  }
  function APTFFbyTAP_shortcode_select(){
    ?>
    <script type="text/javascript">
     jQuery(".auto_select").mouseenter(function(){
        jQuery(this).select();
      }); 
      if( jQuery('#<?php echo APTFFbyTAP_SETTINGS; ?>-shortcode') ){

        jQuery("html,body").animate({ scrollTop: (jQuery('#<?php echo APTFFbyTAP_SETTINGS; ?>-shortcode').offset().top-70) }, 2000);
      
      }

    </script>  
    <?php
  }
  // Load Display JS and CSS
  function APTFFbyTAP_enqueue_display_scripts() {
    wp_enqueue_script( 'jquery' );
    
    wp_deregister_script('APTFFbyTAP_tiles');
    wp_enqueue_script('APTFFbyTAP_tiles',APTFFbyTAP_URL.'/js/aptffbytap_tiles.js','',APTFFbyTAP_VER);
    
    wp_deregister_style('APTFFbyTAP_widget_css'); // Since I wrote the scripts, deregistering and updating version are redundant in this case
    wp_register_style('APTFFbyTAP_widget_css',APTFFbyTAP_URL.'/css/aptffbytap_widget_style.css','',APTFFbyTAP_VER);
    wp_enqueue_style('APTFFbyTAP_widget_css');
    
  }
  add_action('wp_enqueue_scripts', 'APTFFbyTAP_enqueue_display_scripts');
  
  
/**
 * Enqueue admin scripts (and related stylesheets)
 */
  function APTFFbyTAP_enqueue_admin_scripts() {

    wp_enqueue_script( 'jquery' );
    
    wp_enqueue_script('APTFFbyTAP_widget_menu');
    wp_enqueue_style('APTFFbyTAP_admin_css');
    
    add_action('admin_print_footer_scripts', 'APTFFbyTAP_menu_toggles'); 
    add_action('admin_print_footer_scripts', 'APTFFbyTAP_shortcode_select'); 
  }
?>