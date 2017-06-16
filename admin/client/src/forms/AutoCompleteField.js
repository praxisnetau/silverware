/* Auto Complete Field
===================================================================================================================== */

import $ from 'jquery';

$.entwine('ss.autocompletefield', function($) {
  
  // Handle Autocomplete Fields:
  
  $('.field.autocomplete input.text').entwine({
    
    // Handle onMatch:
    
    onmatch: function() {
      
      // Call Super:
      
      this._super();
      
      // Initialise:
      
      this.initField();
      this.initClear();
      
    },
    
    // Handle onFocusIn:
    
    onfocusin: function() {
      if (this.getValue() >= this.getMinLength()) {
        $(this).autocomplete('search');
      }
    },
    
    // Initialise Field:
    
    initField: function() {
      
      var self = this;
      
      $(this).autocomplete({
        source: self.getSourceURL(),
        minLength: self.getMinLength(),
        delay: self.getDelay(),
        change: function (event, ui) {
          self.update(event, ui);
        },
        select: function(event, ui) {
          self.update(event, ui);
        }
      });
      
    },
    
    // Initialise Clear:
    
    initClear: function() {
      
      var self = this;
      
      $(this).parent().find('a.clear').click(function(e) {
        e.preventDefault();
        self.clearValue();
      });
      
    },
    
    setValue: function(val) {
      $(this).val(val);
    },
    
    getValue: function() {
      return $(this).val();
    },
    
    getSourceURL: function() {
      return $(this).attr('data-source-url');
    },
    
    getMinLength: function() {
      return $(this).attr('data-min-length');
    },
    
    isFreeTextAllowed: function() {
      return $(this).attr('data-free-text') ? true : false;
    },
    
    getDelay: function() {
      return $(this).attr('data-delay');
    },
    
    getEmptyValue: function() {
      return $(this).attr('data-empty-value');
    },
    
    getHiddenInput: function() {
      return $(this).parent().find(':hidden');
    },
    
    getValueWrapper: function() {
      return $(this).parent().find('.value-wrapper');
    },
    
    getValueElement: function() {
      return $(this).parent().find('.value-wrapper > .value');
    },
    
    // Update Field:
    
    update: function(event, ui) {
      
      var value = this.getValue();
      
      if (value) {
        
        if (ui.item) {
          this.updateValue(ui.item.value, ui.item.label);
        } else if (this.isFreeTextAllowed()) {
          this.updateValue(value);
        }
        
      }
      
    },
    
    // Update Value:
    
    updateValue: function(value, label) {
      
      if (!label) {
        label = value;
      }
      
      if (this.getHiddenInput().val() !== value) {
        this.getHiddenInput().val(value);
        this.getValueElement().text(label);
        this.getValueWrapper().addClass('has-value');
      }
      
    },
    
    // Clear Value:
    
    clearValue: function() {
      this.getHiddenInput().val('');
      this.getValueElement().text(this.getEmptyValue());
      this.getValueWrapper().removeClass('has-value');
    }
    
  });
  
});
