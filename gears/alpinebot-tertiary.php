<?php
/**
 * AlpineBot Tertiary
 * 
 * Feed fetching and additional back-end functions (mostly related to admin pages)
 * Contains ONLY unique functions
 * 
 */
 
########################## TODO: replace get_option calls with $this->options ################


class PhotoTileForFlickrTertiary extends PhotoTileForFlickrSecondary{  


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

//////////////////////////////////////////////////////////////////////////////////////
//////////////////        Unique Feed Fetch Functions        /////////////////////////
//////////////////////////////////////////////////////////////////////////////////////    

/**
 * Alpine PhotoTile for Flickr: Photo Retrieval Function.
 * The PHP for retrieving content from Flickr.
 *
 * @ Since 1.0.0
 * @ Updated 1.2.4
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
        'off' => $flickr_options['photo_feed_offset'],
        'link' => $flickr_options['flickr_display_link'],
        'text' => $flickr_options['flickr_display_link_text'],
        'size' => $flickr_options['flickr_photo_size']
        )
      );
    $key = $this->key_maker( $key_input );  // Make Key
    if( $this->retrieve_from_cache( $key ) ){  return; } // Check Cache
    $this->set_size_id(); // Set image size (translate size to Flickr id)
    
    //$this->options['api_key'] = '68b8278a33237f1f369cbbf3c9a9f45c';
    if( $this->options['api_key'] ){
      $this->hidden .= '<!-- Using PT Flickr v'.$this->ver.' with API V2 -->';
    }else{
      $this->hidden .= '<!-- Using PT Flickr v'.$this->ver.' with API V1 -->';
    }
    
    if( function_exists('unserialize') ) {
      $this->try_php_serial();
    }
    
    if ( !($this->success) && function_exists('simplexml_load_file') ) {
      if( $this->options['api_key'] ){
        $this->try_rest();
      }else{
        // Use my API key
        $this->hidden .= '<!-- Using stored API key -->';
        $this->options['api_key'] = '68b8278a33237f1f369cbbf3c9a9f45c';
        $this->try_rest();
      }
    }

    if( $this->success ){
      $this->make_display_link();
    }else{
      if( $this->feed_found ){
        $this->message .= '- Flickr feed was successfully retrieved, but no photos found.';
      }else{
        $this->message .= '- Please recheck your ID(s).';
      }
    }
    
    $this->results = array('continue'=>$this->success,'message'=>$this->message,'hidden'=>$this->hidden,'photos'=>$this->photos,'user_link'=>$this->userlink);

    $this->store_in_cache( $key );  // Store in cache

  }
/**
 *  Function for forming Flickr request
 *  
 *  @ Since 1.2.4
 */ 
  function get_flickr_request($format){
    $options = $this->options;
    $offset = ($options['photo_feed_offset']?$options['photo_feed_offset']:0);
    $num = $offset + $options['flickr_photo_number'];
    if( $options['photo_feed_shuffle'] && function_exists('shuffle') ){ // Shuffle the results
      $num = min( 200, $num*6 );
    }
    $flickr_uid = apply_filters( $this->hook, empty($options['flickr_user_id']) ? 'uid' : $options['flickr_user_id'], $options );
    $request = false;

    if( !empty( $options['api_key'] ) ){
      $key = $options['api_key'];
      switch ($options['flickr_source']) {
        case 'user':
          $request = 'http://api.flickr.com/services/rest/?method=flickr.photos.search&api_key='.$key.'&per_page='.$num.'&format='.$format.'&privacy_filter=1&user_id='. $flickr_uid .'&page=1&extras=description,url_sq,url_t,url_s,url_m,url_n,url_z,url_c';
        break;
        case 'favorites':
          $request = 'http://api.flickr.com/services/rest/?method=flickr.favorites.getPublicList&api_key='.$key.'&per_page='.$num.'&format='.$format.'&privacy_filter=1&user_id='. $flickr_uid .'&page=1&extras=description,url_sq,url_t,url_s,url_m,url_n,url_z,url_c';
        break;
        case 'group':
          $flickr_groupid = apply_filters( $this->hook, empty($options['flickr_group_id']) ? '' : $options['flickr_group_id'], $options );
          $request = 'http://api.flickr.com/services/rest/?method=flickr.photos.search&api_key='.$key.'&per_page='.$num.'&format='.$format.'&privacy_filter=1&group_id='. $flickr_groupid .'&page=1&extras=description,url_sq,url_t,url_s,url_m,url_n,url_z,url_c';
        break;
        case 'set':
          $flickr_set = apply_filters( $this->hook, empty($options['flickr_set_id']) ? '' : $options['flickr_set_id'], $options );
          $request = 'http://api.flickr.com/services/rest/?method=flickr.photosets.getPhotos&api_key='.$key.'&per_page='.$num.'&format='.$format.'&privacy_filter=1&photoset_id='. $flickr_set .'&page=1&extras=description,url_sq,url_t,url_s,url_m,url_n,url_z,url_c,url_o'; // API claims no n, z, or c. Add o to cover missing sizes
        break;
        case 'community':
          $flickr_tags = apply_filters( $this->hook, empty($options['flickr_tags']) ? '' : $options['flickr_tags'], $options );
          $request = 'http://api.flickr.com/services/rest/?method=flickr.photos.search&api_key='.$key.'&per_page='.$num.'&format='.$format.'&privacy_filter=1&tags='. $flickr_tags .'&page=1&extras=description,url_sq,url_t,url_s,url_m,url_n,url_z,url_c';
        break;
      } 
    }else{
      switch ($options['flickr_source']) {
        case 'user':
          $request = 'http://api.flickr.com/services/feeds/photos_public.gne?id='. $flickr_uid .'&lang=en-us&format='.$format.'';
        break;
        case 'favorites':
          $request = 'http://api.flickr.com/services/feeds/photos_faves.gne?nsid='. $flickr_uid .'&lang=en-us&format='.$format.'';
        break;
        case 'group':
          $flickr_groupid = apply_filters( $this->hook, empty($options['flickr_group_id']) ? '' : $options['flickr_group_id'], $options );
          $request = 'http://api.flickr.com/services/feeds/groups_pool.gne?id='. $flickr_groupid .'&lang=en-us&format='.$format.'';
        break;
        case 'set':
          $flickr_set = apply_filters( $this->hook, empty($options['flickr_set_id']) ? '' : $options['flickr_set_id'], $options );
          $request = 'http://api.flickr.com/services/feeds/photoset.gne?set=' . $flickr_set . '&nsid='. $flickr_uid .'&lang=en-us&format='.$format.'';
        break;
        case 'community':
          $flickr_tags = apply_filters( $this->hook, empty($options['flickr_tags']) ? '' : $options['flickr_tags'], $options );
          $request = 'http://api.flickr.com/services/feeds/photos_public.gne?tags='. $flickr_tags .'&lang=en-us&format='.$format.'';
        break;
      } 
    }
    return $request;
 }
/**
 *  Determine image size id
 *  
 *  @ Since 1.2.4
 */
  function set_size_id(){
    $this->options['size_id'] = '.'; // Default is 500

    switch ($this->options['flickr_photo_size']) {
      case 75:
        $this->options['size_id'] = 'url_sq';
      break;
      case 100:
        $this->options['size_id'] = 'url_t';
      break;
      case 240:
        $this->options['size_id'] = 'url_s';
      break;
      case 320:
        $this->options['size_id'] = 'url_n';
      break;
      case 500:
        $this->options['size_id'] = 'url_m';
      break;
      case 640:
        $this->options['size_id'] = 'url_z';
      break;
      case 800:
        $this->options['size_id'] = 'url_c';
      break;
    }
  }

/**
 *  Function getting image url given size setting
 *  
 *  @ Since 1.2.2
 *  @ Updated 1.2.4
 */
  function get_image_url($info){
    $size = $this->options['size_id'];
    if( !empty( $this->options['api_key'] ) ){
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
    }else{
      if( ('url_s' == $size || 'url_t' == $size) && isset($info['m_url']) ){ // Checking url_o is same as src==set
        return $info['m_url'];
      }elseif( ('url_sq' == $size) && isset($info['thumb_url']) ){ // Checking url_o is same as src==set
        return $info['thumb_url'];
      }elseif( ('url_n' == $size || 'url_m' == $size) && isset($info['l_url']) ){ // Checking url_o is same as src==set
        return $info['l_url'];
      }elseif( ('url_z' == $size || 'url_c' == $size )&& isset($info['photo_url']) ){
        return $info['photo_url'];
      }
    }
    return false;
  }
  
/**
 *  Function getting original image url given size setting
 *  
 *  @ Since 1.2.2
 *  @ Updated 1.2.4
 */
  function get_image_orig($info){
    $size = $this->options['size_id'];
    if( !empty( $this->options['api_key'] ) ){
      if( isset($info['url_c']) ){
        return $info['url_c'];
      }elseif( isset($info['url_o']) ){
        return $info['url_o'];
      }elseif( isset($info['url_z']) ){ // Checking url_o is same as src==set
        return $info['url_z'];
      }elseif( isset($info['url_m']) ){
        return $info['url_m'];
      }
    }else{
      if( isset($info['photo_url']) ){
        return $info['photo_url'];
      }elseif( isset($info['l_url']) ){ 
        return $info['l_url'];
      }elseif( isset($info['m_url']) ){ 
        return $info['m_url']; 
      }
    }
    return false;
  }
    
/**
 *  Function for making Flickr request with php_serial return format ( API v1 and v2 )
 *  
 *  @ Since 1.2.4
 */
  function try_php_serial(){
    // Retrieve content using wp_remote_get and PHP_serial
    $request = $this->get_flickr_request('php_serial');

    $_flickr_php = array();
    $response = wp_remote_get($request,
      array(
        'method' => 'GET',
        'timeout' => 20,
      )
    );
    
    if( is_wp_error( $response ) || !isset($response['body']) ) {
      $this->hidden .= '<!-- Failed using wp_remote_get() and PHP_Serial @ '.$request.' -->';
    }else{
      $_flickr_php = @unserialize($response['body']);
    }

    if( empty($_flickr_php) || (!$_flickr_php['photos'] && !$_flickr_php['photoset'] && !$_flickr_php['items']) ){
      $this->hidden .= '<!-- Failed using wp_remote_get() and PHP_Serial @ '.$request.' -->';
      if( $_flickr_php['message'] ){
        $this->message .= '- Attempt 1: '.$_flickr_php['message'].'<br>';
      }else{
        $this->message .= '- Attempt 1: Flickr feed not found<br>';
      }
      $this->success = false;
    }else{
      if( !empty( $this->options['api_key'] ) ){
        $this->parse_php_serial_v2($_flickr_php);
      }else{
        $this->parse_php_serial_v1($_flickr_php);
      }
      if(!empty($this->photos) ){
        $this->success = true;
        $this->hidden  .= '<!-- Success using wp_remote_get() and PHP_Serial -->';
      }else{
        $this->success = false;
        $this->feed_found = true;
        $this->hidden .= '<!-- No photos found using wp_remote_get() and PHP_Serial @ '.$request.' -->';
      }
    }   
  }
/**
 *  Function for parsing results in php_serial format ( API v2 )
 *  
 *  @ Since 1.2.4
 */
  function parse_php_serial_v2($_flickr_php){
    $content =  $_flickr_php['photos'];
    $photos = $_flickr_php['photos']['photo'];

    // Check for photosets  
    if( 'set' == $this->options['flickr_source']) {
      $content =  $_flickr_php['photoset'];
      $photos = $_flickr_php['photoset']['photo'];
    }
    if( is_array( $photos ) ){
      // Remove offset
      for($j=0;$j<$this->options['photo_feed_offset'];$j++){
        if( !empty( $photos  ) ){
          array_shift( $photos );
        }
      }
      foreach( $photos as $info ){
        $the_photo = array();
        $the_photo['image_link'] = 'http://www.flickr.com/photos/'.($info['owner']?$info['owner']:$this->options['flickr_user_id']).'/'.$info['id'].'/';
        $the_photo['image_title'] = (string) @str_replace('"','', @str_replace("'","\'",$info['title']) );
        $the_photo['image_caption'] = (string) $info['description']['_content'];
        $the_photo['image_caption'] = str_replace("'","\'",$the_photo['image_caption'] );
        
        $the_photo['image_source'] = (string) $this->get_image_url($info);
        $the_photo['image_original'] = (string) $this->get_image_orig($info);
        $this->photos[] = $the_photo;
      }
    }
    $this->set_user_link($content);
  }
/**
 *  Function for parsing results in php_serial format ( API v1 )
 *  
 *  @ Since 1.2.4
 */
  function parse_php_serial_v1($_flickr_php){
    $this->userlink = $_flickr_php['url']; // Store userlink for later
    $content =  $_flickr_php['items'];

    if( is_array( $content ) ){
      foreach( $content as $info ){
        $the_photo = array();
        $the_photo['image_link'] = (string) $info['url'];
        $the_photo['image_title'] = (string) @str_replace('"','', @str_replace("'","\'",$info['title']) );
        $the_photo['image_caption'] = (string) $info['description_raw']; // retrieve image title
        $the_photo['image_caption'] = str_replace("'","\'",$the_photo['image_caption'] );
        
        $the_photo['image_source'] = (string) $this->get_image_url($info);
        $the_photo['image_original'] = (string) $this->get_image_orig($info);
        $this->photos[] = $the_photo;
      }
    }
  }
  
/**
 *  Function for making flickr request with xml return format ( API v2 )
 *  
 *  @ Since 1.2.4
 */
  function try_rest(){
    $request = $this->get_flickr_request('rest');

    $_flickrurl  = @urlencode( $request );	// just for compatibility
    $_flickr_xml = @simplexml_load_file( $_flickrurl,"SimpleXMLElement",LIBXML_NOCDATA); // @ is shut-up operator

    if( $_flickr_xml === false || !$_flickr_xml || (!$_flickr_xml->photos && !$_flickr_xml->photoset) ){
      $this->hidden .= '<!-- Failed using simplexml_load_file() and XML @ '.$request.' -->';
      if( $_flickr_xml->err['msg'] ){
        $this->message .= '- Attempt 2: '.$_flickr_xml->err['msg'].'<br>';
      }
      $this->success = false;
    }else{
      $this->parse_rest_v2($_flickr_xml);
      if(!empty($this->photos) ){
        $this->success = true;
        $this->hidden  .= '<!-- Success using simplexml_load_file() and XML -->';
      }else{
        $this->success = false;
        $this->feed_found = true;
        $this->hidden .= '<!-- No photos found using simplexml_load_file() and XML @ '.$request.' -->';
      }
    }
  }
/**
 *  Function for parsing results in xml format ( API v2 )
 *  
 *  @ Since 1.2.4
 */  
  function parse_rest_v2($_flickr_xml){
    $_flickr_xml = $this->xml2array($_flickr_xml);

    $content =  $_flickr_xml['photos'];
    $photos = $content['photo'];
      
    // Check for photosets  
    if( 'set' == $this->options['flickr_source']) {
      $content =  $_flickr_xml['photoset'];
      $photos = $content['photo'];
    }
    if( is_array( $photos ) ){
      // Remove offset
      for($j=0;$j<$this->options['photo_feed_offset'];$j++){
        if( !empty( $photos  ) ){
          array_shift( $photos );
        }
      }
      foreach( $photos as $info ){ // $photos not indexed with ints
        $the_photo = array();    
        
        if( is_array( $info['description'] ) ){
          $the_photo['image_caption'] = "";
        }else{
          $the_photo['image_caption'] = (string) $info['description'];
          $the_photo['image_caption'] = str_replace("'","\'",$the_photo['image_caption'] );
        }

        $info = $info['@attributes'];
        $the_photo['image_link'] = (string) 'http://www.flickr.com/photos/'.($info['owner']?$info['owner']:$this->options['flickr_user_id']).'/'.$info['id'].'/';
        $the_photo['image_title'] = (string) @str_replace('"','', @str_replace("'","\'",$info['title']) );
        $the_photo['image_source'] = (string) $this->get_image_url($info);
        $the_photo['image_original'] = (string) $this->get_image_orig($info);
        $this->photos[] = $the_photo;
      }
    }
    $this->set_user_link($content);
  }
/**
 *  Convert SimpleXMLObject to PHP Array
 *  
 *  @ Since 1.2.4
 */  
  function xml2array ( $input, $out = array () ){
    foreach ( (array) $input as $index => $node ){
      $out[$index] = ( is_object ( $node ) ||  is_array ( $node ) ) ? $this->xml2array ( $node ) : $node;
    }
    return $out;
  }
  
  
  
  

