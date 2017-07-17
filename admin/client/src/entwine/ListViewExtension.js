/* List View Extension
===================================================================================================================== */

import $ from 'jquery';

$.entwine('silverware.listviewextension', function($) {
  
  // Handle List View Extension Fields:
  
  $('.tabset.silverware-extensions-lists-listviewextension').entwine({
    
    onmatch: function() {
      
      var $self = $(this);
      
      this.handlePagination();
      
      this.getPaginateItemsField().entwine({
        onchange: function(e) {
          $self.handlePagination();
          this._super(e);
        }
      });
      
      this._super();
      
    },
    
    handlePagination: function() {
      var mode = this.getPaginateItemsField().val();
      if (mode == 1) {
        this.getPaginationHolder().show();
      } else {
        this.getPaginationHolder().hide();
      }
    },
    
    getPaginateItemsField: function() {
      return $(this).find('#Form_EditForm_ListPaginateItems');
    },
    
    getPaginationHolder: function() {
      return $(this).find('#Form_EditForm_ListItemsPerPage_Holder');
    }
    
  });
  
});
