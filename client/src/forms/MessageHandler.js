/* Form Message Handler
===================================================================================================================== */

import jQuery from 'jquery';

(function($) {
  
  // Setup Message Handler Plugin:
  
  $.fn.handleMessages = function(options) {
    
    if (typeof options === 'object') {
      
      // Obtain Options:
      
      var opts = $.extend({}, $.fn.handleMessages.defaults, options);
      
      // Remove Existing Messages:
      
      this.find('.' + opts.messageClass).remove();
      
      // Initialise Variables:
      
      var $item, $message = this.find(opts.messageSelector);
      
      // Record Success:
      
      var success = true;
      
      // Did We Find a Message Element?
      
      if ($message.length) {
        
        // Hide and Empty Message Element:
        
        $message.hide().empty();
        
        // Iterate Messages:
        
        $.each(opts.messages, function(i, item) {
          
          // Record Failure:
          
          if (item.messageType != 'good') {
            success = false;
          }
          
          // Obtain Previous Element:
          
          var $prev = ($item !== undefined) ? $item : $message;
          
          // Create Message Item Element and Change ID:
          
          $item = $message.clone().prop('id', function(key, id) {
            return id + '_' + (i + 1);
          });
          
          // Remove Existing Alert Classes:
          
          $item.removeClass(function(index, className) {
            return (className.match(/alert-\S+/g) || []).join(' ');
          });
          
          // Render Message Icon (if enabled):
          
          if (opts.showIcons) {
            var iconClass = opts.iconClasses[opts.defaultIcon];
            if (opts.iconClasses[item.messageType]) iconClass = opts.iconClasses[item.messageType];
            $item.append($('<i>').addClass(opts.iconBase).addClass(iconClass)).append(' ');
          }
          
          // Render Message Text:
          
          $item.append($('<span>').html(item.message));
          
          // Add Message Class:
          
          $item.addClass(opts.messageClass);
          
          // Add Alert Class:
          
          var alertClass = opts.alertClasses[opts.defaultAlert];
          if (opts.alertClasses[item.messageType]) alertClass = opts.alertClasses[item.messageType];
          $item.addClass(alertClass);
          
          // Add Message Item after Previous:
          
          $prev.after($item);
          
          // Fade In Message Item:
          
          $item.fadeIn();
          
        });
        
      }
      
      // Trigger Success / Failure Callbacks:
      
      if (success) {
        opts.onSuccess.call(this);
      } else {
        opts.onFailure.call(this);
      }
      
    }
    
  };
  
  // Message Handler Default Options:
  
  $.fn.handleMessages.defaults = {
    messages: [],
    alertClasses: {
      good:    'alert-success',
      info:    'alert-info',
      error:   'alert-danger',
      warning: 'alert-warning'
    },
    iconClasses: {
      good:    'fa-check',
      info:    'fa-info-circle',
      error:   'fa-times',
      warning: 'fa-warning'
    },
    showIcons: true,
    iconBase: 'fa fa-fw',
    defaultIcon: 'warning',
    defaultAlert: 'warning',
    messageClass: 'js-message',
    messageSelector: '.message',
    onSuccess: function() {},
    onFailure: function() {}
  };

}(jQuery));
