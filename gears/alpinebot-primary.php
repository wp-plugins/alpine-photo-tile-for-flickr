<?php


class PhotoTileForFlickrBase {  

  /* Set constants for plugin */
  public $url;
  public $dir;
  public $cacheDir;
  public $ver = '1.2.1';
  public $vers = '1-2-1';
  public $domain = 'APTFFbyTAP_domain';
  public $settings = 'alpine-photo-tile-for-flickr-settings'; // All lowercase
  public $name = 'Alpine Photo Tile for Flickr';
  public $info = 'http://thealpinepress.com/alpine-phototile-for-flickr/';
  public $wplink = 'http://wordpress.org/extend/plugins/alpine-photo-tile-for-flickr/';
  public $page = 'AlpineTile: Flickr';
  public $hook = 'APTFFbyTAP_hook';
  public $plugins = array('pinterest','tumblr');

  public $root = 'AlpinePhotoTiles';
  public $wjs = 'AlpinePhotoTiles_script';
  public $wcss = 'AlpinePhotoTiles_style';
  public $wmenujs = 'AlpinePhotoTiles_menu_script';
  public $acss = 'AlpinePhotoTiles_admin_style';
  public $wdesc = 'Add images from Flickr to your sidebar';
//####### DO NOT CHANGE #######//
  public $short = 'alpine-phototile-for-flickr';
  public $id = 'APTFF_by_TAP';
//#############################//
  public $expiryInterval = 360; //1*60*60;  1 hour
  public $cleaningInterval = 1209600; //14*24*60*60;  2 weeks

  function __construct() {
    $this->url = untrailingslashit( plugins_url( '' , dirname(__FILE__) ) );
    $this->dir = untrailingslashit( plugin_dir_path( dirname(__FILE__) ) );
    $this->cacheDir = WP_CONTENT_DIR . '/cache/' . $this->settings;
  }
  
  function widget_positions(){
      $options = array(
      'top' => '',
      'left' => 'Flickr Settings',
      'right' => 'Style Settings',
      'bottom' => 'Format Settings'
    );
    return $options;
  }
  function option_positions(){
    $positions = array(
      'generator' => array(
        'left' => 'Flickr Settings',
        'right' => 'Style Settings',
        'bottom' => 'Format Settings'
      ),
      'plugin-settings' => array(
        'top' => 'Cache Options',
        'center' =>'Global Style Options'
      )
    );
    return $positions;
  }
/**
 * Plugin Admin Settings Page Tabs
 */
  function settings_page_tabs() {
    $tabs = array( 
      'general' => array(
        'name' => 'general',
        'title' => 'General',
      ),
      'generator' => array(
        'name' => 'generator',
        'title' => 'Shortcode Generator',
      ),
      'preview' => array(
        'name' => 'preview',
        'title' => 'Shortcode Preview',
      ),
      'plugin-settings' => array(
        'name' => 'plugin-settings',
        'title' => 'Plugin Settings',
      )
    );
    return $tabs;
  }
  
