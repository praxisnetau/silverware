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
 * @copyright 2018 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\Components;

use SilverStripe\Forms\CheckboxField;
use SilverWare\Forms\FieldSection;

/**
 * An extension of the content component class for a toggle component.
 *
 * @package SilverWare\Components
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2018 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class ToggleComponent extends ContentComponent
{
    /**
     * Human-readable singular name.
     *
     * @var string
     * @config
     */
    private static $singular_name = 'Toggle Component';
    
    /**
     * Human-readable plural name.
     *
     * @var string
     * @config
     */
    private static $plural_name = 'Toggle Components';
    
    /**
     * Description of this object.
     *
     * @var string
     * @config
     */
    private static $description = 'A component to show a toggleable block of content';
    
    /**
     * Icon file for this object.
     *
     * @var string
     * @config
     */
    private static $icon = 'silverware/silverware: admin/client/dist/images/icons/ToggleComponent.png';
    
    /**
     * Defines the table name to use for this object.
     *
     * @var string
     * @config
     */
    private static $table_name = 'SilverWare_ToggleComponent';
    
    /**
     * Maps field names to field types for this object.
     *
     * @var array
     * @config
     */
    private static $db = [
        'StartOpen' => 'Boolean'
    ];
    
    /**
     * Defines the default values for the fields of this object.
     *
     * @var array
     * @config
     */
    private static $defaults = [
        'StartOpen' => 0
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
        
        // Create Options Fields:
        
        $fields->addFieldToTab(
            'Root.Options',
            FieldSection::create(
                'ToggleOptions',
                $this->fieldLabel('ToggleOptions'),
                [
                    CheckboxField::create(
                        'StartOpen',
                        $this->fieldLabel('StartOpen')
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
        
        $labels['StartOpen'] = _t(__CLASS__ . '.STARTOPEN', 'Start open');
        $labels['ToggleOptions'] = _t(__CLASS__ . '.TOGGLE', 'Toggle');
        
        // Answer Field Labels:
        
        return $labels;
    }
    
    /**
     * Answers an array of HTML tag attributes for the object.
     *
     * @return array
     */
    public function getAttributes()
    {
        $attributes = array_merge(
            parent::getAttributes(),
            [
                'data-start-open' => $this->dbObject('StartOpen')->NiceAsBoolean()
            ]
        );
        
        return $attributes;
    }
    
    /**
     * Event method called before the receiver is written to the database.
     *
     * @return void
     */
    public function onBeforeWrite()
    {
        // Call Parent Event:
        
        parent::onBeforeWrite();
        
        // Enforce Visible Title:
        
        $this->HideTitle = 0;
    }
}
