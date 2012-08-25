<?php
/**
 * Alpine PhotoTile for Flickr: Options Page
 *
 * @since 1.1.1
 *
 */

/**
 * Setup the Theme Admin Settings Page
 * 
 */
function APTFFbyTAP_admin_options() {
	$page = add_options_page(__('Alpine PT: Flickr',APTFFbyTAP_SETTINGS), __('Alpine PT: Flickr',APTFFbyTAP_SETTINGS), 'manage_options', APTFFbyTAP_SETTINGS , 'APTFFbyTAP_admin_options_page');
  
  /* Using registered $page handle to hook script load */
  add_action('admin_print_scripts-' . $page, 'APTFFbyTAP_enqueue_admin_scripts');
}
// Load the Admin Options page
add_action('admin_menu', 'APTFFbyTAP_admin_options');


/**
 * Settings Page Markup
 */
function APTFFbyTAP_admin_options_page() { 
  if (!current_user_can('manage_options')) {
    wp_die( __('You do not have sufficient permissions to access this page.') );
  }

	$currenttab = APTFFbyTAP_get_current_tab();
	$settings_section = 'APTFFbyTAP_' . $currenttab . '_tab';
  $submitted = ( ( isset($_POST[ "hidden" ]) && ($_POST[ "hidden" ]=="Y") ) ? true : false );
  
  $options = get_option( APTFFbyTAP_SETTINGS );
  
  if( $submitted ){
    $oldoptions = $options;
    $newoptions = $_POST;
    $optiondetails = APTFFbyTAP_option_defaults();  
    if ( function_exists( 'APTFFbyTAP_MenuOptionsValidate' ) ) {
      foreach( $optiondetails as $id=>$input ){
        $options[$id] = APTFFbyTAP_MenuOptionsValidate( $newoptions[$id],$oldoptions[$id],$optiondetails[$id] );
      }
    }else{
      $options = $newoptions;
    }
    update_option( APTFFbyTAP_SETTINGS, $options);

    if( 'generator' == $currenttab ) {
      $short = APTFFbyTAP_generate_shortcode( $options, $optiondetails );
    }
  }
  
    
	?>

	<div class="wrap APTFFbyTAP_settings_wrap">
		<?php APTFFbyTAP_admin_options_page_tabs( $currenttab ); ?>
		<?php if ( isset( $_GET['settings-updated'] ) ) {
    			echo "<div class='updated'><p>Theme settings updated successfully.</p></div>";
		} ?>
    <?php if( 'general' == $currenttab ){ ?>
      <?php
      APTFFbyTAP_display_general();
      ?>
    <?php }else{ ?>
		<form action="" method="post">
    <input type="hidden" name="hidden" value="Y">
      <?php 
      APTFFbyTAP_display_options_form($options,$currenttab,$short);
      ?>
		</form>
    <?php } ?>
	</div>
<?php 
}

/**
 * Get current settings page tab
 */
function APTFFbyTAP_get_current_tab( $current = 'general' ) {
    if ( isset ( $_GET['tab'] ) ) :
        $current = $_GET['tab'];
    else:
        $current = 'general';
    endif;
	
	return $current;
}

/**
 * Define Settings Page Tab Markup
 * 
 * @link`http://www.onedesigns.com/tutorials/separate-multiple-theme-options-pages-using-tabs	Daniel Tara
 */
function APTFFbyTAP_admin_options_page_tabs( $current = 'general' ) {

    $tabs = APTFFbyTAP_get_settings_page_tabs();
    $links = array();
    
    foreach( $tabs as $tab ) :
		$tabname = $tab['name'];
		$tabtitle = $tab['title'];
        if ( $tabname == $current ) :
            $links[] = "<a class='nav-tab nav-tab-active' href='?page=".APTFFbyTAP_SETTINGS."&tab=$tabname'>$tabtitle</a>";
        else :
            $links[] = "<a class='nav-tab' href='?page=".APTFFbyTAP_SETTINGS."&tab=$tabname'>$tabtitle</a>";
        endif;
    endforeach;
    
    echo '<div style="width:100%;display:block;padding:0;line-height:2.6em;"><div class="icon32 icon-alpine"><br></div><h2>'.APTFFbyTAP_NAME.'</h2></div>';
    echo '<h2 class="nav-tab-wrapper">';
    foreach ( $links as $link )
        echo $link;
    echo '</h2>';
    
}


/**
 * Separate settings by tab
 */
function APTFFbyTAP_get_settings_by_tab() {
	$tabs = APTFFbyTAP_get_settings_page_tabs();
	$tabnames = array();
	foreach ( $tabs as $tab ) {
		$tabname = $tab['name'];
		$tabnames[] = $tabname;
	}
	$settingsbytab = $tabnames;
	$default_options = APTFFbyTAP_option_defaults();
	foreach ( $default_options as $default_option ) {
		if ( 'internal' != $default_option['type'] ) {
			$optiontab = $default_option['tab'];
			$optionname = $default_option['name'];
			$settingsbytab[$optiontab][] = $optionname;
		}
	}
	return $settingsbytab;
}


/**
 * Plugin Admin Settings Page Tabs
 * 
 */
