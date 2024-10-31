<?php
require_once("../../../../wp-blog-header.php");
header("Content-Type: text/css");
if (get_option("php_liquid_compress_css") == "checked") {
	print mytheme_liquid_content_filter(compress_content(get_option("php_liquid_slider_css")));
} else {
	print mytheme_liquid_content_filter(get_option("php_liquid_slider_css"));
}
?>