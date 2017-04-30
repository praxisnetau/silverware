<% if $ListComponent.isFooterShown($isFirst, $isMiddle, $isLast) %>
  <footer>
    <% if $HasMetaLink %>
      <% include Button Tag='a', HREF=$MetaLink, Text=$ListComponent.ButtonLabel %>
    <% end_if %>
  </footer>
<% end_if %>