/* Toggle Component
===================================================================================================================== */

import $ from 'jquery';

$(function() {
  
  // Handle Toggle Components:
  
  $('.togglecomponent').each(function() {
    
    var $self   = $(this);
    var $header = $self.find('header');

    // Detect Start Open Status:
    
    if ($self.data('start-open')) {
      $header.addClass('opened');
    }
    
    // Handle Header Click:
    
    $header.on('click', function() {
      $(this).toggleClass('opened');
    });
    
    // Handle Header Link Click:
    
    $header.find('a').on('click', function(e) {
      e.stopPropagation();
    });
    
  });
  
});
