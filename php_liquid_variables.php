<?php
wp_register_style( 'my-admin-liquid-style', plugins_url('admin_php_liquid.css', __FILE__) );
wp_enqueue_style('my-admin-liquid-style');

global $wpdb;

wp_enqueue_script('jquery');

wp_enqueue_script( 'my-liquid-slider', plugin_dir_url( __FILE__ ) . 'admin_php_liquid.js', array( 'jquery' ) );

$variables_table_name = $wpdb->prefix."php_liquid_variables";
$sliders_table_name = $wpdb->prefix."php_liquid_sliders";

$name_cannot_be_blank_error = false;
$name_unique_error = false;

if ($_POST["name"] == "" && ($_POST["submit"] == "Add" || $_POST["submit"] == "Update")) {
	$name_cannot_be_blank_error = true;
} else if ((str_replace(" ", "_", strtolower($_POST["name"])) == "site_url" || ($wpdb->get_row($wpdb->prepare("SELECT * FROM $variables_table_name WHERE liquid_tag_name = %s AND id <> %d", str_replace(" ", "_", $_POST["name"]), $_POST["id"])) ) || ($wpdb->get_row($wpdb->prepare("SELECT * FROM $sliders_table_name WHERE liquid_tag_name = %s", str_replace(" ", "_", strtolower($_POST["name"])))))) && ($_POST["submit"] == "Add" || $_POST["submit"] == "Update")) {
	$name_unique_error = true;
} else if ($_POST["submit"] == "Add") {
	$rows_affected = $wpdb->insert( $variables_table_name, array('name' => $_POST["name"], 'liquid_tag_name' => str_replace(" ", "_", strtolower($_POST["name"])), 'value' => $_POST["value"]) );
} else if ($_POST["submit"] == "Update") {
	$wpdb->update($variables_table_name, array('name' => $_POST["name"], 'liquid_tag_name' => str_replace(" ", "_", strtolower($_POST["name"])),'value' => $_POST["value"]), array('id' => $_POST['id']));
}
$edit_variable = $wpdb->get_row($wpdb->prepare("SELECT * FROM $variables_table_name WHERE id = %d", $_GET["edit_id"]));

if (isset($_GET["delete_id"])) {
	$wpdb->query($wpdb->prepare("DELETE FROM $variables_table_name WHERE id = %d", $_GET["delete_id"]));
}

$slider_in_common = "('delay_time_before_start_slider','delay_between_slides', 'fade_out_delay', 'fade_in_delay')";
$slider_liquid_variables = $wpdb->get_results("SELECT * FROM $variables_table_name WHERE liquid_tag_name IN $slider_in_common ");
$limit = 10;
$page_no = $_GET["page_no"];
$offset = $page_no * $limit;

$count_my_liquid_variables = count($wpdb->get_results("SELECT * FROM $variables_table_name WHERE liquid_tag_name NOT IN $slider_in_common"));

$my_liquid_variables = $wpdb->get_results("SELECT * FROM $variables_table_name WHERE liquid_tag_name NOT IN $slider_in_common LIMIT $limit OFFSET $offset");

