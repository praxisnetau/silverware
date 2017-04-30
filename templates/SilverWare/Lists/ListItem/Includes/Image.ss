<% if $ListComponent.isImageShown($isFirst, $isMiddle, $isLast) && $HasMetaImage %>
  <div class="image">
    <% if $ListComponent.LinkImages %><a $MetaImageLinkAttributesHTML><% end_if %>
    <% with $getMetaImageResized($ListComponent.ImageResizeWidth, $ListComponent.ImageResizeHeight, $ListComponent.ImageResizeMethod) %>
      <img src="$URL" alt="$Title">
    <% end_with %>
    <% if $ListComponent.LinkImages %></a><% end_if %>
  </div>
<% end_if %>
