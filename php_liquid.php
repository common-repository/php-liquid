<?php
/*
Plugin Name: PHP Liquid
Plugin URI: http://wordpress.org/extend/plugins/php-liquid/
Description: PHP Liquid is a WordPress plugin that allows you to add liquid tags
to your stylesheet and page/posts.

Version: 2.7.0
Author: TheOnlineHero - Tom Skroza
License: GPL2
*/

require_once('slider/slidejs.php');
include_once (dirname (__FILE__) . '/tinymce/tinymce.php'); 

add_action('admin_menu', 'register_php_liquid_slider');

function register_php_liquid_slider() {
	if (are_php_liquid_dependencies_installed()) {
	  add_menu_page('PHP Liquid', 'PHP Liquid', 'update_themes', 'php-liquid/php_liquid_variables.php', '',  '');
	  add_submenu_page('php-liquid/php_liquid_variables.php', 'Liquid Slider', 'Liquid Slider', 'update_themes', 'php-liquid/slider/slider.php', '',  '');
	  add_submenu_page('php-liquid/php_liquid_variables.php', 'Liquid JS', 'Liquid JS', 'update_themes', 'php-liquid/php_liquid_applicationjs.php', '', '');
	  add_submenu_page('php-liquid/php_liquid_variables.php', 'Settings', 'Settings', 'update_themes', 'php-liquid/php_liquid_settings.php', '', '');
	}
}

function php_liquid_activate() {
   
  global $wpdb;

  $table_name = $wpdb->prefix . "php_liquid_variables";

  $sql = "CREATE TABLE $table_name (
id mediumint(9) NOT NULL AUTO_INCREMENT, 
name VARCHAR(255) DEFAULT '', 
liquid_tag_name VARCHAR(255) DEFAULT '', 
value VARCHAR(255) DEFAULT '',
deletable mediumint(9) DEFAULT 1,
PRIMARY KEY  (id)
);";
  
  $wpdb->query($sql);

  $table_name = $wpdb->prefix . "php_liquid_sliders";

  $sql = "CREATE TABLE $table_name (
id mediumint(9) NOT NULL AUTO_INCREMENT, 
name VARCHAR(255) DEFAULT '', 
liquid_tag_name VARCHAR(255) DEFAULT '', 
PRIMARY KEY  (id)
);";
    
  $wpdb->query($sql);

  $table_name = $wpdb->prefix . "php_liquid_slides";

  $sql = "CREATE TABLE $table_name (
