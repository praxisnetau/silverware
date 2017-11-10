<figure class="$FigureClass">
  <% if $LinkImage %><a $ImageLinkAttributesHTML><% end_if %>
  <% if $HasInlineVector %>$Image.RenderInline<% else %><img $ImageAttributesHTML><% end_if %>
  <% if $LinkImage %></a><% end_if %>
  <% if $CaptionShown %>
    <figcaption class="$CaptionClass">
      $Caption
    </figcaption>
  <% end_if %>
</figure>
