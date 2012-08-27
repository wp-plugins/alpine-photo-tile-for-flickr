<?php
/**
 * Alpine PhotoTile for Flickr: Widget Options
 *
 * @since 1.0.0
 *
 */
 
  function APTFFbyTAP_get_option( $option_string ){
    $options = get_option( APTFFbyTAP_SETTINGS );
    // No need to initialize options since defaults are applied as needed
    return ( NULL!==$options[$option_string] ? $options[$option_string] : APTFFbyTAP_set_default_option( $options, $option_string ) );
  }
  // Set default options
  function APTFFbyTAP_set_default_option( $options, $option_string ){
    $default_options = APTFFbyTAP_option_defaults();
    if( NULL !== $default_options[$option_string] ){
      $options[$option_string] = $default_options[$option_string]['default'];
      update_option( APTFFbyTAP_SETTINGS, $options );
      return $options[$option_string];
    }else{
      return NULL;
    }
  }

  function APTFFbyTAP_option_positions(){
    $options = array(
      'top' => array(
        'title' => '',
        'options' =>array('widget_title')
      ),
      'left' => array(
        'title' => 'Flickr Settings',
        'options' =>array('flickr_source','flickr_user_id','flickr_group_id','flickr_set_id','flickr_tags','flickr_display_link','flickr_display_link_text','flickr_image_link','flickr_photo_size' )
      ),
      'right' => array(
        'title' => 'Style Settings',
        'options' =>array('style_option','style_shape','style_gallery_height','style_photo_per_row','style_column_number','flickr_photo_number','style_shadow','style_border','style_highlight','style_curve_corners')
      ),
      'bottom' => array(
        'title' => 'Format Settings',
        'options' =>array('widget_alignment','widget_max_width','widget_disable_credit_link')
      ),
    );
    return $options;
  }
  
  function APTFFbyTAP_shortcode_option_positions(){
    $options = array(
      'left' => array(
        'title' => 'Flickr Settings',
        'options' =>array('flickr_source','flickr_user_id','flickr_group_id','flickr_set_id','flickr_tags','flickr_display_link','flickr_display_link_text','flickr_image_link','flickr_photo_size' )
      ),
      'right' => array(
        'title' => 'Style Settings',
        'options' =>array('style_option','style_shape','style_gallery_height','style_photo_per_row','style_column_number','flickr_photo_number','style_shadow','style_border','style_highlight','style_curve_corners')
      ),
      'bottom' => array(
        'title' => 'Format Settings',
        'options' =>array('widget_alignment','widget_max_width','widget_disable_credit_link')
      ),
    );
    return $options;
  }
  
  function APTFFbyTAP_admin_option_positions(){
    $options = array(
      'top' => array(
        'title' => 'Cache Options',
        'options' =>array('cache_disable','cache_time')
      ),
      'center' => array(
        'title' => 'Global Style Options',
        'options' =>array('general_loader','general_highlight_color')
      )
    );
    return $options;
  }
  
  function APTFFbyTAP_option_defaults(){
    $options = array(
      'widget_title' => array(
        'name' => 'widget_title',
        'title' => 'Title : ',
        'type' => 'text',
        'sanitize' => 'nohtml',
        'description' => '',
        'since' => '1.1',
        'default' => ''
      ),
      'flickr_source' => array(
        'name' => 'flickr_source',
        'short' => 'src',
        'title' => 'Retrieve Photos From : ',
        'type' => 'select',
        'valid_options' => array(
          'user' => array(
            'name' => 'user',
            'title' => 'User'
          ),
          'favorites' => array(
            'name' => 'favorites',
            'title' => 'Favorites'
          ),
          'group' => array(
            'name' => 'group',
            'title' => 'Group'
          ),
          'set' => array(
            'name' => 'set',
            'title' => 'Set'
          ),
          'community' => array(
            'name' => 'community',
            'title' => 'Community'
          )      
        ),
        'description' => '',
        'parent' => 'APTFFbyTAP-parent', 
        'trigger' => 'flickr_source',
        'default' => 'user'
      ),
      'flickr_user_id' => array(
        'name' => 'flickr_user_id',
        'short' => 'uid',
        'title' => 'Flickr User ID : ',
        'type' => 'text',
        'sanitize' => 'nospaces',
        'description' => "Don't know the ID? Use <a href='http://idgettr.com/' target='_blank'>idgettr.com</a> to find it.",
        'child' => 'flickr_source', 
        'hidden' => 'group community',
        'since' => '1.1',
        'default' => ''
      ),
      'flickr_group_id' => array(
        'name' => 'flickr_group_id',
        'short' => 'gid',
        'title' => 'Flickr Group ID : ',
        'type' => 'text',
        'sanitize' => 'nospaces',
        'description' => "Don't know the ID? Use <a href='http://idgettr.com/' target='_blank'>idgettr.com</a> to find it.",
        'child' => 'flickr_source', 
        'hidden' => 'user set community favorites',
        'since' => '1.1',
        'default' => ''
      ),  
      'flickr_set_id' => array(
        'name' => 'flickr_set_id',
        'short' => 'sid',
        'title' => 'Flickr Set ID : ',
        'type' => 'text',
        'sanitize' => 'nospaces',
        'description' => '',
        'child' => 'flickr_source', 
        'hidden' => 'group user community favorites',
        'since' => '1.1',
        'default' => ''
      ), 
      'flickr_tags' => array(
        'name' => 'flickr_tags',
        'short' => 'tags',
        'title' => 'Tag(s) : ',
        'type' => 'text',
        'sanitize' => 'nospaces',
        'description' => 'Comma seperated, no spaces',
        'child' => 'flickr_source',
        'hidden' => 'group user favorites set',
        'since' => '1.1',
        'default' => ''
      ),        
      'flickr_display_link' => array(
        'name' => 'flickr_display_link',
        'short' => 'dl',
        'title' => 'Display link to Flickr page.',
        'type' => 'checkbox',
        'description' => '',
        'child' => 'flickr_source',
        'hidden' => 'community',
        'since' => '1.1',
        'default' => ''
      ),    
      'flickr_display_link_text' => array(
        'name' => 'flickr_display_link_text',
        'short' => 'dltext',
        'title' => 'Link Text : ',
        'type' => 'text',
        'sanitize' => 'nohtml',
        'description' => '',
        'child' => 'flickr_source', 
        'hidden' => 'community',
        'since' => '1.1',
        'default' => 'Flickr'
      ),             
      'flickr_image_link' => array(
        'name' => 'flickr_image_link',
        'short' => 'imgl',
        'title' => 'Link images to Flickr source.',
        'type' => 'checkbox',
        'description' => '',
        'since' => '1.1',
        'default' => ''
      ),
      'flickr_photo_size' => array(
        'name' => 'flickr_photo_size',
        'short' => 'size',
        'title' => 'Photo Size : ',
        'type' => 'select',
        'valid_options' => array(
          '75' => array(
            'name' => 75,
            'title' => '75px'
          ),
          '100' => array(
            'name' => 100,
            'title' => '100px'
          ),
          '240' => array(
            'name' => 240,
            'title' => '240px'
          ),
          '320' => array(
            'name' => 320,
            'title' => '320px'
          ),
          '500' => array(
            'name' => 500,
            'title' => '500px'
          ),
          '640' => array(
            'name' => 640,
            'title' => '640px'
          )      
        ),
        'description' => '',
        'since' => '1.1',
        'default' => '100'
      ),
      'style_option' => array(
        'name' => 'style_option',
        'short' => 'style',
        'title' => 'Style : ',
        'type' => 'select',
        'valid_options' => array(
          'vertical' => array(
            'name' => 'vertical',
            'title' => 'Vertical'
          ),
          'windows' => array(
            'name' => 'windows',
            'title' => 'Windows'
          ),
          'bookshelf' => array(
            'name' => 'bookshelf',
            'title' => 'Bookshelf'
          ),
          'rift' => array(
            'name' => 'rift',
            'title' => 'Rift'
          ),
          'floor' => array(
            'name' => 'floor',
            'title' => 'Floor'
          ),
          'wall' => array(
            'name' => 'wall',
            'title' => 'Wall'
          ),
          'cascade' => array(
            'name' => 'cascade',
            'title' => 'Cascade'
          ),
          'gallery' => array(
            'name' => 'gallery',
            'title' => 'Gallery'
          )           
        ),
        'description' => '',
        'parent' => 'APTFFbyTAP-parent',
        'trigger' => 'style_option',
        'since' => '1.1',
        'default' => 'vertical'
      ),
      'style_shape' => array(
        'name' => 'style_shape',
        'short' => 'shape',
        'title' => 'Shape : ',
        'type' => 'select',
        'valid_options' => array(
          'rectangle' => array(
            'name' => 'rectangle',
            'title' => 'Rectangle'
          ),
          'square' => array(
            'name' => 'square',
            'title' => 'Square'
          )              
        ),
        'description' => '',
        'child' => 'style_option',
        'hidden' => 'vertical cascade floor wall rift bookshelf gallery',
        'since' => '1.1',
        'default' => 'vertical'
      ),          
      'style_photo_per_row' => array(
        'name' => 'style_photo_per_row',
        'short' => 'row',
        'title' => 'Photos per row : ',
        'type' => 'text',
        'sanitize' => 'int',
        'min' => '1',
        'max' => '500',
        'description' => 'Max of 500',
        'child' => 'style_option',
        'hidden' => 'vertical cascade windows',
        'since' => '1.1',
        'default' => '4'
      ),
      'style_column_number' => array(
        'name' => 'style_column_number',
        'short' => 'col',
        'title' => 'Number of columns : ',
        'type' => 'range',
        'min' => '1',
        'max' => '10',
        'description' => '',
        'child' => 'style_option',
        'hidden' => 'vertical floor wall bookshelf windows rift gallery',
        'since' => '1.1',
        'default' => '2'
      ),     
      'style_gallery_height' => array(
        'name' => 'style_gallery_height',
        'short' => 'gheight',
        'title' => 'Gallery Size : ',
        'type' => 'select',
        'valid_options' => array(
          '2' => array(
            'name' => 2,
            'title' => 'XS'
          ),
          '3' => array(
            'name' => 3,
            'title' => 'Small'
          ),
          '4' => array(
            'name' => 4,
            'title' => 'Medium'
          ),
          '5' => array(
            'name' => 5,
            'title' => 'Large'
          ),
          '6' => array(
            'name' => 6,
            'title' => 'XL'
          ),
          '7' => array(
            'name' => 7,
            'title' => 'XXL'
          )             
        ),
        'description' => '',
        'child' => 'style_option',
        'hidden' => 'vertical cascade floor wall rift bookshelf windows',
        'since' => '1.1',
        'default' => '3'
      ),     
      'flickr_photo_number' => array(
        'name' => 'flickr_photo_number',
        'short' => 'num',
        'title' => 'Number of photos : ',
        'type' => 'text',
        'sanitize' => 'int',
        'min' => '1',
        'max' => '500',
        'description' => 'Max of 500, though under 20 is recommended',
        'since' => '1.1',
        'default' => '4'
      ),
      'style_shadow' => array(
        'name' => 'style_shadow',
        'short' => 'shadow',
        'title' => 'Add slight image shadow.',
        'type' => 'checkbox',
        'description' => '',
        'since' => '1.1',
        'default' => ''
      ),   
      'style_border' => array(
        'name' => 'style_border',
        'short' => 'border',
        'title' => 'Add white image border.',
        'type' => 'checkbox',
        'description' => '',
        'since' => '1.1',
        'default' => ''
      ),   
      'style_highlight' => array(
        'name' => 'style_highlight',
        'short' => 'highlight',
        'title' => 'Highlight when hovering.',
        'type' => 'checkbox',
        'description' => '',
        'since' => '1.1',
        'default' => ''
      ),
      'style_curve_corners' => array(
        'name' => 'style_curve_corners',
        'short' => 'curve',
        'title' => 'Add slight curve to corners.',
        'type' => 'checkbox',
        'description' => '',
        'since' => '1.1',
        'default' => ''
      ),          
      'widget_alignment' => array(
        'name' => 'widget_alignment',
        'short' => 'align',
        'title' => 'Photo alignment : ',
        'type' => 'select',
        'valid_options' => array(
          'left' => array(
            'name' => 'left',
            'title' => 'Left'
          ),
          'center' => array(
            'name' => 'center',
            'title' => 'Center'
          ),
          'right' => array(
            'name' => 'right',
            'title' => 'Right'
          )            
        ),
        'since' => '1.1',
        'default' => 'center'
      ),    
      'widget_max_width' => array(
        'name' => 'widget_max_width',
        'short' => 'max',
        'title' => 'Max widget width (%) : ',
        'type' => 'text',
        'sanitize' => 'numeric',
        'min' => '1',
        'max' => '100',
        'description' => "To reduce the widget width, input a percentage (between 1 and 100). If photos are smaller than widget area, reduce percentage until desired width is achieved.",
        'since' => '1.1',
        'default' => '100'
      ),        
      'widget_disable_credit_link' => array(
        'name' => 'widget_disable_credit_link',
        'short' => 'nocredit',
        'title' => 'Disable the tiny link in the bottom left corner, though I have spent months developing this plugin and would appreciate the credit.',
        'type' => 'checkbox',
        'description' => '',
        'since' => '1.1',
        'default' => ''
      ), 
      'cache_disable' => array(
        'name' => 'cache_disable',
        'title' => 'Disable feed caching: ',
        'type' => 'checkbox',
        'description' => '',
        'since' => '1.1',
        'default' => ''
      ), 
      'cache_time' => array(
        'name' => 'cache_time',
        'title' => 'Cache time (hours) : ',
        'type' => 'text',
        'sanitize' => 'numeric',
        'min' => '1',
        'description' => "Set the number of hours that a feed will be stored.",
        'since' => '1.1',
        'default' => '3'
      ), 
      'general_loader' => array(
        'name' => 'general_loader',
        'title' => 'Disable Loading Icon: ',
        'type' => 'checkbox',
        'description' => 'Remove the icon that appears while images are loading.',
        'since' => '1.1',
        'default' => ''
      ), 
      'general_highlight_color' => array(
        'name' => 'general_highlight_color',
        'title' => 'Highlight Color:',
        'type' => 'color',
        'description' => 'Click to choose link color.',
        'section' => 'settings',
        'tab' => 'general',
        'since' => '1.2',
        'default' => '#64a2d8'
      ), 
    );
    return $options;
  }
  
 /**
 * Alpine PhotoTile for Flickr: Options Validate Pseudo-Callback
 *
 * @since 1.0.0
 *
 */
  function APTFFbyTAP_MenuDisplayCallback($options,$option,$fieldname,$fieldid){
      $default = $option['default'];
      $optionname = $option['name'];
      $optiontitle = $option['title'];
      $optiondescription = $option['description'];
      $fieldtype = $option['type'];
      $value = ( Null != $options[$optionname] ? $options[$optionname] : $default );
      
       // Output checkbox form field markup
      if ( 'checkbox' == $fieldtype ) {
        ?>
        <input type="checkbox" id="<?php echo $fieldid; ?>" name="<?php echo $fieldname; ?>" <?php checked( $value ); ?> />
        <label for="<?php echo $fieldid; ?>"><?php echo $optiontitle ?></label>
        <span class="description"><?php echo $optiondescription; ?></span>
        <?php
      }
      // Output radio button form field markup
      else if ( 'radio' == $fieldtype ) {
        $valid_options = array();
        $valid_options = $option['valid_options'];
        ?><label for="<?php echo $fieldid; ?>"><?php echo $optiontitle ?></label><?php
        foreach ( $valid_options as $valid_option ) {
          ?>
          <input type="radio" name="<?php echo $fieldname; ?>" <?php checked( $valid_option['name'] == $value ); ?> value="<?php echo $valid_option['name']; ?>" />
          <span class="description"><?php echo $optiondescription; ?></span>
          <?php
        }
      }
      // Output select form field markup
      else if ( 'select' == $fieldtype ) {
        $valid_options = array();
        $valid_options = $option['valid_options']; 
        ?>
        <label for="<?php echo $fieldid; ?>"><?php echo $optiontitle ?></label>
          <select id="<?php echo $fieldid ?>" name="<?php echo $fieldname; ?>" >
          <?php 
          foreach ( $valid_options as $valid_option ) {
            ?>
            <option <?php selected( $valid_option['name'] == $value ); ?> value="<?php echo $valid_option['name']; ?>" ><?php echo $valid_option['title']; ?></option>
            <?php
          }
          ?>
          </select>
          <span class="description"><?php echo $optiondescription; ?></span>
        
        <?php
      } // Output select form field markup
      else if ( 'range' == $fieldtype ) {     
        ?>
        <label for="<?php echo $fieldid; ?>"><?php echo $optiontitle ?></label>
          <select id="<?php echo $fieldid ?>" name="<?php echo $fieldname; ?>" >
          <?php 
          for($i = $option['min'];$i <= $option['max']; $i++){
            ?>
            <option <?php selected( $i == $value ); ?> value="<?php echo $i; ?>" ><?php echo $i ?></option>
            <?php
          }
          ?>
          </select>
          <span class="description"><?php echo $optiondescription; ?></span>
        
        <?php
      } 
      // Output text input form field markup
      else if ( 'text' == $fieldtype ) {
        ?>
        <label for="<?php echo $fieldid; ?>"><?php echo $optiontitle ?></label>
        <input type="text" id="<?php echo $fieldid ?>" name="<?php echo $fieldname; ?>" value="<?php echo wp_filter_nohtml_kses( $value ); ?>" />
        <div class="description"><span class="description"><?php echo $optiondescription; ?></span></div>
        <?php
      } 
      else if ( 'textarea' == $fieldtype ) {
        ?>
        <label for="<?php echo $fieldid; ?>"><?php echo $optiontitle ?></label>
        <textarea id="<?php echo $fieldid ?>" name="<?php echo $fieldname; ?>" class="APTFFbyTAP_textarea" ><?php echo $value; ?></textarea><br>
        <span class="description"><?php echo esc_textarea( $optiondescription ); ?></span>
        <?php
      }   
      else if ( 'color' == $fieldtype ) {
        ?>
        
        <label for="<?php echo $fieldid ?>">
        <input type="text" id="<?php echo $fieldid ?>" name="<?php echo $fieldname; ?>" class="APTFFbyTAP_color"  value="<?php echo wp_filter_nohtml_kses( $value ); ?>" /><span class="description"><?php echo $optiondescription; ?></span></label>
       
        <div id="<?php echo $fieldid; ?>_picker" class="APTFFbyTAP_color_picker" ></div>

        <?php
      }
  }



  function APTFFbyTAP_AdminDisplayCallback($options,$option,$fieldname,$fieldid){
      $default = $option['default'];
      $optionname = $option['name'];
      $optiontitle = $option['title'];
      $optiondescription = $option['description'];
      $fieldtype = $option['type'];
      $value = ( Null != $options[$optionname] ? $options[$optionname] : $default );
      
       // Output checkbox form field markup
      if ( 'checkbox' == $fieldtype ) {
        ?>
        <div class="title"><label for="<?php echo $fieldid; ?>"><?php echo $optiontitle ?></label></div>
        <input type="checkbox" id="<?php echo $fieldid; ?>" name="<?php echo $fieldname; ?>" <?php checked( $value ); ?> />
        <span class="description"><?php echo $optiondescription; ?></span>
        <?php
      }
      // Output radio button form field markup
      else if ( 'radio' == $fieldtype ) {
        $valid_options = array();
        $valid_options = $option['valid_options'];
        ?><label for="<?php echo $fieldid; ?>"><?php echo $optiontitle ?></label><?php
        foreach ( $valid_options as $valid_option ) {
          ?>
          <input type="radio" name="<?php echo $fieldname; ?>" <?php checked( $valid_option['name'] == $value ); ?> value="<?php echo $valid_option['name']; ?>" />
          <span class="description"><?php echo $optiondescription; ?></span>
          <?php
        }
      }
      // Output select form field markup
      else if ( 'select' == $fieldtype ) {
        $valid_options = array();
        $valid_options = $option['valid_options']; 
        ?>
        <label for="<?php echo $fieldid; ?>"><?php echo $optiontitle ?></label>
          <select id="<?php echo $fieldid ?>" name="<?php echo $fieldname; ?>" >
          <?php 
          foreach ( $valid_options as $valid_option ) {
            ?>
            <option <?php selected( $valid_option['name'] == $value ); ?> value="<?php echo $valid_option['name']; ?>" ><?php echo $valid_option['title']; ?></option>
            <?php
          }
          ?>
          </select>
          <span class="description"><?php echo $optiondescription; ?></span>
        
        <?php
      } // Output select form field markup
      else if ( 'range' == $fieldtype ) {     
        ?>
        <label for="<?php echo $fieldid; ?>"><?php echo $optiontitle ?></label>
          <select id="<?php echo $fieldid ?>" name="<?php echo $fieldname; ?>" >
          <?php 
          for($i = $option['min'];$i <= $option['max']; $i++){
            ?>
            <option <?php selected( $i == $value ); ?> value="<?php echo $i; ?>" ><?php echo $i ?></option>
            <?php
          }
          ?>
          </select>
          <span class="description"><?php echo $optiondescription; ?></span>
        
        <?php
      } 
      // Output text input form field markup
      else if ( 'text' == $fieldtype ) {
        ?>
        <div class="title"><label for="<?php echo $fieldid; ?>"><?php echo $optiontitle ?></label></div>
        <input type="text" id="<?php echo $fieldid ?>" name="<?php echo $fieldname; ?>" value="<?php echo wp_filter_nohtml_kses( $value ); ?>" />
        <span class="description"><?php echo $optiondescription; ?></span>
        <?php
      } 
      else if ( 'textarea' == $fieldtype ) {
        ?>
        <label for="<?php echo $fieldid; ?>"><?php echo $optiontitle ?></label>
        <textarea id="<?php echo $fieldid ?>" name="<?php echo $fieldname; ?>" class="APTFFbyTAP_textarea" ><?php echo $value; ?></textarea><br>
        <span class="description"><?php echo esc_textarea( $optiondescription ); ?></span>
        <?php
      }   
      else if ( 'color' == $fieldtype ) {
        ?>
        
        <div class="title"><label for="<?php echo $fieldid; ?>"><?php echo $optiontitle ?></label></div>
        <input type="text" id="<?php echo $fieldid ?>" name="<?php echo $fieldname; ?>" class="APTFFbyTAP_color"  value="<?php echo wp_filter_nohtml_kses( $value ); ?>" /><span class="description"><?php echo $optiondescription; ?></span></label>
       
        <div id="<?php echo $fieldid; ?>_picker" class="APTFFbyTAP_color_picker" ></div>

        <?php
      }
  }


