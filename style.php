<?php
require_once("../../../wp-blog-header.php");
header("Content-Type: text/css");
if (get_option("php_liquid_compress_css") == "checked") {
	print mytheme_liquid_content_filter(compress_content(file_get_contents(get_stylesheet_uri())));
} else {
	print mytheme_liquid_content_filter(file_get_contents(get_stylesheet_uri()));
}
?>