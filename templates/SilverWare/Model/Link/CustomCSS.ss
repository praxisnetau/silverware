<% loop $LinkImageDimensions %>
  
  <% if $Breakpoint %>@media (min-width: {$Breakpoint}) {<% end_if %>
    
    {$Up.CSSID} > img,
    {$Up.CSSID} > svg {
      width: {$Width}px;
      height: {$Height}px;
    }
    
  <% if $Breakpoint %>}<% end_if %>
  
<% end_loop %>
