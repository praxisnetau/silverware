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

use SilverStripe\ORM\ArrayList;
use SilverStripe\View\ArrayData;
use SilverWare\Forms\FieldSection;
use SilverWare\Forms\ViewportsField;
use SilverWare\Grid\Column;

/**
 * An extension of the base component class for a table component.
 *
 * @package SilverWare\Components
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class TableComponent extends BaseComponent
{
    /**
     * Human-readable singular name.
     *
     * @var string
     * @config
     */
    private static $singular_name = 'Table Component';
    
    /**
     * Human-readable plural name.
     *
     * @var string
     * @config
     */
    private static $plural_name = 'Table Components';
    
    /**
     * Description of this object.
     *
     * @var string
     * @config
     */
    private static $description = 'Renders a series of child components as a table';
    
    /**
     * Icon file for this object.
     *
     * @var string
     * @config
     */
    private static $icon = 'silverware/silverware: admin/client/dist/images/icons/TableComponent.png';
    
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
        'ColumnSpan' => 'Viewports'
    ];
    
    /**
     * Defines the default values for the fields of this object.
     *
     * @var array
     * @config
     */
    private static $defaults = [
        'ColumnSpanSmall' => 6,
        'ColumnSpanMedium' => 4
    ];
    
    /**
     * Defines the allowed children for this object.
     *
     * @var array|string
     * @config
     */
    private static $allowed_children = [
        BaseComponent::class
    ];
    
    /**
     * Answers a list of field objects for the CMS interface.
     *
     * @return FieldList
     */
    public function getCMSFields()
    {
        // Obtain Field Objects (from parent):
        
        $fields = parent::getCMSFields();
        
        // Create Style Fields:
        
        $fields->addFieldToTab(
            'Root.Style',
            FieldSection::create(
                'ColumnStyle',
                $this->fieldLabel('Column'),
                [
                    ViewportsField::create(
                        'ColumnSpan',
                        $this->fieldLabel('Span'),
                        $this->getColumnSpanOptions()
                    )
                ]
            )
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
        
        $labels['Span']   = _t(__CLASS__ . '.SPAN', 'Span');
        $labels['Column'] = _t(__CLASS__ . '.COLUMN', 'Column');
        
        // Answer Field Labels:
        
        return $labels;
    }
    
    /**
     * Answers an array of row class names for the template.
     *
     * @return array
     */
    public function getRowClassNames()
    {
        $classes = $this->styles('row');
        
        $this->extend('updateRowClassNames', $classes);
        
        return $classes;
    }
    
    /**
     * Answers a list of the columns within the receiver.
     *
     * @return ArrayList
     */
    public function getColumns()
    {
        $columns = ArrayList::create();
        
        foreach ($this->getEnabledChildren() as $child) {
            
            $columns->push(
                ArrayData::create([
                    'Child' => $child,
                    'ColumnClass' => $this->ColumnClass
                ])
            );
            
        }
        
        return $columns;
    }
    
    /**
     * Answers an array of column class names for the template.
     *
     * @return array
     */
    public function getColumnClassNames()
    {
        $classes = array_merge(
            $this->styles('column'),
            Column::singleton()->getSpanClassNames($this->dbObject('ColumnSpan'))
        );
        
        $this->extend('updateColumnClassNames', $classes);
        
        return $classes;
    }
    
    /**
     * Answers an array of options for the column size field.
     *
     * @return array
     */
    public function getColumnSpanOptions()
    {
        return Column::singleton()->getColumnSpanOptions();
    }
}
