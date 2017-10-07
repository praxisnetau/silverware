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
              <dt>
                <% if $Up.DetailFieldsUseHeading %><{$Up.DetailFieldHeadingTag}><% end_if %>
                <% include Icon Name=$Icon, FixedWidth=1 %><span class="name">$Name</span>
                <% if $Up.DetailFieldsUseHeading %></{$Up.DetailFieldHeadingTag}><% end_if %>
              </dt>
              <dd>
                $Text.RAW
              </dd>
            <% end_if %>
          </dl>
        </li>
      <% end_loop %>
    </ul>
  </div>
<% end_if %>
