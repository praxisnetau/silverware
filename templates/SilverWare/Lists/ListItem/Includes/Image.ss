<% if $Renderer.isImageShown($isFirst, $isMiddle, $isLast) && $HasMetaImage %>
  <div class="image">
    <% if $Renderer.LinkImages %><a $MetaImageLinkAttributesHTML><% end_if %>
    <% with $getMetaImageResized($Renderer.ImageResizeWidth, $Renderer.ImageResizeHeight, $Renderer.ImageResizeMethod) %>
      <img src="$URL" class="$Up.ListItemImageClass" alt="$Title">
    <% end_with %>
    <% if $Renderer.OverlayImages %>
      <div class="image-overlay">
        <div class="inner">
          <% if $Renderer.OverlayIcon %>
            <div class="icon">
              $Renderer.OverlayIcon.Tag
            </div>
          <% end_if %>
          <% if $Renderer.OverlayTitle %>
            <div class="text">
              <$Renderer.HeadingTag>$MetaTitle</$Renderer.HeadingTag>
            </div>
          <% end_if %>
        </div>
      </div>
    <% end_if %>
    <% if $Renderer.LinkImages %></a><% end_if %>
  </div>
<% end_if %>
