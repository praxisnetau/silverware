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
use SilverWare\Forms\ViewportsField;

/**
 * A style extension which adds alignment styles to the extended object.
 *
 * @package SilverWare\Extensions\Style
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class AlignmentStyle extends StyleExtension
{
    /**
     * Maps field names to field types for the extended object.
     *
     * @var array
     * @config
     */
    private static $db = [
        'TextAlignment' => 'Viewports'
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
        
        // Create Style Fields:
        
        $fields->addFieldsToTab(
            'Root.Style',
            [
                FieldSection::create(
                    'AlignmentStyle',
                    $this->owner->fieldLabel('AlignmentStyle'),
                    [
                        ViewportsField::create(
                            'TextAlignment',
                            $this->owner->fieldLabel('TextAlignment'),
                            $this->owner->getTextAlignmentOptions()
                        )
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
        $labels['TextAlignment']  = _t(__CLASS__ . '.TEXT', 'Text');
        $labels['AlignmentStyle'] = _t(__CLASS__ . '.ALIGNMENT', 'Alignment');
    }
    
    /**
     * Updates the content class names of the extended object.
     *
     * @param array $classes Array of class names from the extended object.
     *
     * @return void
     */
    public function updateContentClassNames(&$classes)
    {
        if (!$this->apply()) {
            return;
        }
        
        foreach ($this->getTextAlignmentClassNames() as $class) {
            $classes[] = $class;
        }
    }
    
    /**
     * Answers an array of classes for text alignment.
     *
     * @return array
     */
    public function getTextAlignmentClassNames()
    {
        $classes = [];
        
        $alignment = $this->owner->dbObject('TextAlignment');
        
        foreach ($alignment->getViewports() as $viewport) {
            $classes[] = $this->getTextAlignmentClass($viewport, $alignment->getField($viewport));
        }
        
        return $classes;
    }
    
    /**
     * Answers an array of options for text alignment fields.
     *
     * @return array
     */
    public function getTextAlignmentOptions()
    {
        return [
            'left'   => _t(__CLASS__ . '.LEFT', 'Left'),
            'center' => _t(__CLASS__ . '.CENTER', 'Center'),
            'right'  => _t(__CLASS__ . '.RIGHT', 'Right'),
        ];
    }
    
    /**
     * Answers the text alignment class for the specified viewport and value.
     *
     * @param string $viewport
     * @param string $value
     *
     * @return string
     */
    protected function getTextAlignmentClass($viewport, $value)
    {
        return $this->grid()->getTextAlignmentClass($viewport, $value);
    }
}
