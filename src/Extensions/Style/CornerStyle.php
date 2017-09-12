<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Extensions\Style
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\Extensions\Style;

use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverWare\Extensions\StyleExtension;
use SilverWare\Forms\FieldSection;

/**
 * A style extension which adds corner styles to the extended object.
 *
 * @package SilverWare\Extensions\Style
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class CornerStyle extends StyleExtension
{
    /**
     * Define corner constants.
     */
    const CORNER_ROUNDED  = 'rounded';
    const CORNER_CIRCULAR = 'circular';
    
    /**
     * Maps field names to field types for the extended object.
     *
     * @var array
     * @config
     */
    private static $db = [
        'CornerStyle' => 'Varchar(16)'
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
        // Update Field Objects (from parent):
        
        parent::updateCMSFields($fields);
        
        // Define Placeholder:
        
        $placeholder = _t(__CLASS__ . '.DROPDOWNDEFAULT', '(default)');
        
        // Create Style Fields:
        
        $fields->addFieldsToTab(
            'Root.Style',
            [
                FieldSection::create(
                    'CornerStyle',
                    $this->owner->fieldLabel('Corners'),
                    [
                        DropdownField::create(
                            'CornerStyle',
                            $this->owner->fieldLabel('CornerStyle'),
                            $this->owner->getCornerStyleOptions()
                        )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder)
                    ]
                )
            ]
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
        $labels['Corners'] = _t(__CLASS__ . '.CORNERS', 'Corners');
        $labels['CornerStyle'] = _t(__CLASS__ . '.CORNERSTYLE', 'Corner style');
    }
    
    /**
     * Updates the given array of class names from the extended object.
     *
     * @param array $classes
     *
     * @return array
     */
    public function updateClassNames(&$classes)
    {
        if (!$this->apply()) {
            return;
        }
        
        if ($class = $this->owner->CornerStyleClass) {
            $classes[] = $class;
        }
    }
    
    /**
     * Answers the corner style class of the extended object.
     *
     * @return string
     */
    public function getCornerStyleClass()
    {
        switch ($this->owner->CornerStyle) {
            case self::CORNER_ROUNDED:
                return $this->style('rounded');
            case self::CORNER_CIRCULAR:
                return $this->style('rounded.circle');
        }
    }
    
    /**
     * Answers an array of options for the corner style field.
     *
     * @return array
     */
    public function getCornerStyleOptions()
    {
        return [
            self::CORNER_ROUNDED  => _t(__CLASS__ . '.ROUNDED', 'Rounded'),
            self::CORNER_CIRCULAR => _t(__CLASS__ . '.CIRCULAR', 'Circular'),
        ];
    }
}
