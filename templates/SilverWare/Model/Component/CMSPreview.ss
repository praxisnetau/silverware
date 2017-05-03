<!DOCTYPE html>

<html class="no-js" lang="$ContentLocale">
  <head>
    <% base_tag %>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    $MetaTags(false)<title>$Title</title>
  </head>
  <body class="cms-preview-component">
    <div class="cms-preview-wrapper">
      <header>
        <h1>
          <i class="fa fa-fw fa-eye"></i>
          <span class="title"><%t SilverWare\Model\Component.PREVIEW 'Preview' %></span>
          <span class="class">($ComponentType)</span>
        </h1>
      </header>
      <div class="component">
        <div class="preview">$renderPreview.RAW</div>
      </div>
    </div>
  </body>
</html>
