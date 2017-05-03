<p align="center">
  <img src="https://raw.githubusercontent.com/praxisnetau/silverware/master/media/bitmap/silverware-logo.png" width="200" height="200" title="SilverWare" alt="SilverWare">
</p>

<p align="center">
  <a href="https://packagist.org/packages/silverware/silverware" target="_blank"><img src="https://poser.pugx.org/silverware/silverware/v/stable" alt="Latest Stable Version"></a>
  <a href="https://packagist.org/packages/silverware/silverware" target="_blank"><img src="https://poser.pugx.org/silverware/silverware/v/unstable" alt="Latest Unstable Version"></a>
  <a href="https://packagist.org/packages/silverware/silverware" target="_blank"><img src="https://poser.pugx.org/silverware/silverware/license" alt="License"></a>
</p>

**SilverWare** is a component framework for [SilverStripe v4][silverstripe] which allows you to build
entire page templates and layouts from small, reusable, configurable and extensible components.
SilverWare and it's associated modules provide a number of components for you to use in your apps
out-of-the-box, however it's easy to extend the SilverWare model and build your own components too.

## Contents

- [Background](#background)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Issues](#issues)
- [To-Do](#to-do)
- [Contribution](#contribution)
- [Attribution](#attribution)
- [Maintainers](#maintainers)
- [License](#license)

## Background

Most web applications consist of a series of small, reusable "blocks" that perform a particular function.
Many of these are hard-coded by the developer, and often tightly-coupled to the underlying theme and/or
scripts that drive the site.

One approach that affords more flexibility for both the developer and the end-user is to use "widgets".
Often an area of the site such as the sidebar is designated as a "widget area", and the end-user is
given the freedom to add and remove widgets from this area at will.

SilverWare goes a step further. In SilverWare, the *entire* page template and layout is
built from reusable blocks similar to widgets, which are known as **components**. SilverWare builds upon
the existing SilverStripe notion of page templates and layouts, and abstracts these concepts into a
rich component data model which can be manipulated using the CMS.

## Requirements

- [SilverStripe Framework v4][silverstripe-framework]
- [SilverStripe CMS v4][silverstripe-cms]
- [SilverWare Theme][silverware-theme]

## Installation

Installation of SilverWare is via [Composer](https://getcomposer.org). We recommend getting started with
the [SilverWare App][silverware-app] repository, which will install everything you need to get underway.

```
$ composer create-project silverware/app my-app-path
```

If you'd prefer to install the SilverWare component framework without the app installer, you can instead
use a regular require command via Composer:

```
$ composer require silverware/silverware
```

You will also need to use Yarn (or NPM) to install the theme dependencies:

```
$ cd themes/silverware-theme
$ yarn install
```

Once your theme dependencies are installed, execute the following to start the webpack development server:

```
$ yarn start
```

The theme should now compile with hot module reloading enabled, allowing the browser to automatically reload
and update your styles as you make changes to the theme SASS.

To prepare your theme for production, execute the following:

```
$ yarn build
```

## Configuration

As SilverWare is based upon SilverStripe, all configuration is performed using YAML configuration files.

## Usage

Upon building your SilverWare app, you will find a series of folders within your CMS site tree:

- Templates
- Layouts
- Panels

These are hidden from display on the public site, and are used to create your page templates, layouts,
and panels (more on those in a moment). SilverWare exploits the built-in hierarchy features of SilverStripe
to define a particular structure for your components.

You may think of templates and layouts in exactly the same fashion as SilverStripe convention dictates.
Each page may have a template and a layout, the template wrapping around the layout for a particular page type.

### Templates

Templates generally consist of a series of sections, rows and columns, with a `LayoutSection` component
informing SilverWare where the layout is to be rendered.

### Layouts

Layouts also consist of sections, rows and columns, with a series of components added to columns in order
to provide any type of functionality you require. Typically, a `PageComponent` will be added to a layout, in
order to render the actual template of the page itself.

### Panels

Panels work in conjunction with an `AreaComponent`. Say you want two different page types to use the same
template and layout, but one page type has a different sidebar. Within the layout, you could add an
`AreaComponent` called "Sidebar". This component simply acts as a marker to inform SilverWare that
certain pages will display different content in this area.

You could now add a `Panel` called "Contact Sidebar". Select the areas this panel will display on
(e.g. "Sidebar"), and then select which pages this panel will appear on (e.g. "Contact Us"). Now, add your
components as children to this panel. Your "Contact Us" page will now show these components in it's sidebar.

### Page Types

SilverWare needs to know which template and layout to render for each particular page type. By default,
the SilverWare app installer creates a "Main" template and layout, and a "Home" layout just for the home page.

You can configure templates and layouts for pages in two places:

- site configuration
- page settings tab

Under the SilverWare tab in site configuration, you will find the "Page Types" tab. This tab allows you to
define the default template and layout for each particular page type. To define a new page type, click the
"Add Page Type" button, then select the type of page, the template, and the layout.

You may also override the template and/or layout on a page-by-page basis.  Click on the page in the site
tree you wish to modify, then click the "Settings" tab, and select the template and/or layout in the
"Appearance" section.

## Issues

Please use the [GitHub issue tracker][issues] for bug reports and feature requests.

## To-Do

- Tests
- Documentation

## Contribution

Your contributions are gladly welcomed to help make this project better. Please see [contributing](CONTRIBUTING.md)
for more information.

## Attribution

- Logo icon designed by [Freepik](http://www.freepik.com) from [www.flaticon.com](http://www.flaticon.com)
  and released under a [Creative Commons BY 3.0](https://creativecommons.org/licenses/by/3.0) license.
- Fugue icons designed by [Yusuke Kamiyamane](https://github.com/yusukekamiyamane/fugue-icons) and released
  under a [Creative Commons BY 3.0](https://creativecommons.org/licenses/by/3.0) license.
- Makes use of [Font Awesome](http://fontawesome.io) by [Dave Gandy](https://github.com/davegandy).
- Makes use of [Bootstrap](https://github.com/twbs/bootstrap) by the
  [Bootstrap Authors](https://github.com/twbs/bootstrap/graphs/contributors)
  and [Twitter, Inc](https://twitter.com).
- Makes use of [webpack](https://github.com/webpack/webpack) and
  [webpack dev server](https://github.com/webpack/webpack-dev-server)
  by [Tobias Koppers](https://github.com/sokra), [Kees Kluskens](https://github.com/SpaceK33z),
  and [many more](https://github.com/webpack/webpack/graphs/contributors).

## Maintainers

[![Colin Tucker](https://avatars3.githubusercontent.com/u/1853705?s=144)](https://github.com/colintucker) | [![Praxis Interactive](https://avatars2.githubusercontent.com/u/1782612?s=144)](http://www.praxis.net.au)
---|---
[Colin Tucker](https://github.com/colintucker) | [Praxis Interactive](http://www.praxis.net.au)

## License

[BSD-3-Clause](LICENSE.md) &copy; Praxis Interactive.

[silverstripe]: https://github.com/silverstripe/silverstripe-framework
[silverstripe-framework]: https://github.com/silverstripe/silverstripe-framework
[silverstripe-cms]: https://github.com/silverstripe/silverstripe-cms
[silverware-app]: https://github.com/praxisnetau/silverware-app
[silverware-theme]: https://github.com/praxisnetau/silverware-theme
[issues]: https://github.com/praxisnetau/silverware/issues
