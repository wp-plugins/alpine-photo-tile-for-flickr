/*
 * Alpine PhotoTile for Flickr: jQuery Tile Display Functions
 * By: Eric Burger, http://thealpinepress.com
 * Version: 1.0.0
 * Updated: August  2012
 * 
 */

(function( w, s ) {
  s.fn.APTFFbyTAPTilesPlugin = function( options ) {
  
    options = s.extend( {}, s.fn.APTFFbyTAPTilesPlugin.options, options );
  
    return this.each(function() {  
      var parent = s(this);
      var imageList = s(".APTFFbyTAP_image_list_class",parent);
      var images = s('.APTFFbyTAP-image',imageList);
      var allPerms = s('.APTFFbyTAP-link',imageList);
      var width = parent.width();
      
      var currentRow,img,newDiv,newDivContainer,src,url,height,theClasses,theHeight,theWidth,perm;
      
      if( 'square' == options.shape && 'windows' == options.style ){
        s.each(images, function(i){
          img = this;
          src = img.src;
          url = 'url("'+src+'")';
          perm = allPerms[i];
          
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
          img = this;
          src = img.src;
          url = 'url("'+src+'")';
          perm = allPerms[i];
          
          if(i%3 == 0){
            theWidth = (width-8);
            height = theWidth*img.naturalHeight/img.naturalWidth;
            height = (height?height:width);
            
            newRow(height);
                        
            theClasses = "APTFFbyTAP-tile APTFFbyTAP-tile-rectangle";
            theHeight = (height);

            addDiv(i);
            
          }else if(i%3 == 1){
            theWidth = (width/2-4-4/2);
            height = theWidth*img.naturalHeight/img.naturalWidth;
            height = (height?height:width);
            newRow( height );
            
            theClasses = "APTFFbyTAP-tile APTFFbyTAP-half-tile APTFFbyTAP-half-tile-first APTFFbyTAP-tile-rectangle";
            theHeight = (height);
            theWidth = (width/2-4-4/2);
            addDiv(i);
            
          }else if(i%3 == 2){
            theWidth = (width/2-4-4/2);
            var nextHeight = theWidth*img.naturalHeight/img.naturalWidth;
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
      else if( 'floor' == options.style){
        parent.css({'width':'100%'});
        width = parent.width();
        theWidth = (width/options.perRow-4-4/options.perRow);
        theHeight = (width/options.perRow);
          
        s.each(images, function(i){
          img = this;
          src = img.src;
          url = 'url("'+src+'")';
          perm = allPerms[i];
          
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
      else if( 'wall' == options.style ){
        parent.css({'width':'100%'});
        width = parent.width();
        var imageRow=[],currentImage,sumWidth=0,maxHeight=0;
        theHeight = (width/options.perRow);
        
        s.each(images, function(i){
          img = this;
          src = img.src;
          url = 'url("'+src+'")';
          perm = allPerms[i];

          currentImage = {
            "width":img.naturalWidth,
            "height":img.naturalHeight,
            "url":url,
            "perm":perm
          } 
          sumWidth += img.naturalWidth;
          imageRow[imageRow.length] = currentImage;  
          
          if(i%options.perRow == (options.perRow -1) || (images.length-1)==i ){
            if( (images.length-1)==i ){
              sumWidth += (options.perRow - i%options.perRow -1)*imageRow[imageRow.length-1].width;
            }
            
            newRow(theHeight);

            var pos = 0;
            s.each(imageRow,function(j){
              var normalWidth = this.width/sumWidth*width;

              url = this.url;
              perm = this.perm;
              theClasses = "APTFFbyTAP-tile";
              theWidth = (normalWidth-4-4/options.perRow);
              addDiv(j);
              
              newDivContainer.css({
                'left':pos+'px'
              });
              
              pos += normalWidth;
            });
          
            imageRow=[];sumWidth=0;
          } 
        });
      }
      else if( 'bookshelf' == options.style ){
        parent.css({'width':'100%'});
        width = parent.width();
        var imageRow=[],currentImage,sumWidth=0,maxHeight=0;
        
        s.each(images, function(i){
          img = this;
          src = img.src;
          url = 'url("'+src+'")';
          perm = allPerms[i];
          
          currentImage = {
            "width":img.naturalWidth,
            "height":img.naturalHeight,
            "url":url,
            "perm":perm
          } 
          sumWidth += img.naturalWidth;
          imageRow[imageRow.length] = currentImage;  
          
          if(i%options.perRow == (options.perRow -1) || (images.length-1)==i ){
            if( (images.length-1)==i ){
              sumWidth += (options.perRow - i%options.perRow -1)*imageRow[imageRow.length-1].width;
            }
            
            newRow(10);
            currentRow.addClass('APTFFbyTAP-bookshelf');
            var pos = 0;
            s.each(imageRow,function(j){
              var normalWidth = this.width/sumWidth*width;
              var normalHeight = normalWidth*this.height/this.width;
              if( normalHeight > maxHeight ){
                maxHeight = normalHeight;
                currentRow.css({'height':normalHeight+"px"});
              }
              
              url = this.url;
              perm = this.perm;
              theClasses = "APTFFbyTAP-book";
              theWidth = (normalWidth-4-4/options.perRow);
              theHeight = normalHeight;
              addDiv(j);
              
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
          img = this;
          src = img.src;
          url = 'url("'+src+'")';
          perm = allPerms[i];
          
          currentImage = {
            "width":img.naturalWidth,
            "height":img.naturalHeight,
            "url":url,
            "perm":perm
          } 
          sumWidth += img.naturalWidth;
          imageRow[imageRow.length] = currentImage;  
          
          if(i%options.perRow == (options.perRow -1) || (images.length-1)==i ){
            if( (images.length-1)==i ){
              sumWidth += (options.perRow - i%options.perRow -1)*imageRow[imageRow.length-1].width;
            }
            newRow(10);
            currentRow.addClass('APTFFbyTAP-riftline');
            var pos = 0;
            s.each(imageRow,function(j){
              var normalWidth = this.width/sumWidth*width;
              var normalHeight = normalWidth*this.height/this.width;
              if( normalHeight > maxHeight ){
                maxHeight = normalHeight;
                currentRow.css({'height':normalHeight+"px"});
              }
                            
              url = this.url;
              perm = this.url;
              theClasses = 'APTFFbyTAP-rift APTFFbyTAP-float-'+row;
              theWidth = (normalWidth-4-4/options.perRow);
              theHeight = normalHeight;
              addDiv(j);
              
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
          img = this;
          src = img.src;
          url = 'url("'+src+'")';
          perm = allPerms[i];
          
          if( 0 == i ){
            galleryHeight = width/options.perRow*options.galleryHeight;
            
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
            if(options.imageHighlight && !options.imageBorder){
              galleryContainer.addClass('APTFFbyTAP-highlight-div');
              galleryContainer.width( galleryContainer.width()-4 );
              galleryContainer.height( galleryContainer.height()-4 );
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

        if(perm){
          newDiv.wrap('<a href="'+perm.href+'" class="APTFFbyTAP-link" target="_blank"></a>');
        }
        if(options.imageBorder){
          newDivContainer.addClass('APTFFbyTAP-border-div');
          newDivContainer.width( newDivContainer.width()-10 );
          newDivContainer.height( newDivContainer.height()-10 );
        }
        if(options.imageHighlight){
          if(!options.imageBorder){
            newDivContainer.addClass('APTFFbyTAP-highlight-div');
            newDivContainer.width( newDivContainer.width()-4 );
            newDivContainer.height( newDivContainer.height()-4 );
          }
          newDivContainer.hover(function(){
            s(this).css({
              "background": options.highlight,
            });
          },function(){
            s(this).css({
              "background-color": "#fff",
            });
          });
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
  
  s.fn.APTFFbyTAPTilesPlugin.options = {
    backgroundClass: 'northbynorth_background',
    parentID: 'parent'
  }    
})( window, jQuery );
  
  
(function( w, s ) {
  s.fn.APTFFbyTAPAdjustBordersPlugin = function( options ) {
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
            'padding':'4px',
            "margin-left": "1px",
            "margin-right": "1px",
          });
        }else if( currentImg.hasClass('APTFFbyTAP-img-noborder') ){
          currentImg.removeClass('APTFFbyTAP-img-noborder');
          currentImg.css({
            'max-width':(width)+'px',
            'padding':'0px',
          });
        }
        
        if( currentImg.hasClass('APTFFbyTAP-img-shadow') ){
          width -= 2;
          currentImg.removeClass('APTFFbyTAP-img-shadow');
          currentImg.css({
            "box-shadow": "0 1px 3px rgba(34, 25, 25, 0.4)",
            "margin-left": "1px",
            "margin-right": "1px",
            'max-width':(width)+'px',
          });
        }else if( currentImg.hasClass('APTFFbyTAP-img-noshadow') ){
          currentImg.removeClass('APTFFbyTAP-img-noshadow');
          currentImg.css({
            'max-width':(width)+'px',
            "box-shadow":"none",
          });
        }
        
        if( currentImg.hasClass('APTFFbyTAP-img-highlight') ){
          currentImg.removeClass('APTFFbyTAP-img-highlight');
          
          if( '4px' != currentImg.css('padding-right') ){
            width -= 6;
            currentImg.css({
              'max-width':(width)+'px',
              'padding':'2px',
              "margin-left": "1px",
              "margin-right": "1px",
            });
          }

          currentImg.hover(function(){
            console.log(options.highlight);
            s(this).css({
              "background-color": options.highlight,
            });
          },function(){
            s(this).css({
              "background-color": "#fff",
            });
          });
        }
      });
    });
  }
    
})( window, jQuery );