<?php
/**
 * The functions for displaying all styles.
 *
 * @since 1.0.0
 */
 
 
function theAlpinePress_flickr_display_vertical($id, $options, $source_results){
  $PTFFbyTAP_linkurl = $source_results['image_perms'];
  $PTFFbyTAP_photocap = $source_results['image_captions'];
  $PTFFbyTAP_photourl = $source_results['image_urls'];
  $PTFFbyTAP_user_link = $source_results['user_link'];

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////       Check Content      /////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  if($options['flickr_photo_number'] != count($PTFFbyTAP_linkurl)){$options['flickr_photo_number']=count($PTFFbyTAP_linkurl);}
  
  for($i = 0;$i<count($PTFFbyTAP_photocap);$i++){
    $PTFFbyTAP_photocap[$i] = str_replace('"','',$PTFFbyTAP_photocap[$i]);
  }
  
  if($PTFFbyTAP_reduced_width && $PTFFbyTAP_reduced_width<$PTFFbyTAP_size ){
    $PTFFbyTAP_style_width = $PTFFbyTAP_reduced_width."px";   }
  else{   $PTFFbyTAP_style_width = $PTFFbyTAP_size."px";    }
  
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////   Begin the Content   /////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                    
  $output .= '<div id="'.$id.'-PTFFbyTAP_container" class="PTFFbyTAP_container_class">';     
  
  // Align photos
  $output .= '<div id="'.$id.'-vertical-parent" class="PTFFbyTAP_parent_class" style="width:'.$options['flickr_photo_size'].'px;max-width:'.$options['widget_max_width'].'%;padding:0px;';
  if( 'center' == $options['widget_alignment'] ){                          //  Optional: Set text alignment (left/right) or center
    $output .= 'margin:0px auto;text-align:center;';
  }
  else{
    $output .= 'float:' . $options['widget_alignment'] . ';text-align:' . $options['widget_alignment'] . ';';
  } 
  $output .= '">';
  
  $shadow = ($options['style_shadow']?'PTFFbyTAP-img-shadow':'PTFFbyTAP-img-noshadow');
  $border = ($options['style_border']?'PTFFbyTAP-img-border':'PTFFbyTAP-img-noborder');
  $curves = ($options['style_curve_corners']?'PTFFbyTAP-img-corners':'PTFFbyTAP-img-nocorners');
  
  for($i = 0;$i<$options['flickr_photo_number'];$i++){
    if( $options['flickr_image_link'] ){ $output .= '<a href="' . $PTFFbyTAP_linkurl[$i] . '" class="PTFFbyTAP-vertical-link" target="_blank" title='."'". $PTFFbyTAP_photocap[$i] ."'".'>'; }
    $output .= '<img id="'.$id.'-tile-'.$i.'" class="PTFFbyTAP-image '.$shadow.' '.$border.' '.$curves.'" src="' . $PTFFbyTAP_photourl[$i] . '" ';
    $output .= 'title='."'". $PTFFbyTAP_photocap[$i] ."'".' alt='."'". $PTFFbyTAP_photocap[$i] ."' "; // Careful about caps with ""
    $output .= 'border="0" hspace="0" vspace="0" />'; // Override the max-width set by theme
    if( $options['flickr_image_link'] ){ $output .= '</a>'; }
  }
  
  $PTFFbyTAP_by_link  =  '<div id="'.$id.'-by-link" class="PTFFbyTAP-by-link"><a href="http://thealpinepress.com/" style="COLOR:#C0C0C0;text-decoration:none;" title="Widget by The Alpine Press">TAP</a></div>';   
  if( !$options['widget_disable_credit_link'] ){
    $output .=  $PTFFbyTAP_by_link;    
  }          
  // Close vertical-parent
  $output .= '</div>';    

  if($PTFFbyTAP_user_link){ 
    $output .= '<div id="'.$id.'-display-link" class="PTFFbyTAP-display-link-container" ';
    $output .= 'style="text-align:' . $options['widget_alignment'] . ';">'.$PTFFbyTAP_user_link.'</div>'; // Only breakline if floating
  }

  // Close container
  $output .= '</div>';
  $output .= '<div class="PTFFbyTAP_breakline"></div>';
 
  echo $output;
  
  if( $options['style_shadow'] || $options['style_border'] || $options['style_curve_corners'] ){
    echo '<script>
         jQuery(window).load(function() {
            jQuery("#'.$id.'-vertical-parent").theAlpinePressAdjustBorders();
          });
        </script>';  
  }
}  

