<% if $Renderer.isDetailsShown($isFirst, $isMiddle, $isLast) %>
  <div class="details">
    <% loop $ListItemDetails %>
      <% if $Text %><span class="$Name"><% include Icon Name=$Icon, FixedWidth=1 %> $Text.RAW</span><% end_if %>
    <% end_loop %>
  </div>
<% end_if %>
