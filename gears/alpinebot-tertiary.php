<?php


class PhotoTileForFlickrBot extends PhotoTileForFlickrBasic{  

   /**
   * Alpine PhotoTile for Flickr: Photo Retrieval Function
   * The PHP for retrieving content from Flickr.
   *
   * @since 1.0.0
   * @updated 1.2.1
   */
   
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  ///////////////////////////////////////////    Generate Image Content    ////////////////////////////////////////////////
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // For Reference:
    // http://www.flickr.com/services/api/response.json.html
    // s = small square 75x75
    // t = thumbnail, 100 on longest side
    // m = small, 240 on longest side
    // - = medium, 500 on longest side
    // z = medium, 640 on longest side
    // b = large, 1024 on longest side*
    // o = original image, either a jpg, gif or png, depending on source format**
    // *Before May 25th 2010 large photos only exist for very large original images.
    // **Original photos behave a little differently. They have their own secret (called originalsecret in responses) and a variable file extension (called originalformat in responses). These values are returned via the API only when the caller has permission to view the original size (based on a user preference and various other criteria). The values are returned by the flickr.photos.getInfo method and by any method that returns a list of photos and allows an extras parameter (with a value of original_format), such as flickr.photos.search. The flickr.photos.getSizes method, as always, will return the full original URL where permissions allow.