  function set_user_link($content){
    if( 'community' != $this->options['flickr_source'] && $this->options['flickr_display_link'] && $this->options['flickr_display_link_text']) {
      switch ($this->options['flickr_source']) {
        case 'user':
          $this->userlink = 'http://www.flickr.com/photos/'.$this->options['flickr_user_id'].'/';
        break;
        case 'favorites':
          $this->userlink = 'http://www.flickr.com/photos/'.$this->options['flickr_user_id'].'/favorites/';
        break;
        case 'group':
          $this->userlink = 'http://www.flickr.com/groups/'.$this->options['flickr_group_id'].'/';
        break;
        case 'set':
          if($content['owner'] && $content['id']){
            $this->userlink = 'http://www.flickr.com/photos/'.$content['owner'].'/sets/'.$content['id'].'/';
          }
        break;
      }
    }
  }
  
  
//////////////////////////////////////////////////////////////////////////////////////
////////////////////        Unique Admin Functions        ////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////    
  
 /**
   * Alpine PhotoTile: Options Page
   *
   * @ Since 1.1.1
   * @ Updated 1.2.4
   */
  function build_settings_page(){
    $currenttab = $this->get_current_tab();
    
    echo '<div class="wrap AlpinePhotoTiles_settings_wrap">';
    $this->admin_options_page_tabs( $currenttab );

      echo '<div class="AlpinePhotoTiles-container '.$this->domain.'">';
      
      if( 'general' == $currenttab ){
        $this->display_general();
      }elseif( 'add' == $currenttab ){
        $this->display_add();
      }elseif( 'preview' == $currenttab ){
        $this->display_preview();
      }else{
        $this->setup_options_form($currenttab);
      }
      echo '</div>'; // Close Container
    echo '</div>'; // Close wrap
  }  
  
  
  function AddKey($key){
    $options = get_option( $this->settings );
    $options['api_key'] = $key;
    update_option( $this->settings, $options );
  }
  function DeleteKey(){
    $options = get_option( $this->settings );
    $options['api_key'] = 0;
    update_option( $this->settings, $options );
  }
/**
 * Display add page
 *
 * @ Since 1.2.3
 *
 */
  function display_add(){ 
    $currenttab = 'add';
    $options = $this->get_all_options();  

    $settings_section = $this->id . '_'.$currenttab.'_tab';
    $submitted = ( ( isset($_POST[ "hidden" ]) && ($_POST[ "hidden" ]=="Y") ) ? true : false );
    $success = false;
    $errormessage = null;
    $errortype = null;
    $options = array();
     
    // Check that key works
    if($submitted && !empty( $_POST['api_key']) ) {
      $key = $_POST['api_key'];

      $request = 'http://api.flickr.com/services/rest/?method=flickr.test.echo&format=php_serial&api_key='.$key;
      
      $response = wp_remote_get( $request );

      if(!is_wp_error($response) && $response['response']['code'] < 400 && $response['response']['code'] >= 200) {
        $echo = @unserialize($response['body']);
        if( $key == $echo['api_key']['_content'] ){
          $this->AddKey($key);
          $success = true;
        }elseif( $echo['stat'] == 'fail' ){
          $errormessage = "  ".$echo['message']."  ";
          $success = false;
        }else{
          $errormessage = "API Key saved but not verified";
          $this->AddKey($key);
          $success = true;
        }
      }elseif( !is_wp_error($response) && $response['response']['code'] >= 400 ) {
        $error = unserialize($response['body']);
        $errormessage = $error->error;
      }elseif( is_wp_error($response) ){
        $errormessage = $response->get_error_message();
      }
    }elseif($submitted && $_POST[ $this->settings.'_add']['submit-add'] == 'Delete Your API Key'){
      $this->DeleteKey();
      $delete = true;
    }

    
    
    
    echo '<div class="AlpinePhotoTiles-add">';
    if( $success ){
      echo '<div class="announcement"> API key verified and saved. </div>';
    }elseif( $delete ){
      echo '<div class="announcement"> API key deleted. </div>';
    }elseif( $errormessage ){
      echo '<div class="announcement"> An error occured ('.$errormessage.'). </div>';
    }
      
      

    $key = $this->get_option('api_key');
    if( !empty($key) ){
     echo '<h4>Thank you for adding an API Key</h4>'; 
     echo '<div id="AlpinePhotoTiles-user-form" style="margin-bottom:20px;padding-bottom:20px;overflow:hidden;border-bottom: 1px solid #DDDDDD;">'; 
      echo '<form id="'.$this->settings.'"-add-user" action="" method="post">';
      echo '<input type="hidden" name="hidden" value="Y">';
      echo '<input id="'.$this->settings.'-submit" name="'.$this->settings.'_'.$currenttab .'[submit-'. $currenttab.']" type="submit" class="button-primary" style="margin-top:15px;" value="Delete Your API Key" />';
      echo '</form>';
     echo '</div>';
      
    }else{
      $defaults = $this->option_defaults();
      $positions = $this->get_option_positions_by_tab( $currenttab );
        if( count($positions) ){
          foreach( $positions as $position=>$positionsinfo){
            echo '<div id="AlpinePhotoTiles-user-form" style="margin-bottom:20px;padding-bottom:20px;overflow:hidden;border-bottom: 1px solid #DDDDDD;">'; 

              ?>
              <form id="<?php echo $this->settings."-add-user";?>" action="" method="post">
              <input type="hidden" name="hidden" value="Y">
                <?php 
              echo '<div class="'. $position .'">'; 
                if( $positionsinfo['title'] ){ echo '<h4>'. $positionsinfo['title'].'</h4>'; } 
                echo '<table class="form-table">';
                  echo '<tbody>';
                    if( count($positionsinfo['options']) ){
                      foreach( $positionsinfo['options'] as $optionname ){
                        $option = $defaults[$optionname];
                        $fieldname = ( $option['name'] );
                        $fieldid = ( $option['name'] );

                        echo '<tr valign="top"><td>';
                          $this->AdminDisplayCallback($options,$option,$fieldname,$fieldid); // Don't display previously input info
                        echo '</td></tr>';   
                            
                      }
                    }
                  echo '</tbody>';
                echo '</table>';
              echo '</div>';
              echo '<input id="'.$this->settings.'-submit" name="'.$this->settings.'_'.$currenttab .'[submit-'. $currenttab.']" type="submit" class="button-primary" style="margin-top:15px;" value="Save API Key" />';
              echo '</form>';
              echo '<br style="clear:both;">';
            echo '</div>';
            
          }
        }
      
    }
    echo '</div>'; // close add div
          ?>
        <div style="max-width:680px;">
          <h1><?php _e('What is an API Key and why do I need one?');?> :</h1>
          <p><?php _e('Photo sharing websites like Flickr want to protect their users and to prevent abuses by keeping track of how their services are being used. ');
          _e('Two of the ways that Flickr does this is by assigning API Keys to plugins, like the Alpine PhotoTile, to keep track of who is who and by limiting the number of times a plugin can talk to the Flickr network.  ');
          _e('While serveral hundred websites could share an API Key without reaching this limit, the Alpine PhotoTile plugin has become popular enough that users now need API Keys of their own. ');
          ?></p>
          <p><?php
          _e('An API Key is free and easy to get. Because this plugin uses multiple methods of talking with the Flickr network, signing up for an API Key is optional. However, users without an API Key will experience the following limitations: ');?>
            <ul style="text-align:left;padding-left:15px;">
            <li>- <?php _e('Images size options limited to 75px, 240px, 500px, and 800px.');?></li>
            <li>- <?php _e('"Photo Offset" option will not work.');?></li>
            <li>- <?php _e('"Shuffle/Randomize Photos" option will not work.');?></li>
            <li>- <?php _e('Lack of helpful error messages if something does not work.');?></li>
            <li>- <?php _e('Possibly slower plugin loading time (It is hard to tell).');?></li>
            <li>- <?php _e('Future options added to this plugin will likely require an API Key.');?></li>
            </ul> 
          <?php _e('If you are fine with these limitations, feel free to use this plugin without an API Key. Otherwise, please add an API Key using the directions below. Thank you for your understanding.');?>
         </p>
          
        </div>
        <div style="max-width:680px;">
          <h1><?php _e('How to get a Flickr API Key');?> :</h1>
          <h2 style="font-size: 18px;padding:0px;">(<?php _e("Don't worry. I promise it's EASY");?>!!!)</h2>
          <p><?php _e('Please <a href="'.$this->info.'" target="_blank">let me know</a> if these directions become outdated.');?></p>
          <ol>
            <li>
              <?php _e("Make sure you are logged into Flickr.com and then visit");?> <a href="http://www.flickr.com/services/apps/create/" target="_blank">http://www.flickr.com/services/apps/create/</a>.
            </li>
            <li>
              <?php _e('Under "Get your API Key", click the "Request an API Key" link.');?>
            </li>
            <li>
              <?php _e('Next, click the button that says "APPLY FOR A NON-COMMERCIAL KEY". Even if your website is commercial, this plugin is non-commercial.');?>
            </li>
            <li>
              <p><?php _e('A form will appear. Fill in the form with the following infomation. Feel free to add details wherever you like. Check the two boxes and finish by clicking "Submit":');?></p>
              <dl>
                <dt><strong><?php _e('What\'s the name of your app');?></strong></dt>
                <dd><em><?php echo $this->name;?> WordPress plugin</em></dd>
                <dt><strong><?php _e('What are you building?');?></strong></dt>
                <dd><em>A simple plugin to display images from Flickr on my WordPress website.</em></dd>
              </dl>
            </li>
            <li>
              <?php _e('Copy and paste the Key into the form above. Click "Save API Key" and you are all done. I hope you enjoy the plugin.');?>
            </li>
          </ol>
        </div>  
    <?php
  }  
}


?>
