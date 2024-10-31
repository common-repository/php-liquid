=== Plugin Name ===
Contributors: MMDeveloper
Tags: php, liquid, php_liquid, slider, javascript
Requires at least: 3.0.1
Tested up to: 3.5.1
Stable tag: 2.7.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Use liquid markup in your posts/pages/stylesheets.

== Description ==

PHP Liquid is a WordPress plugin that allows you to add liquid tags to your stylesheet and page/posts. 

After version 2.0, I've even included a handy GUI interface which allows you to create and edit your liquid tag variables.

As part of this plugin, you can also create an image slider using liquid.



To find out what liquid is please go to the following url:

http://liquidmarkup.org/

Please read the install instructions carefully. You need to replace bloginfo( 'stylesheet_url' ) with liquid_stylesheet_url() in your header.php if you want to use liquid in your stylesheet.



If you write this method in your theme functions file:

function override_mytheme_liquid_array() {

return array('site_url' => get_option('siteurl'), "items" => array("1","2"));

}


and write the following lines in your style.css file:

{% for item in items %}

  #banner_{{ item }} {

    background: url("/images/test_{{ item }}.png");

  }

{% endfor %}

your website will actually render the following css:

#banner_1 {

  background: url("/images/test_1.png");

}

#banner_2 {

  background: url("/images/test_2.png");

}

So with this plugin, you can use dynamic css, cool hey.


Introduced in version 1.4 - You can now write:

echo(parse_liquid("{{site_url}}"));

into your template, and render liquid tags in your template.



== Installation ==

1) Install WordPress 3.5 or higher

2) Download the latest from:

http://wordpress.org/extend/plugins/tom-m8te 

http://wordpress.org/extend/plugins/php-liquid

3) Login to WordPress admin, click on Plugins / Add New / Upload, then upload the zip file you just downloaded.

4) Activate the plugin.

5) Edit your header.php and find bloginfo( 'stylesheet_url' ). Replace this with liquid_stylesheet_url().

6) Thats it, but if you want your own liquid tags, in your functions.php file write the following method

function override_mytheme_liquid_array() {  

  // Declare your liquid tags in this array.  

  return array('site_url' => get_option('siteurl'), "dude" => "TheOnlineHero"); 

  // Then in your post/page or stylesheet type {{site_url}}, {{dude}}, to use them.

  // Example:

  // body {

  //   background: url({{site_url}}/images/test_image.png);

  // }

} 


== Upgrade Notice ==

= 2.7.0 =

* Improved the plugin dependency checker.

= 2.6.2 =

* I found out that the widgets section was breaking other plugins. Its really a hack and not important so its getting removed.

= 2.6.1 =

* Able to add sliders using page editor.

= 2.6 =

* Added pagination to my liquid variables.

= 2.5 =

* Fixed style and javascript references and bugs with ckeditor.

= 2.4.1 =

* Fixed bug with class names.

= 2.4 =

* Able to render widget content using liquid tags. So now you can add widgets to your posts and pages.

= 2.3.1 =

* After upgrading, make sure you deactivate and reactivate the plugin. Hopefully after this version you won't have to reactive to upgrade again.

* Ability to control css/js compression.

* Ability to add jquery and jquery ui as part of php liquid plugin. For example, another plugin may include jquery and perhaps you don't want to include the same library twice. 

= 2.3 =

* After upgrading, make sure you deactivate and reactivate the plugin.

* Able to sort slides and better notification messages.

= 2.2.2 =

* After upgrading, make sure you deactivate and reactivate the plugin.

= 2.0 =

* Before upgrading to 2.1, make sure you backup your slider css, the location is different since version 2.0.


== Changelog ==

= 2.7.0 =

* Improved the plugin dependency checker.

= 2.6.2 =

* I found out that the widgets section was breaking other plugins. Its really a hack and not important so its getting removed.

= 2.6.1 =

* Able to add sliders using page editor.

= 2.6 =

* Added pagination to my liquid variables.

= 2.5 =

* Fixed style and javascript references and bugs with ckeditor.

= 2.4.1 =

* Fixed bug with class names.

= 2.4 =

* Able to render widget content using liquid tags. So now you can add widgets to your posts and pages.

= 2.3.1 =

* After upgrading, make sure you deactivate and reactivate the plugin. Hopefully after this version you won't have to reactive to upgrade again.

* Ability to control css/js compression.

* Ability to add jquery and jquery ui as part of php liquid plugin. For example, another plugin may include jquery and perhaps you don't want to include the same library twice. 

= 2.3 =

* After upgrading, make sure you deactivate and reactivate the plugin.

* Able to sort slides and better notification messages.

= 2.2.2 =

* Allows you to turn ckeditor off for sliders.

= 2.2.1 =

* Allows you to add your own liquid tags using code to the plugin's liquid tags. Just define a method called add_mytheme_liquid_array() in your theme functions file and make sure it returns a liquid array. Here is an example:

function add_mytheme_liquid_array() {
  
  return array('case_studies_array' => ["1","2","3","4","5","6","7"]); 

}

So basically its like the override_mytheme_liquid_array() function, except you get to keep the plugin's liquid tags as well.

= 2.2 =

* Allows you to create a js file that can use your liquid tag variables. Made the UI for slider easier to understand. Ability to remove auto slide by using javascript to remove the "auto-slide" class.

* If you have a slider called Car, you can add the following line to your liquid js file to stop it from auto sliding

jQuery("#car").removeClass("auto-slide");

= 2.1.2 =

* Changed compression, so only compresses css and slider js. Previous version broke youtube plugin.

= 2.1.1 =

* Fixed bug with compression.

= 2.1 =

* Fix bugs - change location of slider css file, better compression for both css and posts.

= 2.0 =

* Handy GUI for managing your Liquid variables.

* Ability to create a image slider.

= 1.4 =

* You can now render liquid in your templates

* echo(parse_liquid("{{site_url}}"));

= 1.3 =

* Minimise css

= 1.2 =

* Fixed stylesheet url

= 1.1 =

* Initial Checkin


