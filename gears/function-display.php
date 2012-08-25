<?php
/**
 * Alpine PhotoTile for Flickr: Style Display Functions
 *
 * @since 1.0.0
 */
 
 
function APTFFbyTAP_display_vertical($id, $options, $source_results){
  $APTFFbyTAP_linkurl = $source_results['image_perms'];
  $APTFFbyTAP_photocap = $source_results['image_captions'];
  $APTFFbyTAP_photourl = $source_results['image_urls'];
  $APTFFbyTAP_user_link = $source_results['user_link'];

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////       Check Content      /////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  if($options['flickr_photo_number'] != count($APTFFbyTAP_linkurl)){$options['flickr_photo_number']=count($APTFFbyTAP_linkurl);}
  
  for($i = 0;$i<count($APTFFbyTAP_photocap);$i++){
    $APTFFbyTAP_photocap[$i] = str_replace('"','',$APTFFbyTAP_photocap[$i]);
  }
  
  if($APTFFbyTAP_reduced_width && $APTFFbyTAP_reduced_width<$APTFFbyTAP_size ){
    $APTFFbyTAP_style_width = $APTFFbyTAP_reduced_width."px";   }
  else{   $APTFFbyTAP_style_width = $APTFFbyTAP_size."px";    }
  
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////   Begin the Content   /////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                    
  $output .= '<div id="'.$id.'-APTFFbyTAP_container" class="APTFFbyTAP_container_class">';     
  
  // Align photos
  $output .= '<div id="'.$id.'-vertical-parent" class="APTFFbyTAP_parent_class" style="width:'.$options['flickr_photo_size'].'px;max-width:'.$options['widget_max_width'].'%;padding:0px;';
  if( 'center' == $options['widget_alignment'] ){                          //  Optional: Set text alignment (left/right) or center
    $output .= 'margin:0px auto;text-align:center;';
  }
  else{
    $output .= 'float:' . $options['widget_alignment'] . ';text-align:' . $options['widget_alignment'] . ';';
  } 
  $output .= '">';
  
  $shadow = ($options['style_shadow']?'APTFFbyTAP-img-shadow':'APTFFbyTAP-img-noshadow');
  $border = ($options['style_border']?'APTFFbyTAP-img-border':'APTFFbyTAP-img-noborder');
  $curves = ($options['style_curve_corners']?'APTFFbyTAP-img-corners':'APTFFbyTAP-img-nocorners');
  $highlight = ($options['style_highlight']?'APTFFbyTAP-img-highlight':'APTFFbyTAP-img-nohighlight');
  
  for($i = 0;$i<$options['flickr_photo_number'];$i++){
    if( $options['flickr_image_link'] ){ $output .= '<a href="' . $APTFFbyTAP_linkurl[$i] . '" class="APTFFbyTAP-vertical-link" target="_blank" title='."'". $APTFFbyTAP_photocap[$i] ."'".'>'; }
    $output .= '<img id="'.$id.'-tile-'.$i.'" class="APTFFbyTAP-image '.$shadow.' '.$border.' '.$curves.' '.$highlight.'" src="' . $APTFFbyTAP_photourl[$i] . '" ';
    $output .= 'title='."'". $APTFFbyTAP_photocap[$i] ."'".' alt='."'". $APTFFbyTAP_photocap[$i] ."' "; // Careful about caps with ""
    $output .= 'border="0" hspace="0" vspace="0" style="margin:1px 0 5px 0;padding:0;max-width:100%;"/>'; // Override the max-width set by theme
    if( $options['flickr_image_link'] ){ $output .= '</a>'; }
  }
  
  $APTFFbyTAP_by_link  =  '<div id="'.$id.'-by-link" class="APTFFbyTAP-by-link"><a href="http://thealpinepress.com/" style="COLOR:#C0C0C0;text-decoration:none;" title="Widget by The Alpine Press">TAP</a></div>';   
  if( !$options['widget_disable_credit_link'] ){
    $output .=  $APTFFbyTAP_by_link;    
  }          
  // Close vertical-parent
  $output .= '</div>';    

  if($APTFFbyTAP_user_link){ 
    $output .= '<div id="'.$id.'-display-link" class="APTFFbyTAP-display-link-container" ';
    $output .= 'style="text-align:' . $options['widget_alignment'] . ';">'.$APTFFbyTAP_user_link.'</div>'; // Only breakline if floating
  }

  // Close container
  $output .= '</div>';
  $output .= '<div class="APTFFbyTAP_breakline"></div>';
  
  $highlight = APTFFbyTAP_get_option("general_highlight_color");
  $highlight = ($highlight?$highlight:'#64a2d8');

  if( $options['style_shadow'] || $options['style_border'] || $options['style_highlight'] ){
    $output .= '<script>
         jQuery(window).load(function() {
            if(jQuery().APTFFbyTAPAdjustBordersPlugin ){
              jQuery("#'.$id.'-vertical-parent").APTFFbyTAPAdjustBordersPlugin({
                highlight:"'.$highlight.'",
              });
            }  
          });
        </script>';  
  }
    
  return $output;
}  

