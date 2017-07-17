<div class="$getSlideClass($isFirst, $isMiddle, $isLast)">
  <% if $LinkShown %><a $LinkAttributesHTML><% end_if %>
  <% if $ImageShown %>
    <img src="$ImageResized.URL" class="$ImageClass" alt="$Title">
  <% end_if %>
  <% if $TitleOrCaptionShown %>
    <div class="$CaptionClass">
      <% if $TitleShown %>
        <{$HeadingTag}>{$FontIconTag}<span class="text">{$Title}</span></{$HeadingTag}>
      <% end_if %>
      <% if $CaptionShown %>
        $Caption
      <% end_if %>
    </div>
  <% end_if %>
  <% if $LinkShown %></a><% end_if %>
</div>
