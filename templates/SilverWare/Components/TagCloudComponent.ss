<canvas id="$CanvasID" width="200" height="200">
  <ul id="$TagListID">
    <% loop $Tags %>
      <a href="$Link" data-weight="$Weight">$Title</a>
    <% end_loop %>
  </ul>
</canvas>
