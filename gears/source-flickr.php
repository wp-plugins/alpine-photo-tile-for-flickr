<?php
/**
 * Alpine PhotoTile for Flickr: Photo Retrieval Function
 * The PHP for retrieving content from Flickr.
 *
 * @since 1.0.0
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

  $key = 'flickr-'.$flickr_options['flickr_source'].'-'.$APTFFbyTAP_flickr_uid.'-'.$APTFFbyTAP_flickr_groupid.'-'.$APTFFbyTAP_flickr_set.'-'.$APTFFbyTAP_flickr_tags.'-'.$flickr_options['flickr_photo_number'].'-'.$flickr_options['flickr_photo_size'];

  $cache = new theAlpinePressSimpleCacheV1();  
  $cache->setCacheDir( APTFFbyTAP_CACHE );
  
  if( $cache->exists($key) ) {
    $results = $cache->get($key);
    $results = @unserialize($results);
    if( count($results) ){
      $results['hidden'] .= '<!-- Retrieved from cache -->';
      return $results;
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
      $APTFFbyTAP_size_id = '_s.';
    break;
    case 100:
      $APTFFbyTAP_size_id = '_t.';
    break;
    case 240:
      $APTFFbyTAP_size_id = '_m.';
    break;
    case 500:
      $APTFFbyTAP_size_id = '.';
    break;
    case 640:
      $APTFFbyTAP_size_id = '_z.';
    break;
  }  
  
  // Retrieve content using curl_init and PHP_serial
 if ( curl_init() ) {
    // @ is shut-up operator
    // For reference: http://www.flickr.com/services/feeds/
    $flickr_uid = apply_filters( APTFFbyTAP_HOOK, empty($flickr_options['flickr_user_id']) ? '' : $flickr_options['flickr_user_id'], $flickr_options );
    
    switch ($flickr_options['flickr_source']) {
    case 'user':
      $request = 'http://api.flickr.com/services/feeds/photos_public.gne?id='. $flickr_uid .'&lang=en-us&format=php_serial';
    break;
    case 'favorites':
      $request = 'http://api.flickr.com/services/feeds/photos_faves.gne?nsid='. $flickr_uid .'&lang=en-us&format=php_serial';
    break;
    case 'group':
      $flickr_groupid = apply_filters( APTFFbyTAP_HOOK, empty($flickr_options['flickr_group_id']) ? '' : $flickr_options['flickr_group_id'], $flickr_options );
      $request = 'http://api.flickr.com/services/feeds/groups_pool.gne?id='. $flickr_groupid .'&lang=en-us&format=php_serial';
    break;
    case 'set':
      $APTFFbyTAP_flickr_set = apply_filters( APTFFbyTAP_HOOK, empty($flickr_options['flickr_set_id']) ? '' : $flickr_options['flickr_set_id'], $flickr_options );
      $request = 'http://api.flickr.com/services/feeds/photoset.gne?set=' . $APTFFbyTAP_flickr_set . '&nsid='. $flickr_uid .'&lang=en-us&format=php_serial';
    break;
    case 'community':
      $APTFFbyTAP_flickr_tags = apply_filters( APTFFbyTAP_HOOK, empty($flickr_options['flickr_tags']) ? '' : $flickr_options['flickr_tags'], $flickr_options );
      $request = 'http://api.flickr.com/services/feeds/photos_public.gne?tags='. $APTFFbyTAP_flickr_tags .'&lang=en-us&format=php_serial';
    break;
    } 

    $ci = @curl_init();
    @curl_setopt($ci, CURLOPT_URL, $request);
    @curl_setopt($ci, CURLOPT_RETURNTRANSFER, 1);
    $_flickrurl = @curl_exec($ci);
    @curl_close($ci);
    
    $_flickr_php = @unserialize($_flickrurl);

    if(empty($_flickr_php)){
      $hidden .= '<!-- Failed using PHP_Serial @ '.$request.' -->';
      $continue = false;
    }else{
      
      $APTFFbyTAP_title = $_flickr_php['title'];
      $APTFFbyTAP_link = $_flickr_php['url'];
      $APTFFbyTAP_content =  $_flickr_php['items'];

      for ($i=0;$i<$flickr_options['flickr_photo_number'];$i++) {
        if($APTFFbyTAP_content[$i]['url']){ // Check if anything is there
          $APTFFbyTAP_linkurl[$i] = $APTFFbyTAP_content[$i]['url'];
          $APTFFbyTAP_photocap[$i] = $APTFFbyTAP_content[$i]['title']; // retrieve image title
           // retrieve image url from feed and set new image size
          $APTFFbyTAP_photourl[$i] = @str_replace('_m.', $APTFFbyTAP_size_id, $APTFFbyTAP_content[$i]['m_url'] );
          $APTFFbyTAP_originalurl[$i] = @str_replace('_m.', '.', $APTFFbyTAP_content[$i]['m_url'] );
        }
      }
      if(!empty($APTFFbyTAP_linkurl) && !empty($APTFFbyTAP_photourl)){
        if( $flickr_options['flickr_display_link'] ) {
          $APTFFbyTAP_user_link = '<div class="APTFFbyTAP-display-link" >';
          $APTFFbyTAP_user_link .='<a href="'.$APTFFbyTAP_link.'" target="_blank" >';
          $APTFFbyTAP_user_link .= $APTFFbyTAP_title;
          $APTFFbyTAP_user_link .= '</a></div>';
        }
        // If content successfully fetched, generate output...
        $continue = true;
        $hidden  .= '<!-- Success using PHP_Serial -->';
      }else{
        $hidden .= '<!-- No photos found using PHP_Serial @ '.$request.' -->';  
        $continue = false;
        $feed_found = true;
      }
    }
  }
  ///////////////////////////////////////////////////
  /// If nothing found, try using xml and rss_200 ///
  ///////////////////////////////////////////////////

  if ( $continue == false && function_exists('simplexml_load_file') ) {
    $flickr_uid = apply_filters( APTFFbyTAP_HOOK, empty($flickr_options['flickr_user_id']) ? '' : $flickr_options['flickr_user_id'], $flickr_options );
    switch ($flickr_options['flickr_source']) {
    case 'user':
      $request = 'http://api.flickr.com/services/feeds/photos_public.gne?id='. $flickr_uid  .'&lang=en-us&format=rss_200';
    break;
    case 'favorites':
      $request = 'http://api.flickr.com/services/feeds/photos_faves.gne?nsid='. $flickr_uid  .'&lang=en-us&format=rss_200';
    break;
    case 'group':
      $flickr_groupid = apply_filters( APTFFbyTAP_HOOK, empty($flickr_options['flickr_group_id']) ? '' : $flickr_options['flickr_group_id'], $flickr_options );
      $request = 'http://api.flickr.com/services/feeds/groups_pool.gne?id='. $flickr_groupid  .'&lang=en-us&format=rss_200';
    break;
    case 'set':
      $APTFFbyTAP_flickr_set = apply_filters( APTFFbyTAP_HOOK, empty($flickr_options['flickr_set_id']) ? '' : $flickr_options['flickr_set_id'], $flickr_options );
      $request = 'http://api.flickr.com/services/feeds/photoset.gne?set=' . $APTFFbyTAP_flickr_set . '&nsid='. $flickr_uid  .'&lang=en-us&format=rss_200';
    break;
    case 'community':
      $APTFFbyTAP_flickr_tags = apply_filters( APTFFbyTAP_HOOK, empty($flickr_options['flickr_tags']) ? '' : $flickr_options['flickr_tags'], $flickr_options );
      $request = 'http://api.flickr.com/services/feeds/photos_public.gne?tags='. $APTFFbyTAP_flickr_tags .'&lang=en-us&format=rss_200';
    break;
    } 

    $_flickrurl  = @urlencode( $request );	// just for compatibility
    $_flickr_xml = @simplexml_load_file( $_flickrurl,"SimpleXMLElement",LIBXML_NOCDATA); // @ is shut-up operator
    if($_flickr_xml===false){ 
      $hidden .= '<!-- Failed using XML @ '.$request.' -->';
      $continue = false;
    }else{
      $APTFFbyTAP_title = $_flickr_xml->channel->title;
      $APTFFbyTAP_link = $_flickr_xml->channel->link;
      
      if(!$_flickr_xml && !$_flickr_xml->channel){
        $hidden .= '<!-- No photos found using XML @ '.$request.' -->';
        $continue = false;
      }else{
        $s = 0; // simple counter
        foreach( $_flickr_xml->channel->item as $p ) { // This will prevent empty images from being added to APTFFbyTAP_linkurl.
          if( $s<$flickr_options['flickr_photo_number'] ){
            // list of link urls
            $APTFFbyTAP_linkurl[$s] = (string) $p->link; // ->i is equivalent of ['i'] for objects
            if($APTFFbyTAP_linkurl[$s]){
              // For Reference: regex references and http://php.net/manual/en/function.preg-match.php
              // Using the RSS feed will require some manipulation to get the image url from flickr;
              // preg_replace is bad at skipping lines so we'll start with preg_match
                // i sets letters in upper or lower case,
              @preg_match( "/<img(.+)\/>/i", $p->description, $matches ); // First, get image from feed.
              // Next, strip away everything surrounding the source url.
                // . means any expression, and + means repeat previous
              $APTFFbyTAP_photourl_current = @preg_replace(array('/(.+)src="/i','/"(.+)/') , '',$matches[ 0 ]);
              // Finally, change the size. [] specifies single character and \w is any word character
              $APTFFbyTAP_photourl[$s] = @preg_replace('/[_]\w[.]/', $APTFFbyTAP_size_id, $APTFFbyTAP_photourl_current );
              $APTFFbyTAP_originalurl[$s] = @preg_replace('/[_]\w[.]/', '.', $APTFFbyTAP_photourl_current );
              $APTFFbyTAP_photocap[$s] = (string) $p->title;
            }
            $s++;
          }
          else{
            break;
          }
        }
        if(!empty($APTFFbyTAP_linkurl) && !empty($APTFFbyTAP_photourl)){
          if( $flickr_options['flickr_display_link'] ) {
            $APTFFbyTAP_user_link = '<div class="APTFFbyTAP-display-link" >';
            $APTFFbyTAP_user_link .='<a href="'.$APTFFbyTAP_link.'" target="_blank" >';
            $APTFFbyTAP_user_link .= $APTFFbyTAP_title;
            $APTFFbyTAP_user_link .= '</a></div>';
          }
          // If content successfully fetched, generate output...
          $continue = true;
          $hidden .= '<!-- Success using XML -->';
        }else{
          $hidden .= '<!-- No photos found using XML @ '.$request.' -->';
          $continue = false;
          $feed_found = true;
        }
      }
    }
  }
  
  ////////////////////////////////////////////////////////
  ////      If still nothing found, try using RSS      ///
  ////////////////////////////////////////////////////////
  if( $continue == false ) {
    // RSS may actually be safest approach since it does not require PHP server extensions,
    // but I had to build my own method for parsing SimplePie Object so I will keep it as the last option.
    
    if(!function_exists(APTFFbyTAP_specialarraysearch)){
      function APTFFbyTAP_specialarraysearch($array, $find){
        foreach ($array as $key=>$value){
          if( is_string($key) && $key==$find){
            return $value;
          }
          elseif(is_array($value)){
            $results = APTFFbyTAP_specialarraysearch($value, $find);
          }
          elseif(is_object($value)){
            $sub = $array->$key;
            $results = APTFFbyTAP_specialarraysearch($sub, $find);
          }
          // If found, return
          if(!empty($results)){return $results;}
        }
        return $results;
      }
    }
    
    $flickr_uid = apply_filters( APTFFbyTAP_HOOK, empty($flickr_options['flickr_user_id']) ? '' : $flickr_options['flickr_user_id'], $flickr_options );
    switch ($flickr_options['flickr_source']) {
    case 'user':
      $request = 'http://api.flickr.com/services/feeds/photos_public.gne?id='. $flickr_uid  .'&lang=en-us&format=rss_200';
    break;
    case 'favorites':
      $request = 'http://api.flickr.com/services/feeds/photos_faves.gne?nsid='. $flickr_uid  .'&lang=en-us&format=rss_200';
    break;
    case 'group':
      $flickr_groupid = apply_filters( APTFFbyTAP_HOOK, empty($flickr_options['flickr_group_id']) ? '' : $flickr_options['flickr_group_id'], $flickr_options );
      $request = 'http://api.flickr.com/services/feeds/groups_pool.gne?id='. $flickr_groupid  .'&lang=en-us&format=rss_200';
    break;
    case 'set':
      $APTFFbyTAP_flickr_set = apply_filters( APTFFbyTAP_HOOK, empty($flickr_options['flickr_set_id']) ? '' : $flickr_options['flickr_set_id'], $flickr_options );
      $request = 'http://api.flickr.com/services/feeds/photoset.gne?set=' . $APTFFbyTAP_flickr_set . '&nsid='. $flickr_uid  .'&lang=en-us&format=rss_200';
    break;
    case 'community':
      $APTFFbyTAP_flickr_tags = apply_filters( APTFFbyTAP_HOOK, empty($flickr_options['flickr_tags']) ? '' : $flickr_options['flickr_tags'], $flickr_options );
      $request = 'http://api.flickr.com/services/feeds/photos_public.gne?tags='. $APTFFbyTAP_flickr_tags .'&lang=en-us&format=rss_200';
    break;
    } 
    include_once(ABSPATH . WPINC . '/feed.php');
    
    function return_noCache( $seconds ){
      // change the default feed cache recreation period to 30 seconds
      return 30;
    }

    add_filter( 'wp_feed_cache_transient_lifetime' , 'return_noCache' );
    $rss = @fetch_feed( $request );
    remove_filter( 'wp_feed_cache_transient_lifetime' , 'return_noCache' );

    if (!is_wp_error( $rss ) && $rss != NULL ){ // Check that the object is created correctly 
      // Bulldoze through the feed to find the items 
      $results = array();
      $APTFFbyTAP_title = @APTFFbyTAP_specialarraysearch($rss,'title');
      $APTFFbyTAP_title = $APTFFbyTAP_title['0']['data'];
      $APTFFbyTAP_link = @APTFFbyTAP_specialarraysearch($rss,'link');
      $APTFFbyTAP_link = $APTFFbyTAP_link['0']['data'];
      $rss_data = @APTFFbyTAP_specialarraysearch($rss,'item');

      $s = 0; // simple counter
      if ($rss_data != NULL ){ // Check again
        foreach ( $rss_data as $item ) {
          if( $s<$flickr_options['flickr_photo_number'] ){
            $APTFFbyTAP_linkurl[$s] = $item['child']['']['link']['0']['data'];    
            $content = $item['child']['']['description']['0']['data'];     
            if($content){
              // For Reference: regex references and http://php.net/manual/en/function.preg-match.php
              // Using the RSS feed will require some manipulation to get the image url from flickr;
              // preg_replace is bad at skipping lines so we'll start with preg_match
              // i sets letters in upper or lower case, s sets . to anything
              @preg_match("/<IMG.+?SRC=[\"']([^\"']+)/si",$content,$matches); // First, get image from feed.
              if($matches[ 0 ]){
                // Next, strip away everything surrounding the source url.
                // . means any expression and + means repeat previous
                $APTFFbyTAP_photourl_current = @preg_replace(array('/(.+)src="/i','/"(.+)/') , '',$matches[ 0 ]);
                // Finally, change the size. 
                  // [] specifies single character and \w is any word character
                $APTFFbyTAP_photourl[$s] = @preg_replace('/[_]\w[.]/', $APTFFbyTAP_size_id, $APTFFbyTAP_photourl_current );
                $APTFFbyTAP_originalurl[$s] = @preg_replace('/[_]\w[.]/', '.', $APTFFbyTAP_photourl_current );
                // Could set the caption as blank instead of default "Photo", but currently not doing so.
                $APTFFbyTAP_photocap[$s] = $item['child']['']['title']['0']['data'];
                $s++;
              }
            }
          }
          else{
            break;
          }
        }
      }
      if(!empty($APTFFbyTAP_linkurl) && !empty($APTFFbyTAP_photourl)){
        if( $flickr_options['flickr_display_link'] ) {
          $APTFFbyTAP_user_link = '<div class="APTFFbyTAP-display-link" >';
          $APTFFbyTAP_user_link .='<a href="'.$APTFFbyTAP_link.'" target="_blank" >';
          $APTFFbyTAP_user_link .= $APTFFbyTAP_title;
          $APTFFbyTAP_user_link .= '</a></div>';
        }
        // If content successfully fetched, generate output...
        $continue = true;
        $hidden .= '<!-- Success using RSS -->';
      }else{
        $hidden .= '<!-- No photos found using RSS @ '.$request.' -->';  
        $continue = false;
        $feed_found = true;
      }
    }
    else{
      $hidden .= '<!-- Failed RSS @ '.$request.' -->';
      $continue = false;
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
  
  if( true == $continue ){     
    $cache_results = $results;
    if(!is_serialized( $cache_results  )) { $cache_results  = maybe_serialize( $cache_results ); }
    $cache->put($key, $cache_results);
  }
  
  return $results;
}
?>