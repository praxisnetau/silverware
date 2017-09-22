<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Extensions\Model
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\Extensions\Model;

use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\SelectionGroup;
use SilverStripe\Forms\SelectionGroup_Item;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataExtension;
use SilverWare\Forms\FieldSection;
use SilverWare\Forms\PageDropdownField;
use SilverWare\Tools\ViewTools;
use Page;

/**
 * A data extension class which adds linking fields to the extended object.
 *
 * @package SilverWare\Extensions\Model
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class LinkToExtension extends DataExtension
{
    /**
     * Define constants.
     */
    const MODE_NONE = 'none';
    const MODE_PAGE = 'page';
    const MODE_URL  = 'url';
    
    /**
     * Maps field names to field types for the extended object.
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
     * Defines the has-one associations for the extended object.
     *
     * @var array
     * @config
     */
    private static $has_one = [
        'LinkPage' => Page::class
    ];
    
    /**
     * Defines the default values for the fields of the extended object.
     *
     * @var array
     * @config
     */
    private static $defaults = [
        'LinkTo' => 'none',
        'OpenLinkInNewTab' => 0
    ];
    
    /**
     * Maps field and method names to the class names of casting objects.
     *
     * @var array
     * @config
     */
    private static $casting = [
        'LinkAttributesHTML' => 'HTMLFragment'
    ];
    
    /**
     * Updates the CMS fields of the extended object.
     *
     * @param FieldList $fields List of CMS fields from the extended object.
     *
     * @return void
     */
    public function updateCMSFields(FieldList $fields)
    {
        // Update Main Fields:
        
        $fields->addFieldsToTab(
            'Root.Main',
            [
                SelectionGroup::create(
                    'LinkTo',
                    [
                        SelectionGroup_Item::create(
                            self::MODE_NONE,
                            null,
                            $this->owner->fieldLabel('None')
                        ),
                        SelectionGroup_Item::create(
                            self::MODE_PAGE,
                            PageDropdownField::create(
                                'LinkPageID',
                                ''
                            ),
                            $this->owner->fieldLabel('Page')
                        ),
                        SelectionGroup_Item::create(
                            self::MODE_URL,
                            TextField::create(
                                'LinkURL',
                                ''
                            ),
                            $this->owner->fieldLabel('URL')
                        )
                    ]
                )->setTitle($this->owner->fieldLabel('LinkTo'))
            ]
        );
        
        // Update Options Fields:
        
        $fields->addFieldsToTab(
            'Root.Options',
            [
                FieldSection::create(
                    'LinkOptions',
                    $this->owner->fieldLabel('LinkOptions'),
                    [
                        CheckboxField::create(
                            'OpenLinkInNewTab',
                            $this->owner->fieldLabel('OpenLinkInNewTab')
                        )
                    ]
                )
            ]
        );
    }
    
    /**
     * Updates the field labels of the extended object.
     *
     * @param array $labels Array of field labels from the extended object.
     *
     * @return void
     */
    public function updateFieldLabels(&$labels)
    {
        $labels['URL'] = _t(__CLASS__ . '.URL', 'URL');
        $labels['None'] = _t(__CLASS__ . '.NONE', 'None');
        $labels['Page'] = _t(__CLASS__ . '.PAGE', 'Page');
        $labels['LinkTo'] = _t(__CLASS__ . '.LINKTO', 'Link To');
        $labels['LinkURL'] = _t(__CLASS__ . '.LINKURL', 'Link URL');
        $labels['LinkPageID'] = _t(__CLASS__ . '.LINKPAGE', 'Link page');
        $labels['LinkOptions'] = _t(__CLASS__ . '.LINK', 'Link');
        $labels['OpenLinkInNewTab'] = _t(__CLASS__ . '.OPENLINKINNEWTAB', 'Open link in new tab');
    }
    
    /**
     * Answers an array of attributes for a link.
     *
     * @return array
     */
    public function getLinkAttributes()
    {
        $attributes = [
            'href' => $this->owner->Link,
            'title' => $this->owner->LinkTitle
        ];
        
        if ($class = $this->owner->LinkClass) {
            $attributes['class'] = $class;
        }
        
        if ($this->owner->OpenLinkInNewTab) {
            $attributes['target'] = '_blank';
        }
        
        return $attributes;
    }
    
    /**
     * Answers the title for the link.
     *
     * @return string
     */
    public function getLinkTitle()
    {
        return $this->owner->Title;
    }
    
    /**
     * Answers a string of link class names for the template.
     *
     * @return string
     */
    public function getLinkClass()
    {
        return ViewTools::singleton()->array2att($this->owner->getLinkClassNames());
    }
    
    /**
     * Answers an array of link class names for the template.
     *
     * @return array
     */
    public function getLinkClassNames()
    {
        return [];
    }
    
    /**
     * Answers a string of attributes for a link.
     *
     * @return string
     */
    public function getLinkAttributesHTML()
    {
        return ViewTools::singleton()->getAttributesHTML($this->owner->getLinkAttributes());
    }
    
    /**
     * Answers the link for the template.
     *
     * @return string
     */
    public function getLink()
    {
        if ($this->owner->isURL() && $this->owner->LinkURL) {
            return $this->owner->dbObject('LinkURL')->URL();
        }
        
        if ($this->owner->isPage() && $this->owner->LinkPageID) {
            return $this->owner->LinkPage()->Link();
        }
    }
    
    /**
     * Answers true if the extended object has a link.
     *
     * @return boolean
     */
    public function hasLink()
    {
        return (boolean) $this->owner->getLink();
    }
    
    /**
     * Answers true if the extended object has a link page.
     *
     * @return boolean
     */
    public function hasLinkPage()
    {
        return $this->owner->LinkPage()->isInDB();
    }
    
    /**
     * Answers true if the link is to a page.
     *
     * @return boolean
     */
    public function isPage()
    {
        return ($this->owner->LinkTo == self::MODE_PAGE);
    }
    
    /**
     * Answers true if the link is to a URL.
     *
     * @return boolean
     */
    public function isURL()
    {
        return ($this->owner->LinkTo == self::MODE_URL);
    }
}
