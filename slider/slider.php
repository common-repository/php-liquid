<?php if (isset($_POST["sort_order"]) && isset($_POST["id"])) { 
	global $wpdb;
	$slide_table_name = $wpdb->prefix."php_liquid_slides";
	$wpdb->update($slide_table_name, array('sort_order' => $_POST["sort_order"]), array('id' => $_POST['id']));
} else { 
	wp_register_style( 'my-admin-liquid-style', get_option("siteurl").'/wp-content/plugins/php-liquid/admin_php_liquid.css' );
	wp_enqueue_style('my-admin-liquid-style');
	
	if ($_GET["disable_ckeditor"] == "yes") {
		update_option("php_liquid_ckeditor_enabled", "no");
	} else if ($_GET["disable_ckeditor"] == "no") { 
		update_option("php_liquid_ckeditor_enabled", "yes");
	} 

	if (get_option("php_liquid_ckeditor_enabled") == "yes") { 
		if (function_exists('include_ckeditor_with_jquery_js')) {
			include_ckeditor_with_jquery_js();
		}	
		?>
		<script type="text/javascript">
			window.onload = function()
			{
				try{
					CKEDITOR.replace( 'content' );
				} catch(e) {}
			};
		</script>
	<?php }

	?>
	<style>
	table input {
		width: 700px;
	}
	</style>
	<?php

		global $wpdb;

		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-sortable');
		wp_enqueue_style('thickbox');
		wp_enqueue_script('media-upload');
		wp_enqueue_script('thickbox');
		wp_register_script('my-upload', WP_PLUGIN_URL, array('jquery','media-upload','thickbox'));
		wp_enqueue_script('my-upload');
		wp_enqueue_script( 'my-application', plugin_dir_url( __FILE__ ) . 'applicationjs.php', array( 'jquery' ) );

		$variables_table_name = $wpdb->prefix . "php_liquid_variables";
		$sliders_table_name = $wpdb->prefix."php_liquid_sliders";
		$slide_table_name = $wpdb->prefix."php_liquid_slides";

		$name_cannot_be_blank_error = false;
		$name_unique_error = false;

		if ($_POST["submit"] == "Update CSS") {
			$content = str_replace('\"', "\"", $_POST["css_content"]);
			$content = str_replace("\'", '\'', $content);
			update_option( "php_liquid_slider_css",str_replace('\"', "\"", $content));
		} else if ($_POST["name"] == "" && ($_POST["submit"] == "Add Slider" || $_POST["submit"] == "Update Slider")) {
			$name_cannot_be_blank_error = true;
		} else if ((($wpdb->get_row($wpdb->prepare("SELECT * FROM $variables_table_name WHERE liquid_tag_name = %s", str_replace(" ", "_", strtolower($_POST["name"]))."_slide"))) || ($wpdb->get_row($wpdb->prepare("SELECT * FROM $sliders_table_name WHERE liquid_tag_name = %s AND id <> %d", str_replace(" ", "_", strtolower($_POST["name"]))."_slide", $_POST["id"])))) && ($_POST["submit"] == "Add Slider" || $_POST["submit"] == "Update Slider")) {
			$name_unique_error = true;
		} else if ($_POST["submit"] == "Add Slider") {
			$rows_affected = $wpdb->insert( $sliders_table_name, array( 'name' => $_POST["name"], 'liquid_tag_name' => str_replace(" ", "_", strtolower($_POST["name"]))."_slide") );
			$new_id = $wpdb->insert_id;
			wp_redirect(get_option("siteurl")."/wp-admin/admin.php?page=php-liquid/slider/slider.php&add_slide_to=$new_id");  exit;
		} else if ($_POST["submit"] == "Update Slider") {
			$wpdb->update($sliders_table_name, array('name' => $_POST["name"], 'liquid_tag_name' => str_replace(" ", "_", strtolower($_POST["name"]))."_slide"), array('id' => $_POST['id']));
		} else if ($_POST["submit"] == "Add Slide") {
			$rows_affected = $wpdb->insert( $slide_table_name, array( 'name' => $_POST["name"], 'background_image_url' => $_POST["background_image_url"], 'url' => $_POST["url"], 'content' => $_POST["content"], 'slider_id' => $_POST['slider_id']) );
		} else if ($_POST["submit"] == "Update Slide") {
			$content = str_replace('\"', "\"", $_POST["content"]);
			$content = str_replace("\'", '\'', $content);
			$wpdb->update($slide_table_name, array( 'name' => $_POST["name"], 'background_image_url' => $_POST["background_image_url"],'url' => $_POST["url"], 'content' => $content), array('id' => $_POST['id']));
		} 
		if (isset($_GET["delete_slider_id"])) {
			$wpdb->query($wpdb->prepare("DELETE FROM $sliders_table_name WHERE id = %d", $_GET["delete_slider_id"]));
		}
		if (isset($_GET["delete_slide_id"])) {
			$wpdb->query($wpdb->prepare("DELETE FROM $slide_table_name WHERE id = %d", $_GET["delete_slide_id"]));
		}

		$edit_slide = $wpdb->get_row($wpdb->prepare("SELECT * FROM $slide_table_name WHERE id = %d", $_GET["edit_slide"]));
		$sliders = $wpdb->get_results("SELECT * FROM $sliders_table_name");
		$slides = $wpdb->get_results($wpdb->prepare("SELECT * FROM $slide_table_name WHERE slider_id = %d ORDER BY sort_order, id", $_GET["add_slide_to"]));

		$slider_id = isset($_GET["edit_slider_id"]) ? $_GET["edit_slider_id"] : $edit_slide->slider_id;
		if ($slider_id == null && isset($_GET["add_slide_to"])) {
			$slider_id = $_GET["add_slide_to"];
		}
		$edit_slider = $wpdb->get_row($wpdb->prepare("SELECT * FROM $sliders_table_name WHERE id = %d", $slider_id));

	?>
		
		<script language="javascript">
			jQuery(document).ready(function() {
	      jQuery( "#sortable" ).sortable({
	      	update: function( event, ui ) {

	      		jQuery("#sortable tr").each(function() {
		      		jQuery.ajax({
							  type: 'POST',
							  url: "<?php echo(get_option('siteurl')); ?>/wp-admin/admin.php?page=php-liquid/slider/slider.php",
							  data: {id: jQuery(this).attr("id"), sort_order: jQuery(this).index()}
							});
	      		});

	    			jQuery("table.data tr").removeClass("odd").removeClass("even");
	    		  jQuery("table.data tr:odd").addClass("odd");
						jQuery("table.data tr:even").addClass("even");
	      	}
	      });
	      jQuery( "#sortable" ).disableSelection();
				jQuery('#upload_image_button').click(function() {
				 formfield = jQuery('#background_image_url').attr('name');
				 tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true', '', function(){jQuery("body").hide();});
				 return false;
				});
				 
				window.send_to_editor = function(html) {
				 imgurl = jQuery('img',html).attr('src');
				 jQuery('#background_image_url').val(imgurl);
				 tb_remove();
				}
			 
			});
		</script>

		<div class="wrap">
		<?php 
			$var_name = $_POST['name'];
			$name_unique_error_message = "<p>Sorry the liquid variable $var_name already exists. Please choose another name.</p>";
			if ($edit_slider) {
				$var_name = $edit_slider->name;
			}
			$common_slider_msg = "Success. To use copy and paste {{".(str_replace(" ", "_", strtolower($var_name)))."_slide}} into your post/page file.";
		?>

		<?php if ($_POST["submit"] == "Update CSS") { ?>
			<div id="message" class="updated below-h2">
				<p>Updated css file</p>
			</div>
		<?php } else if ($name_cannot_be_blank_error) { ?>
			<div id="message" class="updated below-h2">
				<p>Name cannot be blank.</p>
			</div>
		<?php } else { ?>
			<?php if ($_POST["submit"] == "Add Slider") { ?>
			<div id="message" class="updated below-h2">
				<?php if ($name_unique_error) {
					echo $name_unique_error_message;
				} else { ?>
					<p><?php echo ($common_slider_msg); ?></p>
				<?php } ?>
			</div>
			<?php } else if ($_POST["submit"] == "Update Slider") { ?>
			<div id="message" class="updated below-h2">
				<?php if ($name_unique_error) { 
					echo $name_unique_error_message;
				} else { ?>
					<p><?php echo ($common_slider_msg); ?></p>
				<?php } ?>
			</div>
			<?php } else if ($_POST["submit"] == "Add Slide") { ?>
				<div id="message" class="updated below-h2"><p><?php echo ($common_slider_msg); ?></p></div>
			<?php } else if ($_POST["submit"] == "Update Slide") { ?>
				<div id="message" class="updated below-h2"><p><?php echo ($common_slider_msg); ?></p></div>
			<?php } ?>
		<?php } ?>

		<h2>

			<?php 
				if ($edit_slider->name) {
					echo($edit_slider->name)." Slides";
				} else {
					echo ("PHP Liquid Sliders");
				}
			?>
			<?php if ((isset($_GET["action"]) && $_GET["action"] == "Add_Slider") || (isset($_GET["edit_slider_id"])) || ($edit_slide || (isset($_GET["add_slide_to"])))) { ?>
				<a class="add-new-h2" href="<?php echo(get_option('siteurl')); ?>/wp-admin/admin.php?page=php-liquid/slider/slider.php">Slider List</a>
			<?php } else { ?>
				<a class="add-new-h2" href="<?php echo(get_option('siteurl')); ?>/wp-admin/admin.php?page=php-liquid/slider/slider.php&action=Add_Slider">Add Slider</a>
			<?php } ?>

			<?php if ($edit_slide || (isset($_GET["add_slide_to"]))) { ?>
				<?php if (($edit_slide && (isset($_GET["add_slide_to"]))) || $_GET["action"] == "Add_Slide") { ?>
					<a class="add-new-h2" href="<?php echo(get_option('siteurl')); ?>/wp-admin/admin.php?page=php-liquid/slider/slider.php&add_slide_to=<?php echo($slider_id); ?>"><?php echo($edit_slider->name); ?> Slides</a>
				<?php } else { ?>
					<?php 
						$slider_name = $wpdb->get_row($wpdb->prepare("SELECT * FROM $sliders_table_name WHERE id = %d", $_GET["add_slide_to"]))->name; 
						$slider_id = $_GET["add_slide_to"];
					?>
					<a class="add-new-h2" href="<?php echo(get_option('siteurl')); ?>/wp-admin/admin.php?page=php-liquid/slider/slider.php&add_slide_to=<?php echo($slider_id); ?>&action=Add_Slide">Add <?php echo($edit_slider->name); ?> Slide</a>
				<?php } ?>
			<?php } ?>

		</h2>

		<?php if (!isset($_GET["add_slide_to"])) { ?>

			<?php if (($edit_slider) || (isset($_GET["action"]) && $_GET["action"] == "Add_Slider")) { ?>
				<div class="postbox " style="display: block; ">
				<div class="inside">
				<form action="" method="post">
						<?php if ($edit_slider) { ?>
							<input type="hidden" name="id" value="<?php echo($edit_slider->id); ?>">
						<?php } ?>
						<table>
							<tbody>	
								<th scope="row">
									<label for="name">Name</label>
								</th>
								<tr>
									<td><input id="name" type="text" name="name" value="<?php echo($edit_slider->name); ?>" /></td>
								</tr>
							</tbody>
						</table>
					<p class="submit">
						<?php if ($edit_slider) { ?>
							<input type="submit" name="submit" value="Update Slider">
						<?php } else { ?>
							<input type="submit" name="submit" value="Add Slider">
						<?php } ?>
					</p>
				</form>
				</div>
				</div>
			<?php } else { ?>

				<p>To use a liquid variable in your post/page, copy and paste the liquid tag into your post/page.</p>
				<div class="postbox " style="display: block; ">
				<div class="inside">
					<?php if ($sliders) { ?>
						<table class="data">
							<thead>
								<tr>
									<th>Slide Name</th>
									<th>Liquid Tag</th>
								</tr>
							</thead>
							<tbody>	
								<?php foreach ($sliders as $slide) { ?>
									<tr>
										<td style="width: 300px;"><?php echo($slide->name); ?></td>
										<td style="width: 300px;">{{<?php echo(str_replace(" ", "_", strtolower($slide->name))); ?>_slide}}</td>
										<td><a href="<?php echo(get_option('siteurl')); ?>/wp-admin/admin.php?page=php-liquid/slider/slider.php&add_slide_to=<?php echo($slide->id); ?>">Slides</a></td>
										<td><a href="<?php echo(get_option('siteurl')); ?>/wp-admin/admin.php?page=php-liquid/slider/slider.php&edit_slider_id=<?php echo($slide->id); ?>">Edit</a></td>
										<td><a class="delete" href="<?php echo(get_option('siteurl')); ?>/wp-admin/admin.php?page=php-liquid/slider/slider.php&delete_slider_id=<?php echo($slide->id); ?>">Delete</a></td>
									</tr>
								<?php } ?>
							</tbody>
						</table>
					<?php } else { ?>
						<p>You don't have any sliders yet. To add a slider click on the Add Slider link above.</p>
					<?php } ?>
				</div>
				</div>
			<?php } ?>
		<?php } else { ?>


			<?php if (($edit_slide) || (isset($_GET["action"]) && $_GET["action"] == "Add_Slide")) { ?>
				<div class="postbox " style="display: block; ">
				<div class="inside">
				<form action="" method="post">
						<input type="hidden" name="id" value="<?php echo($_GET['edit_slide']); ?>" />
						<input type="hidden" name="slider_id" value="<?php echo($_GET['add_slide_to']); ?>" />
						<table>
							<tbody>
								<th scope="row">
									<label for="name">Name:</label>
								</th>	
								<tr>
									<td><input id="name" type="text" name="name" value="<?php echo($edit_slide->name); ?>" /></td>
								</tr>
								<tr>
									<th scope="row"><label for="background_image_url">Upload Background Image</label></th>
								</tr>
								<tr>
									<td>
									<input id="background_image_url" type="text" size="36" name="background_image_url" value="<?php echo($edit_slide->background_image_url); ?>" />
									<input id="upload_image_button" type="button" value="Upload Image" />
									<br />Enter an URL or upload an image.
									</td>
								</tr>
								<th scope="row">
									<label for="url">After clicking on slide, redirect user to url:</label>
								</th>
								<tr>
									<td><input id="url" type="text" name="url" value="<?php echo($edit_slide->url); ?>" /></td>
								</tr>
								
								<tr>
									<th scope="row">
									<label for="content">Content</label>
									</th>
								</tr>
								<tr>
									<td><a href="<?php echo(get_option("siteurl")); ?>/wp-admin/admin.php?page=php-liquid/slider/slider.php&edit_slide=<?php echo($_GET["edit_slide"]) ?>&add_slide_to=<?php echo($_GET["add_slide_to"]) ?>&disable_ckeditor=<?php echo(get_option("php_liquid_ckeditor_enabled")); ?>">Turn ckeditor <?php echo((get_option("php_liquid_ckeditor_enabled") == "yes") ? "off" : "on"); ?></a> <span style="margin-left: 40px;">You are allowed to use the {{site_url}} liquid tag in the content area.</span></td>
								</tr>
								<tr>
									<td><textarea id="content" name="content" class="wp-editor-area" rows="8" cols="150"><?php echo($edit_slide->content); ?></textarea></td>
								</tr>
							</tbody>
						</table>
					<p class="submit">
						<?php if ($_GET['edit_slide']) { ?>
							<input type="submit" name="submit" value="Update Slide">
						<?php } else { ?>
							<input type="submit" name="submit" value="Add Slide">
						<?php } ?>
					</p>
				</form>
				</div>
				</div>
			<?php } else { ?>

				<div class="postbox " style="display: block; ">
				<div class="inside">
					<?php if ($slides) { ?>
						<table class="data">
							<thead>
								<tr>
									<th></th>
									<th scope="col">Name</th>
									<th scope="col">Background Image URL</th>
								</tr>
							</thead>
							<tbody id="sortable">	
								
									<?php foreach ($slides as $slide) { ?>
										<tr id="<?php echo($slide->id); ?>">
											<td class="shiftable"></td>
											<td><?php echo($slide->name); ?></td>
											<td style="width: 600px;"><?php echo($slide->background_image_url); ?></td>
											<td><a href="<?php echo(get_option('siteurl')); ?>/wp-admin/admin.php?page=php-liquid/slider/slider.php&edit_slide=<?php echo($slide->id); ?>&add_slide_to=<?php echo($_GET['add_slide_to']); ?>">Edit</a></td>
											<td><a class="delete" href="<?php echo(get_option('siteurl')); ?>/wp-admin/admin.php?page=php-liquid/slider/slider.php&delete_slide_id=<?php echo($slide->id); ?>&add_slide_to=<?php echo($_GET['add_slide_to']); ?>">Delete</a></td>
										</tr>
									<?php } ?>

							</tbody>
						</table>
					<?php } else { ?>
						<p>You don't have any slides yet for this slider. To add a slide click on the Add link above.</p>
					<?php } ?>
				</div>
				</div>

			<?php } ?>

		<?php } ?>

		<h2>PHP Liquid Slider CSS</h2>
		<div class="postbox " style="display: block; ">
		<div class="inside">
		<form action="" method="post">
			<table>
				<tbody>	
					<tr>
						<th scope="col"><label for="css_content">CSS</label></th>
					</tr>
					<tr>
						<td><textarea id="css_content" name="css_content" cols="110" rows="9"><?php echo(get_option("php_liquid_slider_css")); ?></textarea></td>
					</tr>
				</tbody>
			</table>
			<p><input type="submit" name="submit" value="Update CSS"></p>
		</form>
		</div>
		</div>

	</div>

<?php 
	tom_add_social_share_links("http://wordpress.org/extend/plugins/php-liquid/");
	} ?>