id mediumint(9) NOT NULL AUTO_INCREMENT, 
background_image_url VARCHAR(255) DEFAULT '', 
url VARCHAR(255) DEFAULT '',
content  longtext NOT NULL,
slider_id mediumint(9) NOT NULL,
PRIMARY KEY  (id)
);";

  $wpdb->query($sql);

  create_default_tags_if_they_dont_exist("Delay time before start slider", "delay_time_before_start_slider", "8000");
  create_default_tags_if_they_dont_exist("Delay between slides", "delay_between_slides", "4000");
  create_default_tags_if_they_dont_exist("Fade out delay", "fade_out_delay", "4000");
  create_default_tags_if_they_dont_exist("Fade in delay", "fade_in_delay", "4000");

  if (get_option("php_liquid_slider_css") == "") {
    add_option( "php_liquid_slider_css", "", "", "yes" );
    update_option( "php_liquid_slider_css", ".slide_show_container {\n height: 0;\n }\n .slide_show_container, .slide_show_container .slide {\n height: 200px;\n width: 200px; }\n .slide_show_container .slide {\n position: absolute;\n display: none;\n width: 100%;\n}\n .slide_show_container .slide.first {\n display: block;\n   }\n  .feature-controls div{\n position:relative;\n float:left;\n }\n  .entry-content .feature-controls p, .entry-content .feature-controls span {\n margin-bottom: 0;\n }\n  .feature-controls-inner {\n background: url(http://127.0.0.1/marketingmix/wp-content/uploads/2012/08/bg_box.png);\n border-radius: 12px;\n padding: 5px;\n }\n  .feature-block {\n overflow: inherit;\n position: static;\n }\n  .feature-desc {\n margin: 15px 0 25px 0;\n display: block;\n font-size: 130%;\n line-height: 140%;\n }\n  .feature-title {\n font-size: 35px;\n letter-spacing: normal;\n }\n  .feature-pad {\n margin-left: 25px;\n }\n  .feature-arrow-r {\n width: 7px;\n height: 10px;\n background-position: -22px -19px;\n top: 1px;\n }\n  .feature-arrow-l {\n width: 7px;\n height: 10px;\n background-position: 0 -19px;\n top: 1px;\n }\n  .arrowleft-hover {\n background-position: 0 -37px;\n }\n  .arrowright-hover {\n background-position: -22px -37px;\n }\n  .feature-story {\n width: 960px;\n }\n  .feature-controls {\n bottom: 14px;\n right: 18px;\n padding: 8px;\n border-radius: 12px;\n }\n  .feature-circles {\n margin: 0 auto;\n height: 12px;\n width: 51px;\n }\n  .feature-circles-sub {\n width: 12px;\n height: 12px;\n background-position: 0 0;\n background-repeat: no-repeat;\n margin: 0 2px;\n display: block;\n float: left;\n  cursor: pointer;\n  cursor: hand;\n }\n  .feature-circles-sub.active {\n background-position: -17px 0;\n }\n  .feature-controls {\n position: absolute;\n top:407px;\n float: none;\n margin: 0 auto;\n width: 167px;\n }\n  .feature-circles-sub, .feature-arrow-r, .feature-arrow-l {\n background-image: url(http://127.0.0.1/marketingmix/wp-content/plugins/php-liquid/images/slide-controls.png);\n cursor: pointer;\n cursor: hand;\n}\n  .feature-circles-sub.active {\n background-position: -17px 0;\n }\n  .slide {\n cursor: pointer;\n cursor: hand;\n }\n  .slide_show_container .text {\n width: 960px;\n margin: 0 auto;\n }\n  .slide_show_container .text p {\n color: #fff;\n }\n .feature-arrow-l {\n cursor: pointer;\n left: 0;\n position: absolute;\n }\n .feature-arrow-r {\n cursor: pointer;\n }\n #car_1, #car_2, #car_3{\n position: absolute;\n top: 133px;\n width: 936px;\n height: 318px;\n margin-left: 4px;\n}\n" );
  }

  if (get_option("php_liquid_application_js") == "") {
    add_option( "php_liquid_application_js", "", "", "yes" );
    update_option( "php_liquid_application_js", "jQuery(function() {\n\n});" );
  }

  add_option("php_liquid_ckeditor_enabled", "yes", "", "yes");

  $table_name = $wpdb->prefix . "php_liquid_slides";
  $sql = "ALTER TABLE $table_name ADD name VARCHAR(255)";
  $wpdb->query($sql);
  $sql = "ALTER TABLE $table_name ADD sort_order mediumint(9)";
  $wpdb->query($sql);


  add_option( "php_liquid_version", "2.3", "", "yes" );
}
register_activation_hook( __FILE__, 'php_liquid_activate' );

function check_php_liquid_version() {
  update_option("php_liquid_version", "2.4.1");
  if (str_replace(".","",get_option("php_liquid_version")) < "231") {
    if (get_option("php_liquid_version") == "2.3") {
      add_option("php_liquid_include_jquery", "checked", "", "yes");
      add_option("php_liquid_include_jquery_ui", "checked", "", "yes");
      add_option("php_liquid_compress_css", "checked", "", "yes");
      add_option("php_liquid_compress_js", "checked", "", "yes");
    }
  }
}
check_php_liquid_version();

function create_default_tags_if_they_dont_exist($name, $liquid_tag_name, $value) {
  global $wpdb;
  $table_name = $wpdb->prefix . "php_liquid_variables";
  // Check if tag is in database
  if (!$wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE liquid_tag_name = %s", $liquid_tag_name))) {
    // Its not so add it.
    $wpdb->insert( $table_name, array( 'name' => $name, 'liquid_tag_name' => $liquid_tag_name, 'value' => $value, 'deletable' => 0) );
  }
}

add_action('wp_ajax_php_liquid_sliders_tinymce', 'php_liquid_sliders_ajax_tinymce');
/**
 * Call TinyMCE window content via admin-ajax
 * 
 * @since 1.7.0 
 * @return html content
 */
