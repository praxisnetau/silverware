<% if $HeaderShown %>
  <header>
    <$HeadingTag class="$HeadingClass">
      <% if $LinkHeading %>
        <a href="$FeaturedPage.MetaLink">$HeadingText</a>
      <% else %>
        $HeadingText
      <% end_if %>
    </$HeadingTag>
    <% if $SubHeadingText %>
      <$SubHeadingTag class="$SubHeadingClass">
        $SubHeadingText
      </$SubHeadingTag>
    <% end_if %>
  </header>
<% end_if %>
