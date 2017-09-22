<% if $Parent.HasListAlerts %>
  <div class="alerts">
    <% loop $Parent.ListAlertsData %>
      <% include Alert Type=$Type, Icon=$Icon, Text=$Text.RAW %>
    <% end_loop %>
  </div>
<% end_if %>
