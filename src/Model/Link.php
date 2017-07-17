<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Model
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\Model;

use SilverStripe\ORM\ArrayLib;
use SilverWare\Extensions\Model\LinkToExtension;
use SilverWare\Extensions\Style\LinkColorStyle;
use SilverWare\FontIcons\Extensions\FontIconExtension;
use Page;

/**
 * An extension of the component class for a link.
 *
 * @package SilverWare\Model
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class Link extends Component
{
    /**
     * Human-readable singular name.
     *
     * @var string
     * @config
     */
    private static $singular_name = 'Link';
    
    /**
     * Human-readable plural name.
     *
     * @var string
     * @config
     */
    private static $plural_name = 'Links';
    
    /**
     * Description of this object.
     *
     * @var string
     * @config
     */
    private static $description = 'A component which represents a link';
    
    /**
     * Icon file for this object.
     *
     * @var string
     * @config
     */
    private static $icon = 'silverware/admin/client/dist/images/icons/Link.png';
    
    /**
     * Defines an ancestor class to hide from the admin interface.
     *
     * @var string
     * @config
     */
    private static $hide_ancestor = Component::class;
    
    /**
     * Defines the allowed children for this object.
     *
     * @var array|string
     * @config
     */
    private static $allowed_children = 'none';
    
    /**
     * Defines the extension classes to apply to this object.
     *
     * @var array
     * @config
     */
    private static $extensions = [
        FontIconExtension::class,
        LinkToExtension::class,
        LinkColorStyle::class
    ];
    
    /**
     * Defines the available icon sizes (in pixels).
     *
     * @var array
     * @config
     */
    private static $icon_sizes = [16, 24, 32, 48, 64, 96, 128];
    
    /**
     * Defines the default size of an icon (in pixels).
     *
     * @var integer
     * @config
     */
    private static $default_icon_size = 32;
    
    /**
     * Answers an array of HTML tag attributes for the object.
     *
     * @return array
     */
    public function getAttributes()
    {
        $attributes = array_merge(
            parent::getAttributes(),
            $this->getLinkAttributes()
        );
        
        return $attributes;
    }
    
    /**
     * Answers an array of class names for the HTML template.
     *
     * @return array
     */
    public function getClassNames()
    {
        $classes = array_merge(
            parent::getClassNames(),
            [
                $this->IconSizeClass,
                $this->CornerStyleClass
            ]
        );
        
        return $classes;
    }
    
    /**
     * Renders the font icon tag for the HTML template.
     *
     * @return string
     */
    public function getFontIconTag()
    {
        if ($this->hasFontIcon()) {
            return parent::getFontIconTag();
        }
        
        if (($parent = $this->getParent()) && $parent->FontIconTag) {
            return $parent->FontIconTag;
        }
    }
    
    /**
     * Answers the icon size class for the receiver.
     *
     * @return string
     */
    public function getIconSizeClass()
    {
        $size = $this->config()->default_icon_size;
        
        if (($parent = $this->getParent()) && $parent->IconSize) {
            $size = $parent->IconSize;
        }
        
        return sprintf('size-%d', $size);
    }
    
    /**
     * Answers the corner style class for the receiver.
     *
     * @return string
     */
    public function getCornerStyleClass()
    {
        if (($parent = $this->getParent()) && $parent->CornerStyleClass) {
            return $parent->CornerStyleClass;
        }
    }
    
    /**
     * Defines the receiver from the given page.
     *
     * @param Page $page
     * @param string $nameField
     *
     * @return $this
     */
    public function fromPage(Page $page, $nameField = 'MenuTitle')
    {
        $this->Title = $page->{$nameField};
        $this->LinkPageID = $page->ID;
        
        return $this;
    }
    
    /**
     * Answers the CSS prefix used for the custom CSS template.
     *
     * @return string
     */
    public function getCustomCSSPrefix()
    {
        if ($parent = $this->getParent()) {
            return sprintf('%s %s', $parent->CSSID, $this->CSSID);
        }
        
        return $this->CSSID;
    }
    
    /**
     * Answers an array of options for an icon size field.
     *
     * @return array
     */
    public function getIconSizeOptions()
    {
        return ArrayLib::valuekey($this->config()->icon_sizes);
    }
}
