<?php
/**
 * The PHP for widget form function
 *
 * @since 1.0.0
 *
 */
?>
  
  <?php $widget_container = $this->get_field_id( 'PTFFbyTAP-flickr' ); ?>

  <div id="<?php echo $widget_container ?>" class="PTFFbyTAP-flickr">
  <?php
    $defaults = thealpinepress_plugin_defaults();
    $positions = thealpinepress_option_positions();
 
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
                $class = ($option['link']?($option['link']):($this->get_field_id('general')) );
                $hidden = ($option['hidden']?' '.$option['hidden']:'');
                $trigger = ($option['trigger']?('data-trigger="'.($option['trigger']).'"'):'');
                ?> <tr valign="top"> <td class="<?php echo $class; ?><?php echo $hidden; ?>"  <?php echo $trigger; ?> ><?php
                  thealpinepress_display_callback($options,$option,$fieldname,$fieldid);
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

