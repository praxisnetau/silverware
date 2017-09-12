<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Sections
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\Sections;

use SilverStripe\Core\Injector\Injector;
use SilverStripe\ORM\ArrayList;
use SilverWare\Grid\Section;
use SilverWare\Model\Template;
use SilverWare\Tools\ClassTools;
use Page;

/**
 * An extension of the section class for a layout section.
 *
 * @package SilverWare\Sections
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class LayoutSection extends Section
{
    /**
     * Human-readable singular name.
     *
     * @var string
     * @config
     */
    private static $singular_name = 'Layout Section';
    
    /**
     * Human-readable plural name.
     *
     * @var string
     * @config
     */
    private static $plural_name = 'Layout Sections';
    
    /**
     * Description of this object.
     *
     * @var string
     * @config
     */
    private static $description = 'A layout section within a SilverWare template';
    
    /**
     * Icon file for this object.
     *
     * @var string
     * @config
     */
    private static $icon = 'silverware/admin/client/dist/images/icons/LayoutSection.png';
    
    /**
     * Defines the allowed children for this object.
     *
     * @var array|string
     * @config
     */
    private static $allowed_children = 'none';
    
    /**
     * Defines the allowed parents for this object.
     *
     * @var array
     * @config
     */
    private static $allowed_parents = [
        Template::class
    ];
    
    /**
     * Tag name to use when rendering this object.
     *
     * @var string
     * @config
     */
    private static $tag = 'main';
    
    /**
     * Answers a list of all children within the receiver.
     *
     * @return SS_List
     */
    public function getAllChildren()
    {
        if ($this->hasPageLayout()) {
            return $this->getPageLayout()->getAllChildren();
        }
        
        return ArrayList::create();
    }
    
    /**
     * Answers true if the receiver can obtain a layout for the current page.
     *
     * @return boolean
     */
    public function hasPageLayout()
    {
        return (boolean) $this->getPageLayout();
    }
    
    /**
     * Answers the layout for the current page.
     *
     * @return Layout
     */
    public function getPageLayout()
    {
        if ($page = $this->getCurrentPage(Page::class)) {
            return $page->getPageLayout();
        }
        
        return Page::create()->getPageLayout();
    }
    
    /**
     * Renders the component for the HTML template.
     *
     * @param string $layout Page layout passed from template.
     * @param string $title Page title passed from template.
     *
     * @return DBHTMLText|string
     */
    public function renderSelf($layout = null, $title = null)
    {
        return $this->renderTag($this->getPageLayout()->render($layout, $title));
    }
}