function APTFFbyTAP_get_settings_page_tabs() {
	
	$tabs = array( 
    'general' => array(
			'name' => 'general',
			'title' => 'General',
		),
    'generator' => array(
			'name' => 'generator',
			'title' => 'Generator',
		),
    'plugin-settings' => array(
			'name' => 'plugin-settings',
			'title' => 'Plugin Settings',
		)
  );
	return $tabs;
}


function APTFFbyTAP_display_options_form($options,$currenttab,$short){
  $widget_container = ( 'APTFFbyTAP-flickr' ); 
  $defaults = APTFFbyTAP_option_defaults();
  if( 'generator' == $currenttab ) {
    $positions = APTFFbyTAP_shortcode_option_positions();
    ?> 
    <br><input name="<?php echo APTFFbyTAP_SETTINGS.'_'.$currenttab ?>[submit-<?php echo $currenttab; ?>]" type="submit" class="button-primary" value="Generate Shortcode" /> 
    <?php
    if($short){
      echo '<div id="'.APTFFbyTAP_SETTINGS.'-shortcode" style="margin:10px 0 0 0;" ><div style="padding:5px;margin:10px 0;display:inline-block;position:relative;background-color:#FFFFE0;border:1px solid #E6DB55;"> Now, copy (Crtl+C) and paste (Crtl+P) the following shortcode into a page or post. </div>';
      
      echo '<div><textarea class="auto_select" style="height:auto;width:100%;max-width:700px;background:#E0E0E0;padding:10px;">'.$short.'</textarea></div><br clear="all"/></div>';
    }
  }elseif( 'plugin-settings' == $currenttab ){
    $positions = APTFFbyTAP_admin_option_positions();
  }
  ?>

  <div id="<?php echo $widget_container ?>" class="APTFFbyTAP-flickr <?php echo $currenttab ?>">
  <?php
  

 
  if( count($positions) && function_exists( 'APTFFbyTAP_AdminDisplayCallback' ) ){
    foreach( $positions as $position=>$positionsinfo){
    ?>
      <div class="<?php echo $position ?>"> 
        <?php if( $positionsinfo['title'] ){ ?><h4><?php echo $positionsinfo['title']; ?></h4><?php } ?>
        <table class="form-table">
          <tbody>
            <?php
            if( count($positionsinfo['options']) ){
              foreach( $positionsinfo['options'] as $optionname ){
                $option = $defaults[$optionname];
                $fieldname = ( $option['name'] );
                $fieldid = ( $option['name'] );

                if( 'generator' == $currenttab ){
                  if($option['parent']){
                    $class = $option['parent'];
                  }elseif($option['child']){
                    $class =($option['child']);
                  }else{
                    $class = ('unlinked');
                  }
                  $trigger = ($option['trigger']?('data-trigger="'.(($option['trigger'])).'"'):'');
                  $hidden = ($option['hidden']?' '.$option['hidden']:'');
                  
                  ?> <tr valign="top"> <td class="<?php echo $class; ?><?php echo $hidden; ?>" <?php echo $trigger; ?>><?php
                    APTFFbyTAP_MenuDisplayCallback($options,$option,$fieldname,$fieldid);
                  ?> </td></tr> <?php     
                }else{
                  ?> <tr valign="top"> <td><?php
                    APTFFbyTAP_AdminDisplayCallback($options,$option,$fieldname,$fieldid);
                  ?> </td></tr> <?php   
                }     
              }
            }?>
          </tbody>  
        </table>
      </div>
    <?php
    }
  }
  ?>
  <div class="help-link"><span><?php _e('Need Help? Visit ') ?><a href="<?php echo APTFFbyTAP_INFO; ?>" target="_blank">the Alpine Press</a> <?php _e('for more about this plugin.') ?></span></div>
  </div> 

  <?php
  if( 'generator' == $currenttab ) {
    ?> <input name="<?php echo APTFFbyTAP_SETTINGS.'_'.$currenttab ?>[submit-<?php echo $currenttab; ?>]" type="submit" class="button-primary" value="Generate Shortcode" /> <?php
  }elseif( 'plugin-settings' == $currenttab ){
    ?> <input name="<?php echo APTFFbyTAP_SETTINGS.'_'.$currenttab ?>[submit-<?php echo $currenttab; ?>]" type="submit" class="button-primary" value="Save Settings" /> <?php
  }

}


function APTFFbyTAP_display_general(){ 
  ?>
  <div class="APTFFbyTAP-flickr" style="max-width:700px;padding:10px;">
    <p>
    <?php _e("Thank you for downloading the Alpine PhotoTile for Flickr, a WordPress plugin by the Alpine Press. On the 'Generator' tab you will find an easy to use shortcode generator that will allow you to insert the PhotoTile plugin in posts and pages. The 'Plugin Settings' tab provides additional back-end options (currently limited to Cache Options)."); ?>
    <div><p><?php _e('If you liked this plugin, try out some of the other plugins by ') ?><a href="http://thealpinepress.com/category/plugins/" target="_blank">the Alpine Press</a><?php _e(' and rate us at ') ?><a href="http://wordpress.org/extend/plugins/alpine-photo-tile-for-flickr/" target="_blank">WordPress.org</a>.</p></div>
    <div class="help-link"><p><?php _e('Need Help? Visit ') ?><a href="<?php echo APTFFbyTAP_INFO; ?>" target="_blank">the Alpine Press</a> <?php _e('for more about this plugin.') ?></p></div>
    </p>
  </div>
  <?php
}



?>