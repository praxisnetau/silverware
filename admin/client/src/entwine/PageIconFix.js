/* Page Icon Fix
===================================================================================================================== */

import $ from 'jQuery';

$.entwine('ss', function($) {
  
  $('*[class*=class-]').entwine({
    
    onmatch: function() {
      this._super();
      $(this).attr('class', $(this).attr('class').replace(/\\/g, '_'));
    }
    
  });
  
});