  function photo_retrieval($id, $flickr_options){
    $defaults = $this->option_defaults();
    
    $uid = apply_filters( $this->hook, empty($flickr_options['flickr_user_id']) ? 'uid' : $flickr_options['flickr_user_id'], $flickr_options );
    $uid = @ereg_replace('[[:cntrl:]]', '', $uid ); // remove ASCII's control characters
    $groupid = apply_filters( $this->hook, empty($flickr_options['flickr_group_id']) ? 'groupid' : $flickr_options['flickr_group_id'], $flickr_options );
    $groupid = @ereg_replace('[[:cntrl:]]', '', $groupid ); // remove ASCII's control characters
    $set = apply_filters( $this->hook, empty($flickr_options['flickr_set_id']) ? 'set' : $flickr_options['flickr_set_id'], $flickr_options );
    $set = @ereg_replace('[[:cntrl:]]', '', $set ); // remove ASCII's control characters
    $tags = apply_filters( $this->hook, empty($flickr_options['flickr_tags']) ? 'tags' : $flickr_options['flickr_tags'], $flickr_options );
    $tags = @ereg_replace('[[:cntrl:]]', '', $tags ); // remove ASCII's control characters

    $key = 'flickr-'.$this->vers.'-'.$flickr_options['flickr_source'].'-'.$uid.'-'.$groupid.'-'.$set.'-'.$tags.'-'.$flickr_options['flickr_photo_number'].'-'.$flickr_options['flickr_photo_size'].'-'.$flickr_options['flickr_display_link'].'-'.$flickr_options['flickr_display_link_text'];

    $disablecache = $this->get_option( 'cache_disable' );
    if ( !$disablecache ) {
      if( $this->cacheExists($key) ) {
        $results = $this->getCache($key);
        $results = @unserialize($results);
        if( count($results) ){
          $results['hidden'] .= '<!-- Retrieved from cache -->';
          return $results;
        }
      }
    }
    
    $message = '';
    $hidden = '';
    $continue = false;
    $feed_found = false;
    $linkurl = array();
    $photocap = array();
    $photourl = array();
            
    // Determine image size id
    $size_id = '.'; // Default is 500
    switch ($flickr_options['flickr_photo_size']) {
      case 75:
        $size_id = 'url_sq';
      break;
      case 100:
        $size_id = 'url_t';
      break;
      case 240:
        $size_id = 'url_s';
      break;
      case 320:
        $size_id = 'url_n';
      break;
      case 500:
        $size_id = 'url_m';
      break;
      case 640:
        $size_id = 'url_z';
      break;
    }  
    
    // Retrieve content using wp_remote_get and PHP_serial
   if ( function_exists('unserialize') ) {
      // @ is shut-up operator
      // For reference: http://www.flickr.com/services/feeds/
      $flickr_uid = apply_filters( $this->hook, empty($flickr_options['flickr_user_id']) ? 'uid' : $flickr_options['flickr_user_id'], $flickr_options );
      
      switch ($flickr_options['flickr_source']) {
      case 'user':
        $request = 'http://api.flickr.com/services/rest/?method=flickr.photos.search&api_key=68b8278a33237f1f369cbbf3c9a9f45c&per_page='.$flickr_options['flickr_photo_number'].'&format=php_serial&privacy_filter=1&user_id='. $flickr_uid .'&page=1&extras=description,url_sq,url_t,url_s,url_m,url_n,url_z';
      break;
      case 'favorites':
        $request = 'http://api.flickr.com/services/rest/?method=flickr.favorites.getPublicList&api_key=68b8278a33237f1f369cbbf3c9a9f45c&per_page='.$flickr_options['flickr_photo_number'].'&format=php_serial&privacy_filter=1&user_id='. $flickr_uid .'&page=1&extras=description,url_sq,url_t,url_s,url_m,url_n,url_z';
      break;
      case 'group':
        $flickr_groupid = apply_filters( $this->hook, empty($flickr_options['flickr_group_id']) ? '' : $flickr_options['flickr_group_id'], $flickr_options );
        $request = 'http://api.flickr.com/services/rest/?method=flickr.photos.search&api_key=68b8278a33237f1f369cbbf3c9a9f45c&per_page='.$flickr_options['flickr_photo_number'].'&format=php_serial&privacy_filter=1&group_id='. $flickr_groupid .'&page=1&extras=description,url_sq,url_t,url_s,url_m,url_n,url_z';
      break;
      case 'set':
        $flickr_set = apply_filters( $this->hook, empty($flickr_options['flickr_set_id']) ? '' : $flickr_options['flickr_set_id'], $flickr_options );
        $request = 'http://api.flickr.com/services/rest/?method=flickr.photosets.getPhotos&api_key=68b8278a33237f1f369cbbf3c9a9f45c&per_page='.$flickr_options['flickr_photo_number'].'&format=php_serial&privacy_filter=1&photoset_id='. $flickr_set .'&page=1&extras=url_sq,url_t,url_s,url_m,url_o';
      break;
      case 'community':
        $flickr_tags = apply_filters( $this->hook, empty($flickr_options['flickr_tags']) ? '' : $flickr_options['flickr_tags'], $flickr_options );
        $request = 'http://api.flickr.com/services/rest/?method=flickr.photos.search&api_key=68b8278a33237f1f369cbbf3c9a9f45c&per_page='.$flickr_options['flickr_photo_number'].'&format=php_serial&privacy_filter=1&tags='. $flickr_tags .'&page=1&extras=description,url_sq,url_t,url_s,url_m,url_n,url_z';
      break;
      } 

      $_flickr_php = array();
      $response = wp_remote_get($request,
        array(
          'method' => 'GET',
          'timeout' => 20,
        )
      );
      if( is_wp_error( $response ) || !isset($response['body']) ) {
        $hidden .= '<!-- Failed using wp_remote_get() and PHP_Serial @ '.$request.' -->';
      }else{
        $_flickr_php = @unserialize($response['body']);
      }

      if(empty($_flickr_php)){
        $hidden .= '<!-- Failed using wp_remote_get() and PHP_Serial @ '.$request.' -->';
        $continue = false;
      }else{
      
        $content =  $_flickr_php['photos'];
        $photos = $_flickr_php['photos']['photo'];
          
        // Check for photosets  
        if( 'set' == $flickr_options['flickr_source']) {
          $content =  $_flickr_php['photoset'];
          $photos = $_flickr_php['photoset']['photo'];
        }
        
        // Check actual number of photos found
        if( count($photos) < $flickr_options['flickr_photo_number'] ){ $flickr_options['flickr_photo_number']=count($photos);}

        for ($i=0;$i<$flickr_options['flickr_photo_number'];$i++) {
          $linkurl[$i] = 'http://www.flickr.com/photos/'.($photos[$i]['owner']?$photos[$i]['owner']:$flickr_uid).'/'.$photos[$i]['id'].'/';
          $photourl[$i] = $photos[$i][$size_id];
          $originalurl[$i] = $photos[$i]['url_m'];
          if( !$photourl[$i] ){ $photourl[$i] = $originalurl[$i]; } // Incase size didn't exist
          $photocap[$i] = $photos[$i]['title'];
          $photocap[$i] = str_replace('"','',$photocap[$i]);
        }
        if(!empty($linkurl) && !empty($photourl)){
          if( 'community' != $flickr_options['flickr_source'] && $flickr_options['flickr_display_link'] && $flickr_options['flickr_display_link_text']) {
            switch ($flickr_options['flickr_source']) {
              case 'user':
                $link = 'http://www.flickr.com/photos/'.$flickr_uid.'/';
              break;
              case 'favorites':
                $link = 'http://www.flickr.com/photos/'.$flickr_uid.'/favorites/';
              break;
              case 'group':
                $link = 'http://www.flickr.com/groups/'.$flickr_groupid.'/';
              break;
              case 'set':
              if($content['owner'] && $content['id']){
                $link = 'http://www.flickr.com/photos/'.$content['owner'].'/sets/'.$content['id'].'/';
              }
              break;
            } 
          
            if($link){
              $user_link = '<div class="AlpinePhotoTiles-display-link" >';
              $user_link .='<a href="'.$link.'" target="_blank" >';
              $user_link .= $flickr_options['flickr_display_link_text'];
              $user_link .= '</a></div>';
            }
          }
          // If content successfully fetched, generate output...
          $continue = true;
          $hidden  .= '<!-- Success using wp_remote_get() and PHP_Serial -->';
        }else{
          $hidden .= '<!-- No photos found using wp_remote_get() and PHP_Serial @ '.$request.' -->';  
          $continue = false;
          $feed_found = true;
        }
      }
    }
    ///////////////////////////////////////////////////
    /// If nothing found, try using xml and rss_200 ///
    ///////////////////////////////////////////////////

    if ( $continue == false && function_exists('simplexml_load_file') ) {
      $flickr_uid = apply_filters( $this->hook, empty($flickr_options['flickr_user_id']) ? 'uid' : $flickr_options['flickr_user_id'], $flickr_options );
      switch ($flickr_options['flickr_source']) {
      case 'user':
        $request = 'http://api.flickr.com/services/rest/?method=flickr.photos.search&api_key=68b8278a33237f1f369cbbf3c9a9f45c&per_page='.$flickr_options['flickr_photo_number'].'&format=rest&privacy_filter=1&user_id='. $flickr_uid .'&page=1&extras=description,url_sq,url_t,url_s,url_m,url_n,url_z';
      break;
      case 'favorites':
        $request = 'http://api.flickr.com/services/rest/?method=flickr.favorites.getPublicList&api_key=68b8278a33237f1f369cbbf3c9a9f45c&per_page='.$flickr_options['flickr_photo_number'].'&format=rest&privacy_filter=1&user_id='. $flickr_uid .'&page=1&extras=description,url_sq,url_t,url_s,url_m,url_n,url_z';
      break;
      case 'group':
        $flickr_groupid = apply_filters( $this->hook, empty($flickr_options['flickr_group_id']) ? '' : $flickr_options['flickr_group_id'], $flickr_options );
        $request = 'http://api.flickr.com/services/rest/?method=flickr.photos.search&api_key=68b8278a33237f1f369cbbf3c9a9f45c&per_page='.$flickr_options['flickr_photo_number'].'&format=rest&privacy_filter=1&group_id='. $flickr_groupid .'&page=1&extras=description,url_sq,url_t,url_s,url_m,url_n,url_z';
      break;
      case 'set':
        $flickr_set = apply_filters( $this->hook, empty($flickr_options['flickr_set_id']) ? '' : $flickr_options['flickr_set_id'], $flickr_options );
        $request = 'http://api.flickr.com/services/rest/?method=flickr.photosets.getPhotos&api_key=68b8278a33237f1f369cbbf3c9a9f45c&per_page='.$flickr_options['flickr_photo_number'].'&format=rest&privacy_filter=1&photoset_id='. $flickr_set .'&page=1&extras=url_sq,url_t,url_s,url_m,url_o';
      break;
      case 'community':
        $flickr_tags = apply_filters( $this->hook, empty($flickr_options['flickr_tags']) ? '' : $flickr_options['flickr_tags'], $flickr_options );
        $request = 'http://api.flickr.com/services/rest/?method=flickr.photos.search&api_key=68b8278a33237f1f369cbbf3c9a9f45c&per_page='.$flickr_options['flickr_photo_number'].'&format=rest&privacy_filter=1&tags='. $flickr_tags .'&page=1&extras=description,url_sq,url_t,url_s,url_m,url_n,url_z';
      break;
      }

      $_flickrurl  = @urlencode( $request );	// just for compatibility
      $_flickr_xml = @simplexml_load_file( $_flickrurl,"SimpleXMLElement",LIBXML_NOCDATA); // @ is shut-up operator

      if( $_flickr_xml===false || !$_flickr_xml || (!$_flickr_xml->photos && !$_flickr_xml->photoset) ){
        $hidden .= '<!-- Failed using simplexml_load_file() and XML @ '.$request.' -->';
        $continue = false;
      }else{
      
        $content =  $_flickr_xml->photos;
        $photos = $content->photo;
          
        // Check for photosets  
        if( 'set' == $flickr_options['flickr_source']) {
          $content =  $_flickr_xml->photoset;
          $photos = $content->photo;
        }
        $attributes = $content->attributes();

        // Check actual number of photos found
        if( count($photos) < $flickr_options['flickr_photo_number'] ){ $flickr_options['flickr_photo_number']=count($photos);}

        for ($i=0;$i<$flickr_options['flickr_photo_number'];$i++) {
          $current_attr = $photos[$i]->attributes();
          $linkurl[$i] = 'http://www.flickr.com/photos/'.(string)($current_attr['owner']?$current_attr['owner']:$flickr_uid).'/'.(string)$current_attr['id'].'/';
          $photourl[$i] = (string)$current_attr[$size_id];
          $originalurl[$i] = (string)$current_attr['url_m'];
          if( !$photourl[$i] ){ $photourl[$i] = $originalurl[$i]; } // Incase size didn't exist
          $photocap[$i] = (string)$current_attr['title'];
          $photocap[$i] = str_replace('"','',$photocap[$i]);
        }
        if(!empty($linkurl) && !empty($photourl)){
          if( 'community' != $flickr_options['flickr_source'] && $flickr_options['flickr_display_link'] && $flickr_options['flickr_display_link_text']) {
            switch ($flickr_options['flickr_source']) {
              case 'user':
                $link = 'http://www.flickr.com/photos/'.$flickr_uid.'/';
              break;
              case 'favorites':
                $link = 'http://www.flickr.com/photos/'.$flickr_uid.'/favorites/';
              break;
              case 'group':
                $link = 'http://www.flickr.com/groups/'.$flickr_groupid.'/';
              break;
              case 'set':
              // NOTE the use of attributes...
              if($attributes['owner'] && $attributes['id']){
                $link = 'http://www.flickr.com/photos/'.$attributes['owner'].'/sets/'.$attributes['id'].'/';
              }
              break;
            } 
          
            if($link){
              $user_link = '<div class="AlpinePhotoTiles-display-link" >';
              $user_link .='<a href="'.$link.'" target="_blank" >';
              $user_link .= $flickr_options['flickr_display_link_text'];
              $user_link .= '</a></div>';
            }
          }
          // If content successfully fetched, generate output...
          $continue = true;
          $hidden .= '<!-- Success using simplexml_load_file() and XML -->';
        }else{
          $hidden .= '<!-- No photos found using simplexml_load_file() and XML @ '.$request.' -->';
          $continue = false;
          $feed_found = true;
        }
      }
    }
    
    ////////////////////////////////////////////////////////
    ////      If still nothing found, try using RSS      ///
    ////////////////////////////////////////////////////////
    if( $continue == false && function_exists('file_get_contents')) {
      // Try simple file_get_contents function
      
      $flickr_uid = apply_filters( $this->hook, empty($flickr_options['flickr_user_id']) ? 'uid' : $flickr_options['flickr_user_id'], $flickr_options );
      
      switch ($flickr_options['flickr_source']) {
      case 'user':
        $request = 'http://api.flickr.com/services/rest/?method=flickr.photos.search&api_key=68b8278a33237f1f369cbbf3c9a9f45c&per_page='.$flickr_options['flickr_photo_number'].'&format=php_serial&privacy_filter=1&user_id='. $flickr_uid .'&page=1&extras=description,url_sq,url_t,url_s,url_m,url_n,url_z';
      break;
      case 'favorites':
        $request = 'http://api.flickr.com/services/rest/?method=flickr.favorites.getPublicList&api_key=68b8278a33237f1f369cbbf3c9a9f45c&per_page='.$flickr_options['flickr_photo_number'].'&format=php_serial&privacy_filter=1&user_id='. $flickr_uid .'&page=1&extras=description,url_sq,url_t,url_s,url_m,url_n,url_z';
      break;
      case 'group':
        $flickr_groupid = apply_filters( $this->hook, empty($flickr_options['flickr_group_id']) ? '' : $flickr_options['flickr_group_id'], $flickr_options );
        $request = 'http://api.flickr.com/services/rest/?method=flickr.photos.search&api_key=68b8278a33237f1f369cbbf3c9a9f45c&per_page='.$flickr_options['flickr_photo_number'].'&format=php_serial&privacy_filter=1&group_id='. $flickr_groupid .'&page=1&extras=description,url_sq,url_t,url_s,url_m,url_n,url_z';
      break;
      case 'set':
        $flickr_set = apply_filters( $this->hook, empty($flickr_options['flickr_set_id']) ? '' : $flickr_options['flickr_set_id'], $flickr_options );
        $request = 'http://api.flickr.com/services/rest/?method=flickr.photosets.getPhotos&api_key=68b8278a33237f1f369cbbf3c9a9f45c&per_page='.$flickr_options['flickr_photo_number'].'&format=php_serial&privacy_filter=1&photoset_id='. $flickr_set .'&page=1&extras=url_sq,url_t,url_s,url_m,url_o';
      break;
      case 'community':
        $flickr_tags = apply_filters( $this->hook, empty($flickr_options['flickr_tags']) ? '' : $flickr_options['flickr_tags'], $flickr_options );
        $request = 'http://api.flickr.com/services/rest/?method=flickr.photos.search&api_key=68b8278a33237f1f369cbbf3c9a9f45c&per_page='.$flickr_options['flickr_photo_number'].'&format=php_serial&privacy_filter=1&tags='. $flickr_tags .'&page=1&extras=description,url_sq,url_t,url_s,url_m,url_n,url_z';
      break;
      } 

      $_flickrurl = @file_get_contents($request);
      $_flickr_php = @unserialize($_flickrurl);

      if(empty($_flickr_php)){
        $hidden .= '<!-- Failed using file_get_contents() and PHP_Serial @ '.$request.' -->';
        $continue = false;
      }else{
      
        $content =  $_flickr_php['photos'];
        $photos = $_flickr_php['photos']['photo'];
          
        // Check for photosets  
        if( 'set' == $flickr_options['flickr_source']) {
          $content =  $_flickr_php['photoset'];
          $photos = $_flickr_php['photoset']['photo'];
        }
        
        // Check actual number of photos found
        if( count($photos) < $flickr_options['flickr_photo_number'] ){ $flickr_options['flickr_photo_number']=count($photos);}

        for ($i=0;$i<$flickr_options['flickr_photo_number'];$i++) {
          $linkurl[$i] = 'http://www.flickr.com/photos/'.($photos[$i]['owner']?$photos[$i]['owner']:$flickr_uid).'/'.$photos[$i]['id'].'/';
          $photourl[$i] = $photos[$i][$size_id];
          $originalurl[$i] = $photos[$i]['url_m'];
          if( !$photourl[$i] ){ $photourl[$i] = $originalurl[$i]; } // Incase size didn't exist
          $photocap[$i] = $photos[$i]['title'];
          $photocap[$i] = str_replace('"','',$photocap[$i]);
        }
        if(!empty($linkurl) && !empty($photourl)){
          if( 'community' != $flickr_options['flickr_source'] && $flickr_options['flickr_display_link'] && $flickr_options['flickr_display_link_text']) {
            switch ($flickr_options['flickr_source']) {
              case 'user':
                $link = 'http://www.flickr.com/photos/'.$flickr_uid.'/';
              break;
              case 'favorites':
                $link = 'http://www.flickr.com/photos/'.$flickr_uid.'/favorites/';
              break;
              case 'group':
                $link = 'http://www.flickr.com/groups/'.$flickr_groupid.'/';
              break;
              case 'set':
              if($content['owner'] && $content['id']){
                $link = 'http://www.flickr.com/photos/'.$content['owner'].'/sets/'.$content['id'].'/';
              }
              break;
            } 
          
            if($link){
              $user_link = '<div class="AlpinePhotoTiles-display-link" >';
              $user_link .='<a href="'.$link.'" target="_blank" >';
              $user_link .= $flickr_options['flickr_display_link_text'];
              $user_link .= '</a></div>';
            }
          }
          // If content successfully fetched, generate output...
          $continue = true;
          $hidden  .= '<!-- Success using file_get_contents() and PHP_Serial -->';
        }else{
          $hidden .= '<!-- No photos found using file_get_contents() and PHP_Serial @ '.$request.' -->';  
          $continue = false;
          $feed_found = true;
        }
      }   
    }
      
    ///////////////////////////////////////////////////////////////////////
    //// If STILL!!! nothing found, report that Flickr ID must be wrong ///
    ///////////////////////////////////////////////////////////////////////
    if( false == $continue ) {
      if($feed_found ){
        $message .= '- Flickr feed was successfully retrieved, but no photos found.';
      }else{
        $message .= '- Flickr feed not found. Please recheck your ID.';
      }
    }
      
    $results = array('continue'=>$continue,'message'=>$message,'hidden'=>$hidden,'user_link'=>$user_link,'image_captions'=>$photocap,'image_urls'=>$photourl,'image_perms'=>$linkurl,'image_originals'=>$originalurl);
    
    if( true == $continue && !$disablecache ){     
      $cache_results = $results;
      if(!is_serialized( $cache_results  )) { $cache_results  = maybe_serialize( $cache_results ); }
      $this->putCache($key, $cache_results);
      $cachetime = $this->get_option( 'cache_time' );
      if( $cachetime && is_numeric($cachetime) ){
        $this->setExpiryInterval( $cachetime*60*60 );
      }
    }
    return $results;
  }
  
  
  
/**
 *  Function for printing vertical style
 *  
 *  @ Since 0.0.1
 */
  function display_vertical($id, $options, $source_results){
    $linkurl = $source_results['image_perms'];
    $photocap = $source_results['image_captions'];
    $photourl = $source_results['image_urls'];
    $userlink = $source_results['user_link'];
    $originalurl = $source_results['image_originals'];

  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////       Check Content      /////////////////////////////////////////////////////
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    if($options['flickr_photo_number'] != count($linkurl)){$options['flickr_photo_number']=count($linkurl);}
        
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  /////////////////////////////////////////////   Begin the Content   /////////////////////////////////////////////////////
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                      
    $output .= '<div id="'.$id.'-AlpinePhotoTiles_container" class="AlpinePhotoTiles_container_class">';     
    
    // Align photos
    $output .= '<div id="'.$id.'-vertical-parent" class="AlpinePhotoTiles_parent_class" style="width:'.$options['flickr_photo_size'].'px;max-width:'.$options['widget_max_width'].'%;padding:0px;';
    if( 'center' == $options['widget_alignment'] ){                          //  Optional: Set text alignment (left/right) or center
      $output .= 'margin:0px auto;text-align:center;';
    }
    else{
      $output .= 'float:' . $options['widget_alignment'] . ';text-align:' . $options['widget_alignment'] . ';';
    } 
    $output .= '">';
    
    $shadow = ($options['style_shadow']?'AlpinePhotoTiles-img-shadow':'AlpinePhotoTiles-img-noshadow');
    $border = ($options['style_border']?'AlpinePhotoTiles-img-border':'AlpinePhotoTiles-img-noborder');
    $curves = ($options['style_curve_corners']?'AlpinePhotoTiles-img-corners':'AlpinePhotoTiles-img-nocorners');
    $highlight = ($options['style_highlight']?'AlpinePhotoTiles-img-highlight':'AlpinePhotoTiles-img-nohighlight');
    
    for($i = 0;$i<$options['flickr_photo_number'];$i++){
      $has_link = false;
      $link = $options['flickr_image_link_option'];
      if( 'original' == $link && !empty($photourl[$i]) ){
        $output .= '<a href="' . $photourl[$i] . '" class="AlpinePhotoTiles-link" target="_blank" title='."'". $photocap[$i] ."'".'>';
        $has_link = true;
      }elseif( ('flickr' == $link || '1' == $link)&& !empty($linkurl[$i]) ){
        $output .= '<a href="' . $linkurl[$i] . '" class="AlpinePhotoTiles-link" target="_blank" title='."'". $photocap[$i] ."'".'>';
        $has_link = true;
      }elseif( 'link' == $link && !empty($options['custom_link_url']) ){
        $output .= '<a href="' . $options['custom_link_url'] . '" class="AlpinePhotoTiles-link" target="_blank" title='."'". $photocap[$i] ."'".'>'; 
        $has_link = true;
      }elseif( 'fancybox' == $link && !empty($originalurl[$i]) ){
        $output .= '<a href="' . $originalurl[$i] . '" class="AlpinePhotoTiles-link" target="_blank" title='."'". $photocap[$i] ."'".'>'; 
        $has_link = true;
      }      
      $output .= '<img id="'.$id.'-tile-'.$i.'" class="AlpinePhotoTiles-image '.$shadow.' '.$border.' '.$curves.' '.$highlight.'" src="' . $photourl[$i] . '" ';
      $output .= 'title='."'". $photocap[$i] ."'".' alt='."'". $photocap[$i] ."' "; // Careful about caps with ""
      $output .= 'border="0" hspace="0" vspace="0" style="margin:1px 0 5px 0;padding:0;max-width:100%;"/>'; // Override the max-width set by theme
      if( $has_link ){ $output .= '</a>'; }
    }
    
    if( !$options['widget_disable_credit_link'] ){
      $by_link  =  '<div id="'.$id.'-by-link" class="AlpinePhotoTiles-by-link"><a href="http://thealpinepress.com/" style="COLOR:#C0C0C0;text-decoration:none;" title="Widget by The Alpine Press">TAP</a></div>';   
      $output .=  $by_link;    
    }          
    // Close vertical-parent
    $output .= '</div>';    

    if($userlink){ 
      $output .= '<div id="'.$id.'-display-link" class="AlpinePhotoTiles-display-link-container" ';
      $output .= 'style="text-align:' . $options['widget_alignment'] . ';">'.$userlink.'</div>'; // Only breakline if floating
    }

    // Close container
    $output .= '</div>';
    $output .= '<div class="AlpinePhotoTiles_breakline"></div>';
    
    $highlight = $this->get_option("general_highlight_color");
    $highlight = ($highlight?$highlight:'#64a2d8');

    if( $options['style_shadow'] || $options['style_border'] || $options['style_highlight']  ){
      $output .= '<script>
           jQuery(window).load(function() {
              if(jQuery().AlpineAdjustBordersPlugin ){
                jQuery("#'.$id.'-vertical-parent").AlpineAdjustBordersPlugin({
                  highlight:"'.$highlight.'",
                });
              }  
            });
          </script>';  
    }   
    if( $options['flickr_image_link_option'] == "fancybox"  ){
      $output .= '<script>
                  jQuery(window).load(function() {
                    jQuery( "a[rel^=\'fancybox-'.$id.'\']" ).fancybox( { titleShow: false, overlayOpacity: .8, overlayColor: "#000" } );
                  })
                </script>';  
    } 
    return $output;
  }  
/**
 *  Function for printing cascade style
 *  
 *  @ Since 0.0.1
 */
  function display_cascade($id, $options, $source_results){
    $linkurl = $source_results['image_perms'];
    $photocap = $source_results['image_captions'];
    $photourl = $source_results['image_urls'];
    $userlink = $source_results['user_link'];
    $originalurl = $source_results['image_originals'];
    
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////       Check Content      /////////////////////////////////////////////////////
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    if($options['flickr_photo_number'] != count($linkurl)){$options['flickr_photo_number']= count($linkurl);}
        

  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  /////////////////////////////////////////////   Begin the Content   /////////////////////////////////////////////////////
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            
    $output .= '<div id="'.$id.'-AlpinePhotoTiles_container" class="AlpinePhotoTiles_container_class">';     
    
    // Align photos
    $output .= '<div id="'.$id.'-cascade-parent" class="AlpinePhotoTiles_parent_class" style="width:100%;max-width:'.$options['widget_max_width'].'%;padding:0px;';
    if( 'center' == $options['widget_alignment'] ){                          //  Optional: Set text alignment (left/right) or center
      $output .= 'margin:0px auto;text-align:center;';
    }
    else{
      $output .= 'float:' . $options['widget_alignment'] . ';text-align:' . $options['widget_alignment'] . ';';
    } 
    $output .= '">';
    
    $shadow = ($options['style_shadow']?'AlpinePhotoTiles-img-shadow':'AlpinePhotoTiles-img-noshadow');
    $border = ($options['style_border']?'AlpinePhotoTiles-img-border':'AlpinePhotoTiles-img-noborder');
    $curves = ($options['style_curve_corners']?'AlpinePhotoTiles-img-corners':'AlpinePhotoTiles-img-nocorners'); 
    $highlight = ($options['style_highlight']?'AlpinePhotoTiles-img-highlight':'AlpinePhotoTiles-img-nohighlight');
    
    for($col = 0; $col<$options['style_column_number'];$col++){
      $output .= '<div class="AlpinePhotoTiles_cascade_column" style="width:'.(100/$options['style_column_number']).'%;float:left;margin:0;">';
      $output .= '<div class="AlpinePhotoTiles_cascade_column_inner" style="display:block;margin:0 3px;overflow:hidden;">';
      for($i = $col;$i<$options['flickr_photo_number'];$i+=$options['style_column_number']){
        $has_link = false;
        $link = $options['flickr_image_link_option'];
        if( 'original' == $link && !empty($photourl[$i]) ){
          $output .= '<a href="' . $photourl[$i] . '" class="AlpinePhotoTiles-link" target="_blank" title='."'". $photocap[$i] ."'".'>';
          $has_link = true;
        }elseif( ('flickr' == $link || '1' == $link)&& !empty($linkurl[$i]) ){
          $output .= '<a href="' . $linkurl[$i] . '" class="AlpinePhotoTiles-link" target="_blank" title='."'". $photocap[$i] ."'".'>';
          $has_link = true;
        }elseif( 'link' == $link && !empty($options['custom_link_url']) ){
          $output .= '<a href="' . $options['custom_link_url'] . '" class="AlpinePhotoTiles-link" target="_blank" title='."'". $photocap[$i] ."'".'>'; 
          $has_link = true;
        }elseif( 'fancybox' == $link && !empty($originalurl[$i]) ){
          $output .= '<a href="' . $originalurl[$i] . '" class="AlpinePhotoTiles-link" target="_blank" title='."'". $photocap[$i] ."'".'>'; 
          $has_link = true;
        }    
        $output .= '<img id="'.$id.'-tile-'.$i.'" class="AlpinePhotoTiles-image '.$shadow.' '.$border.' '.$curves.' '.$highlight.'" src="' . $photourl[$i] . '" ';
        $output .= 'title='."'". $photocap[$i] ."'".' alt='."'". $photocap[$i] ."' "; // Careful about caps with ""
        $output .= 'border="0" hspace="0" vspace="0" style="margin:1px 0 5px 0;padding:0;max-width:100%;"/>'; // Override the max-width set by theme
        if( $has_link ){ $output .= '</a>'; }
      }
      $output .= '</div></div>';
    }
    $output .= '<div class="AlpinePhotoTiles_breakline"></div>';
      
    if( !$options['widget_disable_credit_link'] ){
      $by_link  =  '<div id="'.$id.'-by-link" class="AlpinePhotoTiles-by-link"><a href="http://thealpinepress.com/" style="COLOR:#C0C0C0;text-decoration:none;" title="Widget by The Alpine Press">TAP</a></div>';      
      $output .=  $by_link;    
    }          
    // Close cascade-parent
    $output .= '</div>';    

    $output .= '<div class="AlpinePhotoTiles_breakline"></div>';
    
    if($userlink){ 
      if($options['widget_alignment'] == 'center'){                          //  Optional: Set text alignment (left/right) or center
        $output .= '<div id="'.$id.'-display-link" class="AlpinePhotoTiles-display-link-container" ';
        $output .= 'style="width:100%;margin:0px auto;">'.$userlink.'</div>';
      }
      else{
        $output .= '<div id="'.$id.'-display-link" class="AlpinePhotoTiles-display-link-container" ';
        $output .= 'style="float:' . $options['widget_alignment'] . ';width:'.$options['flickr_photo_size'].'px;max-width:'.$options['widget_max_width'].'%;"><center>'.$userlink.'</center></div>'; // Only breakline if floating
      } 
    }

    // Close container
    $output .= '</div>';
    $output .= '<div class="AlpinePhotoTiles_breakline"></div>';
   
    $highlight = $this->get_option("general_highlight_color");
    $highlight = ($highlight?$highlight:'#64a2d8');
    
    if( $options['style_shadow'] || $options['style_border'] || $options['style_highlight']  ){
      $output .= '<script>
           jQuery(window).load(function() {
              if(jQuery().AlpineAdjustBordersPlugin ){
                jQuery("#'.$id.'-cascade-parent").AlpineAdjustBordersPlugin({
                  highlight:"'.$highlight.'",
                });
              }  
            });
          </script>';  
    }   
    if( $options['flickr_image_link_option'] == "fancybox"  ){
      $output .= '<script>
                  jQuery(window).load(function() {
                    jQuery( "a[rel^=\'fancybox-'.$id.'\']" ).fancybox( { titleShow: false, overlayOpacity: .8, overlayColor: "#000" } );
                  })
                </script>';  
    } 
    return $output;
    
  }

/**
 *  Function for printing and initializing JS styles
 *  
 *  @ Since 0.0.1
 */
  function display_hidden($id, $options, $source_results){
    $linkurl = $source_results['image_perms'];
    $photocap = $source_results['image_captions'];
    $photourl = $source_results['image_urls'];
    $userlink = $source_results['user_link'];
    $originalurl = $source_results['image_originals'];

  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////       Check Content      /////////////////////////////////////////////////////
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    if($options['flickr_photo_number'] != count($linkurl)){$options['flickr_photo_number']=count($linkurl);}
        
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  /////////////////////////////////////////////   Begin the Content   /////////////////////////////////////////////////////
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            
    $output .= '<div id="'.$id.'-AlpinePhotoTiles_container" class="AlpinePhotoTiles_container_class">';     
    
    // Align photos
    $output .= '<div id="'.$id.'-hidden-parent" class="AlpinePhotoTiles_parent_class" style="width:'.$options['flickr_photo_size'].'px;max-width:'.$options['widget_max_width'].'%;padding:0px;';
    if( 'center' == $options['widget_alignment'] ){                          //  Optional: Set text alignment (left/right) or center
      $output .= 'margin:0px auto;text-align:center;';
    }
    else{
      $output .= 'float:' . $options['widget_alignment'] . ';text-align:' . $options['widget_alignment'] . ';';
    } 
    $output .= '">';
    
    $output .= '<div id="'.$id.'-image-list" class="AlpinePhotoTiles_image_list_class" style="display:none;visibility:hidden;">'; 
    
    $shadow = ($options['style_shadow']?'AlpinePhotoTiles-img-shadow':'AlpinePhotoTiles-img-noshadow');
    $border = ($options['style_border']?'AlpinePhotoTiles-img-border':'AlpinePhotoTiles-img-noborder');
    $curves = ($options['style_curve_corners']?'AlpinePhotoTiles-img-corners':'AlpinePhotoTiles-img-nocorners');
    
    for($i = 0;$i<$options['flickr_photo_number'];$i++){
      $has_link = false;
      $link = $options['flickr_image_link_option'];
      if( 'original' == $link && !empty($photourl[$i]) ){
        $output .= '<a href="' . $photourl[$i] . '" class="AlpinePhotoTiles-link" target="_blank" title='."'". $photocap[$i] ."'".'>';
        $has_link = true;
      }elseif( ('flickr' == $link || '1' == $link)&& !empty($linkurl[$i]) ){
        $output .= '<a href="' . $linkurl[$i] . '" class="AlpinePhotoTiles-link" target="_blank" title='."'". $photocap[$i] ."'".'>';
        $has_link = true;
      }elseif( 'link' == $link && !empty($options['custom_link_url']) ){
        $output .= '<a href="' . $options['custom_link_url'] . '" class="AlpinePhotoTiles-link" target="_blank" title='."'". $photocap[$i] ."'".'>'; 
        $has_link = true;
      }elseif( 'fancybox' == $link && !empty($originalurl[$i]) ){
        $output .= '<a href="' . $originalurl[$i] . '" class="AlpinePhotoTiles-link" target="_blank" title='."'". $photocap[$i] ."'".'>'; 
        $has_link = true;
      }     
      $output .= '<img id="'.$id.'-tile-'.$i.'" class="AlpinePhotoTiles-image '.$shadow.' '.$border.' '.$curves.'" src="' . $photourl[$i] . '" ';
      $output .= 'title='."'". $photocap[$i] ."'".' alt='."'". $photocap[$i] ."' "; // Careful about caps with ""
      $output .= 'border="0" hspace="0" vspace="0" />'; // Override the max-width set by theme
      
      // Load original image size
      if( "gallery" == $options['style_option'] && $originalurl[$i] ){
        $output .= '<img class="AlpinePhotoTiles-original-image" src="' . $originalurl[$i]. '" />';
      }
      if( $has_link ){ $output .= '</a>'; }
    }
    $output .= '</div>';
    
    if( !$options['widget_disable_credit_link'] ){
      $by_link  =  '<div id="'.$id.'-by-link" class="AlpinePhotoTiles-by-link"><a href="http://thealpinepress.com/" style="COLOR:#C0C0C0;text-decoration:none;" title="Widget by The Alpine Press">TAP</a></div>';   
      $output .=  $by_link;    
    }          
    // Close vertical-parent
    $output .= '</div>';      

    if($userlink){ 
      if($options['widget_alignment'] == 'center'){                          //  Optional: Set text alignment (left/right) or center
        $output .= '<div id="'.$id.'-display-link" class="AlpinePhotoTiles-display-link-container" ';
        $output .= 'style="width:100%;margin:0px auto;">'.$userlink.'</div>';
      }
      else{
        $output .= '<div id="'.$id.'-display-link" class="AlpinePhotoTiles-display-link-container" ';
        $output .= 'style="float:' . $options['widget_alignment'] . ';width:'.$options['flickr_photo_size'].'px;max-width:'.$options['widget_max_width'].'%;"><center>'.$userlink.'</center></div>'; // Only breakline if floating
      } 
    }

    // Close container
    $output .= '</div>';
    $disable = $this->get_option("general_loader");
    $highlight = $this->get_option("general_highlight_color");
    $highlight = ($highlight?$highlight:'#64a2d8');
    
    $output .= '<script>';
    
    if(!$disable){
      $output .= '
             jQuery(document).ready(function() {
              jQuery("#'.$id.'-AlpinePhotoTiles_container").addClass("loading"); 
             });';
    }
    $output .= '
           jQuery(window).load(function() {
            jQuery("#'.$id.'-AlpinePhotoTiles_container").removeClass("loading");
            if( jQuery().AlpinePhotoTilesPlugin ){
              jQuery("#'.$id.'-hidden-parent").AlpinePhotoTilesPlugin({
                id:"'.$id.'",
                style:"'.($options['style_option']?$options['style_option']:'windows').'",
                shape:"'.($options['style_shape']?$options['style_shape']:'square').'",
                perRow:"'.($options['style_photo_per_row']?$options['style_photo_per_row']:'3').'",
                imageLink:'.($options['flickr_image_link']?'1':'0').',
                imageBorder:'.($options['style_border']?'1':'0').',
                imageShadow:'.($options['style_shadow']?'1':'0').',
                imageCurve:'.($options['style_curve_corners']?'1':'0').',
                imageHighlight:'.($options['style_highlight']?'1':'0').',
                fancybox:'.($options['flickr_image_link_option'] == "fancybox"?'1':'0').',
                galleryHeight:'.($options['style_gallery_height']?$options['style_gallery_height']:'3').',
                highlight:"'.$highlight.'",
                pinIt:'.($options['pinterest_pin_it_button']?'1':'0').',
                siteURL:"'.get_option( 'siteurl' ).'"
              });
            }
          });
        </script>';
        
    return $output; 
  }
 
}

?>
