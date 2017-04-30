<input $AttributesHTML />
<input type="hidden" name="$Name" value="$dataValue" />
<div class="value-wrapper<% if $Value %> has-value<% end_if %>">
  <span class="value">
    <% if $Value %>
      $Value
    <% else %>
      $EmptyValue
    <% end_if %>
  </span>
  <a href="#" class="clear">Clear</a>
</div>
