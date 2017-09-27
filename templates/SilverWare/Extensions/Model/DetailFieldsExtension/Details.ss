<% if $HasDetailFields %>
  <div class="$DetailFieldsClass">
    <header>
      <{$DetailFieldsHeadingTag}>{$DetailFieldsHeadingText}</{$DetailFieldsHeadingTag}>
    </header>
    <ul>
      <% loop $DetailFields %>
        <li>
          <dl>
            <% if $Text %>
              <dt><% include Icon Name=$Icon, FixedWidth=1 %><span class="name">$Name</span></dt>
              <dd>$Text.RAW</dd>
            <% end_if %>
          </dl>
        </li>
      <% end_loop %>
    </ul>
  </div>
<% end_if %>
