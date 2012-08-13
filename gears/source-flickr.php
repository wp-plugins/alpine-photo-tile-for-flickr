<?php
/**
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

function theAlpinePress_flickr_photo_retrieval($id, $flickr_options, $defaults){  
  $PTFFbyTAP_flickr_uid = apply_filters( PTFFbyTAP_HOOK, empty($flickr_options['flickr_user_id']) ? 'uid' : $flickr_options['flickr_user_id'], $flickr_options );
  $PTFFbyTAP_flickr_uid = @ereg_replace('[[:cntrl:]]', '', $PTFFbyTAP_flickr_uid ); // remove ASCII's control characters
  $PTFFbyTAP_flickr_groupid = apply_filters( PTFFbyTAP_HOOK, empty($flickr_options['flickr_group_id']) ? 'groupid' : $flickr_options['flickr_group_id'], $flickr_options );
  $PTFFbyTAP_flickr_groupid = @ereg_replace('[[:cntrl:]]', '', $PTFFbyTAP_flickr_groupid ); // remove ASCII's control characters
  $PTFFbyTAP_flickr_set = apply_filters( PTFFbyTAP_HOOK, empty($flickr_options['flickr_set_id']) ? 'set' : $flickr_options['flickr_set_id'], $flickr_options );
  $PTFFbyTAP_flickr_set = @ereg_replace('[[:cntrl:]]', '', $PTFFbyTAP_flickr_set ); // remove ASCII's control characters
  $PTFFbyTAP_flickr_tags = apply_filters( PTFFbyTAP_HOOK, empty($flickr_options['flickr_tags']) ? 'tags' : $flickr_options['flickr_tags'], $flickr_options );
  $PTFFbyTAP_flickr_tags = @ereg_replace('[[:cntrl:]]', '', $PTFFbyTAP_flickr_tags ); // remove ASCII's control characters

  $key = 'flickr-'.$flickr_options['flickr_source'].'-'.$PTFFbyTAP_flickr_uid.'-'.$PTFFbyTAP_flickr_groupid.'-'.$PTFFbyTAP_flickr_set.'-'.$PTFFbyTAP_flickr_tags.'-'.$flickr_options['flickr_photo_number'].'-'.$flickr_options['flickr_photo_size'];

  $cache = new theAlpinePressSimpleCacheV1();  
  $cache->setCacheDir( PTFFbyTAP_CACHE );
  
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
  $PTFFbyTAP_linkurl = array();
  $PTFFbyTAP_photocap = array();
  $PTFFbyTAP_photourl = array();
          
  // Determine image size id
  $PTFFbyTAP_size_id = '.'; // Default is 500
  switch ($flickr_options['flickr_photo_size']) {
    case 75:
      $PTFFbyTAP_size_id = '_s.';
    break;
    case 100:
      $PTFFbyTAP_size_id = '_t.';
    break;
    case 240:
      $PTFFbyTAP_size_id = '_m.';
    break;
    case 500:
      $PTFFbyTAP_size_id = '.';
    break;
    case 640:
      $PTFFbyTAP_size_id = '_z.';
    break;
  }  
  
  // Retrieve content using curl_init and PHP_serial
 if ( curl_init() ) {
    // @ is shut-up operator
    // For reference: http://www.flickr.com/services/feeds/
    $flickr_uid = apply_filters( PTFFbyTAP_HOOK, empty($flickr_options['flickr_user_id']) ? '' : $flickr_options['flickr_user_id'], $flickr_options );
    
    switch ($flickr_options['flickr_source']) {
    case 'user':
      $request = 'http://api.flickr.com/services/feeds/photos_public.gne?id='. $flickr_uid .'&lang=en-us&format=php_serial';
    break;
    case 'favorites':
      $request = 'http://api.flickr.com/services/feeds/photos_faves.gne?nsid='. $flickr_uid .'&lang=en-us&format=php_serial';
    break;
    case 'group':
      $flickr_groupid = apply_filters( PTFFbyTAP_HOOK, empty($flickr_options['flickr_group_id']) ? '' : $flickr_options['flickr_group_id'], $flickr_options );
      $request = 'http://api.flickr.com/services/feeds/groups_pool.gne?id='. $flickr_groupid .'&lang=en-us&format=php_serial';
    break;
    case 'set':
      $PTFFbyTAP_flickr_set = apply_filters( PTFFbyTAP_HOOK, empty($flickr_options['flickr_set_id']) ? '' : $flickr_options['flickr_set_id'], $flickr_options );
      $request = 'http://api.flickr.com/services/feeds/photoset.gne?set=' . $PTFFbyTAP_flickr_set . '&nsid='. $flickr_uid .'&lang=en-us&format=php_serial';
    break;
    case 'community':
      $PTFFbyTAP_flickr_tags = apply_filters( PTFFbyTAP_HOOK, empty($flickr_options['flickr_tags']) ? '' : $flickr_options['flickr_tags'], $flickr_options );
      $request = 'http://api.flickr.com/services/feeds/photos_public.gne?tags='. $PTFFbyTAP_flickr_tags .'&lang=en-us&format=php_serial';
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
      
      $PTFFbyTAP_title = $_flickr_php['title'];
      $PTFFbyTAP_link = $_flickr_php['url'];
      $PTFFbyTAP_content =  $_flickr_php['items'];

      for ($i=0;$i<$flickr_options['flickr_photo_number'];$i++) {
        if($PTFFbyTAP_content[$i]['url']){ // Check if anything is there
          $PTFFbyTAP_linkurl[$i] = $PTFFbyTAP_content[$i]['url'];
          $PTFFbyTAP_photocap[$i] = $PTFFbyTAP_content[$i]['title']; // retrieve image title
           // retrieve image url from feed and set new image size
          $PTFFbyTAP_photourl[$i] = @str_replace('_m.', $PTFFbyTAP_size_id, $PTFFbyTAP_content[$i]['m_url'] );
          $PTFFbyTAP_originalurl[$i] = @str_replace('_m.', '.', $PTFFbyTAP_content[$i]['m_url'] );
        }
      }
      if(!empty($PTFFbyTAP_linkurl) && !empty($PTFFbyTAP_photourl)){
        if( $flickr_options['flickr_display_link'] ) {
          $PTFFbyTAP_user_link = '<div class="PTFFbyTAP-display-link" >';
          $PTFFbyTAP_user_link .='<a href="'.$PTFFbyTAP_link.'" target="_blank" >';
          $PTFFbyTAP_user_link .= $PTFFbyTAP_title;
          $PTFFbyTAP_user_link .= '</a></div>';
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
    $flickr_uid = apply_filters( PTFFbyTAP_HOOK, empty($flickr_options['flickr_user_id']) ? '' : $flickr_options['flickr_user_id'], $flickr_options );
    switch ($flickr_options['flickr_source']) {
    case 'user':
      $request = 'http://api.flickr.com/services/feeds/photos_public.gne?id='. $flickr_uid  .'&lang=en-us&format=rss_200';
    break;
    case 'favorites':
      $request = 'http://api.flickr.com/services/feeds/photos_faves.gne?nsid='. $flickr_uid  .'&lang=en-us&format=rss_200';
    break;
    case 'group':
      $flickr_groupid = apply_filters( PTFFbyTAP_HOOK, empty($flickr_options['flickr_group_id']) ? '' : $flickr_options['flickr_group_id'], $flickr_options );
      $request = 'http://api.flickr.com/services/feeds/groups_pool.gne?id='. $flickr_groupid  .'&lang=en-us&format=rss_200';
    break;
    case 'set':
      $PTFFbyTAP_flickr_set = apply_filters( PTFFbyTAP_HOOK, empty($flickr_options['flickr_set_id']) ? '' : $flickr_options['flickr_set_id'], $flickr_options );
      $request = 'http://api.flickr.com/services/feeds/photoset.gne?set=' . $PTFFbyTAP_flickr_set . '&nsid='. $flickr_uid  .'&lang=en-us&format=rss_200';
    break;
    case 'community':
      $PTFFbyTAP_flickr_tags = apply_filters( PTFFbyTAP_HOOK, empty($flickr_options['flickr_tags']) ? '' : $flickr_options['flickr_tags'], $flickr_options );
      $request = 'http://api.flickr.com/services/feeds/photos_public.gne?tags='. $PTFFbyTAP_flickr_tags .'&lang=en-us&format=rss_200';
    break;
    } 

    $_flickrurl  = @urlencode( $request );	// just for compatibility
    $_flickr_xml = @simplexml_load_file( $_flickrurl,"SimpleXMLElement",LIBXML_NOCDATA); // @ is shut-up operator
    if($_flickr_xml===false){ 
      $hidden .= '<!-- Failed using XML @ '.$request.' -->';
      $continue = false;
    }else{
      $PTFFbyTAP_title = $_flickr_xml->channel->title;
      $PTFFbyTAP_link = $_flickr_xml->channel->link;
      
      if(!$_flickr_xml && !$_flickr_xml->channel){
        $hidden .= '<!-- No photos found using XML @ '.$request.' -->';
        $continue = false;
      }else{
        $s = 0; // simple counter
        foreach( $_flickr_xml->channel->item as $p ) { // This will prevent empty images from being added to PTFFbyTAP_linkurl.
          if( $s<$flickr_options['flickr_photo_number'] ){
            // list of link urls
            $PTFFbyTAP_linkurl[$s] = (string) $p->link; // ->i is equivalent of ['i'] for objects
            if($PTFFbyTAP_linkurl[$s]){
              // For Reference: regex references and http://php.net/manual/en/function.preg-match.php
              // Using the RSS feed will require some manipulation to get the image url from flickr;
              // preg_replace is bad at skipping lines so we'll start with preg_match
                // i sets letters in upper or lower case,
              @preg_match( "/<img(.+)\/>/i", $p->description, $matches ); // First, get image from feed.
              // Next, strip away everything surrounding the source url.
                // . means any expression, and + means repeat previous
              $PTFFbyTAP_photourl_current = @preg_replace(array('/(.+)src="/i','/"(.+)/') , '',$matches[ 0 ]);
              // Finally, change the size. [] specifies single character and \w is any word character
              $PTFFbyTAP_photourl[$s] = @preg_replace('/[_]\w[.]/', $PTFFbyTAP_size_id, $PTFFbyTAP_photourl_current );
              $PTFFbyTAP_originalurl[$s] = @preg_replace('/[_]\w[.]/', '.', $PTFFbyTAP_photourl_current );
              $PTFFbyTAP_photocap[$s] = (string) $p->title;
            }
            $s++;
          }
          else{
            break;
          }
        }
        if(!empty($PTFFbyTAP_linkurl) && !empty($PTFFbyTAP_photourl)){
          if( $flickr_options['flickr_display_link'] ) {
            $PTFFbyTAP_user_link = '<div class="PTFFbyTAP-display-link" >';
            $PTFFbyTAP_user_link .='<a href="'.$PTFFbyTAP_link.'" target="_blank" >';
            $PTFFbyTAP_user_link .= $PTFFbyTAP_title;
            $PTFFbyTAP_user_link .= '</a></div>';
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
    
    if(!function_exists(PTFFbyTAP_specialarraysearch)){
      function PTFFbyTAP_specialarraysearch($array, $find){
        foreach ($array as $key=>$value){
          if( is_string($key) && $key==$find){
            return $value;
          }
          elseif(is_array($value)){
            $results = PTFFbyTAP_specialarraysearch($value, $find);
          }
          elseif(is_object($value)){
            $sub = $array->$key;
            $results = PTFFbyTAP_specialarraysearch($sub, $find);
          }
          // If found, return
          if(!empty($results)){return $results;}
        }
        return $results;
      }
    }
    
    $flickr_uid = apply_filters( PTFFbyTAP_HOOK, empty($flickr_options['flickr_user_id']) ? '' : $flickr_options['flickr_user_id'], $flickr_options );
    switch ($flickr_options['flickr_source']) {
    case 'user':
      $request = 'http://api.flickr.com/services/feeds/photos_public.gne?id='. $flickr_uid  .'&lang=en-us&format=rss_200';
    break;
    case 'favorites':
      $request = 'http://api.flickr.com/services/feeds/photos_faves.gne?nsid='. $flickr_uid  .'&lang=en-us&format=rss_200';
    break;
    case 'group':
      $flickr_groupid = apply_filters( PTFFbyTAP_HOOK, empty($flickr_options['flickr_group_id']) ? '' : $flickr_options['flickr_group_id'], $flickr_options );
      $request = 'http://api.flickr.com/services/feeds/groups_pool.gne?id='. $flickr_groupid  .'&lang=en-us&format=rss_200';
    break;
    case 'set':
      $PTFFbyTAP_flickr_set = apply_filters( PTFFbyTAP_HOOK, empty($flickr_options['flickr_set_id']) ? '' : $flickr_options['flickr_set_id'], $flickr_options );
      $request = 'http://api.flickr.com/services/feeds/photoset.gne?set=' . $PTFFbyTAP_flickr_set . '&nsid='. $flickr_uid  .'&lang=en-us&format=rss_200';
    break;
    case 'community':
      $PTFFbyTAP_flickr_tags = apply_filters( PTFFbyTAP_HOOK, empty($flickr_options['flickr_tags']) ? '' : $flickr_options['flickr_tags'], $flickr_options );
      $request = 'http://api.flickr.com/services/feeds/photos_public.gne?tags='. $PTFFbyTAP_flickr_tags .'&lang=en-us&format=rss_200';
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
      $PTFFbyTAP_title = @PTFFbyTAP_specialarraysearch($rss,'title');
      $PTFFbyTAP_title = $PTFFbyTAP_title['0']['data'];
      $PTFFbyTAP_link = @PTFFbyTAP_specialarraysearch($rss,'link');
      $PTFFbyTAP_link = $PTFFbyTAP_link['0']['data'];
      $rss_data = @PTFFbyTAP_specialarraysearch($rss,'item');

      $s = 0; // simple counter
      if ($rss_data != NULL ){ // Check again
        foreach ( $rss_data as $item ) {
          if( $s<$flickr_options['flickr_photo_number'] ){
            $PTFFbyTAP_linkurl[$s] = $item['child']['']['link']['0']['data'];    
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
                $PTFFbyTAP_photourl_current = @preg_replace(array('/(.+)src="/i','/"(.+)/') , '',$matches[ 0 ]);
                // Finally, change the size. 
                  // [] specifies single character and \w is any word character
                $PTFFbyTAP_photourl[$s] = @preg_replace('/[_]\w[.]/', $PTFFbyTAP_size_id, $PTFFbyTAP_photourl_current );
                $PTFFbyTAP_originalurl[$s] = @preg_replace('/[_]\w[.]/', '.', $PTFFbyTAP_photourl_current );
                // Could set the caption as blank instead of default "Photo", but currently not doing so.
                $PTFFbyTAP_photocap[$s] = $item['child']['']['title']['0']['data'];
                $s++;
              }
            }
          }
          else{
            break;
          }
        }
      }
      if(!empty($PTFFbyTAP_linkurl) && !empty($PTFFbyTAP_photourl)){
        if( $flickr_options['flickr_display_link'] ) {
          $PTFFbyTAP_user_link = '<div class="PTFFbyTAP-display-link" >';
          $PTFFbyTAP_user_link .='<a href="'.$PTFFbyTAP_link.'" target="_blank" >';
          $PTFFbyTAP_user_link .= $PTFFbyTAP_title;
          $PTFFbyTAP_user_link .= '</a></div>';
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
    
  $results = array('continue'=>$continue,'message'=>$message,'hidden'=>$hidden,'user_link'=>$PTFFbyTAP_user_link,'image_captions'=>$PTFFbyTAP_photocap,'image_urls'=>$PTFFbyTAP_photourl,'image_perms'=>$PTFFbyTAP_linkurl,'image_originals'=>$PTFFbyTAP_originalurl);
  
  if( true == $continue ){     
    $cache_results = $results;
    if(!is_serialized( $cache_results  )) { $cache_results  = maybe_serialize( $cache_results ); }
    $cache->put($key, $cache_results);
  }
  
  return $results;
}
?>