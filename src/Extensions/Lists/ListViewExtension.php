<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Extensions\Lists
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\Extensions\Lists;

use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataExtension;
use SilverWare\Components\BaseListComponent;
use SilverWare\Components\ListComponent;
use SilverWare\Forms\DimensionsField;
use SilverWare\Forms\FieldSection;
use SilverWare\Forms\ViewportsField;
use SilverWare\ORM\FieldType\DBViewports;
use SilverWare\Tools\ImageTools;
use SilverWare\Tools\ViewTools;

/**
 * A data extension class to add list view functionality to the extended object.
 *
 * @package SilverWare\Extensions\Lists
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class ListViewExtension extends DataExtension
{
    /**
     * Maps field names to field types for this object.
     *
     * @var array
     * @config
     */
    private static $db = [
        'ListTitle' => 'Varchar(255)',
        'ListImageResize' => 'Dimensions',
        'ListImageResizeMethod' => 'Varchar(32)',
        'ListImageLinksTo' => 'Varchar(8)',
        'ListImageAlignment' => 'Viewports',
        'ListTextAlignment' => 'Viewports',
        'ListItemsPerPage' => 'AbsoluteInt',
        'ListHeadingLevel' => 'Varchar(2)',
        'ListButtonLabel' => 'Varchar(128)',
        'ListPaginateItems' => 'Varchar(1)',
        'ListLinkTitles' => 'Varchar(1)',
        'ListTitleHidden' => 'Varchar(1)'
    ];
    
    /**
     * Defines the default list component class to use.
     *
     * @var string
     * @config
     */
    private static $default_list_component_class = ListComponent::class;
    
    /**
     * Updates the CMS fields of the extended object.
     *
     * @param FieldList $fields List of CMS fields from the extended object.
     *
     * @return void
     */
    public function updateCMSFields(FieldList $fields)
    {
        // Create Tabs:
        
        $fields->findOrMakeTab('Root.Style', $this->owner->fieldLabel('Style'));
        $fields->findOrMakeTab('Root.Options', $this->owner->fieldLabel('Options'));
        
        // Add Extension Class:
        
        $fields->fieldByName('Root')->addExtraClass(ViewTools::singleton()->convertClass(self::class));
        
        // Define Placeholder:
        
        $placeholder = _t(__CLASS__ . '.DROPDOWNDEFAULT', '(default)');
        
        // Create List Component:
        
        $list = Injector::inst()->get($this->owner->getListComponentClass());
        
        // Create Style Fields:
        
        $fields->addFieldToTab(
            'Root.Style',
            FieldSection::create(
                'ListViewStyle',
                $this->owner->fieldLabel('ListView'),
                [
                    DropdownField::create(
                        'ListHeadingLevel',
                        $this->owner->fieldLabel('ListHeadingLevel'),
                        $list->getTitleLevelOptions()
                    )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder),
                    ViewportsField::create(
                        'ListTextAlignment',
                        $this->owner->fieldLabel('ListTextAlignment'),
                        $list->getTextAlignmentOptions()
                    ),
                    ViewportsField::create(
                        'ListImageAlignment',
                        $this->owner->fieldLabel('ListImageAlignment'),
                        $list->getImageAlignmentOptions()
                    ),
                    DimensionsField::create(
                        'ListImageResize',
                        $this->owner->fieldLabel('ListImageResize')
                    ),
                    DropdownField::create(
                        'ListImageResizeMethod',
                        $this->owner->fieldLabel('ListImageResizeMethod'),
                        ImageTools::singleton()->getResizeMethods()
                    )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder),
                ]
            )
        );
        
        // Create Options Fields:
        
        $fields->addFieldToTab(
            'Root.Options',
            FieldSection::create(
                'ListViewOptions',
                $this->owner->fieldLabel('ListView'),
                [
                    DropdownField::create(
                        'ListPaginateItems',
                        $this->owner->fieldLabel('ListPaginateItems'),
                        $this->getToggleOptions()
                    )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder),
                    TextField::create(
                        'ListItemsPerPage',
                        $this->owner->fieldLabel('ListItemsPerPage')
                    ),
                    DropdownField::create(
                        'ListImageLinksTo',
                        $this->owner->fieldLabel('ListImageLinksTo'),
                        $list->getImageLinksToOptions()
                    )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder),
                    TextField::create(
                        'ListTitle',
                        $this->owner->fieldLabel('ListTitle')
                    ),
                    TextField::create(
                        'ListButtonLabel',
                        $this->owner->fieldLabel('ListButtonLabel')
                    ),
                    DropdownField::create(
                        'ListLinkTitles',
                        $this->owner->fieldLabel('ListLinkTitles'),
                        $this->getToggleOptions()
                    )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder),
                    DropdownField::create(
                        'ListTitleHidden',
                        $this->owner->fieldLabel('ListTitleHidden'),
                        $this->getToggleOptions()
                    )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder)
                ]
            )
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
        $labels['Style'] = _t(__CLASS__ . '.STYLE', 'Style');
        $labels['Options'] = _t(__CLASS__ . '.OPTIONS', 'Options');
        $labels['Enabled'] = _t(__CLASS__ . '.ENABLED', 'Enabled');
        $labels['Disabled'] = _t(__CLASS__ . '.DISABLED', 'Disabled');
        $labels['ListView'] = _t(__CLASS__ . '.LISTVIEW', 'List view');
        $labels['ListTitle'] = _t(__CLASS__ . '.LISTTITLE', 'List title');
        $labels['ListLinkTitles'] = _t(__CLASS__ . '.LINKTITLES', 'Link titles');
        $labels['ListButtonLabel'] = _t(__CLASS__ . '.BUTTONLABEL', 'Button label');
        $labels['ListHeadingLevel'] = _t(__CLASS__ . '.HEADINGLEVEL', 'Heading level');
        $labels['ListItemsPerPage'] = _t(__CLASS__ . '.ITEMSPERPAGE', 'Items per page');
        $labels['ListPaginateItems'] = _t(__CLASS__ . '.PAGINATEITEMS', 'Paginate items');
        $labels['ListImageLinksTo'] = _t(__CLASS__ . '.IMAGELINKSTO', 'Image links to');
        $labels['ListImageResize'] = _t(__CLASS__ . '.IMAGEDIMENSIONS', 'Image dimensions');
        $labels['ListImageResizeMethod'] = _t(__CLASS__ . '.IMAGERESIZEMETHOD', 'Image resize method');
        $labels['ListImageAlignment'] = _t(__CLASS__ . '.IMAGEALIGNMENT', 'Image alignment');
        $labels['ListTextAlignment'] = _t(__CLASS__ . '.TEXTALIGNMENT', 'Text alignment');
        $labels['ListTitleHidden'] = _t(__CLASS__ . '.HIDELISTTITLE', 'Hide list title');
    }
    
    /**
     * Event method called before the extended object is written to the database.
     *
     * @return void
     */
    public function onBeforeWrite()
    {
        if (!$this->owner->ListPaginateItems) {
            $this->owner->ListItemsPerPage = null;
        }
    }
    
    /**
     * Answers the source for the list component.
     *
     * @return ListSource|SS_List
     */
    public function getListSource()
    {
        return $this->owner;
    }
    
    /**
     * Answers the class to use for the list component.
     *
     * @return string
     */
    public function getListComponentClass()
    {
        return Config::inst()->get(self::class, 'default_list_component_class');
    }
    
    /**
     * Answers the list component for the template.
     *
     * @return BaseListComponent
     */
    public function getListComponent()
    {
        // Create List Component:
        
        $list = Injector::inst()->create($this->owner->getListComponentClass());
        
        // Define List Component:
        
        $list->setStyleIDFrom($this->owner);
        
        $this->setListField($list, 'Title', 'ListTitle');
        $this->setListField($list, 'HideTitle', 'ListTitleHidden');
        
        $this->setListField($list, 'HeadingLevel', 'ListHeadingLevel');
        $this->setListField($list, 'LinkTitles', 'ListLinkTitles');
        
        $this->setListField($list, 'PaginateItems', 'ListPaginateItems');
        $this->setListField($list, 'ItemsPerPage', 'ListItemsPerPage');
        
        $this->setListField($list, 'ImageResizeWidth', 'ListImageResizeWidth');
        $this->setListField($list, 'ImageResizeHeight', 'ListImageResizeHeight');
        $this->setListField($list, 'ImageResizeMethod', 'ListImageResizeMethod');
        $this->setListField($list, 'ImageLinksTo', 'ListImageLinksTo');
        
        $this->setListField($list, 'ButtonLabel', 'ListButtonLabel');
        
        // Define Text and Image Alignment:
        
        foreach (DBViewports::singleton()->getViewports() as $viewport) {
            
            $textField  = "TextAlignment{$viewport}";
            $imageField = "ImageAlignment{$viewport}";
            
            $this->setListField($list, $textField, "List{$textField}");
            $this->setListField($list, $imageField, "List{$imageField}");
            
        }
        
        // Define List Parent:
        
        $list->setParent($this->owner);
        
        // Define List Source:
        
        $list->setSource($this->owner->getListSource());
        
        // Initialise Component:
        
        $list->doInit();
        
        // Answer List Component:
        
        return $list;
    }
    
    /**
     * Answers true if the extended object provides a list component for the template.
     *
     * @return boolean
     */
    public function hasListComponent()
    {
        return ($this->owner->ListComponent instanceof BaseListComponent);
    }
    
    /**
     * Defines the specified field for the given list component using a value from the hierarchy.
     *
     * @param BaseListComponent $list
     * @param string $field
     * @param string $value
     *
     * @return void
     */
    protected function setListField(BaseListComponent $list, $field, $value)
    {
        $value = $this->owner->getFieldFromHierarchy($value);
        
        if (!is_null($value)) {
            $list->$field = $value;
        }
    }
    
    /**
     * Answers an array of options for a toggle dropdown field.
     *
     * @return array
     */
    protected function getToggleOptions()
    {
        return [
            0 => _t(__CLASS__ . '.TOGGLENO', 'No'),
            1 => _t(__CLASS__ . '.TOGGLEYES', 'Yes')
        ];
    }
}