function theAlpinePress_flickr_display_cascade($id, $options, $source_results){
  $PTFFbyTAP_linkurl = $source_results['image_perms'];
  $PTFFbyTAP_photocap = $source_results['image_captions'];
  $PTFFbyTAP_photourl = $source_results['image_urls'];
  $PTFFbyTAP_user_link = $source_results['user_link'];

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////       Check Content      /////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  if($options['flickr_photo_number'] != count($PTFFbyTAP_linkurl)){$options['flickr_photo_number']=count($PTFFbyTAP_linkurl);}
  
  for($i = 0;$i<count($PTFFbyTAP_photocap);$i++){
    $PTFFbyTAP_photocap[$i] = str_replace('"','',$PTFFbyTAP_photocap[$i]);
  }
  
  if($PTFFbyTAP_reduced_width && $PTFFbyTAP_reduced_width<$PTFFbyTAP_size ){
    $PTFFbyTAP_style_width = $PTFFbyTAP_reduced_width."px";   }
  else{   $PTFFbyTAP_style_width = $PTFFbyTAP_size."px";    }
  
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////   Begin the Content   /////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
          
  $output .= '<div id="'.$id.'-PTFFbyTAP_container" class="PTFFbyTAP_container_class">';     
  
  // Align photos
  $output .= '<div id="'.$id.'-cascade-parent" class="PTFFbyTAP_parent_class" style="width:100%;max-width:'.$options['widget_max_width'].'%;padding:0px;';
  if( 'center' == $options['widget_alignment'] ){                          //  Optional: Set text alignment (left/right) or center
    $output .= 'margin:0px auto;text-align:center;';
  }
  else{
    $output .= 'float:' . $options['widget_alignment'] . ';text-align:' . $options['widget_alignment'] . ';';
  } 
  $output .= '">';
  
  $shadow = ($options['style_shadow']?'PTFFbyTAP-img-shadow':'PTFFbyTAP-img-noshadow');
  $border = ($options['style_border']?'PTFFbyTAP-img-border':'PTFFbyTAP-img-noborder');
  $curves = ($options['style_curve_corners']?'PTFFbyTAP-img-corners':'PTFFbyTAP-img-nocorners'); 
   
  for($col = 0; $col<$options['style_column_number'];$col++){
    $output .= '<div class="PTFFbyTAP_cascade_column" style="width:'.(100/$options['style_column_number']- 1 - 1/$options['style_column_number']).'%;float:left;margin:0 0 0 1%;">';
    for($i = $col;$i<$options['flickr_photo_number'];$i+=$options['style_column_number']){
      if( $options['flickr_image_link'] ){ $output .= '<a href="' . $PTFFbyTAP_linkurl[$i] . '" class="PTFFbyTAP-vertical-link" target="_blank" title='."'". $PTFFbyTAP_photocap[$i] ."'".'>'; }
      $output .= '<img id="'.$id.'-tile-'.$i.'" class="PTFFbyTAP-image '.$shadow.' '.$border.' '.$curves.'" src="' . $PTFFbyTAP_photourl[$i] . '" ';
      $output .= 'title='."'". $PTFFbyTAP_photocap[$i] ."'".' alt='."'". $PTFFbyTAP_photocap[$i] ."' "; // Careful about caps with ""
      $output .= 'border="0" hspace="0" vspace="0" />'; // Override the max-width set by theme
      if( $options['flickr_image_link'] ){ $output .= '</a>'; }
    }
    $output .= '</div>';
  }
  
  $output .= '<div class="PTFFbyTAP_breakline"></div>';
    
  $PTFFbyTAP_by_link  =  '<div id="'.$id.'-by-link" class="PTFFbyTAP-by-link"><a href="http://thealpinepress.com/" style="COLOR:#C0C0C0;text-decoration:none;" title="Widget by The Alpine Press">TAP</a></div>';      
  if( !$options['widget_disable_credit_link'] ){
    $output .=  $PTFFbyTAP_by_link;    
  }          
  // Close vertical-parent
  $output .= '</div>';    

  $output .= '<div class="PTFFbyTAP_breakline"></div>';
  
  if($PTFFbyTAP_user_link){ 
    if($options['widget_alignment'] == 'center'){                          //  Optional: Set text alignment (left/right) or center
      $output .= '<div id="'.$id.'-display-link" class="PTFFbyTAP-display-link-container" ';
      $output .= 'style="width:100%;margin:0px auto;">'.$PTFFbyTAP_user_link.'</div>';
    }
    else{
      $output .= '<div id="'.$id.'-display-link" class="PTFFbyTAP-display-link-container" ';
      $output .= 'style="float:' . $options['widget_alignment'] . ';width:'.$options['flickr_photo_size'].'px;max-width:'.$options['widget_max_width'].'%;"><center>'.$PTFFbyTAP_user_link.'</center></div>'; // Only breakline if floating
    } 
  }

  // Close container
  $output .= '</div>';
  $output .= '<div class="PTFFbyTAP_breakline"></div>';
 
  echo $output;
  
  echo '<script>
         jQuery(window).load(function() {
            jQuery("#'.$id.'-cascade-parent").theAlpinePressAdjustBorders();
          });
        </script>';
}


