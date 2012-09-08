<?php
/**
 * Alpine PhotoTile for Flickr: Photo Retrieval Function
 * The PHP for retrieving content from Flickr.
 *
 * @since 1.0.0
 * @updated 1.0.3
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

function APTFFbyTAP_photo_retrieval($id, $flickr_options, $defaults){  
  $APTFFbyTAP_flickr_uid = apply_filters( APTFFbyTAP_HOOK, empty($flickr_options['flickr_user_id']) ? 'uid' : $flickr_options['flickr_user_id'], $flickr_options );
  $APTFFbyTAP_flickr_uid = @ereg_replace('[[:cntrl:]]', '', $APTFFbyTAP_flickr_uid ); // remove ASCII's control characters
  $APTFFbyTAP_flickr_groupid = apply_filters( APTFFbyTAP_HOOK, empty($flickr_options['flickr_group_id']) ? 'groupid' : $flickr_options['flickr_group_id'], $flickr_options );
  $APTFFbyTAP_flickr_groupid = @ereg_replace('[[:cntrl:]]', '', $APTFFbyTAP_flickr_groupid ); // remove ASCII's control characters
  $APTFFbyTAP_flickr_set = apply_filters( APTFFbyTAP_HOOK, empty($flickr_options['flickr_set_id']) ? 'set' : $flickr_options['flickr_set_id'], $flickr_options );
  $APTFFbyTAP_flickr_set = @ereg_replace('[[:cntrl:]]', '', $APTFFbyTAP_flickr_set ); // remove ASCII's control characters
  $APTFFbyTAP_flickr_tags = apply_filters( APTFFbyTAP_HOOK, empty($flickr_options['flickr_tags']) ? 'tags' : $flickr_options['flickr_tags'], $flickr_options );
  $APTFFbyTAP_flickr_tags = @ereg_replace('[[:cntrl:]]', '', $APTFFbyTAP_flickr_tags ); // remove ASCII's control characters

  $key = 'flickr-'.$flickr_options['flickr_source'].'-'.$APTFFbyTAP_flickr_uid.'-'.$APTFFbyTAP_flickr_groupid.'-'.$APTFFbyTAP_flickr_set.'-'.$APTFFbyTAP_flickr_tags.'-'.$flickr_options['flickr_photo_number'].'-'.$flickr_options['flickr_photo_size'].'-'.$flickr_options['flickr_display_link'].'-'.$flickr_options['flickr_display_link_text'];

  $disablecache = APTFFbyTAP_get_option( 'cache_disable' );
  if ( class_exists( 'theAlpinePressSimpleCacheV2' ) && APTFFbyTAP_CACHE && !$disablecache ) {
    $cache = new theAlpinePressSimpleCacheV2();  
    $cache->setCacheDir( APTFFbyTAP_CACHE );

    if( $cache->exists($key) ) {
      $results = $cache->get($key);
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
  $APTFFbyTAP_linkurl = array();
  $APTFFbyTAP_photocap = array();
  $APTFFbyTAP_photourl = array();
          
  // Determine image size id
  $APTFFbyTAP_size_id = '.'; // Default is 500
  switch ($flickr_options['flickr_photo_size']) {
    case 75:
      $APTFFbyTAP_size_id = 'url_sq';
    break;
    case 100:
      $APTFFbyTAP_size_id = 'url_t';
    break;
    case 240:
      $APTFFbyTAP_size_id = 'url_s';
    break;
    case 320:
      $APTFFbyTAP_size_id = 'url_n';
    break;
    case 500:
      $APTFFbyTAP_size_id = 'url_m';
    break;
    case 640:
      $APTFFbyTAP_size_id = 'url_z';
    break;
  }  
  
  // Retrieve content using curl_init and PHP_serial
 if ( curl_init() ) {
    // @ is shut-up operator
    // For reference: http://www.flickr.com/services/feeds/
    $flickr_uid = apply_filters( APTFFbyTAP_HOOK, empty($flickr_options['flickr_user_id']) ? 'uid' : $flickr_options['flickr_user_id'], $flickr_options );
    
    switch ($flickr_options['flickr_source']) {
    case 'user':
      $request = 'http://api.flickr.com/services/rest/?method=flickr.photos.search&api_key=68b8278a33237f1f369cbbf3c9a9f45c&per_page='.$flickr_options['flickr_photo_number'].'&format=php_serial&privacy_filter=1&user_id='. $flickr_uid .'&page=1&extras=description,url_sq,url_t,url_s,url_m,url_n,url_z';
    break;
    case 'favorites':
      $request = 'http://api.flickr.com/services/rest/?method=flickr.favorites.getPublicList&api_key=68b8278a33237f1f369cbbf3c9a9f45c&per_page='.$flickr_options['flickr_photo_number'].'&format=php_serial&privacy_filter=1&user_id='. $flickr_uid .'&page=1&extras=description,url_sq,url_t,url_s,url_m,url_n,url_z';
    break;
    case 'group':
      $flickr_groupid = apply_filters( APTFFbyTAP_HOOK, empty($flickr_options['flickr_group_id']) ? '' : $flickr_options['flickr_group_id'], $flickr_options );
      $request = 'http://api.flickr.com/services/rest/?method=flickr.photos.search&api_key=68b8278a33237f1f369cbbf3c9a9f45c&per_page='.$flickr_options['flickr_photo_number'].'&format=php_serial&privacy_filter=1&group_id='. $flickr_groupid .'&page=1&extras=description,url_sq,url_t,url_s,url_m,url_n,url_z';
    break;
    case 'set':
      $APTFFbyTAP_flickr_set = apply_filters( APTFFbyTAP_HOOK, empty($flickr_options['flickr_set_id']) ? '' : $flickr_options['flickr_set_id'], $flickr_options );
      $request = 'http://api.flickr.com/services/rest/?method=flickr.photosets.getPhotos&api_key=68b8278a33237f1f369cbbf3c9a9f45c&per_page='.$flickr_options['flickr_photo_number'].'&format=php_serial&privacy_filter=1&photoset_id='. $APTFFbyTAP_flickr_set .'&page=1&extras=url_sq,url_t,url_s,url_m,url_o';
    break;
    case 'community':
      $APTFFbyTAP_flickr_tags = apply_filters( APTFFbyTAP_HOOK, empty($flickr_options['flickr_tags']) ? '' : $flickr_options['flickr_tags'], $flickr_options );
      $request = 'http://api.flickr.com/services/rest/?method=flickr.photos.search&api_key=68b8278a33237f1f369cbbf3c9a9f45c&per_page='.$flickr_options['flickr_photo_number'].'&format=php_serial&privacy_filter=1&tags='. $APTFFbyTAP_flickr_tags .'&page=1&extras=description,url_sq,url_t,url_s,url_m,url_n,url_z';
    break;
    } 
    
    $ci = @curl_init();
    @curl_setopt($ci, CURLOPT_URL, $request);
    @curl_setopt($ci, CURLOPT_RETURNTRANSFER, 1);
    $_flickrurl = @curl_exec($ci);
    @curl_close($ci);
    
    $_flickr_php = @unserialize($_flickrurl);

    if(empty($_flickr_php)){
      $hidden .= '<!-- Failed using curl_init() and PHP_Serial @ '.$request.' -->';
      $continue = false;
    }else{
    
      $APTFFbyTAP_content =  $_flickr_php['photos'];
      $APTFFbyTAP_photos = $_flickr_php['photos']['photo'];
        
      // Check for photosets  
      if( 'set' == $flickr_options['flickr_source']) {
        $APTFFbyTAP_content =  $_flickr_php['photoset'];
        $APTFFbyTAP_photos = $_flickr_php['photoset']['photo'];
      }
      
      // Check actual number of photos found
      if( count($APTFFbyTAP_photos) < $flickr_options['flickr_photo_number'] ){ $flickr_options['flickr_photo_number']=count($APTFFbyTAP_photos);}

      for ($i=0;$i<$flickr_options['flickr_photo_number'];$i++) {
        $APTFFbyTAP_linkurl[$i] = 'http://www.flickr.com/photos/'.($APTFFbyTAP_photos[$i]['owner']?$APTFFbyTAP_photos[$i]['owner']:$flickr_uid).'/'.$APTFFbyTAP_photos[$i]['id'].'/';
        $APTFFbyTAP_photourl[$i] = $APTFFbyTAP_photos[$i][$APTFFbyTAP_size_id];
        $APTFFbyTAP_originalurl[$i] = $APTFFbyTAP_photos[$i]['url_m'];
        if( !$APTFFbyTAP_photourl[$i] ){ $APTFFbyTAP_photourl[$i] = $APTFFbyTAP_originalurl[$i]; } // Incase size didn't exist
        $APTFFbyTAP_photocap[$i] = $APTFFbyTAP_photos[$i]['title'];
      }
      if(!empty($APTFFbyTAP_linkurl) && !empty($APTFFbyTAP_photourl)){
        if( 'community' != $flickr_options['flickr_source'] && $flickr_options['flickr_display_link'] && $flickr_options['flickr_display_link_text']) {
          switch ($flickr_options['flickr_source']) {
            case 'user':
              $APTFFbyTAP_link = 'http://www.flickr.com/photos/'.$flickr_uid.'/';
            break;
            case 'favorites':
              $APTFFbyTAP_link = 'http://www.flickr.com/photos/'.$flickr_uid.'/favorites/';
            break;
            case 'group':
              $APTFFbyTAP_link = 'http://www.flickr.com/groups/'.$flickr_groupid.'/';
            break;
            case 'set':
            if($APTFFbyTAP_content['owner'] && $APTFFbyTAP_content['id']){
              $APTFFbyTAP_link = 'http://www.flickr.com/photos/'.$APTFFbyTAP_content['owner'].'/sets/'.$APTFFbyTAP_content['id'].'/';
            }
            break;
          } 
        
          if($APTFFbyTAP_link){
            $APTFFbyTAP_user_link = '<div class="APTFFbyTAP-display-link" >';
            $APTFFbyTAP_user_link .='<a href="'.$APTFFbyTAP_link.'" target="_blank" >';
            $APTFFbyTAP_user_link .= $flickr_options['flickr_display_link_text'];
            $APTFFbyTAP_user_link .= '</a></div>';
          }
        }
        // If content successfully fetched, generate output...
        $continue = true;
        $hidden  .= '<!-- Success using curl_init() and PHP_Serial -->';
      }else{
        $hidden .= '<!-- No photos found using curl_init() and PHP_Serial @ '.$request.' -->';  
        $continue = false;
        $feed_found = true;
      }
    }
  }
  ///////////////////////////////////////////////////
  /// If nothing found, try using xml and rss_200 ///
  ///////////////////////////////////////////////////

  if ( $continue == false && function_exists('simplexml_load_file') ) {
    $flickr_uid = apply_filters( APTFFbyTAP_HOOK, empty($flickr_options['flickr_user_id']) ? 'uid' : $flickr_options['flickr_user_id'], $flickr_options );
    switch ($flickr_options['flickr_source']) {
    case 'user':
      $request = 'http://api.flickr.com/services/rest/?method=flickr.photos.search&api_key=68b8278a33237f1f369cbbf3c9a9f45c&per_page='.$flickr_options['flickr_photo_number'].'&format=rest&privacy_filter=1&user_id='. $flickr_uid .'&page=1&extras=description,url_sq,url_t,url_s,url_m,url_n,url_z';
    break;
    case 'favorites':
      $request = 'http://api.flickr.com/services/rest/?method=flickr.favorites.getPublicList&api_key=68b8278a33237f1f369cbbf3c9a9f45c&per_page='.$flickr_options['flickr_photo_number'].'&format=rest&privacy_filter=1&user_id='. $flickr_uid .'&page=1&extras=description,url_sq,url_t,url_s,url_m,url_n,url_z';
    break;
    case 'group':
      $flickr_groupid = apply_filters( APTFFbyTAP_HOOK, empty($flickr_options['flickr_group_id']) ? '' : $flickr_options['flickr_group_id'], $flickr_options );
      $request = 'http://api.flickr.com/services/rest/?method=flickr.photos.search&api_key=68b8278a33237f1f369cbbf3c9a9f45c&per_page='.$flickr_options['flickr_photo_number'].'&format=rest&privacy_filter=1&group_id='. $flickr_groupid .'&page=1&extras=description,url_sq,url_t,url_s,url_m,url_n,url_z';
    break;
    case 'set':
      $APTFFbyTAP_flickr_set = apply_filters( APTFFbyTAP_HOOK, empty($flickr_options['flickr_set_id']) ? '' : $flickr_options['flickr_set_id'], $flickr_options );
      $request = 'http://api.flickr.com/services/rest/?method=flickr.photosets.getPhotos&api_key=68b8278a33237f1f369cbbf3c9a9f45c&per_page='.$flickr_options['flickr_photo_number'].'&format=rest&privacy_filter=1&photoset_id='. $APTFFbyTAP_flickr_set .'&page=1&extras=url_sq,url_t,url_s,url_m,url_o';
    break;
    case 'community':
      $APTFFbyTAP_flickr_tags = apply_filters( APTFFbyTAP_HOOK, empty($flickr_options['flickr_tags']) ? '' : $flickr_options['flickr_tags'], $flickr_options );
      $request = 'http://api.flickr.com/services/rest/?method=flickr.photos.search&api_key=68b8278a33237f1f369cbbf3c9a9f45c&per_page='.$flickr_options['flickr_photo_number'].'&format=rest&privacy_filter=1&tags='. $APTFFbyTAP_flickr_tags .'&page=1&extras=description,url_sq,url_t,url_s,url_m,url_n,url_z';
    break;
    }

    $_flickrurl  = @urlencode( $request );	// just for compatibility
    $_flickr_xml = @simplexml_load_file( $_flickrurl,"SimpleXMLElement",LIBXML_NOCDATA); // @ is shut-up operator
//print_r( $_flickr_xml);
    if( $_flickr_xml===false || !$_flickr_xml || (!$_flickr_xml->photos && !$_flickr_xml->photoset) ){
      $hidden .= '<!-- Failed using simplexml_load_file() and XML @ '.$request.' -->';
      $continue = false;
    }else{
    
      $APTFFbyTAP_content =  $_flickr_xml->photos;
      $APTFFbyTAP_photos = $APTFFbyTAP_content->photo;
        
      // Check for photosets  
      if( 'set' == $flickr_options['flickr_source']) {
        $APTFFbyTAP_content =  $_flickr_xml->photoset;
        $APTFFbyTAP_photos = $APTFFbyTAP_content->photo;
      }
      $APTFFbyTAP_attributes = $APTFFbyTAP_content->attributes();

      // Check actual number of photos found
      if( count($APTFFbyTAP_photos) < $flickr_options['flickr_photo_number'] ){ $flickr_options['flickr_photo_number']=count($APTFFbyTAP_photos);}

      for ($i=0;$i<$flickr_options['flickr_photo_number'];$i++) {
        $current_attr = $APTFFbyTAP_photos[$i]->attributes();
        $APTFFbyTAP_linkurl[$i] = 'http://www.flickr.com/photos/'.(string)($current_attr['owner']?$current_attr['owner']:$flickr_uid).'/'.(string)$current_attr['id'].'/';
        $APTFFbyTAP_photourl[$i] = (string)$current_attr[$APTFFbyTAP_size_id];
        $APTFFbyTAP_originalurl[$i] = (string)$current_attr['url_m'];
        if( !$APTFFbyTAP_photourl[$i] ){ $APTFFbyTAP_photourl[$i] = $APTFFbyTAP_originalurl[$i]; } // Incase size didn't exist
        $APTFFbyTAP_photocap[$i] = (string)$current_attr['title'];
      }
      if(!empty($APTFFbyTAP_linkurl) && !empty($APTFFbyTAP_photourl)){
        if( 'community' != $flickr_options['flickr_source'] && $flickr_options['flickr_display_link'] && $flickr_options['flickr_display_link_text']) {
          switch ($flickr_options['flickr_source']) {
            case 'user':
              $APTFFbyTAP_link = 'http://www.flickr.com/photos/'.$flickr_uid.'/';
            break;
            case 'favorites':
              $APTFFbyTAP_link = 'http://www.flickr.com/photos/'.$flickr_uid.'/favorites/';
            break;
            case 'group':
              $APTFFbyTAP_link = 'http://www.flickr.com/groups/'.$flickr_groupid.'/';
            break;
            case 'set':
            // NOTE the use of attributes...
            if($APTFFbyTAP_attributes['owner'] && $APTFFbyTAP_attributes['id']){
              $APTFFbyTAP_link = 'http://www.flickr.com/photos/'.$APTFFbyTAP_attributes['owner'].'/sets/'.$APTFFbyTAP_attributes['id'].'/';
            }
            break;
          } 
        
          if($APTFFbyTAP_link){
            $APTFFbyTAP_user_link = '<div class="APTFFbyTAP-display-link" >';
            $APTFFbyTAP_user_link .='<a href="'.$APTFFbyTAP_link.'" target="_blank" >';
            $APTFFbyTAP_user_link .= $flickr_options['flickr_display_link_text'];
            $APTFFbyTAP_user_link .= '</a></div>';
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
    
    $flickr_uid = apply_filters( APTFFbyTAP_HOOK, empty($flickr_options['flickr_user_id']) ? 'uid' : $flickr_options['flickr_user_id'], $flickr_options );
    
    switch ($flickr_options['flickr_source']) {
    case 'user':
      $request = 'http://api.flickr.com/services/rest/?method=flickr.photos.search&api_key=68b8278a33237f1f369cbbf3c9a9f45c&per_page='.$flickr_options['flickr_photo_number'].'&format=php_serial&privacy_filter=1&user_id='. $flickr_uid .'&page=1&extras=description,url_sq,url_t,url_s,url_m,url_n,url_z';
    break;
    case 'favorites':
      $request = 'http://api.flickr.com/services/rest/?method=flickr.favorites.getPublicList&api_key=68b8278a33237f1f369cbbf3c9a9f45c&per_page='.$flickr_options['flickr_photo_number'].'&format=php_serial&privacy_filter=1&user_id='. $flickr_uid .'&page=1&extras=description,url_sq,url_t,url_s,url_m,url_n,url_z';
    break;
    case 'group':
      $flickr_groupid = apply_filters( APTFFbyTAP_HOOK, empty($flickr_options['flickr_group_id']) ? '' : $flickr_options['flickr_group_id'], $flickr_options );
      $request = 'http://api.flickr.com/services/rest/?method=flickr.photos.search&api_key=68b8278a33237f1f369cbbf3c9a9f45c&per_page='.$flickr_options['flickr_photo_number'].'&format=php_serial&privacy_filter=1&group_id='. $flickr_groupid .'&page=1&extras=description,url_sq,url_t,url_s,url_m,url_n,url_z';
    break;
    case 'set':
      $APTFFbyTAP_flickr_set = apply_filters( APTFFbyTAP_HOOK, empty($flickr_options['flickr_set_id']) ? '' : $flickr_options['flickr_set_id'], $flickr_options );
      $request = 'http://api.flickr.com/services/rest/?method=flickr.photosets.getPhotos&api_key=68b8278a33237f1f369cbbf3c9a9f45c&per_page='.$flickr_options['flickr_photo_number'].'&format=php_serial&privacy_filter=1&photoset_id='. $APTFFbyTAP_flickr_set .'&page=1&extras=url_sq,url_t,url_s,url_m,url_o';
    break;
    case 'community':
      $APTFFbyTAP_flickr_tags = apply_filters( APTFFbyTAP_HOOK, empty($flickr_options['flickr_tags']) ? '' : $flickr_options['flickr_tags'], $flickr_options );
      $request = 'http://api.flickr.com/services/rest/?method=flickr.photos.search&api_key=68b8278a33237f1f369cbbf3c9a9f45c&per_page='.$flickr_options['flickr_photo_number'].'&format=php_serial&privacy_filter=1&tags='. $APTFFbyTAP_flickr_tags .'&page=1&extras=description,url_sq,url_t,url_s,url_m,url_n,url_z';
    break;
    } 

    $_flickrurl = @file_get_contents($request);
    $_flickr_php = @unserialize($_flickrurl);

    if(empty($_flickr_php)){
      $hidden .= '<!-- Failed using file_get_contents() and PHP_Serial @ '.$request.' -->';
      $continue = false;
    }else{
    
      $APTFFbyTAP_content =  $_flickr_php['photos'];
      $APTFFbyTAP_photos = $_flickr_php['photos']['photo'];
        
      // Check for photosets  
      if( 'set' == $flickr_options['flickr_source']) {
        $APTFFbyTAP_content =  $_flickr_php['photoset'];
        $APTFFbyTAP_photos = $_flickr_php['photoset']['photo'];
      }
      
      // Check actual number of photos found
      if( count($APTFFbyTAP_photos) < $flickr_options['flickr_photo_number'] ){ $flickr_options['flickr_photo_number']=count($APTFFbyTAP_photos);}

      for ($i=0;$i<$flickr_options['flickr_photo_number'];$i++) {
        $APTFFbyTAP_linkurl[$i] = 'http://www.flickr.com/photos/'.($APTFFbyTAP_photos[$i]['owner']?$APTFFbyTAP_photos[$i]['owner']:$flickr_uid).'/'.$APTFFbyTAP_photos[$i]['id'].'/';
        $APTFFbyTAP_photourl[$i] = $APTFFbyTAP_photos[$i][$APTFFbyTAP_size_id];
        $APTFFbyTAP_originalurl[$i] = $APTFFbyTAP_photos[$i]['url_m'];
        if( !$APTFFbyTAP_photourl[$i] ){ $APTFFbyTAP_photourl[$i] = $APTFFbyTAP_originalurl[$i]; } // Incase size didn't exist
        $APTFFbyTAP_photocap[$i] = $APTFFbyTAP_photos[$i]['title'];
      }
      if(!empty($APTFFbyTAP_linkurl) && !empty($APTFFbyTAP_photourl)){
        if( 'community' != $flickr_options['flickr_source'] && $flickr_options['flickr_display_link'] && $flickr_options['flickr_display_link_text']) {
          switch ($flickr_options['flickr_source']) {
            case 'user':
              $APTFFbyTAP_link = 'http://www.flickr.com/photos/'.$flickr_uid.'/';
            break;
            case 'favorites':
              $APTFFbyTAP_link = 'http://www.flickr.com/photos/'.$flickr_uid.'/favorites/';
            break;
            case 'group':
              $APTFFbyTAP_link = 'http://www.flickr.com/groups/'.$flickr_groupid.'/';
            break;
            case 'set':
            if($APTFFbyTAP_content['owner'] && $APTFFbyTAP_content['id']){
              $APTFFbyTAP_link = 'http://www.flickr.com/photos/'.$APTFFbyTAP_content['owner'].'/sets/'.$APTFFbyTAP_content['id'].'/';
            }
            break;
          } 
        
          if($APTFFbyTAP_link){
            $APTFFbyTAP_user_link = '<div class="APTFFbyTAP-display-link" >';
            $APTFFbyTAP_user_link .='<a href="'.$APTFFbyTAP_link.'" target="_blank" >';
            $APTFFbyTAP_user_link .= $flickr_options['flickr_display_link_text'];
            $APTFFbyTAP_user_link .= '</a></div>';
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
    
  $results = array('continue'=>$continue,'message'=>$message,'hidden'=>$hidden,'user_link'=>$APTFFbyTAP_user_link,'image_captions'=>$APTFFbyTAP_photocap,'image_urls'=>$APTFFbyTAP_photourl,'image_perms'=>$APTFFbyTAP_linkurl,'image_originals'=>$APTFFbyTAP_originalurl);
  
  if( true == $continue && !$disablecache && $cache ){     
    $cache_results = $results;
    if(!is_serialized( $cache_results  )) { $cache_results  = maybe_serialize( $cache_results ); }
    $cache->put($key, $cache_results);
    $cachetime = APTFFbyTAP_get_option( 'cache_time' );
    if( $cachetime && is_numeric($cachetime) ){
      $cache->setExpiryInterval( $cachetime*60*60 );
    }
  }
  
  return $results;
}
?>