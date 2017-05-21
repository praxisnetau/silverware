<figure class="$FigureClass">
  <% if $LinkImage %><a $ImageLinkAttributesHTML><% end_if %>
  <img src="$ImageResized.URL" class="$ImageClass" title="$Self.Title" alt="$Self.Title">
  <% if $LinkImage %></a><% end_if %>
  <% if $CaptionShown %>
    <figcaption class="$CaptionClass">
      $Caption
    </figcaption>
  <% end_if %>
</figure>
