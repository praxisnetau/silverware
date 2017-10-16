<article class="$ListItemClass">
  <% include SilverWare\Lists\ListItem\Image %>
  <% if not $Renderer.isContentEmpty($isFirst, $isMiddle, $isLast) %>
    <section class="$ListItemContentClass">
      <% include SilverWare\Lists\ListItem\Header %>
      <% include SilverWare\Lists\ListItem\Details %>
      <% include SilverWare\Lists\ListItem\Summary %>
      <% include SilverWare\Lists\ListItem\Content %>
      <% include SilverWare\Lists\ListItem\Footer %>
    </section>
  <% end_if %>
</article>
