jQuery.noConflict();
jQuery(function() {
  /*Slideshow */
  jQuery("span.feature-circles-sub").click(function() {
    var slider_id = jQuery(this).parent().parent().parent().parent().attr("id");
    var slide_id = jQuery(this).attr('class').replace('feature-circles-sub', '').replace('active', '');
    jQuery("#"+slider_id+" .feature-circles-sub").removeClass("active");
    jQuery("#"+slider_id+" .slide").hide();
    jQuery("#"+slider_id+" #"+slide_id).show();
    jQuery(this).addClass("active");
  });

  jQuery(".feature-arrow-l, .feature-arrow-r").mouseover(function() {
    jQuery(this).addClass("hover");
  });
  jQuery(".feature-arrow-l, .feature-arrow-r").mouseout(function() {
    jQuery(this).removeClass("hover");
  });

  jQuery(".feature-arrow-r").click(function() {
      jQuery(this).parent().find(".feature-circles-sub.active").removeClass("active");
      var current_image = jQuery(this).parent().parent().parent().find(".slide:visible")
      ,   next_image = current_image.next('.slide');
      if (next_image.length == 0) {
        next_image = current_image.parent().parent().parent().parent().find('.slide.first');
      }
      jQuery(this).parent().parent().parent().find(".slide").hide();
      next_image.show().fadeOut({{fade_out_delay}});
      next_image.fadeIn({{fade_in_delay}});
      jQuery("span."+next_image.attr("id")).addClass("active");
      return false;
  });

  jQuery(".feature-arrow-l").click(function() {
    jQuery(this).parent().find(".feature-circles-sub.active").removeClass("active");
    var current_image = jQuery(this).parent().parent().parent().find(".slide:visible")
    ,   next_image = current_image.prev('.slide');
    if (next_image.length == 0) {
      next_image = current_image.parent().parent().parent().parent().find('.slide.last');
    }
    jQuery(this).parent().parent().parent().find(".slide").hide();
    next_image.show().fadeOut({{fade_out_delay}});
    next_image.fadeIn({{fade_in_delay}});
    jQuery("span."+next_image.attr("id")).addClass("active");
    return false;
  });

  function slide() {
    jQuery(".slide_show_container.auto-slide").each(function() {
      jQuery(this).find(".feature-circles-sub.active").removeClass("active");
      var current_image = jQuery(this).find(".slide:visible")
      ,   next_image = current_image.next('.slide');
      if (next_image.length == 0) {
        next_image = current_image.parent().find('.slide.first');
      }
      jQuery(this).find(".slide").hide();
      next_image.show().fadeOut({{fade_out_delay}});
      next_image.fadeIn({{fade_in_delay}});
      jQuery("span."+next_image.attr("id")).addClass("active");
    });
  }

  function slideshow() {
    setInterval(slide, {{delay_between_slides}});
  }
  
  setTimeout(function() {
    slideshow();
  }, {{delay_time_before_start_slider}});

});