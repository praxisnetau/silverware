/* Toggle Group
===================================================================================================================== */

import $ from 'jquery';

$.entwine('silverware.togglegroup', function($) {
  
  // Handle Toggle Groups:
  
  $('.field.togglegroup').entwine({
    
    onmatch: function() {
      
      var $self = $(this);
      
      $self.doToggle();
      
      $self.getToggleInput().entwine({
        
        onclick: function(e) {
          $self.doToggle();
          this._super(e);
        }
        
      });
      
      this._super();
      
    },
    
    doToggle: function() {
      
      var $input = $(this.getToggleInput());
      var whenChecked = this.getToggleMode();
      
      this.getFields().toggle(whenChecked ? $input.is(':checked') : !$input.is(':checked'));
      
    },
    
    getToggle: function() {
      return $(this).find('.group-toggle');
    },
    
    getFields: function() {
      return $(this).find('.group-fields');
    },
    
    getToggleInput: function() {
      return this.getToggle().find('input');
    },
    
    getToggleMode: function() {
      return this.getToggle().data('show-when-checked');
    }
    
  });
  
});
