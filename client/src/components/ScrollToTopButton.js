/* Scroll to Top Button
===================================================================================================================== */

import $ from 'jQuery';

$(function() {
  
  $('.scrolltotopbutton').each(function() {
    
    var $button = $(this);
    
    var offset_show     = parseInt($button.data('offset-show'));
    var offset_opacity  = parseInt($button.data('offset-opacity'));
    var scroll_duration = parseInt($button.data('scroll-duration'));
    
    // Hide or Show Button:
    
    $(window).scroll(function() {
      
      if ($(this).scrollTop() > offset_show) {
        $button.addClass('is-visible');
      } else {
        $button.removeClass('is-visible fade-out');
      }
      
      if ($(this).scrollTop() > offset_opacity) {
        $button.addClass('fade-out');
      }
      
    });
    
    // Animate Scroll to Top:
    
    $button.on('click', function(e) {
      e.preventDefault();
      $('body, html').animate({ scrollTop: 0 }, scroll_duration);
    });
    
  });
  
});
