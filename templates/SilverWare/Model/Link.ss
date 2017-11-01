<% if $HasLinkImage %>
  <a $AttributesHTML>
    <% if $HasInlineVector %>
      $LinkImage.RenderInline
    <% else %>
      <img src="$LinkImage.URL" class="$LinkImageClass" alt="$Title">
    <% end_if %>
  </a>
<% else %>
  <a $AttributesHTML>$FontIconTag<span class="text">$Title</span></a>
<% end_if %>
