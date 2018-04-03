/* Header Section
===================================================================================================================== */

import $ from 'jquery';

$(function() {
  
  // Handle Header Sections:
  
  $('.headersection').each(function() {
    
    var $self = $(this);
    
    // Add or Remove Class on Scroll:
    
    $(window).scroll(function() {
      
      if ($(this).scrollTop() > 0) {
        $self.addClass('scrolled');
      } else {
        $self.removeClass('scrolled');
      }
      
    });
    
  });
  
});
