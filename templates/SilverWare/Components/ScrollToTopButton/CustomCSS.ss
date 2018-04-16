<% loop $ButtonDimensions %>
  
  <% if $Breakpoint %>@media (min-width: {$Breakpoint}) {<% end_if %>
    
    {$Up.CSSID} {
      <% if $Size %>width: {$Size}px;<% end_if %>
      <% if $Size %>height: {$Size}px;<% end_if %>
      <% if $Size %>line-height: {$Size}px;<% end_if %>
      <% if $IconSize %>font-size: {$IconSize}px;<% end_if %>
    }
    
  <% if $Breakpoint %>}<% end_if %>
  
<% end_loop %>