function php_liquid_sliders_ajax_tinymce() {

    // check for rights
    if ( !current_user_can('edit_pages') && !current_user_can('edit_posts') ) 
      die(__("You are not allowed to be here"));
          
    include_once( dirname( dirname(__FILE__) ) . '/php-liquid/tinymce/window.php');
    
    die();  
}

function liquid_stylesheet_url() {
                echo get_option('siteurl')."/wp-content/plugins/php-liquid/style.php";
}
 
function liquid() {
  require_once('harrydeluxe-php-liquid/Liquid.class.php');
  define('PROTECTED_PATH', dirname(__FILE__).'/harrydeluxe-php-liquid/example/protected/');
  $liquid = new LiquidTemplate(PROTECTED_PATH.'templates/');
  $cache = array('cache' => 'file', 'cache_dir' => PROTECTED_PATH.'cache/');
  //$cache = array('cache' => 'apc');
  $liquid->setCache($cache);
  return $liquid;
}

function mytheme_liquid_content_filter( $content ) {
  return parse_liquid($content);
}
add_filter( 'the_content', 'mytheme_liquid_content_filter',2 );
 
function parse_liquid($content) {
    $liquid = liquid();
    $liquid->parse($content);
    $assigns = liquid_assigns();
    return $liquid->render($assigns);
}
 
function liquid_assigns() {
    if (function_exists( 'override_mytheme_liquid_array' )) {
      return override_mytheme_liquid_array();
    } else {
      global $wpdb;
      $variables_table_name = $wpdb->prefix."php_liquid_variables";
      $liquid_variables = $wpdb->get_results("SELECT * FROM $variables_table_name");
      $my_variables = array('site_url' => get_option('siteurl'));
      foreach ($liquid_variables as $liquid_variable) {
        $my_variables = array_merge($my_variables, array($liquid_variable->liquid_tag_name => $liquid_variable->value));
      }

      $sliders_table_name = $wpdb->prefix."php_liquid_sliders";
      $sliders = $wpdb->get_results("SELECT * FROM $sliders_table_name");

      foreach ($sliders as $slider) {
        $slide_content = preg_replace ("/\{\{(| *)site_url(| *)\}\}/", get_option("siteurl"), get_slide_show($slider->id));
        $my_variables = array_merge($my_variables, array(str_replace(" ", "_", strtolower($slider->name))."_slide" => $slide_content));
      }

      if (function_exists( 'add_mytheme_liquid_array' )) {
        $my_variables = array_merge($my_variables, add_mytheme_liquid_array());
      }

      // NOTE: The following code was causing havoc amongst other plugins, so its good bye for now.
      // global $wp_widget_factory;
      // foreach ($wp_widget_factory->widgets as $key => $value) {
      //   $widget_obj = $wp_widget_factory->widgets[$key];
      //   $before_widget = sprintf('<div class="widget %s">', $widget_obj->widget_options['classname'] );
      //   $default_args = array( 'before_widget' => $before_widget, 'after_widget' => "</div>", 'before_title' => '<h2 class="widgettitle">', 'after_title' => '</h2>' );
      //   $args = wp_parse_args($args, $default_args);
      //   $instance = wp_parse_args($instance);
      //   $widget_obj->_set(-1);
      //   ob_start(); // Start output buffering
      //   $widget_obj->widget($args, $instance);
      //   $widget_content = ob_get_contents();
      //   ob_end_clean(); // End buffering and clean up

      //   $my_variables = array_merge($my_variables, array($key => $widget_content));
      // }

      return $my_variables;
    }
}


function compress_content($content) {
  /* remove comments */
  $content = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $content);
  /* remove tabs, spaces, newlines, etc. */
  return str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), ' ', $content);
}

function are_php_liquid_dependencies_installed() {
  return is_plugin_active("tom-m8te/tom-m8te.php") && is_plugin_active("ckeditor-with-jquery/ckeditor-with-jquery.php");
}

