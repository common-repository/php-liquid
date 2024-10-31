<?php
	wp_register_style( 'my-admin-liquid-style', plugins_url('admin_php_liquid.css', __FILE__) );
	wp_enqueue_style('my-admin-liquid-style');

	if ($_POST["submit"] == "Update JS") {
		$content = str_replace('\"', "\"", $_POST["js_content"]);
		$content = str_replace("\'", '\'', $content);
		update_option( "php_liquid_application_js",str_replace('\"', "\"", $content));
	}
?>

<div class="wrap">
	<?php if ($_POST["submit"] == "Update JS") { ?>
		<div id="message" class="updated below-h2">
			<p>Updated js file</p>
		</div>
	<?php } ?>
	<h2>PHP Liquid JS</h2>
	<div class="postbox " style="display: block; ">
	<div class="inside">
	<form action="" method="post">
		<table>
			<tbody>	
				<tr>
					<th scope="col"><label for="js_content">JS</label></th>
				</tr>
				<tr>
					<td><textarea name="js_content" cols="110" rows="9"><?php echo(get_option("php_liquid_application_js")); ?></textarea></td>
				</tr>
			</tbody>
		</table>
		<p><input type="submit" name="submit" value="Update JS"></p>
	</form>
	</div>
	</div>

</div>

<?php tom_add_social_share_links("http://wordpress.org/extend/plugins/php-liquid/"); ?>