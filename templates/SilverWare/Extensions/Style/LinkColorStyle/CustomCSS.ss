<% if $HasLinkColors %>
  
  {$CustomCSSPrefix} {
    <% if $ColorForegroundLink %>color: {$ColorForegroundLink};<% end_if %>
    <% if $ColorBackgroundLink %>background-color: {$ColorBackgroundLink};<% end_if %>
  }

<% end_if %>

<% if $HasHoverColors %>
  
  {$CustomCSSPrefix}:focus,
  {$CustomCSSPrefix}:hover {
    <% if $ColorForegroundHover %>color: {$ColorForegroundHover};<% end_if %>
    <% if $ColorBackgroundHover %>background-color: {$ColorBackgroundHover};<% end_if %>
  }

<% end_if %>

<% if $HasActiveColors %>
  
  {$CustomCSSPrefix}:active {
    <% if $ColorForegroundActive %>color: {$ColorForegroundActive};<% end_if %>
    <% if $ColorBackgroundActive %>background-color: {$ColorBackgroundActive};<% end_if %>
  }

<% end_if %>
