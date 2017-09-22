<% if $HasDetailFields %>
  <div class="$DetailFieldsClass">
    <{$DetailFieldsHeadingTag}>{$DetailFieldsHeadingText}</{$DetailFieldsHeadingTag}>
    <dl>
      <% loop $DetailFields %>
        <% if $Text %>
          <dt><% include Icon Name=$Icon, FixedWidth=1 %>$Name</dt>
          <dd>$Text.RAW</dd>
        <% end_if %>
      <% end_loop %>
    </dl>
  </div>
<% end_if %>
