<?php
if (isset($_GET["slidecode"])) {
  header("Content-Type: text/javascript");
  require_once("../../../../wp-blog-header.php");
  if (get_option("php_liquid_compress_js") == "checked") {
    print mytheme_liquid_content_filter(compress_content(file_get_contents(get_option('siteurl')."/wp-content/plugins/php-liquid/slider/slide.js")));
  } else {
    print mytheme_liquid_content_filter(file_get_contents(get_option('siteurl')."/wp-content/plugins/php-liquid/slider/slide.js"));
  }
}

function get_slide_show($id) {
  global $wpdb;
  $sliders_table_name = $wpdb->prefix."php_liquid_sliders";
  $slide_table_name = $wpdb->prefix."php_liquid_slides";

  $slider = $wpdb->get_row($wpdb->prepare("SELECT * FROM $sliders_table_name WHERE id = %d", $id));
  $slides = $wpdb->get_results($wpdb->prepare("SELECT * FROM $slide_table_name WHERE slider_id = %d ORDER BY sort_order", $slider->id));

  $slider_name = str_replace(" ", "_", strtolower($slider->name));
  $slide_content = "";
  $slide_button_content = "";
  $count = 0;
  foreach ($slides as $slide) {
    $count++;
    $click_code = "";
    if ($slide->url != "") {
      $click_code = "onclick='window.location=\"".$slide->url."\"'";
    }
    $slide_name = str_replace(" ", "_", strtolower($slide->name));
    $slide_content .= "<div $click_code style='background: url(".$slide->background_image_url.") no-repeat;' class='".$slider_name." slide $slide_name ".(($count == 1) ? 'first' : '')." ".(($count == count($slides)) ? 'last' : '')."' href='#' id='".$slider_name."_".($count)."'><div class='text'>".$slide->content."</div></div>";
    $slide_button_content .= "<span id='slide_button_".$slide_name."_".$slide->id."' title='".$slide->name."' class='".$slider_name."_".($count)." feature-circles-sub ".(($count == 1) ? 'active' : '')."'><span> </span></span>";
  }

  $feature_circles_width = (($count * 16)."px");
  $content = <<< EOT

<div class="slide_show_container auto-slide" id="$slider_name">
  $slide_content
  <div class="feature-controls">
  <div class="feature-controls-inner">
  <div class='feature-arrow-l' title='tip'></div>
  <div class="feature-circles" style='width: $feature_circles_width'>
     $slide_button_content
  </div>
  <div class='feature-arrow-r' title='tip'></div>
  </div>
  </div>
</div>

EOT;

  return $content;

}

?>