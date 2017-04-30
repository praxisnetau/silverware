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

use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\CompositeField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\SelectionGroup;
use SilverStripe\Forms\SelectionGroup_Item;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataExtension;
use SilverWare\Components\ListComponent;
use SilverWare\Forms\DimensionsField;
use SilverWare\Tools\ImageTools;

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
        'ListImageAlignment' => 'Varchar(16)',
        'ListImageLinksTo' => 'Varchar(8)',
        'ListItemsPerPage' => 'AbsoluteInt',
        'ListHeadingLevel' => 'Varchar(2)',
        'ListButtonLabel' => 'Varchar(128)',
        'ListPaginateItems' => 'Boolean',
        'ListLinkTitles' => 'Boolean',
        'ListTitleHidden' => 'Boolean'
    ];
    
    /**
     * Defines the default values for the fields of this object.
     *
     * @var array
     * @config
     */
    private static $defaults = [
        'ListImageLinksTo' => 'item',
        'ListItemsPerPage' => 10,
        'ListPaginateItems' => 1,
        'ListLinkTitles' => 1,
        'ListTitleHidden' => 1
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
        // Create Tabs:
        
        $fields->findOrMakeTab('Root.Style', $this->owner->fieldLabel('Style'));
        $fields->findOrMakeTab('Root.Options', $this->owner->fieldLabel('Options'));
        
        // Define Placeholder:
        
        $placeholder = _t(__CLASS__ . '.DROPDOWNDEFAULT', '(default)');
        
        // Create Style Fields:
        
        $fields->addFieldToTab(
            'Root.Style',
            CompositeField::create([
                DropdownField::create(
                    'ListHeadingLevel',
                    $this->owner->fieldLabel('ListHeadingLevel'),
                    ListComponent::singleton()->getTitleLevelOptions()
                )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder),
            ])->setName('ListViewStyle')->setTitle($this->owner->fieldLabel('ListView'))
        );
        
        // Create Options Fields:
        
        $fields->addFieldToTab(
            'Root.Options',
            CompositeField::create([
                SelectionGroup::create(
                    'ListPaginateItems',
                    [
                        SelectionGroup_Item::create(
                            '0',
                            null,
                            $this->owner->fieldLabel('Disabled')
                        ),
                        SelectionGroup_Item::create(
                            '1',
                            TextField::create(
                                'ListItemsPerPage',
                                $this->owner->fieldLabel('ListItemsPerPage')
                            ),
                            $this->owner->fieldLabel('Enabled')
                        )
                    ]
                )->setTitle($this->owner->fieldLabel('ListPaginateItems')),
                DimensionsField::create(
                    'ListImageResize',
                    $this->owner->fieldLabel('ListImageResize')
                ),
                DropdownField::create(
                    'ListImageResizeMethod',
                    $this->owner->fieldLabel('ListImageResizeMethod'),
                    ImageTools::singleton()->getResizeMethods()
                )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder),
                DropdownField::create(
                    'ListImageAlignment',
                    $this->owner->fieldLabel('ListImageAlignment'),
                    ListComponent::singleton()->getImageAlignmentOptions()
                )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder),
                DropdownField::create(
                    'ListImageLinksTo',
                    $this->owner->fieldLabel('ListImageLinksTo'),
                    ListComponent::singleton()->getImageLinksToOptions()
                ),
                TextField::create(
                    'ListTitle',
                    $this->owner->fieldLabel('ListTitle')
                ),
                TextField::create(
                    'ListButtonLabel',
                    $this->owner->fieldLabel('ListButtonLabel')
                ),
                CheckboxField::create(
                    'ListLinkTitles',
                    $this->owner->fieldLabel('ListLinkTitles')
                ),
                CheckboxField::create(
                    'ListTitleHidden',
                    $this->owner->fieldLabel('ListTitleHidden')
                )
            ])->setName('ListViewOptions')->setTitle($this->owner->fieldLabel('ListView'))
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
        $labels['ListTitleHidden'] = _t(__CLASS__ . '.HIDELISTTITLE', 'Hide list title');
    }
    
    /**
     * Populates the default values for the fields of the receiver.
     *
     * @return void
     */
    public function populateDefaults()
    {
        $this->owner->ListButtonLabel = _t(__CLASS__ . '.DEFAULTBUTTONLABEL', 'Read More');
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
     * Answers the list component for the template.
     *
     * @return ListComponent
     */
    public function getListComponent()
    {
        // Create List Component:
        
        $list = ListComponent::create();
        
        // Define List Component:
        
        $list->setStyleIDFrom($this->owner);
        
        $list->Title = $this->owner->getFieldFromHierarchy('ListTitle');
        $list->HideTitle = $this->owner->getFieldFromHierarchy('ListTitleHidden');
        
        $list->HeadingLevel = $this->owner->getFieldFromHierarchy('ListHeadingLevel');
        $list->LinkTitles   = $this->owner->getFieldFromHierarchy('ListLinkTitles');
        
        $list->PaginateItems = $this->owner->getFieldFromHierarchy('ListPaginateItems');
        $list->ItemsPerPage  = $this->owner->getFieldFromHierarchy('ListItemsPerPage');
        
        $list->ImageResizeWidth  = $this->owner->getFieldFromHierarchy('ListImageResizeWidth');
        $list->ImageResizeHeight = $this->owner->getFieldFromHierarchy('ListImageResizeHeight');
        $list->ImageResizeMethod = $this->owner->getFieldFromHierarchy('ListImageResizeMethod');
        
        $list->ImageAlignment = $this->owner->getFieldFromHierarchy('ListImageAlignment');
        $list->ImageLinksTo = $this->owner->getFieldFromHierarchy('ListImageLinksTo');
        
        $list->ButtonLabel = $this->owner->getFieldFromHierarchy('ListButtonLabel');
        
        // Define List Source:
        
        $list->setSource($this->owner->getListSource());
        
        $list->ID = 99999;
        
        // Answer List Component:
        
        return $list;
    }
}
