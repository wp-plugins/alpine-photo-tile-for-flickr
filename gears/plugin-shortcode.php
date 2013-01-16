<?php
/**
 * Alpine PhotoTile for Flickr: Shortcode
 *
 * @since 1.1.1
 *
 */
 
  function APTFFbyTAP_shortcode_function( $atts ) {
    $bot = new PhotoTileForFlickrBot();
    
    $optiondetails = $bot->option_defaults();
    $options = array();
    foreach( $optiondetails as $opt=>$details ){
      $options[$opt] = $details['default'];
      if( $atts[ $details['short'] ] ){
        $options[$opt] = $atts[ $details['short'] ];
      }
    }
    if( $options['flickr_image_link_option'] == "fancybox" ){
      wp_enqueue_script( 'fancybox' );
      wp_enqueue_style( 'fancybox-stylesheet');
    } 
    wp_enqueue_style($bot->wcss);
    wp_enqueue_script($bot->wjs);

    $id = rand(100, 1000);
    $bot->wid = $id;
    $bot->options = $options;
    $bot->photo_retrieval($id, $options);
    
    $return .= '<div id="'.$bot->id.'-by-shortcode-'.$id.'" class="AlpinePhotoTiles_inpost_container">';
    $return .= $bot->results['hidden'];
    if( $bot->results['continue'] ){  
      if( "vertical" == $options['style_option'] ){
        $bot->display_vertical();
      }elseif( "cascade" == $options['style_option'] ){
        $bot->display_cascade();
      }else{
        $bot->display_hidden();
      }
      $return .= $bot->out;
    }
    // If user does not have necessary extensions 
    // or error occured before content complete, report such...
    else{
      $return .= 'Sorry:<br>'.$bot->results['message'];
    }
    $return .= $after_widget;
    $return .= '</div>';
    
    return $return;
  }
  add_shortcode( 'alpine-phototile-for-flickr', 'APTFFbyTAP_shortcode_function' );
   
?>