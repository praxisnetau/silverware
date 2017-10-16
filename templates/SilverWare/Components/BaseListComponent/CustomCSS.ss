<% if $OverlayBackground %>
{$CSSID} .image-overlay {
  background-color: $OverlayBackground;
}
<% end_if %>

<% if $OverlayIconColor %>
{$CSSID} .image-overlay div.icon > i {
  color: $OverlayIconColor;
}
<% end_if %>

<% if $OverlayForeground %> 
{$CSSID} .image-overlay div.text > * {
  color: $OverlayForeground;
}
<% end_if %>
