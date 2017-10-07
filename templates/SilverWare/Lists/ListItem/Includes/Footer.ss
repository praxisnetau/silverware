<% if $Renderer.isFooterShown($isFirst, $isMiddle, $isLast) %>
  <footer>
    <% loop $ListItemButtons %>
      <% if $Up.Renderer.isButtonLink %>
        <% include Link Icon=$Icon, HREF=$HREF, Text=$Text, ExtraClass=$ExtraClass %>
      <% else %>
        <% include Button Tag='a', Icon=$Icon, Type=$Type, HREF=$HREF, Text=$Text, ExtraClass=$ExtraClass %>
      <% end_if %>
    <% end_loop %>
  </footer>
<% end_if %>
