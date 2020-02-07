<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Components
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\Components;

use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverWare\Colorpicker\Forms\ColorField;
use SilverWare\Extensions\Lists\ListSourceExtension;
use SilverWare\Extensions\Model\ImageResizeExtension;
use SilverWare\Extensions\Style\AlignmentStyle;
use SilverWare\Extensions\Style\ButtonStyle;
use SilverWare\Extensions\Style\PaginationStyle;
use SilverWare\FontIcons\Forms\FontIconField;
use SilverWare\Forms\FieldSection;

/**
 * An extension of the base component class for a base list component.
 *
 * @package SilverWare\Components
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class BaseListComponent extends BaseComponent
{
    /**
     * Define show constants.
     */
    const SHOW_ALL   = 'all';
    const SHOW_LAST  = 'last';
    const SHOW_FIRST = 'first';
    const SHOW_NONE  = 'none';
    
    /**
     * Define image align constants.
     */
    const IMAGE_ALIGN_LEFT    = 'left';
    const IMAGE_ALIGN_RIGHT   = 'right';
    const IMAGE_ALIGN_STAGGER = 'stagger';
    
    /**
     * Define image link constants.
     */
    const IMAGE_LINK_ITEM = 'item';
    const IMAGE_LINK_FILE = 'file';
    
    /**
     * Defines the table name to use for this object.
     *
     * @var string
     * @config
     */
    private static $table_name = 'SilverWare_BaseListComponent';
    
    /**
     * Defines an ancestor class to hide from the admin interface.
     *
     * @var string
     * @config
     */
    private static $hide_ancestor = BaseComponent::class;
    
    /**
     * Maps field names to field types for this object.
     *
     * @var array
     * @config
     */
    private static $db = [
        'ShowImage' => 'Varchar(8)',
        'ShowHeader' => 'Varchar(8)',
        'ShowDetails' => 'Varchar(8)',
        'ShowSummary' => 'Varchar(8)',
        'ShowContent' => 'Varchar(8)',
        'ShowFooter' => 'Varchar(8)',
        'ButtonLink' => 'Boolean',
        'ButtonIcon' => 'FontIcon',
        'ButtonLabel' => 'Varchar(128)',
        'HeadingLevel' => 'Varchar(2)',
        'ImageAlign' => 'Varchar(8)',
        'ImageLinksTo' => 'Varchar(8)',
        'OverlayIcon' => 'FontIcon',
        'OverlayIconColor' => 'Color',
        'OverlayBackground' => 'Color',
        'OverlayForeground' => 'Color',
        'OverlayImages' => 'Boolean',
        'OverlayTitle' => 'Boolean',
        'LinkImages' => 'Boolean',
        'LinkTitles' => 'Boolean',
        'HideNoDataMessage' => 'Boolean'
    ];
    
    /**
     * Defines the default values for the fields of this object.
     *
     * @var array
     * @config
     */
    private static $defaults = [
        'ShowImage' => 'all',
        'ShowHeader' => 'all',
        'ShowDetails' => 'all',
        'ShowSummary' => 'all',
        'ShowContent' => 'none',
        'ShowFooter' => 'all',
        'ImageLinksTo' => 'item',
        'OverlayIcon' => 'search',
        'OverlayImages' => 0,
        'OverlayTitle' => 0,
        'LinkImages' => 1,
        'LinkTitles' => 1,
        'ButtonLink' => 0,
        'HideNoDataMessage' => 0
    ];
    
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
        ButtonStyle::class,
        AlignmentStyle::class,
        ListSourceExtension::class,
        ImageResizeExtension::class,
        PaginationStyle::class
    ];
    
    /**
     * Defines the style extension classes to apply to this object.
     *
     * @var array
     * @config
     */
    private static $apply_styles = [
        AlignmentStyle::class,
        PaginationStyle::class
    ];
    
    /**
     * Defines the default heading level to use.
     *
     * @var array
     * @config
     */
    private static $heading_level_default = 'h4';
    
    /**
     * Hides the image fields from the alignment style extension.
     *
     * @var boolean
     * @config
     */
    private static $alignment_style_hide_image = true;
    
    /**
     * Answers a list of field objects for the CMS interface.
     *
     * @return FieldList
     */
    public function getCMSFields()
    {
        
        $this->beforeUpdateCMSFields(function (FieldList $fields) {

            // Define Placeholders:
            $placeholderDefault = _t(__CLASS__ . '.DROPDOWNDEFAULT', '(default)');
            
            // Create Style Fields:
            $fields->addFieldsToTab(
                'Root.Style',
                [
                    FieldSection::create(
                        'ListStyle',
                        $this->fieldLabel('ListStyle'),
                        [
                            DropdownField::create(
                                'HeadingLevel',
                                $this->fieldLabel('HeadingLevel'),
                                $this->getTitleLevelOptions()
                            )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholderDefault),
                            DropdownField::create(
                                'ImageAlign',
                                $this->fieldLabel('ImageAlign'),
                                $this->getImageAlignOptions()
                            )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholderDefault),
                            ColorField::create(
                                'OverlayBackground',
                                $this->fieldLabel('OverlayBackground')
                            ),
                            ColorField::create(
                                'OverlayForeground',
                                $this->fieldLabel('OverlayForeground')
                            ),
                            FontIconField::create(
                                'OverlayIcon',
                                $this->fieldLabel('OverlayIcon')
                            ),
                            ColorField::create(
                                'OverlayIconColor',
                                $this->fieldLabel('OverlayIconColor')
                            ),
                            FontIconField::create(
                                'ButtonIcon',
                                $this->fieldLabel('ButtonIcon')
                            )
                        ]
                    )
                ]
            );
            
            // Add List Style Fields:
            $fields->addFieldsToTab('Root.Style', $this->getListStyleFields());
            
            // Create Options Fields:
            $fields->addFieldsToTab(
                'Root.Options',
                [
                    FieldSection::create(
                        'ListOptions',
                        $this->fieldLabel('ListOptions'),
                        [
                            DropdownField::create(
                                'ShowImage',
                                $this->fieldLabel('ShowImage'),
                                $this->getShowOptions()
                            ),
                            DropdownField::create(
                                'ShowHeader',
                                $this->fieldLabel('ShowHeader'),
                                $this->getShowOptions()
                            ),
                            DropdownField::create(
                                'ShowDetails',
                                $this->fieldLabel('ShowDetails'),
                                $this->getShowOptions()
                            ),
                            DropdownField::create(
                                'ShowSummary',
                                $this->fieldLabel('ShowSummary'),
                                $this->getShowOptions()
                            ),
                            DropdownField::create(
                                'ShowContent',
                                $this->fieldLabel('ShowContent'),
                                $this->getShowOptions()
                            ),
                            DropdownField::create(
                                'ShowFooter',
                                $this->fieldLabel('ShowFooter'),
                                $this->getShowOptions()
                            ),
                            TextField::create(
                                'ButtonLabel',
                                $this->fieldLabel('ButtonLabel')
                            ),
                            CheckboxField::create(
                                'ButtonLink',
                                $this->fieldLabel('ButtonLink')
                            ),
                            CheckboxField::create(
                                'LinkTitles',
                                $this->fieldLabel('LinkTitles')
                            ),
                            CheckboxField::create(
                                'HideNoDataMessage',
                                $this->fieldLabel('HideNoDataMessage')
                            )
                        ]
                    ),
                    FieldSection::create(
                        'ListImageOptions',
                        $this->fieldLabel('ListImageOptions'),
                        [
                            DropdownField::create(
                                'ImageLinksTo',
                                $this->fieldLabel('ImageLinksTo'),
                                $this->getImageLinksToOptions()
                            ),
                            CheckboxField::create(
                                'OverlayImages',
                                $this->fieldLabel('OverlayImages')
                            ),
                            CheckboxField::create(
                                'OverlayTitle',
                                $this->fieldLabel('OverlayTitle')
                            ),
                            CheckboxField::create(
                                'LinkImages',
                                $this->fieldLabel('LinkImages')
                            )
                        ]
                    )
                ]
            );
            
            // Add List Option Fields:
            $fields->addFieldsToTab('Root.Options', $this->getListOptionFields());
        });
        
        return parent::getCMSFields();;
    }
    
    /**
     * Answers the list style fields for the receiver.
     *
     * @return FieldList
     */
    public function getListStyleFields()
    {
        return FieldList::create();
    }
    
    /**
     * Answers the list option fields for the receiver.
     *
     * @return FieldList
     */
    public function getListOptionFields()
    {
        return FieldList::create();
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
        
        $labels['ShowImage'] = _t(__CLASS__ . '.SHOWIMAGE', 'Show image');
        $labels['ShowHeader'] = _t(__CLASS__ . '.SHOWHEADER', 'Show header');
        $labels['ShowDetails'] = _t(__CLASS__ . '.SHOWDETAILS', 'Show details');
        $labels['ShowSummary'] = _t(__CLASS__ . '.SHOWSUMMARY', 'Show summary');
        $labels['ShowContent'] = _t(__CLASS__ . '.SHOWCONTENT', 'Show content');
        $labels['ShowFooter'] = _t(__CLASS__ . '.SHOWFOOTER', 'Show footer');
        $labels['LinkImages'] = _t(__CLASS__ . '.LINKIMAGES', 'Link images');
        $labels['LinkTitles'] = _t(__CLASS__ . '.LINKTITLES', 'Link titles');
        $labels['ButtonIcon'] = _t(__CLASS__ . '.BUTTONICON', 'Button icon');
        $labels['ButtonLink'] = _t(__CLASS__ . '.SHOWBUTTONSASLINKS', 'Show buttons as links');
        $labels['ButtonLabel'] = _t(__CLASS__ . '.BUTTONLABEL', 'Button label');
        $labels['HeadingLevel'] = _t(__CLASS__ . '.HEADINGLEVEL', 'Heading level');
        $labels['ImageLinksTo'] = _t(__CLASS__ . '.IMAGELINKSTO', 'Image links to');
        $labels['ImageAlign'] = _t(__CLASS__ . '.IMAGEALIGNMENT', 'Image alignment');
        $labels['OverlayImages'] = _t(__CLASS__ . '.OVERLAYIMAGES', 'Overlay images');
        $labels['ListImageOptions'] = _t(__CLASS__ . '.LISTIMAGES', 'List Images');
        $labels['OverlayIcon'] = _t(__CLASS__ . '.OVERLAYICON', 'Overlay icon');
        $labels['OverlayIconColor'] = _t(__CLASS__ . '.OVERLAYICONCOLOR', 'Overlay icon color');
        $labels['OverlayBackground'] = _t(__CLASS__ . '.OVERLAYBACKGROUNDCOLOR', 'Overlay background color');
        $labels['OverlayForeground'] = _t(__CLASS__ . '.OVERLAYFOREGROUNDCOLOR', 'Overlay foreground color');
        $labels['OverlayTitle'] = _t(__CLASS__ . '.SHOWTITLEINOVERLAY', 'Show title in overlay');
        $labels['HideNoDataMessage'] = _t(__CLASS__ . '.HIDENODATAMESSAGE', 'Hide "no data" message');
        $labels['ListStyle'] = $labels['ListOptions'] = _t(__CLASS__ . '.LIST', 'List');
        
        // Answer Field Labels:
        
        return $labels;
    }
    
    /**
     * Populates the default values for the fields of the receiver.
     *
     * @return void
     */
    public function populateDefaults()
    {
        // Populate Defaults (from parent):
        
        parent::populateDefaults();
        
        // Populate Defaults:
        
        $this->ButtonLabel = _t(__CLASS__ . '.DEFAULTBUTTONLABEL', 'More');
    }
    
    /**
     * Answers true if the receiver can paginate.
     *
     * @return boolean
     */
    public function canPaginate()
    {
        return true;
    }
    
    /**
     * Answers an array of wrapper class names for the HTML template.
     *
     * @return array
     */
    public function getWrapperClassNames()
    {
        $classes = ['items'];
        
        if ($class = $this->getImageAlignClass()) {
            $classes[] = $class;
        }
        
        $this->extend('updateWrapperClassNames', $classes);
        
        return $classes;
    }
    
    /**
     * Answers the image align class for the receiver.
     *
     * @return string
     */
    public function getImageAlignClass()
    {
        switch ($this->ImageAlign) {
            case self::IMAGE_ALIGN_LEFT:
                return 'image-align-left';
            case self::IMAGE_ALIGN_RIGHT:
                return 'image-align-right';
            case self::IMAGE_ALIGN_STAGGER:
                return 'image-align-stagger';
        }
    }
    
    /**
     * Answers the list item template for the specified class.
     *
     * @param string $class
     *
     * @return string
     */
    public function getListItemTemplate($class)
    {
        return sprintf('%s\ListItem', $class);
    }
    
    /**
     * Answers the heading tag for the receiver.
     *
     * @return string
     */
    public function getHeadingTag()
    {
        if ($tag = $this->getField('HeadingLevel')) {
            return $tag;
        }
        
        return $this->config()->heading_level_default;
    }
    
    /**
     * Answers true if the image is to be shown for the current list item.
     *
     * @param boolean $isFirst Item is first in the list.
     * @param boolean $isMiddle Item is in the middle of the list.
     * @param boolean $isLast Item is last in the list.
     *
     * @return boolean
     */
    public function isImageShown($isFirst = false, $isMiddle = false, $isLast = false)
    {
        return $this->isShown('Image', $isFirst, $isMiddle, $isLast);
    }
    
    /**
     * Answers true if the header is to be shown for the current list item.
     *
     * @param boolean $isFirst Item is first in the list.
     * @param boolean $isMiddle Item is in the middle of the list.
     * @param boolean $isLast Item is last in the list.
     *
     * @return boolean
     */
    public function isHeaderShown($isFirst = false, $isMiddle = false, $isLast = false)
    {
        return $this->isShown('Header', $isFirst, $isMiddle, $isLast);
    }
    
    /**
     * Answers true if the details are to be shown for the current list item.
     *
     * @param boolean $isFirst Item is first in the list.
     * @param boolean $isMiddle Item is in the middle of the list.
     * @param boolean $isLast Item is last in the list.
     *
     * @return boolean
     */
    public function isDetailsShown($isFirst = false, $isMiddle = false, $isLast = false)
    {
        return $this->isShown('Details', $isFirst, $isMiddle, $isLast);
    }
    
    /**
     * Answers true if the summary is to be shown for the current list item.
     *
     * @param boolean $isFirst Item is first in the list.
     * @param boolean $isMiddle Item is in the middle of the list.
     * @param boolean $isLast Item is last in the list.
     *
     * @return boolean
     */
    public function isSummaryShown($isFirst = false, $isMiddle = false, $isLast = false)
    {
        return $this->isShown('Summary', $isFirst, $isMiddle, $isLast);
    }
    
    /**
     * Answers true if the content is to be shown for the current list item.
     *
     * @param boolean $isFirst Item is first in the list.
     * @param boolean $isMiddle Item is in the middle of the list.
     * @param boolean $isLast Item is last in the list.
     *
     * @return boolean
     */
    public function isContentShown($isFirst = false, $isMiddle = false, $isLast = false)
    {
        return $this->isShown('Content', $isFirst, $isMiddle, $isLast);
    }
    
    /**
     * Answers true if the content section is empty for the current list item.
     *
     * @param boolean $isFirst Item is first in the list.
     * @param boolean $isMiddle Item is in the middle of the list.
     * @param boolean $isLast Item is last in the list.
     *
     * @return boolean
     */
    public function isContentEmpty($isFirst = false, $isMiddle = false, $isLast = false)
    {
        return !(
            $this->isHeaderShown($isFirst, $isMiddle, $isLast) ||
            $this->isDetailsShown($isFirst, $isMiddle, $isLast) ||
            $this->isSummaryShown($isFirst, $isMiddle, $isLast) ||
            $this->isContentShown($isFirst, $isMiddle, $isLast) ||
            $this->isFooterShown($isFirst, $isMiddle, $isLast)
        );
    }
    
    /**
     * Answers true if the footer is to be shown for the current list item.
     *
     * @param boolean $isFirst Item is first in the list.
     * @param boolean $isMiddle Item is in the middle of the list.
     * @param boolean $isLast Item is last in the list.
     *
     * @return boolean
     */
    public function isFooterShown($isFirst = false, $isMiddle = false, $isLast = false)
    {
        return $this->isShown('Footer', $isFirst, $isMiddle, $isLast);
    }
    
    /**
     * Answers true if the item buttons are to be rendered as links.
     *
     * @return boolean
     */
    public function isButtonLink()
    {
        return (boolean) $this->ButtonLink;
    }
    
    /**
     * Answers a message string to be shown when no data is available.
     *
     * @return string
     */
    public function getNoDataMessage()
    {
        return _t(__CLASS__ . '.NODATAAVAILABLE', 'No data available.');
    }
    
    /**
     * Answers true if the no data message is to be shown.
     *
     * @return boolean
     */
    public function getNoDataMessageShown()
    {
        return !$this->HideNoDataMessage;
    }
    
    /**
     * Defines the value of the hide no data message property.
     *
     * @param boolean $hideNoDataMessage
     *
     * @return $this
     */
    public function setHideNoDataMessage($hideNoDataMessage)
    {
        return $this->setField('HideNoDataMessage', (boolean) $hideNoDataMessage);
    }
    
    /**
     * Answers the value of the hide no data message property.
     *
     * @return boolean
     */
    public function getHideNoDataMessage()
    {
        return $this->getField('HideNoDataMessage');
    }
    
    /**
     * Answers an array of options for the show fields.
     *
     * @return array
     */
    public function getShowOptions()
    {
        return [
            self::SHOW_NONE => _t(__CLASS__ . '.NONE', 'None'),
            self::SHOW_FIRST => _t(__CLASS__ . '.FIRST', 'First'),
            self::SHOW_LAST => _t(__CLASS__ . '.LAST', 'Last'),
            self::SHOW_ALL => _t(__CLASS__ . '.ALL', 'All')
        ];
    }
    
    /**
     * Answers an array of options for the image align field.
     *
     * @return array
     */
    public function getImageAlignOptions()
    {
        return [
            self::IMAGE_ALIGN_LEFT => _t(__CLASS__ . '.LEFT', 'Left'),
            self::IMAGE_ALIGN_RIGHT => _t(__CLASS__ . '.LEFT', 'Right'),
            self::IMAGE_ALIGN_STAGGER => _t(__CLASS__ . '.LEFT', 'Stagger')
        ];
    }
    
    /**
     * Answers an array of options for the image links to field.
     *
     * @return array
     */
    public function getImageLinksToOptions()
    {
        return [
            self::IMAGE_LINK_ITEM => _t(__CLASS__ . '.ITEM', 'Item'),
            self::IMAGE_LINK_FILE => _t(__CLASS__ . '.FILE', 'File')
        ];
    }
    
    /**
     * Answers a URL for the list component.
     *
     * @return string
     */
    public function getURL()
    {
        if ($this->isInDB()) {
            return $this->Link();
        }
        
        return $this->Link('ListComponent');
    }
    
    /**
     * Answers true if the part with the given name is shown.
     *
     * @param string $name Name of part.
     * @param boolean $isFirst Item is first in the list.
     * @param boolean $isMiddle Item is in the middle of the list.
     * @param boolean $isLast Item is last in the list.
     *
     * @return boolean
     */
    protected function isShown($name, $isFirst, $isMiddle, $isLast)
    {
        if (!$this->{"Show{$name}"} || $this->{"Show{$name}"} == self::SHOW_NONE) {
            return false;
        }
        
        return (
            ($this->{"Show{$name}"} == self::SHOW_FIRST && $isFirst) ||
            ($this->{"Show{$name}"} == self::SHOW_LAST && $isLast) ||
            ($this->{"Show{$name}"} == self::SHOW_ALL)
        );
    }
}
