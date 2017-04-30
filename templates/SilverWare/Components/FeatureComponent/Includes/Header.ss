<% if $HeaderShown %>
  <header>
    <$HeadingTag class="$HeadingClass">
      <% if $LinkHeading %>
        <a href="$FeaturedPage.MetaLink">$FeaturedPage.MetaTitle</a>
      <% else %>
        $FeaturedPage.MetaTitle
      <% end_if %>
    </$HeadingTag>
  </header>
<% end_if %>
