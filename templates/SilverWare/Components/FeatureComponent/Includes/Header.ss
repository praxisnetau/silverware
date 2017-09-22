<% if $HeaderShown %>
  <header>
    <$HeadingTag class="$HeadingClass">
      <% if $HeadingLinked %>
        <a $LinkAttributesHTML>$HeadingText</a>
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
