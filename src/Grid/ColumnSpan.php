<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Grid
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2018 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\Grid;

use SilverStripe\Control\Director;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\TabSet;
use SilverStripe\ORM\DataObject;
use SilverWare\Forms\PageMultiselectField;
use SilverWare\Forms\ViewportsField;
use SilverWare\Model\PageType;
use SilverWare\Security\SiteConfigPermissions;
use Page;

/**
 * An extension of the data object class which maintains column span configuration.
 *
 * @package SilverWare\Grid
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2018 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class ColumnSpan extends DataObject
{
    use SiteConfigPermissions;
    
    /**
     * Human-readable singular name.
     *
     * @var string
     * @config
     */
    private static $singular_name = 'Column Span';
    
    /**
     * Human-readable plural name.
     *
     * @var string
     * @config
     */
    private static $plural_name = 'Column Spans';
    
    /**
     * Defines the table name to use for this object.
     *
     * @var string
     * @config
     */
    private static $table_name = 'SilverWare_ColumnSpan';
    
    /**
     * Maps field names to field types for this object.
     *
     * @var array
     * @config
     */
    private static $db = [
        'Span' => 'Viewports',
        'DoNotInherit' => 'Boolean'
    ];
    
    /**
     * Defines the has-one associations for this object.
     *
     * @var array
     * @config
     */
    private static $has_one = [
        'Column' => Column::class,
        'PageType' => PageType::class
    ];
    
    /**
     * Defines the many-many associations for this object.
     *
     * @var array
     * @config
     */
    private static $many_many = [
        'Pages' => Page::class
    ];
    
    /**
     * Defines the default values for the fields of this object.
     *
     * @var array
     * @config
     */
    private static $defaults = [
        'DoNotInherit' => 0
    ];
    
    /**
     * Maps field and method names to the class names of casting objects.
     *
     * @var array
     * @config
     */
    private static $casting = [
        'Inherited' => 'Boolean'
    ];
    
    /**
     * Defines the summary fields of this record.
     *
     * @var array
     * @config
     */
    private static $summary_fields = [
        'ColumnTitle',
        'LinkedPages',
        'SpanSummary',
        'Inherited.Nice'
    ];
    
    /**
     * Answers a list of field objects for the CMS interface.
     *
     * @return FieldList
     */
    public function getCMSFields()
    {
        // Create Field Tab Set:
        
        $fields = FieldList::create(TabSet::create('Root'));
        
        // Define Placeholder:
        
        $placeholder = _t(__CLASS__ . '.DROPDOWNSELECT', 'Select');
        
        // Create Main Fields:
        
        $fields->addFieldsToTab(
            'Root.Main',
            [
                DropdownField::create(
                    'ColumnID',
                    $this->fieldLabel('ColumnID')
                )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder),
                PageMultiselectField::create(
                    'Pages',
                    $this->fieldLabel('Pages')
                ),
                ViewportsField::create(
                    'Span',
                    $this->fieldLabel('Span'),
                    $this->getColumnSpanOptions()
                ),
                CheckboxField::create(
                    'DoNotInherit',
                    $this->fieldLabel('DoNotInherit')
                )
            ]
        );
        
        // Extend Field Objects:
        
        $this->extend('updateCMSFields', $fields);
        
        // Answer Field Objects:
        
        return $fields;
    }
    
    /**
     * Answers a validator for the CMS interface.
     *
     * @return RequiredFields
     */
    public function getCMSValidator()
    {
        return RequiredFields::create([
            'ColumnID',
            'Pages'
        ]);
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
    
        $labels['LinkedPages']    = _t(__CLASS__ . '.PAGES', 'Pages');
        $labels['DoNotInherit']   = _t(__CLASS__ . '.DONOTINHERIT', 'Do not inherit');
        $labels['Inherited.Nice'] = _t(__CLASS__ . '.INHERITED', 'Inherited');
        
        $labels['Span']     = $labels['SpanSummary'] = _t(__CLASS__ . '.SPAN', 'Span');
        $labels['ColumnID'] = $labels['ColumnTitle'] = _t(__CLASS__ . '.COLUMN', 'Column');
        
        // Define Relation Labels:
        
        if ($includerelations) {
            $labels['Pages']    = _t(__CLASS__ . '.many_many_Pages', 'Pages');
            $labels['Column']   = _t(__CLASS__ . '.has_one_Column', 'Column');
            $labels['PageType'] = _t(__CLASS__ . '.has_one_PageType', 'Page Type');
        }
        
        // Answer Field Labels:
        
        return $labels;
    }
    
    /**
     * Answers the title of the receiver for the CMS interface.
     *
     * @return string
     */
    public function getTitle()
    {
        return sprintf(
            _t(__CLASS__ . '.CMSTITLE', 'Column Span for "%s"'),
            $this->getColumnTitle()
        );
    }
    
    /**
     * Answers the title for the associated column object.
     *
     * @return string
     */
    public function getColumnTitle()
    {
        return $this->Column()->NestedTitle(5, ' > ');
    }
    
    /**
     * Answers a string describing the linked pages.
     *
     * @return string
     */
    public function getLinkedPages()
    {
        return implode(', ', $this->Pages()->column('Title'));
    }
    
    /**
     * Answers a summary of the defined column spans.
     *
     * @return string
     */
    public function getSpanSummary()
    {
        $summary = [];
        
        $span = $this->dbObject('Span');
        
        foreach ($span->getViewports() as $viewport) {
            
            if ($value = $span->getField($viewport)) {
                $summary[] = sprintf('%s: %s', $viewport, $value);
            }
            
        }
        
        return implode(', ', $summary);
    }
    
    /**
     * Answers the span viewports field defined for the receiver.
     *
     * @return DBViewports
     */
    public function getSpanViewports()
    {
        return $this->dbObject('Span');
    }
    
    /**
     * Answers true if the column span is active for the current page.
     *
     * @return boolean
     */
    public function isActive()
    {
        if (($page = Director::get_current_page()) && $page instanceof Page) {
            
            $ids = $this->Pages()->column('ID');
            
            if (!$this->isInherited()) {
                return in_array($page->ID, $ids);
            }
            
            return !empty(array_intersect($ids, $page->getAncestors(true)->column('ID')));
            
        }
        
        return false;
    }
    
    /**
     * Alias for the isInherited() method.
     *
     * @return boolean
     */
    public function getInherited()
    {
        return $this->isInherited();
    }
    
    /**
     * Answers true if the column span is to be inherited by child pages.
     *
     * @return boolean
     */
    public function isInherited()
    {
        return !$this->DoNotInherit;
    }
    
    /**
     * Answers an array of options for the column span field.
     *
     * @return array
     */
    public function getColumnSpanOptions()
    {
        return Column::singleton()->getColumnSpanOptions();
    }
}
