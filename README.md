php_liquid
==========

PHP Liquid is a WordPress plugin that allows you to add liquid tags
to your stylesheet and page/posts.

Installation:

1) Install WordPress 3.4.2 or higher

2) Download the following file:
https://github.com/TheOnlineHero/php_liquid/zipball/master

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
