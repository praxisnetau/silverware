<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Extensions\Control
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\Extensions;

use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Core\Extension;
use SilverStripe\ORM\ArrayList;
use SilverStripe\View\SSViewer;
use SilverWare\Crumbs\Crumbable;
use Exception;

/**
 * An extension which adds extra breadcrumbs functionality to controllers.
 *
 * @package SilverWare\Extensions\Control
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class BreadcrumbsExtension extends Extension
{
    /**
     * Defines the default template to use for rendering breadcrumbs.
     *
     * @var string
     * @config
     */
    private static $default_breadcrumbs_template = 'BreadcrumbsTemplate';
    
    /**
     * Renders a breadcrumb trail for the current page (overrides method from SiteTree).
     *
     * @param integer $maxDepth
     * @param boolean $unlinked
     * @param boolean|string $stopAtPageType
     * @param boolean $showHidden
     *
     * @return DBHTMLText
     */
    public function Breadcrumbs($maxDepth = 20, $unlinked = false, $stopAtPageType = false, $showHidden = false)
    {
        $pages = $this->owner->getBreadcrumbItems($maxDepth, $stopAtPageType, $showHidden);
        
        $template = new SSViewer($this->owner->getBreadcrumbsTemplate());
        
        return $template->process(
            $this->owner->customise([
                'Pages' => $pages,
                'Unlinked' => $unlinked
            ])
        );
    }
    
    /**
     * Answers the name of the template to use for rendering breadcrumbs.
     *
     * @return string
     */
    public function getBreadcrumbsTemplate()
    {
        if ($template = $this->owner->config()->breadcrumbs_template) {
            return $template;
        }
        
        return $this->owner->config()->default_breadcrumbs_template;
    }
    
    /**
     * Answers a list of breadcrumbs for the current page.
     *
     * @param integer $maxDepth
     * @param boolean|string $stopAtPageType
     * @param boolean $showHidden
     *
     * @throws Exception
     *
     * @return ArrayList
     */
    public function getBreadcrumbItems($maxDepth = 20, $stopAtPageType = false, $showHidden = false)
    {
        // Obtain Model Breadcrumb Items:
        
        $items = $this->owner->data()->getBreadcrumbItems($maxDepth, $stopAtPageType, $showHidden);
        
        // Obtain Extra Breadcrumb Items:
        
        $extra = $this->owner->getExtraBreadcrumbItems();
        
        // Check Breadcrumb Item Instances:
        
        $extra->each(function ($item) {
            
            if (!($item instanceof SiteTree) && !($item instanceof Crumbable)) {
                
                throw new Exception(
                    sprintf(
                        'Item of class "%s" is not a SiteTree object or an implementor of Crumbable',
                        get_class($item)
                    )
                );
                
            }
            
        });
        
        // Merge Extra Breadcrumb Items:
        
        $items->merge($extra);
        
        // Answer Breadcrumb Items:
        
        return $items;
    }
    
    /**
     * Answers a list of extra breadcrumb items for the template.
     *
     * @return ArrayList
     */
    public function getExtraBreadcrumbItems()
    {
        return ArrayList::create();
    }
}