/**
 * Alpine PhotoTile for Flickr: Options Validate Pseudo-Callback
 *
 * @since 1.0.0
 *
 */


  function APTFFbyTAP_MenuOptionsValidate( $newinput, $oldinput, $optiondetails ) {
      $valid_input = $oldinput;

      // Validate checkbox fields
      if ( 'checkbox' == $optiondetails['type'] ) {
        // If input value is set and is true, return true; otherwise return false
        $valid_input = ( ( isset( $newinput ) && true == $newinput ) ? true : false );
      }
      // Validate radio button fields
      else if ( 'radio' == $optiondetails['type'] ) {
        // Get the list of valid options
        $valid_options = $optiondetails['valid_options'];
        // Only update setting if input value is in the list of valid options
        $valid_input = ( array_key_exists( $newinput, $valid_options ) ? $newinput : $valid_input );
      }
      // Validate select fields
      else if ( 'select' == $optiondetails['type'] || 'select-trigger' == $optiondetails['type']) {
        // Get the list of valid options
        $valid_options = $optiondetails['valid_options'];
        // Only update setting if input value is in the list of valid options
        $valid_input = ( array_key_exists( $newinput, $valid_options ) ? $newinput : $valid_input );
      }
      else if ( 'range' == $optiondetails['type'] ) {
        // Only update setting if input value is in the list of valid options
        $valid_input = ( ($newinput>=$optiondetails['min'] && $newinput<=$optiondetails['max']) ? $newinput : $valid_input );
      }    
      // Validate text input and textarea fields
      else if ( ( 'text' == $optiondetails['type'] || 'textarea' == $optiondetails['type'] || 'image-upload' == $optiondetails['type']) ) {
        // Check if numeric
        if ( 'numeric' == $optiondetails['sanitize'] && is_numeric( wp_filter_nohtml_kses( $newinput ) ) ) {
          // Pass input data through the wp_filter_nohtml_kses filter
          $valid_input = wp_filter_nohtml_kses( $newinput );
          if( NULL !== $optiondetails['min'] && $valid_input<$optiondetails['min']){
            $valid_input = $optiondetails['min'];
          }
          if( NULL !== $optiondetails['max'] && $valid_input>$optiondetails['max']){
            $valid_input = $optiondetails['max'];
          }
        }
        if ( 'int' == $optiondetails['sanitize'] && is_numeric( wp_filter_nohtml_kses( $newinput ) ) ) {
          // Pass input data through the wp_filter_nohtml_kses filter
          $valid_input = round( wp_filter_nohtml_kses( $newinput ) );
          if( NULL !== $optiondetails['min'] && $valid_input<$optiondetails['min']){
            $valid_input = $optiondetails['min'];
          }
          if( NULL !== $optiondetails['max'] && $valid_input>$optiondetails['max']){
            $valid_input = $optiondetails['max'];
          }
        }      
        // Validate no-HTML content
        if ( 'nospaces' == $optiondetails['sanitize'] ) {
          // Pass input data through the wp_filter_nohtml_kses filter
          $valid_input = wp_filter_nohtml_kses( $newinput );
          $valid_input = str_replace(' ','',$valid_input);
        }      
        if ( 'tag' == $optiondetails['sanitize'] ) {
          // Pass input data through the wp_filter_nohtml_kses filter
          $valid_input = wp_filter_nohtml_kses( $newinput );
          $valid_input = str_replace(' ','-',$valid_input);
        }            
        // Validate no-HTML content
        if ( 'nohtml' == $optiondetails['sanitize'] ) {
          // Pass input data through the wp_filter_nohtml_kses filter
          $valid_input = wp_filter_nohtml_kses( $newinput );
          $valid_input = wp_filter_nohtml_kses( $newinput );
        }
        // Validate HTML content
        if ( 'html' == $optiondetails['sanitize'] ) {
          // Pass input data through the wp_filter_kses filter using allowed post tags
          $valid_input = wp_kses_post($newinput );
        }
        // Validate URL address
        if( 'url' == $optiondetails['sanitize'] ){
          $valid_input = esc_url( $newinput );
        }
        // Validate URL address
        if( 'css' == $optiondetails['sanitize'] ){
          $valid_input = wp_htmledit_pre( stripslashes( $newinput ) );
        }      
      }else if( 'wp-textarea' == $optiondetails['type'] ){
          // Text area filter
          $valid_input = wp_kses_post( force_balance_tags($newinput) );
      }
      elseif( 'color' == $optiondetails['type'] ){
        $value =  wp_filter_nohtml_kses( $newinput );
        if( '#' == $value ){
          $valid_input = '';
        }else{
          $valid_input = $value;
        }
      }
      
      return $valid_input;
  }

  ?>
