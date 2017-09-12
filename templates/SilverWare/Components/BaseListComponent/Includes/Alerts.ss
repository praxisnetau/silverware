<% if $ParentInstance.HasListAlerts %>
  <div class="alerts">
    <% loop $ParentInstance.ListAlertsData %>
      <% include Alert Type=$Type, Icon=$Icon, Text=$Text.RAW %>
    <% end_loop %>
  </div>
<% end_if %>
