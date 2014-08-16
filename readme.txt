=== Alpine PhotoTile for Flickr ===
Contributors: theAlpinePress
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=eric%40thealpinepress%2ecom&lc=US&item_name=Alpine%20PhotoTile%20for%20Flickr%20Donation&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted
Tags: photos, flickr, photostream, stylish, pictures, images, widget, sidebar, gallery, lightbox, fancybox, colorbox, prettybox
Requires at least: 2.8
Tested up to: 3.8
Stable tag: 1.2.6.7
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Stylish and compact plugin for displaying Flickr images in a sidebar, post, or page. 

== Description == 
The Alpine PhotoTile for Flickr, the first plugin in the Alpine PhotoTile series, is capable of retrieving photos from a particular 
user, a group, a set, or the Flickr community. The photos can be linked to the your Flickr page, a specific URL, or to a Fancybox slideshow.
Also, the Shortcode Generator makes it easy to insert the widget into posts without learning any of the code. This lightweight but powerful
widget takes advantage of WordPress's built in JQuery scripts to create a sleek presentation that I hope you will like. A full description
and demonstration is available at [the Alpine Press](http://thealpinepress.com/alpine-phototile-for-flickr/ "Flickr Plugin Demo").

**Features:**

* Display images in a sidebar, post, or page
* Multiple styles to allow for customization
* Lighbox feature for interactive slideshow (Fancybox, prettyBox, or ColorBox)
* Simple instructions
* Widget & shortcode options
* Feed caching/storage for improved page loading

**Quick Start Guide:**

1. After installing the Alpine PhotoTile for Flickr plugin on your WordPress site, make sure it is activated by logging into your admin area and going to Plugins in the left menu.
2. To add the plugin to a sidebar, go to Appearance->Widgets in the left menu.
3. Find the rectangle labeled Alpine PhotoTile for Flickr. Click and drag the rectangle to one of the sidebar containers on the right.
4. Once you drop the rectangle in a sidebar area, it should open to reveal a menu of options. The only required information for the plugin to work is Flickr User ID. See "How do I find my Flickr user ID or group ID?" in the FAQ section for further guidance about finding your ID. Enter this ID and click save in the right bottom corner of the menu.
5. If you want to add the Flickr plugin to a post or page, you will need to use a Shortcode. A Shortcode is a line of text that tells WordPress to load a plugin inside a post along with what settings to use. The Alpine PhotoTile comes with an interactive Shortcode Generator to make this as easy as possible. To find the Shortcode Generator, click on Settings in the left menu of your admin area. Under Settings, click on AlpineTile. Lastly, click on the tab labeled Shortcode Generator and follow the instructions there.
6. It is recommended that you read through the additional options on the plugin's settings page. To find the settings page, click on Settings in the left menu of your admin area. Under Settings, click on AlpineTile. Lastly, click on the tab labeled Plugin Settings. It is very challenging to create a plugin that does not interfere with other plugins or themes. Many common issues can be resolved by changing the Lightbox option, preventing the plugin from loading Lightbox files, or loading Styles and Scripts in the Header.
7. (Optional: To enable all the plugin's features, add an API Key). To find the API Key page, click on Settings in the left menu of your admin area. Under Settings, click on AlpineTile. Lastly, click on the tab labeled Add API Key. The page will explain what an API Key is and how to get one.
8. Play around with the various styles and options to find what works best for your site.

== Installation ==

**Flickr Plugin Installation**

1. Go to the *Plugins->Add New* section of the Admin Panel.
2. Either search for "Alpine PhotoTile for Flickr" or upload the `alpine-photo-tile-for-flickr` folder using the upload tool.
3. Go to the *Plugins->Installed Plugins* and activate the "Alpine PhotoTile for Flickr" plugin.

**Add a Flickr API Key**

4. Make sure you are logged into Flickr.com and then visit http://www.flickr.com/services/apps/create/.
5. Under "Get your API Key", click the "Request an API Key" link.
6. Next, click the button that says "APPLY FOR A NON-COMMERCIAL KEY". Even if your website is commercial, the Alpine Flickr plugin is non-commercial.
7. A form will appear. Fill in the form with your information. Check the two boxes and finish by clicking "Submit".
8. Copy and paste the Key into the form on the plugin's "Add API Key" page. Click "Save API Key" and you are all done.

**Using the Flickr Widget**

9. Use the widget like any other widget. Go to *Appearance->Widgets* in the left menu. Find the rectangle labeled "Alpine PhotoTile for Flickr". Click and drag the rectangle to one of the sidebar containers on the right.
10. Customize Alpine PhotoTile for Flickr plugin based on your preference.

**Using the Flickr Shortcode**

11. A shortcode is a line of texted used for loading plugins within WordPress pages or posts. Rather than explaining how to setup the shortcode, I have added a tool to the Alpine PhotoTile for plugin that generates the shortcode for you. Visit the "Shortcode Generator" on the Flickr plugin's settings page (*Settings->AlpineTile: Flickr->Shortcode Generator*).

== Frequently Asked Questions ==

**I'm getting the message "Flickr feed was successfully retrieved, but no photos found". What does that mean?**

This message simply means that while no distinguishable errors occurred, the plugin found your Flickr feed to be empty. This might occur if you set the plugin source to Favorites, but you have not actually "favorited" any of your photos.

**I'm getting the message "Flickr feed not found. Please recheck your ID". What does that mean?**

This message can mean two things. First, it can indicate that the Flickr user ID, group ID, or set ID were input incorrectly, causing the Flickr feed to fail. In this case, you should try to correct and re-save your IDs.
Second, this message can also mean that the server your WordPress site is being hosted on has prevented the feed from being retrieved. While it is rare, we have encountered web-hosts that disable the feed fetching functions used in the PhotoTile for Flickr plugin. If this is the case, there is nothing we can do to override or work around the settings on your host server.

**Can I insert the Flickr plugin in posts or pages? Is there a shortcode function?**

Yes, rather than explaining how to setup the shortcode, I've created a method of generating the shortcode. Check out the Shortcode Generator on the plugin's settings page ( Settings->AlpineTile: Flickr->Shortcode Generator).

**Why doesn't the widget show my most recent photos?**

The plugin caches or stores the Flickr photo feed for three hours or the time set on the Settings->AlpineTile: Flickr->Plugin Settings page (see Caching above).  If the new photos have still not appeared after this time, it is possible that Flickr is responsible for the delay. While Flickr is fairly prompt about updating photo feeds, periods of high traffic (especially on weekdays between 10am and 4pm) can cause a delay in feed updates.

**Can I put captions below the photos?**

No, I have not yet found a good way to add captions to the images, but I am working on it.

**Why does it take so long for the Flickr plugin to load?**

The Apline PhotoTile for Flickr plugin actually takes less than a second to load. The reason you may see the loading icon for several seconds is because the plugin is programmed to wait until all the images and the rest of the webpage are done loading before displaying anything. The intent is for the plugin to avoid slowing down your website by waiting patiently for everything else to finish loading. If you are still looking to speed up your website's loading time, selecting smaller photo sizes should always help.

**The Flickr plugin works in display mode but when I put the shortcode in my page, nothing happens and there is no error message. What's wrong?**

A number of users have reported this problem and unfortunately I am not sure exactly what is going wrong. However, one simple fix has been to go to the Flickr plugin's settings page  (Settings->AlpineTile: Flickr->Plugin Settings) and put a check next to the option "Always Load Styles and Scripts in Header".

**What is an API Key and why do I need one?*

Photo sharing websites like Flickr want to protect their users and to prevent abuses by keeping track of how their services are being used. Two of the ways that Flickr does this is by assigning API Keys to plugins, like the Alpine PhotoTile, to keep track of who is who and by limiting the number of times a plugin can talk to the Flickr network. While several hundred websites could share an API Key without reaching this limit, the Alpine PhotoTile for Flickr plugin has become popular enough that users now need API Keys of their own. A Flickr API Key is free and easy to get. Because the Flickr plugin uses multiple methods of talking with the Flickr network, signing up for a Flickr API Key is optional. However, users without a Flickr API Key will experience the following limitations:
* Flickr image size options limited to 75px, 240px, 500px, and 800px.
* "Photo Offset" option will not work.
* "Shuffle/Randomize Photos" option will not work.
* Lack of helpful error messages if something does not work.
* Possibly slower plugin loading time (It is hard to tell).
* Future options added to the plugin will likely require a Flickr API Key.

If you have any more questions, please leave a message at [the Alpine Press](http://thealpinepress.com/alpine-phototile-for-flickr/ "Flickr Plugin Demo").
I am a one-man development team and I distribute these plugins for free, so please be patient with me.

== Changelog ==

= 1.0.0 =
* First Release

= 1.0.1 =
* Added caching functions

= 1.0.2 =
* Fixed AJAX menu plugin loading problem

= 1.0.3 =
* Rebuilt photo retrieval method using API
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
* Implemented Flickr fetch with wp_remote_get()

= 1.2.1 =
* Rebuilt admin div structure
* Fixed admin css issues

= 1.2.2 =
* Added 800px photo size
* Added aspect ratio options for gallery style
* Added key generator function
* Added get_image_url() functions
* Object oriented id, options, results, and output storage
* Object oriented display generation

= 1.2.3 =
* Added FancyboxForAlpine (Fancybox Safemode)
* Added choice between Fancybox, prettyBox, and ColorBox
* Added hidden options, including custom rel for lightbox
 
= 1.2.3.1 =
* Fixed cache retrieval

= 1.2.4 =
* Added "Add API Key" page and API Key option
* Restructured plugin objects and reassinged functions
* Object oriented message, hidden, etc.
* Added option to disable right-clicking on images
* Added updateGlobalOptions and removed individual option calls
* Added donate button
* Fixed lightbox param option

= 1.2.5 =
* Added fallback to dynamic style and script loading using jQuery
* Various small fixes
* Moved cache location
* Updated ColorBox plugin
* Set Object params to private and implemeted set, check, and get function
* Implemeted do_alpine_method call
* Created active options and results functions
* Improved dynamic script loading

= 1.2.6 =
* Fixed jQuery bug (Removed all <> tags from inline scripts)
* Add stripslashes text sanitization
* Changed lightbox parameters option from CSS to stripslashes sanitization

= 1.2.6.1  =
* Check compatibility with WP 3.8
* Small CSS changes (Padding and hover white background)

= 1.2.6.2  =
* Replaced deprecated jQuery APIs  ( .load() and .browser )
* Updated prettyPhoto and colorbox

= 1.2.6.4  =
* jQuery backwards compatibility ( .bind() function for jQuery v1.6.3 and less )

= 1.2.6.6  =
* Change http: to https:

= 1.2.6.7 =
* Load files before APTFFbyTAP_widget_register()

= TODO =
* Change to FancyBox 2
* Add caption to display
* Rebuild jQuery display
* "Anded" Tag option
* Check with Contact Form 7
* Look into showing private photos