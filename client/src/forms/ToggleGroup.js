/* Toggle Group
===================================================================================================================== */

import $ from 'jquery';

$(function() {
  
  $('.field.togglegroup').each(function() {
    
    var $self = $(this);
    var $mode = $self.find('.group-toggle').data('show-when-checked');
    
    var $toggle = $self.find('.group-toggle input');
    var $fields = $self.find('.group-fields');
    
    $fields.toggle($mode ? $toggle.is(':checked') : !$toggle.is(':checked'));
    
    $toggle.on('click', function() {
      $fields.toggle($mode ? $(this).is(':checked') : !$(this).is(':checked'));
    });
    
  });
  
});
