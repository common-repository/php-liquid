<?php

if ( !defined('ABSPATH') )
    die('You are not allowed to call this page directly.');

@header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>PHP Liquid Sliders</title>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
	<script language="javascript" type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/jquery/jquery.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/tinymce/utils/mctabs.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/jquery/ui/jquery.ui.core.min.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/jquery/ui/jquery.ui.widget.min.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo site_url(); ?>/wp-content/plugins/php-liquid/tinymce/tinymce.js"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo plugins_url("/css/style.css", __FILE__); ?>" media="all" />

  <base target="_self" />
</head>

<body id="link" onload="tinyMCEPopup.executeOnLoad('init();');document.body.style.display='';" style="display: none">
	
	<div class="panel_wrapper">
		<?php
		global $wpdb;
		$sliders_table_name = $wpdb->prefix."php_liquid_sliders";
		$sliders = $wpdb->get_results("SELECT * FROM $sliders_table_name");
		?>
		<p><label for="slider">Slider</label> <select id="slider" name="slider">
			<option value=""></option>
			<?php foreach ($sliders as $slider) { ?>
				<option value="{{<?php echo(str_replace(" ", "_", strtolower($slider->name))); ?>_slide}}"><?php echo($slider->name); ?></option>
			<?php }?>
		</select></p>
		<div class="mceActionPanel">
			<div id="cancel_php_liquid_slider">
				<input type="button" id="cancel" name="cancel_php_liquid_slider" value="<?php _e("Cancel", 'php_liquid_slider'); ?>" onclick="tinyMCEPopup.close();" />
			</div>
			<div id="insert_php_liquid_slider">
				<input type="submit" id="insert" name="insert_php_liquid_slider" value="<?php _e("Insert", 'php_liquid_slider'); ?>" onclick="insertPHPLiquidSlider();" />
			</div>
		</div>
	</div>
</body>
</html>