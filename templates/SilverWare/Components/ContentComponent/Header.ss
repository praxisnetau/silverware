<% if $ShowTitle %>
  <header>
    <$TitleTag>
      {$FontIconTag}
      <% if $HasLink %>
        <a $LinkAttributesHTML>{$TitleText}</a>
      <% else %>
        {$TitleText}
      <% end_if %>
    </$TitleTag>
  </header>
<% end_if %>
