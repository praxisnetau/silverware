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
use SilverStripe\Control\Controller;
use SilverStripe\Core\ClassInfo;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\SelectionGroup;
use SilverStripe\Forms\SelectionGroup_Item;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\DB;
use SilverStripe\ORM\PaginatedList;
use SilverStripe\ORM\SS_List;
use SilverWare\Forms\FieldSection;
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
     * Define sort constants.
     */
    const SORT_DEFAULT = 'default';
    const SORT_RANDOM  = 'random';
    
    /**
     * Maps field names to field types for this object.
     *
     * @var array
     * @config
     */
    private static $db = [
        'ItemsPerPage' => 'AbsoluteInt',
        'NumberOfItems' => 'AbsoluteInt',
        'SortItemsBy' => 'Varchar(32)',
        'PaginateItems' => 'Boolean',
        'ReverseItems' => 'Boolean',
        'ImageItems' => 'Boolean'
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
        'SortItemsBy' => self::SORT_DEFAULT,
        'ItemsPerPage' => 10,
        'PaginateItems' => 0,
        'ReverseItems' => 0,
        'ImageItems' => 0
    ];
    
    /**
     * Holds a list source instance which overrides the associated list source.
     *
     * @var ListSource
     */
    protected $source;
    
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
        
        $fields->addFieldToTab(
            'Root.Options',
            FieldSection::create(
                'ListSourceOptions',
                $this->owner->fieldLabel('ListSourceOptions'),
                [
                    TextField::create(
                        'NumberOfItems',
                        $this->owner->fieldLabel('NumberOfItems')
                    ),
                    DropdownField::create(
                        'SortItemsBy',
                        $this->owner->fieldLabel('SortItemsBy'),
                        $this->owner->getSortItemsByOptions()
                    ),
                    CheckboxField::create(
                        'ReverseItems',
                        $this->owner->fieldLabel('ReverseItems')
                    ),
                    CheckboxField::create(
                        'ImageItems',
                        $this->owner->fieldLabel('ImageItems')
                    )
                ]
            )
        );
        
        // Create Pagination Options (if permitted):
        
        if ($this->owner->canPaginate()) {
            
            $fields->insertBefore(
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
                'ReverseItems'
            );
            
        }
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
        $labels['ListSource'] = $labels['ListSourceID'] = _t(__CLASS__ . '.LISTSOURCE', 'List Source');
        $labels['ImageItems'] = _t(__CLASS__ . '.IMAGEITEMS', 'Show only items with images');
        $labels['SortItemsBy'] = _t(__CLASS__ . '.SORTITEMSBY', 'Sort items by');
        $labels['ReverseItems'] = _t(__CLASS__ . '.REVERSEITEMS', 'Reverse items');
        $labels['ItemsPerPage'] = _t(__CLASS__ . '.ITEMSPERPAGE', 'Items per page');
        $labels['PaginateItems'] = _t(__CLASS__ . '.PAGINATEITEMS', 'Paginate items');
        $labels['NumberOfItems'] = _t(__CLASS__ . '.NUMBEROFITEMS', 'Number of items');
        $labels['ListSourceOptions'] = _t(__CLASS__ . '.LISTSOURCE', 'List Source');
    }
    
    /**
     * Answers true if the extended object can paginate.
     *
     * @return boolean
     */
    public function canPaginate()
    {
        return false;
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
        
        if ($source = $this->owner->getSource()) {
            
            // Obtain Source List:
            
            $list = $source->getListItems();
            
            // Filter List Items (if applicable):
            
            if (in_array(ListFilter::class, class_uses($source))) {
                
                if ($where = $source->getListWhere()) {
                    $list = $list->where($where);
                }
                
                foreach ($source->getListFilters() as $filter) {
                    $list = $list->filter($filter);
                }
                
            }
            
            // Merge List Items:
            
            $items->merge($list);
            
        }
        
        // Sort Items (if applicable):
        
        if ($this->owner->SortItemsBy) {
            $items = $this->sort($items);
        }
        
        // Remove Items without Images (if applicable):
        
        if ($this->owner->ImageItems) {
            
            $items = $items->filterByCallback(function ($item) {
                return $item->hasMetaImage();
            });
            
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
        
        if ($this->owner->PaginateItems && $this->owner->SortItemsBy != self::SORT_RANDOM) {
            
            $items = PaginatedList::create($items, $this->getRequest());
            
            $items->setPaginationGetVar($this->owner->getPaginationGetVar());
            
            if ($this->owner->ItemsPerPage) {
                $items->setPageLength($this->owner->ItemsPerPage);
            }
            
        }
        
        // Associate Items with Rendering Component:
        
        foreach ($items as $item) {
            $item->setRenderer($this->owner);
        }
        
        // Answer List:
        
        return $items;
    }
    
    /**
     * Answers true if the extended object has list items available.
     *
     * @return boolean
     */
    public function hasListItems()
    {
        return $this->owner->getListItems()->exists();
    }
    
    /**
     * Defines the list source for the extended object.
     *
     * @param ListSource|SS_List $source
     *
     * @return DataObject
     */
    public function setSource($source)
    {
        if ($source instanceof ListSource) {
            $this->source = $source;
        }
        
        if ($source instanceof SS_List) {
            $this->source = ListWrapper::create($source);
        }
        
        return $this->owner;
    }
    
    /**
     * Answers the list source from the extended object.
     *
     * @return ListSource
     */
    public function getSource()
    {
        if ($this->source) {
            return $this->source;
        }
        
        if ($this->owner->ListSourceID) {
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
     * Answers an array of options for the sort items by field.
     *
     * @return array
     */
    public function getSortItemsByOptions()
    {
        return [
            self::SORT_DEFAULT => _t(__CLASS__ . '.DEFAULT', 'Default'),
            self::SORT_RANDOM  => _t(__CLASS__ . '.RANDOM', 'Random'),
        ];
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
    
    /**
     * Sorts the given list of items.
     *
     * @param SS_List $list
     *
     * @return SS_List
     */
    protected function sort(SS_List $list)
    {
        switch ($this->owner->SortItemsBy) {
            
            // Random Sort Order:
            
            case self::SORT_RANDOM:
                
                if ($list instanceof DataList) {
                    return $list->sort(DB::get_conn()->random());
                }
                
                $items = $list->toArray();
                
                shuffle($items);
                
                return ArrayList::create($items);
                
            // Default Sort Order:
                
            default:
                
                return $list;
                
        }
    }
    
    /**
     * Answers the request object from the current controller.
     *
     * @return HTTPRequest
     */
    protected function getRequest()
    {
        return Controller::curr()->getRequest();
    }
}
