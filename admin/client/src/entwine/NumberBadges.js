/* Number Badges
===================================================================================================================== */

import $ from 'jQuery';

$.entwine('silverware.numberbadges', function($) {
  
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
  
  // Handle Tree Badges:
  
  $('.cms-tree li').entwine({
    
    updateBadge: function(value) {
      
      var $span = this.find('span.status-number-badge');
      
      if ($span.length) {
        
        var id = '#' + this.attr('id');
          
        var selector = id + '.status-number-badge a span.jstree-pageicon::before';
        var content  = 'content: "' + (value > 0 ? value : '') + '";';
          
        $('head').append('<style type="text/css">' + selector + ' { ' + content + ' } </style>');
        
      }
      
    }
    
  });
  
  $('span.status-number-badge-value').entwine({
    
    onmatch: function() {
      
      this._super();
      
      if (!this.data('updated')) {
        
        var value = parseInt(this.attr('title'));
        
        this.closest('li').updateBadge(value);
        
        this.data('updated', true);
        
      }
      
    }
    
  });
  
});
