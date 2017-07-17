<% if $Renderer.isImageShown($isFirst, $isMiddle, $isLast) && $HasMetaImage %>
  <div class="image">
    <% if $Renderer.LinkImages %><a $MetaImageLinkAttributesHTML><% end_if %>
    <% with $getMetaImageResized($Renderer.ImageResizeWidth, $Renderer.ImageResizeHeight, $Renderer.ImageResizeMethod) %>
      <img src="$URL" class="$Up.MetaImageClass" alt="$Title">
    <% end_with %>
    <% if $Renderer.LinkImages %></a><% end_if %>
  </div>
<% end_if %>
