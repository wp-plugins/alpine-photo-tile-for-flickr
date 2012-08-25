<?php
/**
 * Alpine PhotoTile for Flickr: Widget Form Generation
 *
 * @since 1.0.0
 *
 */
?>
  
  <?php $widget_container = $this->get_field_id( 'APTFFbyTAP-flickr' ); ?>

  <div id="<?php echo $widget_container ?>" class="APTFFbyTAP-flickr">
  <?php
    $defaults = APTFFbyTAP_option_defaults();
    $positions = APTFFbyTAP_option_positions();
 
  if( count($positions) ){
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
                $fieldname = $this->get_field_name( $option['name'] );
                $fieldid = $this->get_field_id( $option['name'] );
                if($option['parent']){
                  $class = $option['parent'];
                }elseif($option['child']){
                  $class = $this->get_field_id($option['child']);
                }else{
                  $class = $this->get_field_id('unlinked');
                }
                $trigger = ($option['trigger']?('data-trigger="'.($this->get_field_id($option['trigger'])).'"'):'');
                $hidden = ($option['hidden']?' '.$option['hidden']:'');
                
                ?> <tr valign="top"> <td class="<?php echo $class; ?><?php echo $hidden; ?>" <?php echo $trigger; ?>><?php
                if ( function_exists( 'theAlpinePressMenuDisplayCallbackV1' ) ) {
                  theAlpinePressMenuDisplayCallbackV1($options,$option,$fieldname,$fieldid);
                }  
                ?> </td></tr> <?php
              }
            }?>
          </tbody>  
        </table>
      </div>
    <?php
    }
  }
  ?>
  </div> 
  <div><span><?php _e('Need Help? Visit ') ?><a href="<?php echo APTFFbyTAP_INFO; ?>" target="_blank">the Alpine Press</a> <?php _e('for more about this plugin.') ?></span></div>