  function option_defaults(){
    $options = array(
      'widget_title' => array(
        'name' => 'widget_title',
        'title' => 'Title : ',
        'type' => 'text',
        'description' => '',
        'since' => '1.1',
        'widget' => true,
        'tab' => '',
        'position' => 'top',
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
        'parent' => 'AlpinePhotoTiles-parent', 
        'trigger' => 'flickr_source',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'left',
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
        'widget' => true,
        'tab' => 'generator',
        'position' => 'left',        
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
        'widget' => true,
        'tab' => 'generator',
        'position' => 'left',
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
        'widget' => true,
        'tab' => 'generator',
        'position' => 'left',
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
        'widget' => true,
        'tab' => 'generator',
        'position' => 'left',
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
        'widget' => true,
        'tab' => 'generator',
        'position' => 'left',
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
        'widget' => true,
        'tab' => 'generator',
        'position' => 'left',
        'default' => 'Flickr'
      ),    

      
      'flickr_image_link_option' => array(
        'name' => 'flickr_image_link_option',
        'short' => 'imgl',
        'title' => 'Image Links : ',
        'type' => 'select',
        'valid_options' => array(
          'none' => array(
            'name' => 'none',
            'title' => 'Do not link images'
          ),
          'original' => array(
            'name' => 'original',
            'title' => 'Link to Image Source'
          ),
          'flickr' => array(
            'name' => 'flickr',
            'title' => 'Link to Flickr Page'
          ),
          'link' => array(
            'name' => 'link',
            'title' => 'Link to URL Address'
          ),
          'fancybox' => array(
            'name' => 'fancybox',
            'title' => 'Use Fancybox'
          )               
        ),
        'description' => '',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'left',
        'parent' => 'AlpinePhotoTiles-parent', 
        'trigger' => 'flickr_image_link_option',
        'default' => 'flickr'
      ),      
      'custom_link_url' => array(
        'name' => 'custom_link_url',
        'title' => 'Custom Link URL : ',
        'short' => 'curl',
        'type' => 'text',
        'sanitize' => 'url',
        'description' => '',
        'child' => 'flickr_image_link_option', 
        'hidden' => 'none original flickr fancybox',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'left',
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
        'widget' => true,
        'tab' => 'generator',
        'position' => 'left',
        'default' => '240'
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
        'parent' => 'AlpinePhotoTiles-parent',
        'trigger' => 'style_option',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'right',
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
        'widget' => true,
        'tab' => 'generator',
        'position' => 'right',
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
        'widget' => true,
        'tab' => 'generator',
        'position' => 'right',
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
        'widget' => true,
        'tab' => 'generator',
        'position' => 'right',
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
        'widget' => true,
        'tab' => 'generator',
        'position' => 'right',
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
        'widget' => true,
        'tab' => 'generator',
        'position' => 'right',
        'default' => '4'
      ),
      'style_shadow' => array(
        'name' => 'style_shadow',
        'short' => 'shadow',
        'title' => 'Add slight image shadow.',
        'type' => 'checkbox',
        'description' => '',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'right',
        'default' => ''
      ),   
      'style_border' => array(
        'name' => 'style_border',
        'short' => 'border',
        'title' => 'Add white image border.',
        'type' => 'checkbox',
        'description' => '',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'right',
        'default' => ''
      ),   
      'style_highlight' => array(
        'name' => 'style_highlight',
        'short' => 'highlight',
        'title' => 'Highlight when hovering.',
        'type' => 'checkbox',
        'description' => '',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'right',
        'default' => ''
      ),
      'style_curve_corners' => array(
        'name' => 'style_curve_corners',
        'short' => 'curve',
        'title' => 'Add slight curve to corners.',
        'type' => 'checkbox',
        'description' => '',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'right',
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
        'widget' => true,
        'tab' => 'generator',
        'position' => 'bottom',
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
        'description' => "To reduce the widget width, input a percentage (between 1 and 100). <br> If photos are smaller than widget area, reduce percentage until desired width is achieved.",
        'widget' => true,
        'tab' => 'generator',
        'position' => 'bottom',
        'default' => '100'
      ),        
      'widget_disable_credit_link' => array(
        'name' => 'widget_disable_credit_link',
        'short' => 'nocredit',
        'title' => 'Disable the tiny "TAP" link in the bottom left corner, though I have spent several months developing this plugin and would appreciate the credit.',
        'type' => 'checkbox',
        'description' => '',
        'widget' => true,
        'tab' => 'generator',
        'position' => 'bottom',
        'default' => ''
      ), 
      'cache_disable' => array(
        'name' => 'cache_disable',
        'title' => 'Disable feed caching: ',
        'type' => 'checkbox',
        'description' => '',
        'since' => '1.1',
        'tab' => 'plugin-settings',
        'position' => 'top',
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
        'tab' => 'plugin-settings',
        'position' => 'top',
        'default' => '3'
      ), 
      'general_loader' => array(
        'name' => 'general_loader',
        'title' => 'Disable Loading Icon: ',
        'type' => 'checkbox',
        'description' => 'Remove the icon that appears while images are loading.',
        'since' => '1.1',
        'tab' => 'plugin-settings',
        'position' => 'center',
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
        'tab' => 'plugin-settings',
        'position' => 'center',
        'default' => '#64a2d8'
      ), 
    );
    return $options;
  }
  
// END
}

?>
