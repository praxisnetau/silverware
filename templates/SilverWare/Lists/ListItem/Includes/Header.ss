<% if $ListComponent.isHeaderShown($isFirst, $isMiddle, $isLast) %>
  <header>
    <$ListComponent.HeadingTag>
      <% if $ListComponent.LinkTitles %>
        <a href="$MetaLink" title="$MetaTitle">$MetaTitle</a>
      <% else %>
        $MetaTitle
      <% end_if %>
    </$ListComponent.HeadingTag>
  </header>
<% end_if %>
