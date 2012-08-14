/*
 * Alpine PhotoTile for Flickr: jQuery Tile Display Functions
 * By: Eric Burger, http://thealpinepress.com
 * Version: 1.0.0
 * Updated: August  2012
 * 
 */

(function( w, s ) {
  s.fn.theAlpinePressTiles = function( options ) {
  
    options = s.extend( {}, s.fn.theAlpinePressTiles.options, options );
  
    return this.each(function() {  
      var parent = s(this);
      var imageList = s(".APTFFbyTAP_image_list_class",parent);
      var images = s('.APTFFbyTAP-image',imageList);
      var perm = s('.APTFFbyTAP-link',imageList);
      var width = parent.width();
      
      var currentRow,img,newDiv,newDivContainer,src,url,height,theClasses,theHeight,theWidth;
      
      if( 'square' == options.shape && 'windows' == options.style ){
        s.each(images, function(i){
          img = s(this);
          src = img.attr('src');
          url = 'url("'+src+'")';
          
          if(i%3 == 0){
            
            theClasses = "APTFFbyTAP-tile";
            theWidth = (width-8);
            theHeight = theWidth;
            newRow( theHeight );
            addDiv(i);
            
          }else if(i%3 == 1){

            theClasses = "APTFFbyTAP-tile APTFFbyTAP-half-tile APTFFbyTAP-half-tile-first";
            theWidth = (width/2-4-4/2);
            theHeight = theWidth;
            newRow( theHeight );
            addDiv(i);
     
          }else if(i%3 == 2){
        
            theClasses = "APTFFbyTAP-tile APTFFbyTAP-half-tile APTFFbyTAP-half-tile-last";
            theWidth = (width/2-4-4/2);
            theHeight = theWidth;
            addDiv(i);
          }
          
          
        });
      }
      else if( 'rectangle' == options.shape && 'windows' == options.style ){
        s.each(images, function(i){
          img = s(this);
          src = img.attr('src');
          url = 'url("'+src+'")';
          
          if(i%3 == 0){
            theWidth = (width-8);
            height = theWidth*img.get(0).naturalHeight/img.get(0).naturalWidth;
            height = (height?height:width);
            
            newRow(height);
                        
            theClasses = "APTFFbyTAP-tile APTFFbyTAP-tile-rectangle";
            theHeight = (height);

            addDiv(i);
            
          }else if(i%3 == 1){
            theWidth = (width/2-4-4/2);
            height = theWidth*img.get(0).naturalHeight/img.get(0).naturalWidth;
            height = (height?height:width);
            newRow( height );
            
            theClasses = "APTFFbyTAP-tile APTFFbyTAP-half-tile APTFFbyTAP-half-tile-first APTFFbyTAP-tile-rectangle";
            theHeight = (height);
            theWidth = (width/2-4-4/2);
            addDiv(i);
            
          }else if(i%3 == 2){
            theWidth = (width/2-4-4/2);
            var nextHeight = theWidth*img.get(0).naturalHeight/img.get(0).naturalWidth;
            nextHeight = (nextHeight?nextHeight:theWidth);
            if(nextHeight && nextHeight<height){
              height = nextHeight;
              updateHeight(newDivContainer,height);
              currentRow.css({'height':height+'px'});
            }
                        
            theClasses = "APTFFbyTAP-tile APTFFbyTAP-half-tile APTFFbyTAP-half-tile-last APTFFbyTAP-tile-rectangle";
            theHeight = (height);
            addDiv(i);
          }

        });
      }      
      else if( 'floor' == options.style ){
        parent.css({'width':'100%'});
        width = parent.width();
        
        s.each(images, function(i){
          img = s(this);
          src = img.attr('src');
          url = 'url("'+src+'")';
          theWidth = (width/options.perRow-4-4/options.perRow);
          theHeight = (width/options.perRow);
          
          if(i%options.perRow == 0){
            newRow(width/options.perRow); 
            theClasses = "APTFFbyTAP-tile APTFFbyTAP-half-tile APTFFbyTAP-half-tile-first";            
            addDiv(i);
          }else if(i%options.perRow == (options.perRow -1) ){
            theClasses = "APTFFbyTAP-tile APTFFbyTAP-half-tile APTFFbyTAP-half-tile-last";
            addDiv(i);
          }else{    
            theClasses = "APTFFbyTAP-tile APTFFbyTAP-half-tile";
            addDiv(i);
          }
        });
      }
      else if( 'bookshelf' == options.style ){
        parent.css({'width':'100%'});
        width = parent.width();
        var imageRow=[],currentImage,sumWidth=0,maxHeight=0;
        
        s.each(images, function(i){
          img = s(this);
          src = img.attr('src');
          url = 'url("'+src+'")';
          
          currentImage = {
            "width":img.get(0).naturalWidth,
            "height":img.get(0).naturalHeight,
            "url":url
          } 
          sumWidth += img.get(0).naturalWidth;
          imageRow[imageRow.length] = currentImage;  
          
          if(i%options.perRow == (options.perRow -1) || (images.length-1)==i ){
            if( (images.length-1)==i ){
              sumWidth += (options.perRow - i%options.perRow -1)*imageRow[imageRow.length-1].width;
            }
            
            newRow(10);
            currentRow.addClass('APTFFbyTAP-bookshelf');
            var pos = 0;
            s.each(imageRow,function(){
              var normalWidth = this.width/sumWidth*width;
              var normalHeight = normalWidth*this.height/this.width;
              if( normalHeight > maxHeight ){
                maxHeight = normalHeight;
                currentRow.css({'height':normalHeight+"px"});
              }
              
              url = this.url;
              theClasses = "APTFFbyTAP-book";
              theWidth = (normalWidth-4-4/options.perRow);
              theHeight = normalHeight;
              addDiv(i);
              
              newDivContainer.css({
                'left':pos+'px'
              });
              
              pos += normalWidth;
            });
          
            imageRow=[];sumWidth=0;maxHeight=0;
          }          
          
        });
      }      
      else if( 'rift' == options.style ){
        parent.css({'width':'100%'});
        width = parent.width();
        var imageRow=[],currentImage,sumWidth=0,maxHeight=0,row=0;
        
        s.each(images, function(i){
          img = s(this);
          src = img.attr('src');
          url = 'url("'+src+'")';
          
          currentImage = {
            "width":img.get(0).naturalWidth,
            "height":img.get(0).naturalHeight,
            "url":url
          } 
          sumWidth += img.get(0).naturalWidth;
          imageRow[imageRow.length] = currentImage;  
          
          if(i%options.perRow == (options.perRow -1) || (images.length-1)==i ){
            if( (images.length-1)==i ){
              sumWidth += (options.perRow - i%options.perRow -1)*imageRow[imageRow.length-1].width;
            }
            newRow(10);
            currentRow.addClass('APTFFbyTAP-riftline');
            var pos = 0;
            s.each(imageRow,function(){
              var normalWidth = this.width/sumWidth*width;
              var normalHeight = normalWidth*this.height/this.width;
              if( normalHeight > maxHeight ){
                maxHeight = normalHeight;
                currentRow.css({'height':normalHeight+"px"});
              }
                            
              url = this.url;
              theClasses = 'APTFFbyTAP-rift APTFFbyTAP-float-'+row;
              theWidth = (normalWidth-4-4/options.perRow);
              theHeight = normalHeight;
              addDiv(i);
              
              newDivContainer.css({
                'left':pos+'px'
              });
              
              pos += normalWidth;
            });
          
            imageRow=[];sumWidth=0;maxHeight=0,row=(row?0:1);
          }          
          
        });
      }   
      else if( 'gallery' == options.style ){
        parent.css({'width':'100%','opacity':0});
        width = parent.width();
        var originalImages = s('img.APTFFbyTAP-original-image',parent);
        
        var gallery,galleryContainer,galleryHeight;
        theWidth = (width/options.perRow-4-4/options.perRow);
        theHeight = (width/options.perRow);
             
        s.each(images, function(i){
          img = s(this);
          src = img.attr('src');
          url = 'url("'+src+'")';
          
          if( 0 == i ){
            galleryHeight = width/options.perRow*3;
            
            newRow(galleryHeight); 
                 
            galleryContainer = s('<div class="APTFFbyTAP-image-div-container APTFFbyTAP-gallery-container"></div>');
            galleryContainer.css({
              "height":galleryHeight+"px",
              "width":(width-8)+"px",
            });
            
            currentRow.append(galleryContainer);
                             
            if(options.imageBorder){
              galleryContainer.addClass('APTFFbyTAP-border-div');
              galleryContainer.width( galleryContainer.width()-10 );
              galleryContainer.height( galleryContainer.height()-10 );
            }
            if(options.imageShadow){
              galleryContainer.addClass('APTFFbyTAP-shadow-div');
            }
            if(options.imageCurve){
              galleryContainer.addClass('APTFFbyTAP-curve-div');
            }

          }
                    
          if(i%options.perRow == 0){     
            newRow(width/options.perRow); 
            theClasses = "APTFFbyTAP-tile APTFFbyTAP-half-tile APTFFbyTAP-half-tile-first";            
            addDiv(i);
          }else if(i%options.perRow == (options.perRow -1) ){           
            theClasses = "APTFFbyTAP-tile APTFFbyTAP-half-tile APTFFbyTAP-half-tile-last";            
            addDiv(i);
          }else{
            theClasses = "APTFFbyTAP-tile APTFFbyTAP-half-tile";            
            addDiv(i);
          }
          
          var storeUrl = url;
          if( originalImages[i] ){
            if( originalImages[i].src ){
              storeUrl = 'url("'+originalImages[i].src+'")';
            }
          }

          gallery = s('<div id="'+parent.attr('id')+'-image-'+i+'-gallery" class="APTFFbyTAP-image-div APTFFbyTAP-image-gallery"></div>');   
          gallery.css({
            'background-image':storeUrl,
          });
          if( 0 != i ){
            gallery.hide();
          }
          galleryContainer.append(gallery);
          
        });  

        var allThumbs = s('.APTFFbyTAP-image-div',parent);
        var allGalleries = s('.APTFFbyTAP-image-gallery',parent);
        s.each(allThumbs,function(){
          var theThumb = s(this);
          if( !theThumb.hasClass('APTFFbyTAP-image-gallery') ){
            theThumb.hover(function() {
              allGalleries.hide();
              s("#"+theThumb.attr('id')+"-gallery").show();
            }); 
          }
        });
        
        parent.ready(function(){
          parent.css({'opacity':1});
        });
      }

      function newRow(height){
        currentRow = s('<div class="APTFFbyTAP-row"></div>');
        currentRow.css({'height':height+'px'});
        parent.append(currentRow);
      }
      function addDiv(i){
        newDiv = s('<div id="'+parent.attr('id')+'-image-'+i+'" class="APTFFbyTAP-image-div"></div>');   
        newDiv.css({
          'background-image':url,
        });
            
        newDivContainer = s('<div class="APTFFbyTAP-image-div-container '+theClasses+'"></div>');
        newDivContainer.css({
          "height":theHeight+"px",
          "width":theWidth+"px",
        });
        
        currentRow.append(newDivContainer);
        newDivContainer.append(newDiv);
        
        if(perm[i]){
          newDiv.wrap('<a href="'+perm[i]+'" class="APTFFbyTAP-link" target="_blank"></a>');
        }
        if(options.imageBorder){
          newDivContainer.addClass('APTFFbyTAP-border-div');
          newDivContainer.width( newDivContainer.width()-10 );
          newDivContainer.height( newDivContainer.height()-10 );
        }
        if(options.imageShadow){
          newDivContainer.addClass('APTFFbyTAP-shadow-div');
        }
        if(options.imageCurve){
          newDivContainer.addClass('APTFFbyTAP-curve-div');
        }
      }
      
      function updateHeight(aDiv,aHeight){
        aDiv.height(aHeight);
        if(options.imageBorder){
          aDiv.height( aDiv.height()-10 );
        }
      }

    });
  }
  
  s.fn.theAlpinePressTiles.options = {
    backgroundClass: 'northbynorth_background',
    parentID: 'parent'
  }    
})( window, jQuery );
  
  
(function( w, s ) {
  s.fn.theAlpinePressAdjustBorders = function( options ) {
    return this.each(function() {  
      var parent = s(this);
      var images = s('img',parent);

      s.each(images,function(){
        var currentImg = s(this);
        var width = currentImg.parent().width();
        
        // Remove and replace ! important classes
        if( currentImg.hasClass('APTFFbyTAP-img-border') ){
          width -= 10;
          currentImg.removeClass('APTFFbyTAP-img-border');
          currentImg.css({
            'max-width':(width)+'px',
            'padding':'5px',
          });
        }else if( currentImg.hasClass('APTFFbyTAP-img-noborder') ){
          currentImg.removeClass('APTFFbyTAP-img-noborder');
          currentImg.css({
            'max-width':(width)+'px',
            'padding':'0px',
          });
        }
        
        if( currentImg.hasClass('APTFFbyTAP-img-shadow') ){
          width -= 8;
          currentImg.removeClass('APTFFbyTAP-img-shadow');
          currentImg.css({
            "box-shadow": "0 1px 3px rgba(34, 25, 25, 0.4)",
            "margin-left": "4px",
            "margin-right": "4px",
            "margin-bottom": "9px",
            'max-width':(width)+'px',
          });
        }else if( currentImg.hasClass('APTFFbyTAP-img-noshadow') ){
          currentImg.removeClass('APTFFbyTAP-img-noshadow');
          currentImg.css({
            'max-width':(width)+'px',
            "box-shadow":"none",
            "margin-left": 0,
            "margin-right": 0
          });
        }
        
      });
    });
  }
    
})( window, jQuery );