function theAlpinePress_flickr_display_hidden($id, $options, $source_results){
  $PTFFbyTAP_linkurl = $source_results['image_perms'];
  $PTFFbyTAP_photocap = $source_results['image_captions'];
  $PTFFbyTAP_photourl = $source_results['image_urls'];
  $PTFFbyTAP_user_link = $source_results['user_link'];
  $PTFFbyTAP_originalurl = $source_results['image_originals'];

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////       Check Content      /////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  if($options['flickr_photo_number'] != count($PTFFbyTAP_linkurl)){$options['flickr_photo_number']=count($PTFFbyTAP_linkurl);}
  
  for($i = 0;$i<count($PTFFbyTAP_photocap);$i++){
    $PTFFbyTAP_photocap[$i] = str_replace('"','',$PTFFbyTAP_photocap[$i]);
  }
  
  if($PTFFbyTAP_reduced_width && $PTFFbyTAP_reduced_width<$PTFFbyTAP_size ){
    $PTFFbyTAP_style_width = $PTFFbyTAP_reduced_width."px";   }
  else{   $PTFFbyTAP_style_width = $PTFFbyTAP_size."px";    }
  
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////   Begin the Content   /////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
          
  $output .= '<div id="'.$id.'-PTFFbyTAP_container" class="PTFFbyTAP_container_class">';     
  
  // Align photos
  $output .= '<div id="'.$id.'-hidden-parent" class="PTFFbyTAP_parent_class" style="width:'.$options['flickr_photo_size'].'px;max-width:'.$options['widget_max_width'].'%;padding:0px;';
  if( 'center' == $options['widget_alignment'] ){                          //  Optional: Set text alignment (left/right) or center
    $output .= 'margin:0px auto;text-align:center;';
  }
  else{
    $output .= 'float:' . $options['widget_alignment'] . ';text-align:' . $options['widget_alignment'] . ';';
  } 
  $output .= '">';
  
  $output .= '<div id="'.$id.'-image-list" class="PTFFbyTAP_image_list_class" style="display:none;visibility:hidden;">'; 
  
  $shadow = ($options['style_shadow']?'PTFFbyTAP-img-shadow':'PTFFbyTAP-img-noshadow');
  $border = ($options['style_border']?'PTFFbyTAP-img-border':'PTFFbyTAP-img-noborder');
  $curves = ($options['style_curve_corners']?'PTFFbyTAP-img-corners':'PTFFbyTAP-img-nocorners');
  
  for($i = 0;$i<$options['flickr_photo_number'];$i++){
    if( $options['flickr_image_link'] ){ $output .= '<a href="' . $PTFFbyTAP_linkurl[$i] . '" class="PTFFbyTAP-link" target="_blank" title='."'". $PTFFbyTAP_photocap[$i] ."'".'>'; }
    $output .= '<img id="'.$id.'-tile-'.$i.'" class="PTFFbyTAP-image '.$shadow.' '.$border.' '.$curves.'" src="' . $PTFFbyTAP_photourl[$i] . '" ';
    $output .= 'title='."'". $PTFFbyTAP_photocap[$i] ."'".' alt='."'". $PTFFbyTAP_photocap[$i] ."' "; // Careful about caps with ""
    $output .= 'border="0" hspace="0" vspace="0" />'; // Override the max-width set by theme
    
    // Load original image size
    if( "gallery" == $options['style_option'] && $PTFFbyTAP_originalurl[$i] ){
      $output .= '<img class="PTFFbyTAP-original-image" src="' . $PTFFbyTAP_originalurl[$i]. '" />';
    }
    
    if( $options['flickr_image_link'] ){ $output .= '</a>'; }
  }
  $output .= '</div>';
  
  $PTFFbyTAP_by_link  =  '<div id="'.$id.'-by-link" class="PTFFbyTAP-by-link"><a href="http://thealpinepress.com/" style="COLOR:#C0C0C0;text-decoration:none;" title="Widget by The Alpine Press">TAP</a></div>';   
  if( !$options['widget_disable_credit_link'] ){
    $output .=  $PTFFbyTAP_by_link;    
  }          
  // Close vertical-parent
  $output .= '</div>';      

  if($PTFFbyTAP_user_link){ 
    if($options['widget_alignment'] == 'center'){                          //  Optional: Set text alignment (left/right) or center
      $output .= '<div id="'.$id.'-display-link" class="PTFFbyTAP-display-link-container" ';
      $output .= 'style="width:100%;margin:0px auto;">'.$PTFFbyTAP_user_link.'</div>';
    }
    else{
      $output .= '<div id="'.$id.'-display-link" class="PTFFbyTAP-display-link-container" ';
      $output .= 'style="float:' . $options['widget_alignment'] . ';width:'.$options['flickr_photo_size'].'px;max-width:'.$options['widget_max_width'].'%;"><center>'.$PTFFbyTAP_user_link.'</center></div>'; // Only breakline if floating
    } 
  }

  // Close container
  $output .= '</div>';
 
  echo $output;
  
  echo '<script>
         jQuery(window).load(function() {
            jQuery("#'.$id.'-hidden-parent").theAlpinePressTiles({
              style:"'.($options['style_option']?$options['style_option']:'windows').'",
              shape:"'.($options['style_shape']?$options['style_shape']:'square').'",
              perRow:"'.($options['style_photo_per_row']?$options['style_photo_per_row']:'3').'",
              imageLink:'.($options['flickr_image_link']?'1':'0').',
              imageBorder:'.($options['style_border']?'1':'0').',
              imageShadow:'.($options['style_shadow']?'1':'0').',
              imageCurve:'.($options['style_curve_corners']?'1':'0').',
            });
          });
        </script>';
}

?>