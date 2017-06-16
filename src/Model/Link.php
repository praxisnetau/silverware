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

use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\SelectionGroup;
use SilverStripe\Forms\SelectionGroup_Item;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\ArrayLib;
use SilverWare\Extensions\Style\LinkColorStyle;
use SilverWare\FontIcons\Extensions\FontIconExtension;
use SilverWare\Forms\FieldSection;
use SilverWare\Forms\PageDropdownField;
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
     * Define constants.
     */
    const MODE_PAGE = 'page';
    const MODE_URL  = 'url';
    
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
     * Maps field names to field types for this object.
     *
     * @var array
     * @config
     */
    private static $db = [
        'LinkTo' => 'Varchar(8)',
        'LinkURL' => 'Varchar(2048)',
        'OpenLinkInNewTab' => 'Boolean'
    ];
    
    /**
     * Defines the has-one associations for this object.
     *
     * @var array
     * @config
     */
    private static $has_one = [
        'LinkPage' => Page::class
    ];
    
    /**
     * Defines the default values for the fields of this object.
     *
     * @var array
     * @config
     */
    private static $defaults = [
        'LinkTo' => 'page',
        'OpenLinkInNewTab' => 0
    ];
    
    /**
     * Defines the extension classes to apply to this object.
     *
     * @var array
     * @config
     */
    private static $extensions = [
        FontIconExtension::class,
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
     * Answers a list of field objects for the CMS interface.
     *
     * @return FieldList
     */
    public function getCMSFields()
    {
        // Obtain Field Objects (from parent):
        
        $fields = parent::getCMSFields();
        
        // Create Main Fields:
        
        $fields->addFieldsToTab(
            'Root.Main',
            [
                SelectionGroup::create(
                    'LinkTo',
                    [
                        SelectionGroup_Item::create(
                            self::MODE_PAGE,
                            PageDropdownField::create(
                                'LinkPageID',
                                ''
                            ),
                            $this->fieldLabel('Page')
                        ),
                        SelectionGroup_Item::create(
                            self::MODE_URL,
                            TextField::create(
                                'LinkURL',
                                ''
                            ),
                            $this->fieldLabel('URL')
                        )
                    ]
                )->setTitle($this->fieldLabel('LinkTo'))
            ]
        );
        
        // Create Options Fields:
        
        $fields->addFieldsToTab(
            'Root.Options',
            [
                FieldSection::create(
                    'LinkOptions',
                    $this->i18n_singular_name(),
                    [
                        CheckboxField::create(
                            'OpenLinkInNewTab',
                            $this->fieldLabel('OpenLinkInNewTab')
                        )
                    ]
                )
            ]
        );
        
        // Answer Field Objects:
        
        return $fields;
    }
    
    /**
     * Answers the labels for the fields of the receiver.
     *
     * @param boolean $includerelations Include labels for relations.
     *
     * @return array
     */
    public function fieldLabels($includerelations = true)
    {
        // Obtain Field Labels (from parent):
        
        $labels = parent::fieldLabels($includerelations);
        
        // Define Field Labels:
        
        $labels['URL'] = _t(__CLASS__ . '.URL', 'URL');
        $labels['Page'] = _t(__CLASS__ . '.PAGE', 'Page');
        $labels['LinkTo'] = _t(__CLASS__ . '.LINKTO', 'Link to');
        $labels['LinkURL'] = _t(__CLASS__ . '.LINKURL', 'Link URL');
        $labels['LinkPageID'] = _t(__CLASS__ . '.LINKPAGE', 'Link page');
        $labels['OpenLinkInNewTab'] = _t(__CLASS__ . '.OPENLINKINNEWTAB', 'Open link in new tab');
        
        // Define Relation Labels:
        
        if ($includerelations) {
            $labels['LinkPage'] = _t(__CLASS__ . '.has_one_LinkPage', 'Page');
        }
        
        // Answer Field Labels:
        
        return $labels;
    }
    
    /**
     * Answers an array of HTML tag attributes for the object.
     *
     * @return array
     */
    public function getAttributes()
    {
        $attributes = array_merge(
            parent::getAttributes(),
            [
                'href' => $this->Link,
                'title' => $this->Title
            ]
        );
        
        if ($this->OpenLinkInNewTab) {
            $attributes['target'] = '_blank';
        }
        
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
     * Answers the link for the template.
     *
     * @return string
     */
    public function getLink()
    {
        if ($this->isURL() && $this->LinkURL) {
            return $this->dbObject('LinkURL')->URL();
        }
        
        if ($this->isPage() && $this->LinkPageID) {
            return $this->LinkPage()->Link();
        }
    }
    
    /**
     * Answers true if the link is to a page.
     *
     * @return boolean
     */
    public function isPage()
    {
        return ($this->LinkTo == self::MODE_PAGE);
    }
    
    /**
     * Answers true if the link is to a URL.
     *
     * @return boolean
     */
    public function isURL()
    {
        return ($this->LinkTo == self::MODE_URL);
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
    
    /**
     * Renders the object for the HTML template.
     *
     * @param string $layout Page layout passed from template.
     * @param string $title Page title passed from template.
     *
     * @return DBHTMLText|string
     */
    public function renderSelf($layout = null, $title = null)
    {
        return $this->renderWith(static::class);
    }
}
