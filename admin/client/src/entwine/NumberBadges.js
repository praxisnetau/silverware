/* Number Badges
===================================================================================================================== */

import $ from 'jQuery';

$.entwine('ss.tab.badges', function($) {
  
  // Handle Tab Badges:
  
  $('div.ss-tabset').entwine({
    
    onmatch: function() {
      
      this._super();
      
      var self = this;
      
      if (this.attr('data-number-badges')) {
        
        var badges = $.parseJSON(this.attr('data-number-badges'));
        
        $.each(badges, function (key, value) {
          
          if (value) {
            
            var tab = self.findTab(key);
            
            if (tab.length) {
              tab.append('<span class="number-badge">' + value + '</strong>');
            }
            
          }
          
        });
        
      }
      
    },
    
    findTab: function(name) {
      return this.find(this.getTabId(name));
    },
    
    getTabId: function(name) {
      return 'a#tab-' + name.replace('.', '_');
    }
    
  });
  
});
