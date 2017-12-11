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
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\Grid;

use SilverStripe\Forms\CheckboxField;
use SilverWare\Components\BaseComponent;
use SilverWare\Forms\FieldSection;
use SilverWare\Forms\ViewportsField;

/**
 * An extension of the grid class for a column.
 *
 * @package SilverWare\Grid
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class Column extends Grid
{
    /**
     * Human-readable singular name.
     *
     * @var string
     * @config
     */
    private static $singular_name = 'Column';
    
    /**
     * Human-readable plural name.
     *
     * @var string
     * @config
     */
    private static $plural_name = 'Columns';
    
    /**
     * Description of this object.
     *
     * @var string
     * @config
     */
    private static $description = 'A column within a SilverWare template or layout row';
    
    /**
     * Icon file for this object.
     *
     * @var string
     * @config
     */
    private static $icon = 'silverware/silverware: admin/client/dist/images/icons/Column.png';
    
    /**
     * Defines the table name to use for this object.
     *
     * @var string
     * @config
     */
    private static $table_name = 'SilverWare_Column';
    
    /**
     * Defines an ancestor class to hide from the admin interface.
     *
     * @var string
     * @config
     */
    private static $hide_ancestor = Grid::class;
    
    /**
     * Maps field names to field types for this object.
     *
     * @var array
     * @config
     */
    private static $db = [
        'Span' => 'Viewports',
        'Offset' => 'Viewports',
        'Sidebar' => 'Boolean',
        'HideEmpty' => 'Boolean'
    ];
    
    /**
     * Defines the default values for the fields of this object.
     *
     * @var array
     * @config
     */
    private static $defaults = [
        'Sidebar' => 0,
        'HideEmpty' => 0
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
     * Tag name to use when rendering this object as a sidebar.
     *
     * @var string
     * @config
     */
    private static $tag_sidebar = 'aside';
    
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
        
        $style = [];
        
        // Create Column Span Field:
        
        if (self::framework()->useColumnSpan()) {
            
            $style[] = ViewportsField::create(
                'Span',
                $this->fieldLabel('Span'),
                $this->getColumnSpanOptions()
            );
            
        }
        
        // Create Column Offset Field:
        
        if (self::framework()->useColumnOffset()) {
            
            $style[] = ViewportsField::create(
                'Offset',
                $this->fieldLabel('Offset'),
                $this->getColumnOffsetOptions()
            );
            
        }
        
        // Add Style Fields to Tab:
        
        if (!empty($style)) {
            
            $fields->addFieldToTab(
                'Root.Style',
                FieldSection::create(
                    'ColumnStyle',
                    $this->fieldLabel('ColumnStyle'),
                    $style
                )
            );
            
        }
        
        // Create Options Fields:
        
        $fields->addFieldToTab(
            'Root.Options',
            FieldSection::create(
                'ColumnOptions',
                $this->fieldLabel('ColumnOptions'),
                [
                    CheckboxField::create(
                        'Sidebar',
                        $this->fieldLabel('Sidebar')
                    ),
                    CheckboxField::create(
                        'HideEmpty',
                        $this->fieldLabel('HideEmpty')
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
        
        $labels['Span'] = _t(__CLASS__ . '.SPAN', 'Span');
        $labels['Offset'] = _t(__CLASS__ . '.OFFSET', 'Offset');
        $labels['Sidebar'] = _t(__CLASS__ . '.SIDEBAR', 'Sidebar');
        $labels['HideEmpty'] = _t(__CLASS__ . '.HIDEWHENEMPTY', 'Hide when empty');
        $labels['ColumnStyle'] = $labels['ColumnOptions'] = _t(__CLASS__ . '.COLUMN', 'Column');
        
        // Answer Field Labels:
        
        return $labels;
    }
    
    /**
     * Answers an array of options for column size fields.
     *
     * @return array
     */
    public function getColumnSizeOptions()
    {
        return self::framework()->getColumnSizeOptions();
    }
    
    /**
     * Answers an array of options for column span fields.
     *
     * @return array
     */
    public function getColumnSpanOptions()
    {
        $options = self::framework()->getColumnSpanOptions();
        
        $this->extend('updateColumnSpanOptions', $options);
        
        return $options;
    }
    
    /**
     * Answers an array of options for column offset fields.
     *
     * @return array
     */
    public function getColumnOffsetOptions()
    {
        $options = self::framework()->getColumnOffsetOptions();
        
        $this->extend('updateColumnOffsetOptions', $options);
        
        return $options;
    }
    
    /**
     * Answers true if the column is empty.
     *
     * @return boolean
     */
    public function isEmpty()
    {
        return !$this->getEnabledChildren()->exists();
    }
    
    /**
     * Answers true if the column is hidden.
     *
     * @return boolean
     */
    public function isHidden()
    {
        return ($this->HideEmpty && $this->isEmpty());
    }
    
    /**
     * Answers true if the column is a sidebar.
     *
     * @return boolean
     */
    public function isSidebar()
    {
        return (boolean) $this->Sidebar;
    }
    
    /**
     * Answers the HTML tag for the receiver.
     *
     * @return string
     */
    public function getTag()
    {
        if ($this->isSidebar()) {
            return $this->config()->tag_sidebar;
        }
        
        return parent::getTag();
    }
    
    /**
     * Answers true if the column is hidden or disabled.
     *
     * @return boolean
     */
    public function isDisabled()
    {
        return $this->isHidden() ?: parent::isDisabled();
    }
    
    /**
     * Renders the component for the HTML template.
     *
     * @param string $layout Page layout passed from template.
     * @param string $title Page title passed from template.
     *
     * @return DBHTMLText|string
     */
    public function renderSelf($layout = null, $title = null)
    {
        return $this->renderTag($this->renderChildren($layout, $title));
    }
}
