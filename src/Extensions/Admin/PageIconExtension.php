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

use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Control\Director;
use SilverStripe\Core\ClassInfo;
use SilverStripe\Core\Convert;
use SilverStripe\Core\Extension;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\View\Requirements;

/**
 * An extension which generates correct page icon CSS for the admin (fixes a bug in SS4 alpha).
 *
 * @package SilverWare\Extensions\Admin
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class PageIconExtension extends Extension
{
    /**
     * Selectors for page icon elements.
     *
     * @var array
     */
    protected $selectors = [
        '.page-icon.class-%1$s',
        'li.class-%1$s > a .jstree-pageicon'
    ];
    
    /**
     * Performs initialisation before any action is called on the receiver.
     */
    public function init()
    {
        Requirements::customCSS($this->generateCustomCSS());
    }
    
    /**
     * Generates a string of custom CSS to display the icons for site tree subclasses.
     *
     * @return string
     */
    private function generateCustomCSS()
    {
        // Create CSS Array:
        
        $css = [];
        
        // Define CSS Array:
        
        foreach (ClassInfo::subclassesFor(SiteTree::class) as $class) {
            
            // Obtain Singleton:
            
            $singleton = Injector::inst()->get($class);
            
            // Obtain Icon Config:
            
            if ($icon = $singleton->config()->icon) {
                
                // Obtain Page Icon Class:
                
                $iconClass = Convert::raw2htmlid($class);
                
                // Create CSS Selector:
                
                $selector = sprintf(implode(', ', $this->selectors), $iconClass);
                
                // Create CSS Definition:
                
                if (Director::fileExists($icon)) {
                    $css[] = sprintf("%s { background: transparent url('%s') 0 0 no-repeat; }", $selector, $icon);
                } else {
                    $css[] = sprintf("%s { %s }", $selector, $icon);
                }
                
            }
            
        }
        
        // Answer CSS String:
        
        return implode("\n", $css);
    }
}
