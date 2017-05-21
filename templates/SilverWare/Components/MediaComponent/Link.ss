<div class="$MediaClass">
  <% if $HasImage %>
    <% if $LinkImage %><a $ImageLinkAttributesHTML><% end_if %>
    <img src="$ImageURL" class="$ImageClass" alt="$ImageAltText">
    <% if $LinkImage %></a><% end_if %>
  <% end_if %>
  <% if $TextLinkShown %>
    <a class="text" href="$MediaURL">
      <% if $IconShown %>$TextLinkIcon.Tag<% end_if %>
      <span class="title">$MediaTitle</span>
    </a>
  <% end_if %>
</div>
