<% if $Renderer.isFooterShown($isFirst, $isMiddle, $isLast) %>
  <footer>
    <% loop $ListItemButtons %>
      <% include Button Tag='a', Icon=$Icon, Type=$Type, HREF=$HREF, Text=$Text, ExtraClass=$ExtraClass %>
    <% end_loop %>
  </footer>
<% end_if %>