function APTFFbyTAP_display_cascade($id, $options, $source_results){
  $APTFFbyTAP_linkurl = $source_results['image_perms'];
  $APTFFbyTAP_photocap = $source_results['image_captions'];
  $APTFFbyTAP_photourl = $source_results['image_urls'];
  $APTFFbyTAP_user_link = $source_results['user_link'];

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////       Check Content      /////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  if($options['flickr_photo_number'] != count($APTFFbyTAP_linkurl)){$options['flickr_photo_number']=count($APTFFbyTAP_linkurl);}
  
  for($i = 0;$i<count($APTFFbyTAP_photocap);$i++){
    $APTFFbyTAP_photocap[$i] = str_replace('"','',$APTFFbyTAP_photocap[$i]);
  }
  
  if($APTFFbyTAP_reduced_width && $APTFFbyTAP_reduced_width<$APTFFbyTAP_size ){
    $APTFFbyTAP_style_width = $APTFFbyTAP_reduced_width."px";   }
  else{   $APTFFbyTAP_style_width = $APTFFbyTAP_size."px";    }
  
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////   Begin the Content   /////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
          
  $output .= '<div id="'.$id.'-APTFFbyTAP_container" class="APTFFbyTAP_container_class">';     
  
  // Align photos
  $output .= '<div id="'.$id.'-cascade-parent" class="APTFFbyTAP_parent_class" style="width:100%;max-width:'.$options['widget_max_width'].'%;padding:0px;';
  if( 'center' == $options['widget_alignment'] ){                          //  Optional: Set text alignment (left/right) or center
    $output .= 'margin:0px auto;text-align:center;';
  }
  else{
    $output .= 'float:' . $options['widget_alignment'] . ';text-align:' . $options['widget_alignment'] . ';';
  } 
  $output .= '">';
  
  $shadow = ($options['style_shadow']?'APTFFbyTAP-img-shadow':'APTFFbyTAP-img-noshadow');
  $border = ($options['style_border']?'APTFFbyTAP-img-border':'APTFFbyTAP-img-noborder');
  $curves = ($options['style_curve_corners']?'APTFFbyTAP-img-corners':'APTFFbyTAP-img-nocorners'); 
  $highlight = ($options['style_highlight']?'APTFFbyTAP-img-highlight':'APTFFbyTAP-img-nohighlight');
  
  for($col = 0; $col<$options['style_column_number'];$col++){
    $output .= '<div class="APTFFbyTAP_cascade_column" style="width:'.(100/$options['style_column_number']).'%;float:left;margin:0;">';
    $output .= '<div class="APTFFbyTAP_cascade_column_inner" style="display:block;margin:0 3px;overflow:hidden;">';
    for($i = $col;$i<$options['flickr_photo_number'];$i+=$options['style_column_number']){
      if( $options['flickr_image_link'] ){ $output .= '<a href="' . $APTFFbyTAP_linkurl[$i] . '" class="APTFFbyTAP-cascade-link" target="_blank" title='."'". $APTFFbyTAP_photocap[$i] ."'".'>'; }
      $output .= '<img id="'.$id.'-tile-'.$i.'" class="APTFFbyTAP-image '.$shadow.' '.$border.' '.$curves.' '.$highlight.'" src="' . $APTFFbyTAP_photourl[$i] . '" ';
      $output .= 'title='."'". $APTFFbyTAP_photocap[$i] ."'".' alt='."'". $APTFFbyTAP_photocap[$i] ."' "; // Careful about caps with ""
      $output .= 'border="0" hspace="0" vspace="0" style="margin:1px 0 5px 0;padding:0;max-width:100%;"/>'; // Override the max-width set by theme
      if( $options['flickr_image_link'] ){ $output .= '</a>'; }
    }
    $output .= '</div></div>';
  }
  
  $output .= '<div class="APTFFbyTAP_breakline"></div>';
    
  $APTFFbyTAP_by_link  =  '<div id="'.$id.'-by-link" class="APTFFbyTAP-by-link"><a href="http://thealpinepress.com/" style="COLOR:#C0C0C0;text-decoration:none;" title="Widget by The Alpine Press">TAP</a></div>';      
  if( !$options['widget_disable_credit_link'] ){
    $output .=  $APTFFbyTAP_by_link;    
  }          
  // Close cascade-parent
  $output .= '</div>';    

  $output .= '<div class="APTFFbyTAP_breakline"></div>';
  
  if($APTFFbyTAP_user_link){ 
    if($options['widget_alignment'] == 'center'){                          //  Optional: Set text alignment (left/right) or center
      $output .= '<div id="'.$id.'-display-link" class="APTFFbyTAP-display-link-container" ';
      $output .= 'style="width:100%;margin:0px auto;">'.$APTFFbyTAP_user_link.'</div>';
    }
    else{
      $output .= '<div id="'.$id.'-display-link" class="APTFFbyTAP-display-link-container" ';
      $output .= 'style="float:' . $options['widget_alignment'] . ';width:'.$options['flickr_photo_size'].'px;max-width:'.$options['widget_max_width'].'%;"><center>'.$APTFFbyTAP_user_link.'</center></div>'; // Only breakline if floating
    } 
  }

  // Close container
  $output .= '</div>';
  $output .= '<div class="APTFFbyTAP_breakline"></div>';
 
  $highlight = APTFFbyTAP_get_option("general_highlight_color");
  $highlight = ($highlight?$highlight:'#64a2d8');
  
  if( $options['style_shadow'] || $options['style_border'] || $options['style_highlight'] ){
    $output .= '<script>
            jQuery(window).load(function() {
              if(jQuery().APTFFbyTAPAdjustBordersPlugin ){
                jQuery("#'.$id.'-cascade-parent").APTFFbyTAPAdjustBordersPlugin({
                  highlight:"'.$highlight.'",
                });
              }  
            });
          </script>';
  }
  return $output;  
}


