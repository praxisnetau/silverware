<article class="$WrapperClass">
  <% if $FeatureLinked %><a $LinkAttributesHTML><% end_if %>
  <% include SilverWare\Components\FeatureComponent\Image %>
  <section class="$BodyClass">
    <% include SilverWare\Components\FeatureComponent\Icon %>
    <% include SilverWare\Components\FeatureComponent\Header %>
    <% include SilverWare\Components\FeatureComponent\Summary %>
    <% include SilverWare\Components\FeatureComponent\Footer %>
  </section>
  <% if $FeatureLinked %></a><% end_if %>
</article>
