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

use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Core\ClassInfo;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\CompositeField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\SelectionGroup;
use SilverStripe\Forms\SelectionGroup_Item;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\PaginatedList;
use SilverStripe\ORM\SS_List;
use SilverWare\Lists\ListFilter;
use SilverWare\Lists\ListSource;
use SilverWare\Lists\ListWrapper;
use SilverWare\Tools\ClassTools;
use SilverWare\View\Renderable;

/**
 * A data extension class to add list source functionality to the extended object.
 *
 * @package SilverWare\Extensions\Lists
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class ListSourceExtension extends DataExtension
{
    /**
     * Maps field names to field types for this object.
     *
     * @var array
     * @config
     */
    private static $db = [
        'ItemsPerPage' => 'AbsoluteInt',
        'NumberOfItems' => 'AbsoluteInt',
        'ImageResize' => 'Dimensions',
        'ImageResizeMethod' => 'Varchar(32)',
        'PaginateItems' => 'Boolean',
        'ReverseItems' => 'Boolean'
    ];
    
    /**
     * Defines the has-one associations for this object.
     *
     * @var array
     * @config
     */
    private static $has_one = [
        'ListSource' => SiteTree::class
    ];
    
    /**
     * Defines the default values for the fields of this object.
     *
     * @var array
     * @config
     */
    private static $defaults = [
        'ItemsPerPage' => 10,
        'PaginateItems' => 0,
        'ReverseItems' => 0
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
        // Define Placeholder:
        
        $placeholder = _t(__CLASS__ . '.DROPDOWNSELECT', 'Select');
        
        // Create Main Fields:
        
        $fields->addFieldsToTab(
            'Root.Main',
            [
                DropdownField::create(
                    'ListSourceID',
                    $this->owner->fieldLabel('ListSourceID'),
                    $this->getListSourceOptions()
                )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder)
            ]
        );
        
        // Create Options Fields:
        
        $fields->addFieldsToTab(
            'Root.Options',
            CompositeField::create([
                TextField::create(
                    'NumberOfItems',
                    $this->owner->fieldLabel('NumberOfItems')
                ),
                SelectionGroup::create(
                    'PaginateItems',
                    [
                        SelectionGroup_Item::create(
                            '0',
                            null,
                            $this->owner->fieldLabel('Disabled')
                        ),
                        SelectionGroup_Item::create(
                            '1',
                            TextField::create(
                                'ItemsPerPage',
                                $this->owner->fieldLabel('ItemsPerPage')
                            ),
                            $this->owner->fieldLabel('Enabled')
                        )
                    ]
                )->setTitle($this->owner->fieldLabel('PaginateItems')),
                CheckboxField::create(
                    'ReverseItems',
                    $this->owner->fieldLabel('ReverseItems')
                )
            ])->setName('ListSourceOptions')->setTitle($this->owner->fieldLabel('ListSourceOptions'))
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
        $labels['Enabled'] = _t(__CLASS__ . '.ENABLED', 'Enabled');
        $labels['Disabled'] = _t(__CLASS__ . '.DISABLED', 'Disabled');
        $labels['ListSource'] = $labels['ListSourceID'] = _t(__CLASS__ . '.LISTSOURCE', 'List source');
        $labels['ReverseItems'] = _t(__CLASS__ . '.REVERSEITEMS', 'Reverse items');
        $labels['ItemsPerPage'] = _t(__CLASS__ . '.ITEMSPERPAGE', 'Items per page');
        $labels['PaginateItems'] = _t(__CLASS__ . '.PAGINATEITEMS', 'Paginate items');
        $labels['NumberOfItems'] = _t(__CLASS__ . '.NUMBEROFITEMS', 'Number of items');
        $labels['ListSourceOptions'] = _t(__CLASS__ . '.LISTSOURCE', 'List source');
    }
    
    /**
     * Answers a list of items.
     *
     * @return ArrayList
     */
    public function getListItems()
    {
        // Create List:
        
        $items = ArrayList::create();
        
        // Obtain Items from Source:
        
        if ($source = $this->getSource()) {
            
            // Merge List Items:
            
            $items->merge($source->getListItems());
            
            // Filter List Items (if applicable):
            
            if (in_array(ListFilter::class, class_uses($source))) {
                
                foreach ($source->getListFilters() as $filter) {
                    $items = $items->filter($filter);
                }
                
            }
            
        }
        
        // Reverse Items (if applicable):
        
        if ($this->owner->ReverseItems) {
            $items = $items->reverse();
        }
        
        // Limit Items (if applicable):
        
        if ($limit = $this->owner->NumberOfItems) {
            $items = $items->limit($limit);
        }
        
        // Paginate Items (if applicable):
        
        if ($this->owner->PaginateItems) {
            
            $items = PaginatedList::create($items, $_GET)->setPaginationGetVar($this->getPaginationGetVar());
            
            if ($this->owner->ItemsPerPage) {
                $items->setPageLength($this->owner->ItemsPerPage);
            }
            
        }
        
        // Answer List:
        
        return $items;
    }
    
    /**
     * Defines the list source for the extended object.
     *
     * @param ListSource|SS_List $source
     *
     * @return $this
     */
    public function setSource($source)
    {
        if ($source instanceof ListSource) {
            $this->owner->ListSource = $source;
        }
        
        if ($source instanceof SS_List) {
            $this->owner->ListSource = ListWrapper::create($source);
        }
        
        return $this;
    }
    
    /**
     * Answers the list source from the extended object.
     *
     * @return ListSource
     */
    public function getSource()
    {
        if (!$this->owner->ListSourceID) {
            return $this->owner->getField('ListSource');
        } else {
            return $this->owner->ListSource();
        }
    }
    
    /**
     * Answers true if a list source is defined for the extended object.
     *
     * @return boolean
     */
    public function getSourceExists()
    {
        return (boolean) $this->owner->getSource();
    }
    
    /**
     * Answers an array of options for the list source field.
     *
     * @return array
     */
    public function getListSourceOptions()
    {
        return ClassTools::singleton()->getImplementorMap(ListSource::class);
    }
    
    /**
     * Answers the name of the GET var to use for paginating the extended object.
     *
     * @return string
     */
    public function getPaginationGetVar()
    {
        if ($this->owner->hasMethod('getHTMLID')) {
            return sprintf('%s_%s', $this->owner->getHTMLID(), 'start');
        }
        
        return 'start';
    }
}