function APTFFbyTAP_display_hidden($id, $options, $source_results){
  $APTFFbyTAP_linkurl = $source_results['image_perms'];
  $APTFFbyTAP_photocap = $source_results['image_captions'];
  $APTFFbyTAP_photourl = $source_results['image_urls'];
  $APTFFbyTAP_user_link = $source_results['user_link'];
  $APTFFbyTAP_originalurl = $source_results['image_originals'];

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////       Check Content      /////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  if($options['flickr_photo_number'] != count($APTFFbyTAP_linkurl)){$options['flickr_photo_number']=count($APTFFbyTAP_linkurl);}
  
  for($i = 0;$i<count($APTFFbyTAP_photocap);$i++){
    $APTFFbyTAP_photocap[$i] = str_replace('"','',$APTFFbyTAP_photocap[$i]);
  }
  
  if($APTFFbyTAP_reduced_width && $APTFFbyTAP_reduced_width<$APTFFbyTAP_size ){
    $APTFFbyTAP_style_width = $APTFFbyTAP_reduced_width."px";   }
  else{   $APTFFbyTAP_style_width = $APTFFbyTAP_size."px";    }
  
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////   Begin the Content   /////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
          
  $output .= '<div id="'.$id.'-APTFFbyTAP_container" class="APTFFbyTAP_container_class">';     
  
  // Align photos
  $output .= '<div id="'.$id.'-hidden-parent" class="APTFFbyTAP_parent_class" style="width:'.$options['flickr_photo_size'].'px;max-width:'.$options['widget_max_width'].'%;padding:0px;';
  if( 'center' == $options['widget_alignment'] ){                          //  Optional: Set text alignment (left/right) or center
    $output .= 'margin:0px auto;text-align:center;';
  }
  else{
    $output .= 'float:' . $options['widget_alignment'] . ';text-align:' . $options['widget_alignment'] . ';';
  } 
  $output .= '">';
  
  $output .= '<div id="'.$id.'-image-list" class="APTFFbyTAP_image_list_class" style="display:none;visibility:hidden;">'; 
  
  $shadow = ($options['style_shadow']?'APTFFbyTAP-img-shadow':'APTFFbyTAP-img-noshadow');
  $border = ($options['style_border']?'APTFFbyTAP-img-border':'APTFFbyTAP-img-noborder');
  $curves = ($options['style_curve_corners']?'APTFFbyTAP-img-corners':'APTFFbyTAP-img-nocorners');
  
  for($i = 0;$i<$options['flickr_photo_number'];$i++){
    if( $options['flickr_image_link'] ){ $output .= '<a href="' . $APTFFbyTAP_linkurl[$i] . '" class="APTFFbyTAP-link" target="_blank" title='."'". $APTFFbyTAP_photocap[$i] ."'".'>'; }
    $output .= '<img id="'.$id.'-tile-'.$i.'" class="APTFFbyTAP-image '.$shadow.' '.$border.' '.$curves.'" src="' . $APTFFbyTAP_photourl[$i] . '" ';
    $output .= 'title='."'". $APTFFbyTAP_photocap[$i] ."'".' alt='."'". $APTFFbyTAP_photocap[$i] ."' "; // Careful about caps with ""
    $output .= 'border="0" hspace="0" vspace="0" />'; // Override the max-width set by theme
    
    // Load original image size
    if( "gallery" == $options['style_option'] && $APTFFbyTAP_originalurl[$i] ){
      $output .= '<img class="APTFFbyTAP-original-image" src="' . $APTFFbyTAP_originalurl[$i]. '" />';
    }
    
    if( $options['flickr_image_link'] ){ $output .= '</a>'; }
  }
  $output .= '</div>';
  
  $APTFFbyTAP_by_link  =  '<div id="'.$id.'-by-link" class="APTFFbyTAP-by-link"><a href="http://thealpinepress.com/" style="COLOR:#C0C0C0;text-decoration:none;" title="Widget by The Alpine Press">TAP</a></div>';   
  if( !$options['widget_disable_credit_link'] ){
    $output .=  $APTFFbyTAP_by_link;    
  }          
  // Close vertical-parent
  $output .= '</div>';      

  if($APTFFbyTAP_user_link){ 
    if($options['widget_alignment'] == 'center'){                          //  Optional: Set text alignment (left/right) or center
      $output .= '<div id="'.$id.'-display-link" class="APTFFbyTAP-display-link-container" ';
      $output .= 'style="width:100%;margin:0px auto;">'.$APTFFbyTAP_user_link.'</div>';
    }
    else{
      $output .= '<div id="'.$id.'-display-link" class="APTFFbyTAP-display-link-container" ';
      $output .= 'style="float:' . $options['widget_alignment'] . ';width:'.$options['flickr_photo_size'].'px;max-width:'.$options['widget_max_width'].'%;"><center>'.$APTFFbyTAP_user_link.'</center></div>'; // Only breakline if floating
    } 
  }

  // Close container
  $output .= '</div>';
  $disable = APTFFbyTAP_get_option('general_loader');
  $highlight = APTFFbyTAP_get_option("general_highlight_color");
  $highlight = ($highlight?$highlight:'#64a2d8');
  
  $output .= '<script>';
  
  if(!$disable){
    $output .= '
            jQuery(document).ready(function() {
            jQuery("#'.$id.'-APTFFbyTAP_container").addClass("loading"); 
           });';
  }
  $output .= '
         jQuery(window).load(function() {
          jQuery("#'.$id.'-APTFFbyTAP_container").removeClass("loading");
          if( jQuery().APTFFbyTAPTilesPlugin ){
            jQuery("#'.$id.'-hidden-parent").APTFFbyTAPTilesPlugin({
              style:"'.($options['style_option']?$options['style_option']:'windows').'",
              shape:"'.($options['style_shape']?$options['style_shape']:'square').'",
              perRow:"'.($options['style_photo_per_row']?$options['style_photo_per_row']:'3').'",
              imageLink:'.($options['flickr_image_link']?'1':'0').',
              imageBorder:'.($options['style_border']?'1':'0').',
              imageShadow:'.($options['style_shadow']?'1':'0').',
              imageCurve:'.($options['style_curve_corners']?'1':'0').',
              imageHighlight:'.($options['style_highlight']?'1':'0').',
              galleryHeight:'.($options['style_gallery_height']?$options['style_gallery_height']:'3').',
              highlight:"'.$highlight.'",
            });
          }
        });
      </script>';
      
  return $output; 
}

?>