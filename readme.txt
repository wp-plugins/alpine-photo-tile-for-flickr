=== Alpine PhotoTile for Flickr ===
Contributors: theAlpinePress
Donate link: thealpinepress.com
Tags: photos, flickr, photostream, stylish, pictures, images, widget, sidebar, gallery, lightbox, fancybox
Requires at least: 2.8
Tested up to: 3.5
Stable tag: 1.2.1
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Stylish and compact plugin for displaying Flickr images in a sidebar, post, or page. 

== Description == 
The Alpine PhotoTile for Flickr, the first plugin in the Alpine PhotoTile series, is capable of retrieving photos from a particular Flickr
user, a group, a set, or the Flickr community. The photos can be linked to the your Flickr page, a specific URL, or to a Fancybox slideshow.
Also, the Shortcode Generator makes it easy to insert the widget into posts without learning any of the code. This lightweight but powerful
widget takes advantage of WordPress's built in JQuery scripts to create a sleek presentation that I hope you will like. A full description
and demonstration is available at [the Alpine Press](http://thealpinepress.com/alpine-phototile-for-flickr/ "Plugin Demo").

**Features:**

* Display Flickr images in a sidebar, post, or page
* Multiple styles to allow for customization
* Fancybox/lighbox feature for interactive slideshow
* Simple instructions
* Widget & shortcode options
* Feed caching/storage for improved page loading

**Quick Start Guide:**

1. After installing the plugin on your WordPress site, make sure it is activated by logging into your admin area and going to Plugins in the left menu.
2. To add the plugin to a sidebar, go to Appearance->Widgets in the left menu.
3. Find the rectangle labeled Alpine PhotoTile for Flickr. Click and drag the rectangle to one of the sidebar containers on the right.
4. Once you drop the rectangle in a sidebar area, it should open to reveal a menu of options. The only required information for the plugin to work is Flickr User ID. See "How do I find my Flickr user ID or group ID?" in the FAQ section for further guidance about finding your ID. Enter this ID and click save in the right bottom corner of the menu.
5. Open another page/window in your web browser and navigate to your WordPress site to see how the sidebar looks with the Alpine PhotoTile for Flickr included.
6. Play around with the various styles and options to find what works best for your site.

== Installation ==

1. Upload `alpine-photo-tile-for-flickr` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Use the widget like any other widget.
4. Customize based on your preference.

== Frequently Asked Questions ==

**I'm getting the message "Flickr feed was successfully retrieved, but no photos found". What does that mean?**

This message simply means that while no distinguishable errors occurred, the plugin found your feed to be empty. This might occur if you set the plugin source to Favorites, but you have not actually "favorited" any of your photos.

**I'm getting the message "Flickr feed not found. Please recheck your ID". What does that mean?**

This message can mean two things. First, it can indicate that the user ID, group ID, or set ID were input incorrectly, causing the feed to fail. In this case, you should try to correct and re-save your IDs.
Second, this message can also mean that the server your WordPress site is being hosted on has prevented the feed from being retrieved. While it is rare, we have encountered web-hosts that disable the feed fetching functions used in the PhotoTile plugin. If this is the case, there is nothing we can do to override or work around the settings on your host server.

**Is there a shortcode function?**

Yes, rather than explaining how to setup the shortcode, I've created a method of generating the shortcode. Check out the Shortcode Generator on the plugin's settings page ( Settings->AlpineTile: Flickr->Shortcode Generator).

**Why doesn't the widget show my most recent photos?**

The plugin caches or stores the Flickr photo feed for three hours or the time set on the Settings->AlpineTile: Flickr->Plugin Settings page (see Caching above).  If the new photos have still not appeared after this time, it is possible that Flickr is responsible for the delay. While Flickr is fairly prompt about updating photo feeds, periods of high traffic (especially on weekdays between 10am and 4pm) can cause a delay in feed updates.

If you have any more questions, please leave a message at [the Alpine Press](http://thealpinepress.com/alpine-phototile-for-flickr/ "Plugin Demo").
I am a one-man development team and I distribute these plugins for free, so please be patient with me.

== Changelog ==

= 1.0.0 =
* First Release

= 1.0.1 =
* Added caching functions

= 1.0.2 =
* Fixed AJAX menu plugin loading problem

= 1.0.3 =
* Rebuilt photo retrieval method using Flickr API
* Changed "per row" and "image number" options
* Added int high and low to sanitization function
* Repaired photo linking issue with rift and bookshelf styles
* Added height option to gallery style
* Renamed functions where needed
* Custom display link (and removed display link option from Community source option)
* Added "wall" style

= 1.0.3.1 =
* Added function and class check before call

= 1.1.1 =
* Cache filter for .info and .cache (V2)
* Load styles and scripts to widget.php only
* Added options page and shortcode generator
* Added highlight, highlight color option, cache option, and cache time
* Made option callbacks plugin specific (not global names)
* Edited style layouts
* Fixed url generation for set links
* Enqueue JS and CSS on pages containing widget or shortcode only

= 1.2.0 =
* Rebuilt plugin structure into OBJECT
* Combined all Alpine Photo Tiles scripts and styles into identical files
* Improved IE 7 compatibility
* Added custom image link options
* Added Fancybox jQuery option
* Fixed galleryHeight bug
* Implemented fetch with wp_remote_get()

= 1.2.1 =
* Rebuilt admin div structure
* Fixed admin css issues