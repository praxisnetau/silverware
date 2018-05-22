<% if $ImageShown %>
  <div class="image">
    <% if $ImageLinked %><a $ImageLinkAttributesHTML><% end_if %>
    <img src="$ImageResized.URL" class="$ImageClass" title="$Self.Title" alt="$Self.Title">
    <% if $ImageLinked %></a><% end_if %>
  </div>
<% end_if %>
