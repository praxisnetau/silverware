<% if $ListItems %>
  <div class="$WrapperClass">
    <% loop $ListItems %>
      $renderListItem($First, $Middle, $Last)
    <% end_loop %>
  </div>
  <% if $PaginateItems %>
    <% include Pagination List=$ListItems %>
  <% end_if %>
<% else %>
  <% include Alert Type='warning', Text=$NoDataMessage %>
<% end_if %>
