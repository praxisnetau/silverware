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

use SilverStripe\Core\ClassInfo;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\GridField\GridFieldDetailForm;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\TabSet;
use SilverStripe\ORM\DataObject;
use SilverStripe\SiteConfig\SiteConfig;
use SilverWare\Grid\Column;
use SilverWare\Grid\ColumnSpan;
use SilverWare\Model\Layout;
use SilverWare\Model\SectionHolder;
use SilverWare\Model\Template;
use SilverWare\Security\SiteConfigPermissions;
use Page;

/**
 * An extension of the data object class for a page type.
 *
 * @package SilverWare\Model
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class PageType extends DataObject
{
    use SiteConfigPermissions;
    
    /**
     * Human-readable singular name.
     *
     * @var string
     * @config
     */
    private static $singular_name = 'Page Type';
    
    /**
     * Human-readable plural name.
     *
     * @var string
     * @config
     */
    private static $plural_name = 'Page Types';
    
    /**
     * Defines the table name to use for this object.
     *
     * @var string
     * @config
     */
    private static $table_name = 'SilverWare_PageType';
    
    /**
     * Maps field names to field types for this object.
     *
     * @var array
     * @config
     */
    private static $db = [
        'PageClass' => 'Varchar(255)'
    ];
    
    /**
     * Defines the has-one associations for this object.
     *
     * @var array
     * @config
     */
    private static $has_one = [
        'MyLayout' => Layout::class,
        'MyTemplate' => Template::class,
        'SiteConfig' => SiteConfig::class
    ];
    
    /**
     * Defines the has-many associations for this object.
     *
     * @var array
     * @config
     */
    private static $has_many = [
        'ColumnSpans' => ColumnSpan::class
    ];
    
    /**
     * Maps field and method names to the class names of casting objects.
     *
     * @var array
     * @config
     */
    private static $casting = [
        'HasCustomSpans' => 'Boolean'
    ];
    
    /**
     * Defines the summary fields of this record.
     *
     * @var array
     * @config
     */
    private static $summary_fields = [
        'PageName',
        'MyTemplate.Title',
        'MyLayout.Title',
        'HasCustomSpans.Nice'
    ];
    
    /**
     * Answers the page type for the specified class.
     *
     * @param string $class Class name of page.
     *
     * @return PageType
     */
    public static function findByClass($class)
    {
        return self::get()->find('PageClass', $class);
    }
    
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
                    'PageClass',
                    $this->fieldLabel('PageClass'),
                    $this->getPageClassOptions()
                )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder),
                DropdownField::create(
                    'MyTemplateID',
                    $this->fieldLabel('MyTemplateID'),
                    Template::get()->map()
                )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder),
                DropdownField::create(
                    'MyLayoutID',
                    $this->fieldLabel('MyLayoutID'),
                    Layout::get()->map()
                )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder)
            ]
        );
        
        // Create Grid Field Config:
        
        $spansConfig = GridFieldConfig_RecordEditor::create();
        
        // Obtain Edit Form Component:
        
        $editComponent = $spansConfig->getComponentByType(GridFieldDetailForm::class);
        
        // Define Edit Form Callback:
        
        $editComponent->setItemEditFormCallback(function ($form, $itemRequest) {
            
            // Define Column Options:
            
            $form->Fields()->dataFieldByName('ColumnID')->setSource($this->getColumnOptions());
            
        });
        
        // Create Column Spans Grid Field:
        
        $spans = GridField::create(
            'ColumnSpans',
            $this->fieldLabel('Spans'),
            $this->ColumnSpans(),
            $spansConfig
        );
        
        // Create Column Spans Tab:
        
        $fields->findOrMakeTab('Root.ColumnSpans', $this->owner->fieldLabel('Spans'));
        
        // Add Grid Field to Column Spans Tab:
        
        $fields->addFieldToTab('Root.ColumnSpans', $spans);
        
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
            'PageClass',
            'MyTemplateID',
            'MyLayoutID'
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
        
        $labels['Spans'] = _t(__CLASS__ . '.SPANS', 'Spans');
        
        $labels['PageName']     = $labels['PageClass'] = _t(__CLASS__ . '.TYPE', 'Type');
        $labels['MyLayoutID']   = $labels['MyLayout.Title'] = _t(__CLASS__ . '.LAYOUT', 'Layout');
        $labels['MyTemplateID'] = $labels['MyTemplate.Title'] = _t(__CLASS__ . '.TEMPLATE', 'Template');
        
        $labels['HasCustomSpans.Nice'] = _t(__CLASS__ . '.HASCUSTOMSPANS', 'Has Custom Spans');
        
        // Define Relation Labels:
        
        if ($includerelations) {
            $labels['MyLayout']   = _t(__CLASS__ . '.has_one_MyLayout', 'Layout');
            $labels['MyTemplate'] = _t(__CLASS__ . '.has_one_MyTemplate', 'Template');
            $labels['SiteConfig'] = _t(__CLASS__ . '.has_one_SiteConfig', 'Site Config');
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
        return $this->getPageName();
    }
    
    /**
     * Answers the singular name of the associated page class.
     *
     * @return string
     */
    public function getPageName()
    {
        if ($this->PageClass && class_exists($this->PageClass)) {
            return Injector::inst()->get($this->PageClass)->i18n_singular_name();
        }
    }
    
    /**
     * Answers true if the receiver has a page layout defined.
     *
     * @return boolean
     */
    public function hasPageLayout()
    {
        return (boolean) $this->MyLayoutID;
    }
    
    /**
     * Answers the page layout defined for the receiver.
     *
     * @return Template
     */
    public function getPageLayout()
    {
        return $this->MyLayout();
    }
    
    /**
     * Answers true if the receiver has a page template defined.
     *
     * @return boolean
     */
    public function hasPageTemplate()
    {
        return (boolean) $this->MyTemplateID;
    }
    
    /**
     * Answers the page template defined for the receiver.
     *
     * @return Template
     */
    public function getPageTemplate()
    {
        return $this->MyTemplate();
    }
    
    /**
     * Answers true if the receiver has custom column spans defined.
     *
     * @return boolean
     */
    public function getHasCustomSpans()
    {
        return $this->ColumnSpans()->exists();
    }
    
    /**
     * Answers an array of options for the column field in child column span records.
     *
     * @return array
     */
    public function getColumnOptions()
    {
        // Create Options Array:
        
        $options = [];
        
        // Merge Template Columns:
        
        if ($template = $this->getPageTemplate()) {
            
            foreach ($template->getAllComponentsByClass(Column::class) as $column) {
                $options[$column->ID] = $this->getColumnLabel($column, $template);
            }
            
        }
        
        // Merge Layout Columns:
        
        if ($layout = $this->getPageLayout()) {
            
            foreach ($layout->getAllComponentsByClass(Column::class) as $column) {
                $options[$column->ID] = $this->getColumnLabel($column, $layout);
            }
            
        }
        
        // Answer Options Array:
        
        return $options;
    }
    
    /**
     * Answers a label for a column field option.
     *
     * @param Column $column
     * @param SectionHolder $holder
     * @param integer $depth
     *
     * @return string
     */
    public function getColumnLabel(Column $column, SectionHolder $holder, $depth = 3)
    {
        $label = sprintf('%s > %s',
            $holder->i18n_singular_name(),
            $column->NestedTitle($depth, ' > ')
        );
        
        if ($column->isSidebar()) {
            $label .= sprintf(
                ' (%s)',
                _t(__CLASS__ . '.SIDEBAR', 'Sidebar')
            );
        }
        
        return $label;
    }
    
    /**
     * Answers a sorted array of available page classes mapped to their singular name.
     *
     * @return array
     */
    public function getPageClassOptions()
    {
        $options = [];
        
        foreach (ClassInfo::subclassesFor(Page::class) as $class) {
            
            $type = self::findByClass($class);
            
            if (!$type || $type->ID == $this->ID) {
                $options[$class] = Injector::inst()->get($class)->i18n_singular_name();
            }
            
        }
        
        asort($options);
        
        return $options;
    }
}
