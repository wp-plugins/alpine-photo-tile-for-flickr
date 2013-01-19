<?php


class PhotoTileForFlickrBot extends PhotoTileForFlickrBasic{
 
/**
 *  Create constants for storing info 
 *  
 *  @ Since 1.2.2
 */
   public $out = "";
   public $options;
   public $wid; // Widget id
   public $results;
   public $shadow;
   public $border;
   public $curves;
   public $highlight;
   public $rel;
   
  // For Reference:
  // http://www.flickr.com/services/api/response.json.html
  // sq = thumbnail 75x75
  // t = 100 on longest side
  // s = 240 on longest side
  // n = 320 on longest side
  // m = 500 on longest side
  // z = 640 on longest side
  // c = 800 on longest side
  // b = 1024 on longest side*
  // o = original image, either a jpg, gif or png, depending on source format**
  // *Before May 25th 2010 large photos only exist for very large original images.
  // **Original photos behave a little differently. They have their own secret (called originalsecret in responses) and a variable file extension (called originalformat in responses). These values are returned via the API only when the caller has permission to view the original size (based on a user preference and various other criteria). The values are returned by the flickr.photos.getInfo method and by any method that returns a list of photos and allows an extras parameter (with a value of original_format), such as flickr.photos.search. The flickr.photos.getSizes method, as always, will return the full original URL where permissions allow.

/**
 *  Function getting image url given size setting
 *  
 *  @ Since 1.2.2
 */
  function get_image_url($info,$size){
    if( isset($info[$size]) ){
      return $info[$size];
    }elseif( 'url_c' == $size && isset($info['url_o']) ){ // Checking url_o is same as src==set
      return $info['url_o'];
    }elseif( 'url_c' == $size && isset($info['url_z']) ){
      return $info['url_z'];
    }elseif( isset($info['url_m']) ){
      return $info['url_m'];
    }elseif( isset($info['url_n']) ){
      return $info['url_n'];
    }
    return false;
  }
  
/**
 *  Function getting original image url given size setting
 *  
 *  @ Since 1.2.2
 */
  function get_image_orig($info,$size){
    if( 'url_c' == $size && isset($info['url_c']) ){
      return $info['url_c'];
    }elseif( 'url_c' == $size && isset($info['url_o']) ){ // Checking url_o is same as src==set
      return $info['url_o'];
    }elseif( ('url_c' == $size || 'url_z' == $size) && isset($info['url_z']) ){
      return $info['url_z'];
    }elseif( isset($info['url_z']) ){
      return $info['url_z'];
    }elseif( isset($info['url_m']) ){
      return $info['url_m'];
    }
    return false;
  }
  
/**
 *  Function for creating cache key
 *  
 *  @ Since 1.2.2
 */
   function key_maker( $array ){
    if( isset($array['name']) && is_array( $array['info'] ) ){
      $return = $array['name'];
      foreach( $array['info'] as $key=>$val ){
        $return = $return."-".($val?$val:$key);
      }
      $return = @ereg_replace('[[:cntrl:]]', '', $return ); // remove ASCII's control characters
      $bad = array_merge(
        array_map('chr', range(0,31)),
        array("<",">",":",'"',"/","\\","|","?","*"," ",",","\'",".")); 
      $return = str_replace($bad, "", $return); // Remove Windows filename prohibited characters
      return $return;
    }
  }
  
/**
 * Alpine PhotoTile for Flickr: Photo Retrieval Function.
 * The PHP for retrieving content from Flickr.
 *
 * @ Since 1.0.0
 * @ Updated 1.2.2
 */  
  function photo_retrieval(){
    $flickr_options = $this->options;
    $defaults = $this->option_defaults();
    
    $key_input = array(
      'name' => 'flickr',
      'info' => array(
        'vers' => $this->vers,
        'src' => $flickr_options['flickr_source'],
        'uid' => $flickr_options['flickr_user_id'],
        'groupid' => $flickr_options['flickr_group_id'],
        'set' => $flickr_options['flickr_set_id'],
        'tags' => $flickr_options['flickr_tags'],
        'num' => $flickr_options['flickr_photo_number'],
        'link' => $flickr_options['flickr_display_link'],
        'text' => $flickr_options['flickr_display_link_text'],
        'size' => $flickr_options['flickr_photo_size']
        )
      );
    $key = $this->key_maker( $key_input );
    
    $disablecache = $this->get_option( 'cache_disable' );
    if ( !$disablecache ) {
      if( $this->cacheExists($key) ) {
        $results = $this->getCache($key);
        $results = @unserialize($results);
        if( count($results) ){
          $results['hidden'] .= '<!-- Retrieved from cache -->';
          $this->results = $results;
          return;
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
      case 800:
        $size_id = 'url_c';
      break;
    }  
    
    // Retrieve content using wp_remote_get and PHP_serial
   if ( function_exists('unserialize') ) {
      // @ is shut-up operator
      // For reference: http://www.flickr.com/services/feeds/
      $flickr_uid = apply_filters( $this->hook, empty($flickr_options['flickr_user_id']) ? 'uid' : $flickr_options['flickr_user_id'], $flickr_options );
      
      switch ($flickr_options['flickr_source']) {
      case 'user':
        $request = 'http://api.flickr.com/services/rest/?method=flickr.photos.search&api_key=68b8278a33237f1f369cbbf3c9a9f45c&per_page='.$flickr_options['flickr_photo_number'].'&format=php_serial&privacy_filter=1&user_id='. $flickr_uid .'&page=1&extras=description,url_sq,url_t,url_s,url_m,url_n,url_z,url_c';
      break;
      case 'favorites':
        $request = 'http://api.flickr.com/services/rest/?method=flickr.favorites.getPublicList&api_key=68b8278a33237f1f369cbbf3c9a9f45c&per_page='.$flickr_options['flickr_photo_number'].'&format=php_serial&privacy_filter=1&user_id='. $flickr_uid .'&page=1&extras=description,url_sq,url_t,url_s,url_m,url_n,url_z,url_c';
      break;
      case 'group':
        $flickr_groupid = apply_filters( $this->hook, empty($flickr_options['flickr_group_id']) ? '' : $flickr_options['flickr_group_id'], $flickr_options );
        $request = 'http://api.flickr.com/services/rest/?method=flickr.photos.search&api_key=68b8278a33237f1f369cbbf3c9a9f45c&per_page='.$flickr_options['flickr_photo_number'].'&format=php_serial&privacy_filter=1&group_id='. $flickr_groupid .'&page=1&extras=description,url_sq,url_t,url_s,url_m,url_n,url_z,url_c';
      break;
      case 'set':
        $flickr_set = apply_filters( $this->hook, empty($flickr_options['flickr_set_id']) ? '' : $flickr_options['flickr_set_id'], $flickr_options );
        $request = 'http://api.flickr.com/services/rest/?method=flickr.photosets.getPhotos&api_key=68b8278a33237f1f369cbbf3c9a9f45c&per_page='.$flickr_options['flickr_photo_number'].'&format=php_serial&privacy_filter=1&photoset_id='. $flickr_set .'&page=1&extras=url_sq,url_t,url_s,url_m,url_o';
      break;
      case 'community':
        $flickr_tags = apply_filters( $this->hook, empty($flickr_options['flickr_tags']) ? '' : $flickr_options['flickr_tags'], $flickr_options );
        $request = 'http://api.flickr.com/services/rest/?method=flickr.photos.search&api_key=68b8278a33237f1f369cbbf3c9a9f45c&per_page='.$flickr_options['flickr_photo_number'].'&format=php_serial&privacy_filter=1&tags='. $flickr_tags .'&page=1&extras=description,url_sq,url_t,url_s,url_m,url_n,url_z,url_c';
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
          
          $photourl[$i] = $this->get_image_url($photos[$i],$size_id);
          $originalurl[$i] = $this->get_image_orig($photos[$i],$size_id);

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
 
          $photourl[$i] = $this->get_image_url($current_attr,$size_id);
          $originalurl[$i] = $this->get_image_orig($current_attr,$size_id);
          
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
          
          $photourl[$i] = $this->get_image_url($photos[$i],$size_id);
          $originalurl[$i] = $this->get_image_orig($photos[$i],$size_id);
          
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
    $this->results = $results;
  }
  
/**
 *  Get Image Link
 *  
 *  @ Since 1.2.2
 */
  function get_link($i){
    $link = $this->options['flickr_image_link_option'];
    $photocap = $this->results['image_captions'][$i];
    $photourl = $this->results['image_urls'][$i];
    $linkurl = $this->results['image_perms'][$i];
    $url = $this->options['custom_link_url'];
    $originalurl = $this->results['image_originals'][$i];
    
    if( 'original' == $link && !empty($photourl) ){
      $this->out .= '<a href="' . $photourl . '" class="AlpinePhotoTiles-link" target="_blank" title='."'". $photocap ."'".'>';
      return true;
    }elseif( ('flickr' == $link || '1' == $link)&& !empty($linkurl) ){
      $this->out .= '<a href="' . $linkurl . '" class="AlpinePhotoTiles-link" target="_blank" title='."'". $photocap ."'".'>';
      return true;
    }elseif( 'link' == $link && !empty($url) ){
      $this->out .= '<a href="' . $url . '" class="AlpinePhotoTiles-link" target="_blank" title='."'". $photocap ."'".'>'; 
      return true;
    }elseif( 'fancybox' == $link && !empty($originalurl) ){
      $this->out .= '<a href="' . $originalurl . '" class="AlpinePhotoTiles-link AlpinePhotoTiles-lightbox" title='."'". $photocap ."'".'>'; 
      return true;
    }  
    return false;    
  }
  
/**
 *  Update photo number count
 *  
 *  @ Since 1.2.2
 */
  function updateCount(){
    if( $this->options['flickr_photo_number'] != count( $this->results['image_urls'] ) ){
      $this->options['flickr_photo_number'] = count( $this->results['image_urls'] );
    }
  }

/**
 *  Get Parent CSS
 *  
 *  @ Since 1.2.2
 */
  function get_parent_css(){
    $opts = $this->options;
    $return = 'width:100%;max-width:'.$opts['widget_max_width'].'%;padding:0px;';
    if( 'center' == $opts['widget_alignment'] ){                          //  Optional: Set text alignment (left/right) or center
      $return .= 'margin:0px auto;text-align:center;';
    }
    elseif( 'right' == $opts['widget_alignment'] || 'left' == $opts['widget_alignment'] ){                          //  Optional: Set text alignment (left/right) or center
      $return .= 'float:' . $opts['widget_alignment'] . ';text-align:' . $opts['widget_alignment'] . ';';
    }
    else{
      $return .= 'margin:0px auto;text-align:center;';
    }
    return $return;
 }
 
/**
 *  Add Image Function
 *  
 *  @ Since 1.2.2
 *
 ** Possible change: place original image as 'alt' and load image as needed
 */
  function add_image($i,$css=""){
    $this->out .= '<img id="'.$this->wid.'-tile-'.$i.'" class="AlpinePhotoTiles-image '.$this->shadow.' '.$this->border.' '.$this->curves.' '.$this->highlight.'" src="' . $this->results['image_urls'][$i] . '" ';
    $this->out .= 'title='."'". $this->results['image_captions'][$i] ."'".' alt='."'". $this->results['image_captions'][$i] ."' "; // Careful about caps with ""
    $this->out .= 'border="0" hspace="0" vspace="0" style="'.$css.'"/>'; // Override the max-width set by theme
  }
  
/**
 *  Credit Link Function
 *  
 *  @ Since 1.2.2
 */
  function add_credit_link(){
    if( !$this->options['widget_disable_credit_link'] ){
      $by_link  =  '<div id="'.$this->wid.'-by-link" class="AlpinePhotoTiles-by-link"><a href="http://thealpinepress.com/" style="COLOR:#C0C0C0;text-decoration:none;" title="Widget by The Alpine Press">TAP</a></div>';   
      $this->out .=  $by_link;    
    }  
  }
  
/**
 *  User Link Function
 *  
 *  @ Since 1.2.2
 */
  function add_user_link(){
    $userlink = $this->results['user_link'];
    if($userlink){ 
      if($this->options['widget_alignment'] == 'center'){                          //  Optional: Set text alignment (left/right) or center
        $this->out .= '<div id="'.$this->wid.'-display-link" class="AlpinePhotoTiles-display-link-container" ';
        $this->out .= 'style="width:100%;margin:0px auto;">'.$userlink.'</div>';
      }
      else{
        $this->out .= '<div id="'.$this->wid.'-display-link" class="AlpinePhotoTiles-display-link-container" ';
        $this->out .= 'style="float:'.$this->options['widget_alignment'].';max-width:'.$this->options['widget_max_width'].'%;"><center>'.$userlink.'</center></div>'; 
        $this->out .= '<div class="AlpinePhotoTiles_breakline"></div>'; // Only breakline if floating
      }
    }
  }
  
/**
 *  Setup Lightbox Call
 *  
 *  @ Since 1.2.3
 */
  function add_lightbox_call(){
    if( "fancybox" == $this->options['flickr_image_link_option'] ){
      $this->out .= '<script>jQuery(window).load(function() {'.$this->get_lightbox_call().'})</script>';
    }   
  }
  
/**
 *  Get Lightbox Call
 *  
 *  @ Since 1.2.3
 */
  function get_lightbox_call(){
    $this->set_lightbox_rel();
  
    $lightbox = $this->get_option('general_lightbox');
    $lightbox_style = $this->get_option('general_lightbox_params');
    $lightbox_style = str_replace( array("{","}"), "", $lightbox_style);
    $lightbox_style = str_replace( "'", "\'", $lightbox_style);
    
    $setRel = 'jQuery( "#'.$this->wid.'-AlpinePhotoTiles_container a.AlpinePhotoTiles-lightbox" ).attr( "rel", "'.$this->rel.'" );';
    
    if( 'fancybox' == $lightbox ){
      $lightbox_style = ($lightbox_style?$lightbox_style:'titleShow: false, overlayOpacity: .8, overlayColor: "#000"');
      return $setRel.'if(jQuery().fancybox){jQuery( "a[rel^=\''.$this->rel.'\']" ).fancybox( { '.$lightbox_style.' } );}';  
    }elseif( 'prettyphoto' == $lightbox ){
      //theme: 'pp_default', /* light_rounded / dark_rounded / light_square / dark_square / facebook
      $lightbox_style = ($lightbox_style?$lightbox_style:'theme:"facebook",social_tools:false');
      return $setRel.'if(jQuery().prettyPhoto){jQuery( "a[rel^=\''.$this->rel.'\']" ).prettyPhoto({ '.$lightbox_style.' });}';  
    }elseif( 'colorbox' == $lightbox ){
      $lightbox_style = ($lightbox_style?$lightbox_style:'height:"80%"');
      return $setRel.'if(jQuery().colorbox){jQuery( "a[rel^=\''.$this->rel.'\']" ).colorbox( {'.$lightbox_style.'} );}';  
    }elseif( 'alpine-fancybox' == $lightbox ){
      $lightbox_style = ($lightbox_style?$lightbox_style:'titleShow: false, overlayOpacity: .8, overlayColor: "#000"');
      return $setRel.'if(jQuery().fancyboxForAlpine){jQuery( "a[rel^=\''.$this->rel.'\']" ).fancyboxForAlpine( { '.$lightbox_style.' } );}';  
    }
    return "";
  }
  
/**
 *  Set Lightbox "rel"
 *  
 *  @ Since 1.2.3
 */
 function set_lightbox_rel(){
    $lightbox = $this->get_option('general_lightbox');
    $custom = $this->get_option('hidden_lightbox_custom_rel');
    if( $custom && !empty($this->options['custom_lightbox_rel']) ){
      $this->rel = $this->options['custom_lightbox_rel'];
      $this->rel = str_replace('{rtsq}',']',$this->rel); // Decode right and left square brackets
      $this->rel = str_replace('{ltsq}','[',$this->rel);
    }elseif( 'fancybox' == $lightbox ){
      $this->rel = 'alpine-fancybox-'.$this->wid;
    }elseif( 'prettyphoto' == $lightbox ){
      $this->rel = 'alpine-prettyphoto['.$this->wid.']';
    }elseif( 'colorbox' == $lightbox ){
      $this->rel = 'alpine-colorbox['.$this->wid.']';
    }else{
      $this->rel = 'alpine-fancybox-safemode-'.$this->wid;
    }
 }
  
/**
 *  Function for printing vertical style
 *  
 *  @ Since 0.0.1
 *  @ Updated 1.2.2
 */
  function display_vertical(){
    $this->out = ""; // Clear any output;
    $this->updateCount(); // Check number of images found
    $opts = $this->options;
    $this->shadow = ($opts['style_shadow']?'AlpinePhotoTiles-img-shadow':'AlpinePhotoTiles-img-noshadow');
    $this->border = ($opts['style_border']?'AlpinePhotoTiles-img-border':'AlpinePhotoTiles-img-noborder');
    $this->curves = ($opts['style_curve_corners']?'AlpinePhotoTiles-img-corners':'AlpinePhotoTiles-img-nocorners');
    $this->highlight = ($opts['style_highlight']?'AlpinePhotoTiles-img-highlight':'AlpinePhotoTiles-img-nohighlight');
                      
    $this->out .= '<div id="'.$this->wid.'-AlpinePhotoTiles_container" class="AlpinePhotoTiles_container_class">';     
    
      // Align photos
      $css = $this->get_parent_css();
      $this->out .= '<div id="'.$this->wid.'-vertical-parent" class="AlpinePhotoTiles_parent_class" style="'.$css.'">';

        for($i = 0;$i<$opts['flickr_photo_number'];$i++){
          $has_link = $this->get_link($i);  // Add link
          $css = "margin:1px 0 5px 0;padding:0;max-width:100%;";
          $this->add_image($i,$css); // Add image
          if( $has_link ){ $this->out .= '</a>'; } // Close link
        }
        
        $this->add_credit_link();
      
      $this->out .= '</div>'; // Close vertical-parent

      $this->add_user_link();

    $this->out .= '</div>'; // Close container
    $this->out .= '<div class="AlpinePhotoTiles_breakline"></div>';
    
    $highlight = $this->get_option("general_highlight_color");
    $highlight = ($highlight?$highlight:'#64a2d8');

    $this->add_lightbox_call();
    
    if( $opts['style_shadow'] || $opts['style_border'] || $opts['style_highlight']  ){
      $this->out .= '<script>
           jQuery(window).load(function() {
              if(jQuery().AlpineAdjustBordersPlugin ){
                jQuery("#'.$this->wid.'-vertical-parent").AlpineAdjustBordersPlugin({
                  highlight:"'.$highlight.'"
                });
              }  
            });
          </script>';  
    }
  }  
/**
 *  Function for printing cascade style
 *  
 *  @ Since 0.0.1
 *  @ Updated 1.2.2
 */
  function display_cascade(){
    $this->out = ""; // Clear any output;
    $this->updateCount(); // Check number of images found
    $opts = $this->options;
    $this->shadow = ($opts['style_shadow']?'AlpinePhotoTiles-img-shadow':'AlpinePhotoTiles-img-noshadow');
    $this->border = ($opts['style_border']?'AlpinePhotoTiles-img-border':'AlpinePhotoTiles-img-noborder');
    $this->curves = ($opts['style_curve_corners']?'AlpinePhotoTiles-img-corners':'AlpinePhotoTiles-img-nocorners');
    $this->highlight = ($opts['style_highlight']?'AlpinePhotoTiles-img-highlight':'AlpinePhotoTiles-img-nohighlight');
    
    $this->out .= '<div id="'.$this->wid.'-AlpinePhotoTiles_container" class="AlpinePhotoTiles_container_class">';     
    
      // Align photos
      $css = $this->get_parent_css();
      $this->out .= '<div id="'.$this->wid.'-cascade-parent" class="AlpinePhotoTiles_parent_class" style="'.$css.'">';
      
        for($col = 0; $col<$opts['style_column_number'];$col++){
          $this->out .= '<div class="AlpinePhotoTiles_cascade_column" style="width:'.(100/$opts['style_column_number']).'%;float:left;margin:0;">';
          $this->out .= '<div class="AlpinePhotoTiles_cascade_column_inner" style="display:block;margin:0 3px;overflow:hidden;">';
          for($i = $col;$i<$opts['flickr_photo_number'];$i+=$opts['style_column_number']){
            $has_link = $this->get_link($i); // Add link
            $css = "margin:1px 0 5px 0;padding:0;max-width:100%;";
            $this->add_image($i,$css); // Add image
            if( $has_link ){ $this->out .= '</a>'; } // Close link
          }
          $this->out .= '</div></div>';
        }
        $this->out .= '<div class="AlpinePhotoTiles_breakline"></div>';
          
        $this->add_credit_link();
      
      $this->out .= '</div>'; // Close cascade-parent

      $this->out .= '<div class="AlpinePhotoTiles_breakline"></div>';
      
      $this->add_user_link();

    // Close container
    $this->out .= '</div>';
    $this->out .= '<div class="AlpinePhotoTiles_breakline"></div>';
   
    $highlight = $this->get_option("general_highlight_color");
    $highlight = ($highlight?$highlight:'#64a2d8');
    
    $this->add_lightbox_call();
    
    if( $opts['style_shadow'] || $opts['style_border'] || $opts['style_highlight']  ){
      $this->out .= '<script>
           jQuery(window).load(function() {
              if(jQuery().AlpineAdjustBordersPlugin ){
                jQuery("#'.$this->wid.'-cascade-parent").AlpineAdjustBordersPlugin({
                  highlight:"'.$highlight.'"
                });
              }  
            });
          </script>';  
    }
  }

/**
 *  Function for printing and initializing JS styles
 *  
 *  @ Since 0.0.1
 *  @ Updated 1.2.2
 */
  function display_hidden(){
    $this->out = ""; // Clear any output;
    $this->updateCount(); // Check number of images found
    $opts = $this->options;
    $this->shadow = ($opts['style_shadow']?'AlpinePhotoTiles-img-shadow':'AlpinePhotoTiles-img-noshadow');
    $this->border = ($opts['style_border']?'AlpinePhotoTiles-img-border':'AlpinePhotoTiles-img-noborder');
    $this->curves = ($opts['style_curve_corners']?'AlpinePhotoTiles-img-corners':'AlpinePhotoTiles-img-nocorners');
    $this->highlight = ($opts['style_highlight']?'AlpinePhotoTiles-img-highlight':'AlpinePhotoTiles-img-nohighlight');
    
    $this->out .= '<div id="'.$this->wid.'-AlpinePhotoTiles_container" class="AlpinePhotoTiles_container_class">';     
      // Align photos
      $css = $this->get_parent_css();
      $this->out .= '<div id="'.$this->wid.'-hidden-parent" class="AlpinePhotoTiles_parent_class" style="'.$css.'">';
      
        $this->out .= '<div id="'.$this->wid.'-image-list" class="AlpinePhotoTiles_image_list_class" style="display:none;visibility:hidden;">'; 
        
          for($i = 0;$i<$opts['flickr_photo_number'];$i++){
            $has_link = $this->get_link($i); // Add link
            $css = "";
            $this->add_image($i,$css); // Add image
            
            // Load original image size
            if( "gallery" == $opts['style_option'] && !empty( $this->results['image_originals'][$i] ) ){
              $this->out .= '<img class="AlpinePhotoTiles-original-image" src="' . $this->results['image_originals'][$i]. '" />';
            }
            if( $has_link ){ $this->out .= '</a>'; } // Close link
          }
        $this->out .= '</div>';
        
        $this->add_credit_link();       
      
      $this->out .= '</div>'; // Close parent  

      $this->add_user_link();
      
    $this->out .= '</div>'; // Close container
    
    $disable = $this->get_option("general_loader");
    $highlight = $this->get_option("general_highlight_color");
    $highlight = ($highlight?$highlight:'#64a2d8');
    
    $this->out .= '<script>';
      if(!$disable){
        $this->out .= '
               jQuery(document).ready(function() {
                jQuery("#'.$this->wid.'-AlpinePhotoTiles_container").addClass("loading"); 
               });';
      }
    $this->out .= '
           jQuery(window).load(function() {
            jQuery("#'.$this->wid.'-AlpinePhotoTiles_container").removeClass("loading");
            if( jQuery().AlpinePhotoTilesPlugin ){
              jQuery("#'.$this->wid.'-hidden-parent").AlpinePhotoTilesPlugin({
                id:"'.$this->wid.'",
                style:"'.($opts['style_option']?$opts['style_option']:'windows').'",
                shape:"'.($opts['style_shape']?$opts['style_shape']:'square').'",
                perRow:"'.($opts['style_photo_per_row']?$opts['style_photo_per_row']:'3').'",
                imageLink:'.($opts['flickr_image_link']?'1':'0').',
                imageBorder:'.($opts['style_border']?'1':'0').',
                imageShadow:'.($opts['style_shadow']?'1':'0').',
                imageCurve:'.($opts['style_curve_corners']?'1':'0').',
                imageHighlight:'.($opts['style_highlight']?'1':'0').',
                lightbox:'.($opts['flickr_image_link_option'] == "fancybox"?'1':'0').',
                galleryHeight:'.($opts['style_gallery_height']?$opts['style_gallery_height']:'0').', // Keep for Compatibility
                galRatioWidth:'.($opts['style_gallery_ratio_width']?$opts['style_gallery_ratio_width']:'800').',
                galRatioHeight:'.($opts['style_gallery_ratio_height']?$opts['style_gallery_ratio_height']:'600').',
                highlight:"'.$highlight.'",
                pinIt:'.($opts['pinterest_pin_it_button']?'1':'0').',
                siteURL:"'.get_option( 'siteurl' ).'",
                callback: '.($opts['flickr_image_link_option'] == "fancybox"?'function(){'.$this->get_lightbox_call().'}':'""').'
              });
            }
          });
        </script>';
        
  }
 
}

?>
