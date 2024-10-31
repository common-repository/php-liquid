<?php
require_once("../../../wp-blog-header.php");
header("Content-Type: text/javascript");
if (get_option("php_liquid_compress_js") == "checked") {
	print mytheme_liquid_content_filter(compress_content(get_option("php_liquid_application_js")));
} else {
	print mytheme_liquid_content_filter(get_option("php_liquid_application_js"));
}
?>