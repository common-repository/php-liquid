<?php
wp_register_style( 'my-admin-liquid-style', plugins_url('admin_php_liquid.css', __FILE__) );
wp_enqueue_style('my-admin-liquid-style');

global $wpdb;

wp_enqueue_script('jquery');
wp_enqueue_script('my-application');

?>

	<div class="wrap">
		<?php

			function update_php_liquid_check_option($checkbox_field_name, $option_name) {
				if ($_POST[$checkbox_field_name] == "on") {
					update_option($option_name, "checked");
				} else {
					update_option($option_name, "");
				}
			}

			if ($_POST["submit"] == "Update") {
				?>
				<div id="message" class="updated below-h2">
					<p>Settings Updated</p>
				</div>
				<?php

				update_php_liquid_check_option("compress_css", "php_liquid_compress_css");
				update_php_liquid_check_option("compress_js", "php_liquid_compress_js");
			}
		?>


		<h2>Settings</h2>
		<div class="postbox " style="display: block; ">
		<div class="inside">
		<form action="" method="post">
			<table>
				<tbody>	
					<tr>
						<th scope="row" style="width: 150px;"><label for="compress_css">Compress Liquid CSS</label></th>
						<td><input type="checkbox" name="compress_css" id="compress_css" <?php echo(get_option("php_liquid_compress_css")); ?> /></td>
					</tr>
					<tr>
						<th scope="row" style="width: 150px;"><label for="compress_js">Compress Liquid Javascript</label></th>
						<td><input type="checkbox" name="compress_js" id="compress_js" <?php echo(get_option("php_liquid_compress_js")); ?> /></td>
					</tr>
				
				</tbody>
			</table>
			<p class="submit">
				<input type="submit" name="submit" value="Update">
			</p>
		</form>
		</div>
		</div>
	
	</div>
<?php tom_add_social_share_links("http://wordpress.org/extend/plugins/php-liquid/"); ?>