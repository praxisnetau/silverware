/* Tag Cloud Component
===================================================================================================================== */

import $ from 'jQuery';

$(function() {
  
  $('.tagcloudcomponent').each(function() {
    
    var $self   = $(this);
    var $canvas = $($self.data('canvas'));
    
    $canvas.tagcanvas({
      depth: parseFloat($self.data('depth')),
      zoom: parseFloat($self.data('zoom')),
      zoomMin: parseFloat($self.data('zoom-min')),
      zoomMax: parseFloat($self.data('zoom-max')),
      textColour: $self.data('color-text'),
      outlineColour: $self.data('color-outline'),
      initial: $self.data('initial'),
      weightSizeMin: parseInt($self.data('weight-size-min')),
      weightSizeMax: parseInt($self.data('weight-size-max')),
      weightFrom: 'data-weight',
      weight: $self.data('weight')
    }, $self.data('tag-list'));
    
  });
  
  // Detect Browser Resize:
  
  var id = null;
  
  var resizeTagCloud = function() {
    
    $('.tagcloudcomponent').each(function() {
      
      var $self   = $(this);
      var $canvas = $($self.data('canvas'));
      
      var width = $self.width();
      
      $canvas.attr('width', width);
      
    });
    
  };
  
  $(window).resize(function() {
    
    if (id !== null) {
      clearTimeout(id);
    }
    
    id = setTimeout(resizeTagCloud, 500);
    
  });
  
  resizeTagCloud();
  
});
