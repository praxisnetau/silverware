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
use SilverStripe\Forms\TextField;
use SilverWare\Forms\FieldSection;

/**
 * An extension of the base component class for a list component.
 *
 * @package SilverWare\Components
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class ListComponent extends BaseListComponent
{
    /**
     * Define show constants.
     */
    const SHOW_ALL = 'all';
    const SHOW_LAST = 'last';
    const SHOW_FIRST = 'first';
    
    /**
     * Define align constants.
     */
    const ALIGN_LEFT = 'left';
    const ALIGN_RIGHT = 'right';
    const ALIGN_STAGGER = 'stagger';
    
    /**
     * Human-readable singular name.
     *
     * @var string
     * @config
     */
    private static $singular_name = 'List Component';
    
    /**
     * Human-readable plural name.
     *
     * @var string
     * @config
     */
    private static $plural_name = 'List Components';
    
    /**
     * Description of this object.
     *
     * @var string
     * @config
     */
    private static $description = 'A component to show a list of items';
    
    /**
     * Icon file for this object.
     *
     * @var string
     * @config
     */
    private static $icon = 'silverware/admin/client/dist/images/icons/ListComponent.png';
    
    /**
     * Defines an ancestor class to hide from the admin interface.
     *
     * @var string
     * @config
     */
    private static $hide_ancestor = BaseListComponent::class;
    
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
        'DateFormat' => 'Varchar(32)',
        'HeadingLevel' => 'Varchar(2)',
        'ImageAlignment' => 'Varchar(16)',
        'ButtonLabel' => 'Varchar(128)',
        'LinkTitles' => 'Boolean'
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
        'ShowFooter' => 'all',
        'DateFormat' => 'd MMMM Y',
        'LinkTitles' => 1
    ];
    
    /**
     * Defines the allowed children for this object.
     *
     * @var array|string
     * @config
     */
    private static $allowed_children = 'none';
    
    /**
     * Defines the default heading level to use.
     *
     * @var array
     * @config
     */
    private static $heading_level_default = 'h4';
    
    /**
     * Answers a list of field objects for the CMS interface.
     *
     * @return FieldList
     */
    public function getCMSFields()
    {
        // Obtain Field Objects (from parent):
        
        $fields = parent::getCMSFields();
        
        // Define Placeholders:
        
        $placeholderNone    = _t(__CLASS__ . '.NONE', 'None');
        $placeholderDefault = _t(__CLASS__ . '.DROPDOWNDEFAULT', '(default)');
        
        // Create Style Fields:
        
        $fields->addFieldToTab(
            'Root.Style',
            FieldSection::create(
                'ListComponentStyle',
                $this->i18n_singular_name(),
                [
                    DropdownField::create(
                        'HeadingLevel',
                        $this->fieldLabel('HeadingLevel'),
                        $this->getTitleLevelOptions()
                    )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholderDefault),
                ]
            )
        );
        
        // Create Options Fields:
        
        $fields->addFieldsToTab(
            'Root.Options',
            [
                FieldSection::create(
                    'ListComponentOptions',
                    $this->i18n_singular_name(),
                    [
                        DropdownField::create(
                            'ShowImage',
                            $this->fieldLabel('ShowImage'),
                            $this->getShowOptions()
                        )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholderNone),
                        DropdownField::create(
                            'ShowHeader',
                            $this->fieldLabel('ShowHeader'),
                            $this->getShowOptions()
                        )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholderNone),
                        DropdownField::create(
                            'ShowDetails',
                            $this->fieldLabel('ShowDetails'),
                            $this->getShowOptions()
                        )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholderNone),
                        DropdownField::create(
                            'ShowSummary',
                            $this->fieldLabel('ShowSummary'),
                            $this->getShowOptions()
                        )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholderNone),
                        DropdownField::create(
                            'ShowContent',
                            $this->fieldLabel('ShowContent'),
                            $this->getShowOptions()
                        )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholderNone),
                        DropdownField::create(
                            'ShowFooter',
                            $this->fieldLabel('ShowFooter'),
                            $this->getShowOptions()
                        )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholderNone),
                        DropdownField::create(
                            'ImageAlignment',
                            $this->fieldLabel('ImageAlignment'),
                            $this->getImageAlignmentOptions()
                        )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholderNone),
                        TextField::create(
                            'DateFormat',
                            $this->fieldLabel('DateFormat')
                        ),
                        TextField::create(
                            'ButtonLabel',
                            $this->fieldLabel('ButtonLabel')
                        ),
                        CheckboxField::create(
                            'LinkTitles',
                            $this->fieldLabel('LinkTitles')
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
        
        $labels['DateFormat'] = _t(__CLASS__ . '.DATEFORMAT', 'Date format');
        $labels['ShowImage'] = _t(__CLASS__ . '.SHOWIMAGE', 'Show image');
        $labels['ShowHeader'] = _t(__CLASS__ . '.SHOWHEADER', 'Show header');
        $labels['ShowDetails'] = _t(__CLASS__ . '.SHOWDETAILS', 'Show details');
        $labels['ShowSummary'] = _t(__CLASS__ . '.SHOWSUMMARY', 'Show summary');
        $labels['ShowContent'] = _t(__CLASS__ . '.SHOWCONTENT', 'Show content');
        $labels['ShowFooter'] = _t(__CLASS__ . '.SHOWFOOTER', 'Show footer');
        $labels['HeadingLevel'] = _t(__CLASS__ . '.HEADINGLEVEL', 'Heading level');
        $labels['LinkTitles'] = _t(__CLASS__ . '.LINKTITLES', 'Link titles');
        $labels['ButtonLabel'] = _t(__CLASS__ . '.BUTTONLABEL', 'Button label');
        $labels['ImageAlignment'] = _t(__CLASS__ . '.IMAGEALIGNMENT', 'Image alignment');
        
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
        
        $this->ButtonLabel = _t(__CLASS__ . '.DEFAULTBUTTONLABEL', 'Read More');
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
        
        if ($this->ImageAlignment) {
            $classes[] = sprintf('image-align-%s', $this->ImageAlignment);
        }
        
        $this->extend('updateWrapperClassNames', $classes);
        
        return $classes;
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
     * Answers an array of options for the show fields.
     *
     * @return array
     */
    public function getShowOptions()
    {
        return [
            self::SHOW_FIRST => _t(__CLASS__ . '.FIRST', 'First'),
            self::SHOW_LAST => _t(__CLASS__ . '.LAST', 'Last'),
            self::SHOW_ALL => _t(__CLASS__ . '.ALL', 'All')
        ];
    }
    
    /**
     * Answers an array of options for the image alignment field.
     *
     * @return array
     */
    public function getImageAlignmentOptions()
    {
        return [
            self::ALIGN_LEFT => _t(__CLASS__ . '.LEFT', 'Left'),
            self::ALIGN_RIGHT => _t(__CLASS__ . '.RIGHT', 'Right'),
            self::ALIGN_STAGGER => _t(__CLASS__ . '.STAGGER', 'Stagger')
        ];
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
        return (
            ($this->{"Show{$name}"} == self::SHOW_FIRST && $isFirst) ||
            ($this->{"Show{$name}"} == self::SHOW_LAST && $isLast) ||
            ($this->{"Show{$name}"} == self::SHOW_ALL)
        );
    }
}
