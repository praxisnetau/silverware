<% if $Renderer.isHeaderShown($isFirst, $isMiddle, $isLast) %>
  <header>
    <$Renderer.HeadingTag>
      <% if $Renderer.LinkTitles %>
        <a href="$MetaLink" title="$MetaTitle">$MetaTitle</a>
      <% else %>
        $MetaTitle
      <% end_if %>
    </$Renderer.HeadingTag>
  </header>
<% end_if %>
