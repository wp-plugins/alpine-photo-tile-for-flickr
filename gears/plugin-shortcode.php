<?php
/**
 * Alpine PhotoTile for Flickr: Shortcode
 *
 * @since 1.1.1
 *
 */
 
  function generate_shortcode( $options, $optiondetails ){
    $short = '['.APTFFbyTAP_SHORT;
    $trigger = '';
    
    foreach( $options as $key=>$value ){
      if($value && $optiondetails[$key]['short']){
        if( $optiondetails[$key]['child'] && $optiondetails[$key]['hidden'] ){
          $hidden = @explode(' ',$optiondetails[$key]['hidden']);
          if( !in_array( $options[ $optiondetails[$key]['child'] ] ,$hidden) ){
            $short .= ' '.$optiondetails[$key]['short'].'="'.$value.'"';
          }
        }else{
          $short .= ' '.$optiondetails[$key]['short'].'="'.$value.'"';
        }
      }
    }
    $short .= ']';
    
    return $short;
  }

  function APTFFbyTAP_shortcode_function( $atts ) {
    $optiondetails = APTFFbyTAP_option_defaults();
    $options = array();
    
    foreach( $optiondetails as $opt=>$details ){
      $options[$opt] = $details['default'];
      if( $atts[ $details['short'] ] ){
        $options[$opt] = $atts[ $details['short'] ];
      }
    }
    
    $id = rand(100, 1000);
    
    $source_results = APTFFbyTAP_photo_retrieval($id, $options, $optiondetails);
    
    $return .= '<div id="'.APTFFbyTAP_ID.'-by-shortcode-'.$id.'" class="APTFFbyTAP_inpost_container">';
    $return .= $source_results['hidden'];
    if( $source_results['continue'] ){  
      switch ($options['style_option']) {
        case "vertical":
          $return .= APTFFbyTAP_display_vertical($id, $options, $source_results);
        break;
        case "windows":
          $return .= APTFFbyTAP_display_hidden($id, $options, $source_results);
        break; 
        case "bookshelf":
          $return .= APTFFbyTAP_display_hidden($id, $options, $source_results);
        break;
        case "rift":
          $return .= APTFFbyTAP_display_hidden($id, $options, $source_results);
        break;
        case "floor":
          $return .= APTFFbyTAP_display_hidden($id, $options, $source_results);
        break;
        case "wall":
          $return .= APTFFbyTAP_display_hidden($id, $options, $source_results);
        break;
        case "cascade":
          $return .= APTFFbyTAP_display_cascade($id, $options, $source_results);
        break;
        case "gallery":
          $return .= APTFFbyTAP_display_hidden($id, $options, $source_results);
        break;
      }
    }
    // If user does not have necessary extensions 
    // or error occured before content complete, report such...
    else{
      $return .= 'Sorry:<br>'.$source_results['message'];
    }
    $return .= $after_widget;
    $return .= '</div>';
    
    return $return;
  }
  add_shortcode( APTFFbyTAP_SHORT, 'APTFFbyTAP_shortcode_function' );
  
?>