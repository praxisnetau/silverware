<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Extensions\Admin
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\Extensions\Admin;

use SilverStripe\Admin\LeftAndMainExtension as BaseExtension;
use SilverStripe\Forms\HTMLEditor\TinyMCEConfig;
use SilverStripe\View\SSViewer;
use SilverStripe\View\ThemeResourceLoader;
use SilverWare\Grid\Grid;

/**
 * A left and main extension to add SilverWare functionality to the admin.
 *
 * @package SilverWare\Extensions\Admin
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class LeftAndMainExtension extends BaseExtension
{
    /**
     * Event handler method triggered when the CMS initialises.
     *
     * @return void
     */
    public function init()
    {
        // Initialise Grid Framework:
        
        Grid::framework()->doInit();
        
        // Initialise Themed Editor CSS:
        
        $this->initThemedEditorCSS();
    }
    
    /**
     * Merges configured editor CSS from the theme into HTML editor config.
     *
     * @return void
     */
    protected function initThemedEditorCSS()
    {
        // Initialise:
        
        $paths = [];
        
        // Iterate Themed Editor CSS Files:
        
        foreach ($this->getThemedEditorCSS() as $name) {
            
            if ($path = ThemeResourceLoader::inst()->findThemedCSS($name, SSViewer::get_themes())) {
                $paths[] = $path;
            }
            
        }
        
        // Merge Themed Editor CSS Paths into HTML Editor Config:
        
        TinyMCEConfig::config()->merge('editor_css', $paths);
    }
    
    /**
     * Answers an array of the themed editor CSS required for the HTML editor.
     *
     * @return array
     */
    protected function getThemedEditorCSS()
    {
        return (array) $this->owner->config()->themed_editor_css;
    }
}