add_action( 'admin_notices', 'php_liquid_notice_notice' );
function php_liquid_notice_notice(){
  $activate_nonce = wp_create_nonce( "activate-php-liquid-dependencies" );
  $tom_active = is_plugin_active("tom-m8te/tom-m8te.php");
  $ckeditor_active = is_plugin_active("ckeditor-with-jquery/ckeditor-with-jquery.php");
  if (!($tom_active && $ckeditor_active)) { ?>
    <div class='updated below-h2'><p>Before you can use PHP Liquid, please install/activate the following plugin(s):</p>
    <ul>
      <?php if (!$tom_active) { ?>
        <li>
          <a target="_blank" href="http://wordpress.org/extend/plugins/tom-m8te/">Tom M8te</a> 
           &#8211; 
          <?php if (file_exists(ABSPATH."/wp-content/plugins/tom-m8te/tom-m8te.php")) { ?>
            <a href="<?php echo(get_option("siteurl")); ?>/wp-admin/?php_liquid_install_dependency=tom-m8te&_wpnonce=<?php echo($activate_nonce); ?>">Activate</a>
          <?php } else { ?>
            <a href="<?php echo(get_option("siteurl")); ?>/wp-admin/plugin-install.php?tab=plugin-information&plugin=tom-m8te&_wpnonce=<?php echo($activate_nonce); ?>&TB_iframe=true&width=640&height=876">Install</a> 
          <?php } ?>
        </li>
      <?php }
      if (!$ckeditor_active) { ?>
        <li>
          <a target="_blank" href="http://wordpress.org/extend/plugins/ckeditor-with-jquery/">CKEditor With JQuery</a>
           &#8211; 
          <?php if (file_exists(ABSPATH."/wp-content/plugins/ckeditor-with-jquery/ckeditor-with-jquery.php")) { ?>
            <a href="<?php echo(get_option("siteurl")); ?>/wp-admin/?php_liquid_install_dependency=ckeditor-with-jquery&_wpnonce=<?php echo($activate_nonce); ?>">Activate</a>
          <?php } else { ?>
            <a href="<?php echo(get_option("siteurl")); ?>/wp-admin/plugin-install.php?tab=plugin-information&plugin=ckeditor-with-jquery&_wpnonce=<?php echo($activate_nonce); ?>&TB_iframe=true&width=640&height=876">Install</a> 
          <?php } ?>
        </li>
      <?php } ?>
    </ul>
    </div>
    <?php
  }

}

add_action( 'admin_init', 'register_php_liquid_install_dependency_settings' );
function register_php_liquid_install_dependency_settings() {
  if (isset($_GET["php_liquid_install_dependency"])) {
    if (wp_verify_nonce($_REQUEST['_wpnonce'], "activate-php-liquid-dependencies")) {
      switch ($_GET["php_liquid_install_dependency"]) {
        case 'ckeditor-with-jquery':
          activate_plugin('ckeditor-with-jquery/ckeditor-with-jquery.php', 'plugins.php?error=false&plugin=ckeditor-with-jquery.php');
          wp_redirect(get_option("siteurl")."/wp-admin/");
          exit();
          break; 
        case 'tom-m8te':  
          activate_plugin('tom-m8te/tom-m8te.php', 'plugins.php?error=false&plugin=tom-m8te.php');
          wp_redirect(get_option("siteurl")."/wp-admin/admin.php?page=php-liquid/php-liquid.php");
          exit();
          break;   
        default:
          throw new Exception("Sorry unable to install plugin.");
          break;
      }
    } else {
      die("Security Check Failed.");
    }
  }
}

add_action('wp_head', 'add_slider_js_and_css');
function add_slider_js_and_css() {
  wp_enqueue_script('jquery');
  wp_register_script( 'my-liquid-slider', plugin_dir_url( __FILE__ ) . 'slider/slidejs.php?slidecode=1', array( 'jquery' ) );
  wp_enqueue_script('my-liquid-slider');
  wp_register_script( 'my-application', plugin_dir_url( __FILE__ ) . 'applicationjs.php', array( 'jquery' ) );
  wp_enqueue_script('my-application');
  wp_register_script( 'my-jquery-ui', plugin_dir_url( __FILE__ ) . 'jquery-ui.js', array( 'jquery' ) );
  wp_enqueue_script('my-jquery-ui');
  wp_register_style( 'my-liquid-slider-style', plugins_url('slider/slidecss.php', __FILE__) );
  wp_enqueue_style('my-liquid-slider-style');  
}

?>