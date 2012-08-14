<?php
/**
 * Alpine PhotoTile for Flickr: Widget Options
 *
 * @since 1.0.0
 *
 */
 
  function APTFFbyTAP_option_positions(){
    $options = array(
      'top' => array(
        'title' => '',
        'options' =>array('widget_title')
      ),
      'left' => array(
        'title' => 'Flickr Settings',
        'options' =>array('flickr_source','flickr_user_id','flickr_group_id','flickr_set_id','flickr_tags','flickr_image_link','flickr_display_link','flickr_photo_size' )
      ),
      'right' => array(
        'title' => 'Style Settings',
        'options' =>array('style_option','style_shape','style_photo_per_row','style_column_number','flickr_photo_number','style_shadow','style_border','style_curve_corners')
      ),
      'bottom' => array(
        'title' => 'Format Settings',
        'options' =>array('widget_alignment','widget_max_width','widget_disable_credit_link')
      ),
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
        'link' => 'APTFFbyTAP-parent', 
        'trigger' => 'flickr_source',
        'default' => 'user'
      ),
      'flickr_user_id' => array(
        'name' => 'flickr_user_id',
        'title' => 'Flickr User ID : ',
        'type' => 'text',
        'sanitize' => 'nospaces',
        'description' => "Don't know the ID? Use <a href='http://idgettr.com/' target='_blank'>idgettr.com</a> to find it.",
        'link' => 'flickr_source', 
        'hidden' => 'group community',
        'since' => '1.1',
        'default' => ''
      ),
      'flickr_group_id' => array(
        'name' => 'flickr_group_id',
        'title' => 'Flickr Group ID : ',
        'type' => 'text',
        'sanitize' => 'nospaces',
        'description' => "Don't know the ID? Use <a href='http://idgettr.com/' target='_blank'>idgettr.com</a> to find it.",
        'link' => 'flickr_source', 
        'hidden' => 'user set community favorites',
        'since' => '1.1',
        'default' => ''
      ),  
      'flickr_set_id' => array(
        'name' => 'flickr_set_id',
        'title' => 'Flickr Set ID : ',
        'type' => 'text',
        'sanitize' => 'nospaces',
        'description' => '',
        'link' => 'flickr_source', 
        'hidden' => 'group user community favorites',
        'since' => '1.1',
        'default' => ''
      ), 
      'flickr_tags' => array(
        'name' => 'flickr_tags',
        'title' => 'Tag(s) : ',
        'type' => 'text',
        'sanitize' => 'nospaces',
        'description' => 'Comma seperated, no spaces',
        'link' => 'flickr_source',
        'hidden' => 'group user favorites set',
        'since' => '1.1',
        'default' => ''
      ),            
      'flickr_image_link' => array(
        'name' => 'flickr_image_link',
        'title' => 'Link images to Flickr source.',
        'type' => 'checkbox',
        'description' => '',
        'since' => '1.1',
        'default' => ''
      ),
      'flickr_display_link' => array(
        'name' => 'flickr_display_link',
        'title' => 'Display link to Flickr page.',
        'type' => 'checkbox',
        'description' => '',
        'since' => '1.1',
        'default' => ''
      ),      
      'flickr_photo_size' => array(
        'name' => 'flickr_photo_size',
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
        'link' => 'APTFFbyTAP-parent',
        'trigger' => 'style_option',
        'since' => '1.1',
        'default' => 'vertical'
      ),
      'style_shape' => array(
        'name' => 'style_shape',
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
        'link' => 'style_option',
        'hidden' => 'vertical cascade floor rift bookshelf gallery',
        'since' => '1.1',
        'default' => 'vertical'
      ),          
      'style_photo_per_row' => array(
        'name' => 'style_photo_per_row',
        'title' => 'Photos per row : ',
        'type' => 'range',
        'min' => '1',
        'max' => '20',
        'description' => '',
        'link' => 'style_option',
        'hidden' => 'vertical cascade windows',
        'since' => '1.1',
        'default' => '4'
      ),
      'style_column_number' => array(
        'name' => 'style_column_number',
        'title' => 'Number of columns : ',
        'type' => 'range',
        'min' => '1',
        'max' => '10',
        'description' => '',
        'link' => 'style_option',
        'hidden' => 'vertical floor bookshelf windows rift gallery',
        'since' => '1.1',
        'default' => '2'
      ),          
      'flickr_photo_number' => array(
        'name' => 'flickr_photo_number',
        'title' => 'Number of photos : ',
        'type' => 'range',
        'min' => '1',
        'max' => '20',
        'description' => '',
        'since' => '1.1',
        'default' => '4'
      ),
      'style_shadow' => array(
        'name' => 'style_shadow',
        'title' => 'Add slight image shadow.',
        'type' => 'checkbox',
        'description' => '',
        'since' => '1.1',
        'default' => ''
      ),   
      'style_border' => array(
        'name' => 'style_border',
        'title' => 'Add white image border.',
        'type' => 'checkbox',
        'description' => '',
        'since' => '1.1',
        'default' => ''
      ),
      'style_curve_corners' => array(
        'name' => 'style_curve_corners',
        'title' => 'Add slight curve to corners.',
        'type' => 'checkbox',
        'description' => '',
        'since' => '1.1',
        'default' => ''
      ),          
      'widget_alignment' => array(
        'name' => 'widget_alignment',
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
        'title' => 'Max widget width (%) : ',
        'type' => 'text',
        'sanitize' => 'int',
        'min' => '1',
        'max' => '100',
        'description' => "To reduce the widget width, input a percentage (between 1 and 100). If photos are smaller than widget area, reduce percentage until desired width is achieved.",
        'since' => '1.1',
        'default' => '100'
      ),        
      'widget_disable_credit_link' => array(
        'name' => 'widget_disable_credit_link',
        'title' => 'Disable the tiny link in the bottom left corner, though I have spent months developing this plugin and would appreciate the credit.',
        'type' => 'checkbox',
        'description' => '',
        'since' => '1.1',
        'default' => ''
      ),      
    );
    return $options;
  }
  
  
  ?>
