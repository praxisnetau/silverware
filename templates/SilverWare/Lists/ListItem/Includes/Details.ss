<% if $Renderer.isDetailsShown($isFirst, $isMiddle, $isLast) %>
  <div class="details">
    <% loop $ListItemDetails %>
      <span class="$Name"><% include Icon Name=$Icon, FixedWidth=1 %> $Text.RAW</span>
    <% end_loop %>
  </div>
<% end_if %>
