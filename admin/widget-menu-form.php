<?php
/**
 * The PHP for widget form function
 *
 * @since 1.0.0
 *
 */

 ?>

 
 <?php
    $defaults = tap_plugin_defaults();
    $positions = tap_option_positions();
 
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
                $class = ($option['link']?($this->get_field_id($option['link'])):($this->get_field_id('general')) );
                $hidden = ($option['hidden']?' '.$option['hidden']:'');
                $trigger = ($option['trigger']?'data-trigger="'.($this->get_field_id($option['trigger'])).'"':'');
                ?> <tr valign="top"> <td id="<?php echo $fieldid; ?>_td" class="<?php echo $class; ?><?php echo $hidden; ?>"  <?php echo $trigger; ?> ><?php
                  display_callback($options,$option,$fieldname,$fieldid);
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
  <script type="text/javascript">
  jQuery(document).ready(function() {
    if( jQuery().theAlpinePressWidgetMenuPlugin  ){
      jQuery('.PTFFbyTAP-flickr .<?php echo ($this->get_field_id( 'parent' )); ?>').theAlpinePressWidgetMenuPlugin();
    }
  });
  </script>