$standard_th_width = "190px";
?>

	<div class="wrap">
	<?php 
		$var_name = $_POST['name'];
		$name_unique_error_message = "<p>Sorry the liquid variable $var_name already exists. Please choose another name.</p>";
	?>

	<?php if ($name_cannot_be_blank_error) { ?>
		<div id="message" class="updated below-h2">
			<p>Name cannot be blank.</p>
		</div>
	<?php } else { ?>
		<?php
			$common_variable_msg = "To use copy and paste {{".(str_replace(" ", "_", strtolower($_POST["name"])))."}} into your post/page/style.css file.";
		?>
		<?php if ($_POST["submit"] == "Add") { ?>
		<div id="message" class="updated below-h2">
			<?php if ($name_unique_error) {
				echo $name_unique_error_message;
			} else { ?>
				<p>New tag created. <?php echo($common_variable_msg); ?></p>
			<?php } ?>
		</div>
		<?php } ?>
		<?php if ($_POST["submit"] == "Update") { ?>
		<div id="message" class="updated below-h2">
			<?php if ($name_unique_error) { 
				echo $name_unique_error_message;
			} else { ?>
				<p>Updated tag: <?php echo($common_variable_msg); ?></p>
			<?php } ?>
		</div>
		<?php } ?>
		<?php if (isset($_GET["delete_id"])) { ?>
			<div id="message" class="updated below-h2"><p>Liquid tag was deleted.</p></div>
		<?php } ?>
	<?php } ?>

	<h2>PHP Liquid Tags
		<?php if (isset($_GET["edit_id"]) || isset($_GET["action"])) { ?>
		  <a class="add-new-h2" href="<?php echo(get_option('siteurl')); ?>/wp-admin/admin.php?page=php-liquid/php_liquid_variables.php&">Tag List</a>
		<?php } else { ?>
			<a class="add-new-h2" href="<?php echo(get_option('siteurl')); ?>/wp-admin/admin.php?page=php-liquid/php_liquid_variables.php&action=New">Add New</a>
		<?php } ?>		
	</h2>

	<?php if (!(isset($_GET["edit_id"]) || isset($_GET["action"]))) { ?>
		<p>To use a liquid tag in your post/page/javascript/style.css, copy and paste the tag into your post/page/javascript/style.css file.</p>
		<h3>My Liquid Tags</h3>
		<div class="postbox " style="display: block; ">
		<div class="inside">
			<?php if ($my_liquid_variables) { ?>
				<table class="data">
					<thead>
						<tr>
							<th style="width: <?php echo($standard_th_width); ?>;">Name</th>
							<th style="width: <?php echo($standard_th_width); ?>;">Liquid Tag</th>
							<th style="width: <?php echo($standard_th_width); ?>;">Value</th>
							<th></th>
							<th></th>
						</tr>
					</thead>
					<tbody>	
						<?php foreach ($my_liquid_variables as $liquid_variable) { ?>
							<tr>
								<td><?php echo($liquid_variable->name); ?></td>
								<td>{{<?php echo($liquid_variable->liquid_tag_name); ?>}}</td>
								<td><?php echo($liquid_variable->value); ?></td>
								<td><a href="<?php echo(get_option('siteurl')); ?>/wp-admin/admin.php?page=php-liquid/php_liquid_variables.php&edit_id=<?php echo($liquid_variable->id); ?>">Edit</a></td>							
								<td>
								<?php if ($liquid_variable->deletable) { ?>
									<a class="delete" href="<?php echo(get_option('siteurl')); ?>/wp-admin/admin.php?page=php-liquid/php_liquid_variables.php&delete_id=<?php echo($liquid_variable->id); ?>">Delete</a>
								<?php } ?>
								</td>
							</tr>
						<?php } ?>
					</tbody>
				</table>

				<?php if ($count_my_liquid_variables > 0)  { ?>
					<ul class="pagination">

						<?php if (!($page_no == "" || $page_no == "0")) { 
							$prev_page_no = ($page_no - 1);
							?>
							<li><a href="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=php-liquid/php_liquid_variables.php&page_no=<?php echo($prev_page_no); ?>">Prev</a></li>
						<?php } ?>
						<li>Page 
							<?php if ($page_no != "") {
								echo($page_no+1);
							} else {
								echo "1";
							}
							?> of <?php echo(intval($count_my_liquid_variables / $limit) + 1); ?></li>
						<?php if ($page_no == "" || (($limit * ($page_no + 1)) < $count_my_liquid_variables)) { 
							$next_page_no = ($page_no + 1);
							?>
							<li><a href="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=php-liquid/php_liquid_variables.php&page_no=<?php echo($next_page_no); ?>">Next</a></li>
						<?php } ?>
					</ul>
				<?php } ?>
			<?php } else { ?>
				<p>You haven't created a custom liquid tag yet. To create a new liquid tag click the Add New link.</p>
			<?php } ?>
		</div>
		</div>
	<?php } ?>

	<?php if (isset($_GET["edit_id"]) || isset($_GET["action"])) { ?>
		<input type="hidden" name="action" value="<?php echo($_GET['action']); ?>" />
		<div class="postbox " style="display: block; ">
		<div class="inside">
		<form action="" method="post">
				<input type="hidden" name="id" value="<?php echo($edit_variable->id); ?>"/>
				<table>
					<thead>
						<tr>
							<th scope="col" style="width: <?php echo($standard_th_width); ?>;"><label for="name">Name</label></th>
							<th scope="col"><label for="value">Value</label></th>
						</tr>
					</thead>
					<tbody>	
						<tr>
							<td>
							  <?php if ((!isset($edit_variable->id)) || $edit_variable->deletable) { ?>
							    <input type="text" id="name" name="name" value="<?php echo($edit_variable->name); ?>" />
							  <?php } else { ?>
							    <input type="text" id="name" readonly name="name" value="<?php echo($edit_variable->name); ?>" />
							  <?php } ?>						  
							</td>
							<td><input type="text" id="value" name="value" value="<?php echo($edit_variable->value); ?>" /></td>
						</tr>
					</tbody>
				</table>
			<p class="submit">
				<?php if (isset($_GET["edit_id"])) { ?>
					<input type="submit" name="submit" value="Update">
				<?php } else { ?>
					<input type="submit" name="submit" value="Add">
				<?php } ?>
			</p>
		</form>
		</div>
		</div>

	<?php } else { ?>
		<h3>System Liquid Tags</h3>
		<div class="postbox " style="display: block; ">
		<div class="inside">
				<table class="data">
					<thead>
						<tr>
							<th style="width: <?php echo($standard_th_width); ?>;">Name</th>
							<th style="width: <?php echo($standard_th_width); ?>;">Liquid Tag</th>
							<th style="width: <?php echo($standard_th_width); ?>;">Value</th>
							<th></th>
						</tr>
					</thead>
					<tbody>	
						<tr>
							<td>Site URL</td>
							<td>{{site_url}}</td>
							<td><?php echo(get_option("siteurl")); ?></td>
							<td></td>
						</tr>
					</tbody>
				</table>
		</div>
		</div>


		<h3>Slider Liquid Tags</h3>
		<div class="postbox " style="display: block; ">
		<div class="inside">
				<table class="data">
					<thead>
						<tr>
							<th style="width: <?php echo($standard_th_width); ?>;">Name</th>
							<th style="width: <?php echo($standard_th_width); ?>;">Liquid Tag</th>
							<th style="width: <?php echo($standard_th_width); ?>;">Value</th>
							<th></th>
						</tr>
					</thead>
					<tbody>	
						<?php foreach ($slider_liquid_variables as $liquid_variable) { ?>
							<tr>
								<td><?php echo($liquid_variable->name); ?></td>
								<td>{{<?php echo($liquid_variable->liquid_tag_name); ?>}}</td>
								<td><?php echo($liquid_variable->value); ?></td>
								<td><a href="<?php echo(get_option('siteurl')); ?>/wp-admin/admin.php?page=php-liquid/php_liquid_variables.php&edit_id=<?php echo($liquid_variable->id); ?>">Edit</a></td>							
								<?php if ($liquid_variable->deletable) { ?>
									<td><a class="delete" href="<?php echo(get_option('siteurl')); ?>/wp-admin/admin.php?page=php-liquid/php_liquid_variables.php&delete_id=<?php echo($liquid_variable->id); ?>">Delete</a></td>
								<?php } ?>
							</tr>
						<?php } ?>
					</tbody>
				</table>
		</div>
		</div>

	<?php } ?>
	</div>
<?php tom_add_social_share_links("http://wordpress.org/extend/plugins/php-liquid/"); ?>