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
     * Define align constants.
     */
    const ALIGN_LEFT    = 'left';
    const ALIGN_CENTER  = 'center';
    const ALIGN_RIGHT   = 'right';
    const ALIGN_JUSTIFY = 'justify';
    
    /**
     * Maps field names to field types for the extended object.
     *
     * @var array
     * @config
     */
    private static $db = [
        'TextAlignment' => 'Viewports',
        'ImageAlignment' => 'Viewports'
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
                        ),
                        ViewportsField::create(
                            'ImageAlignment',
                            $this->owner->fieldLabel('ImageAlignment'),
                            $this->owner->getImageAlignmentOptions()
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
        $labels['ImageAlignment'] = _t(__CLASS__ . '.IMAGES', 'Images');
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
        
        foreach ($this->getImageAlignmentClassNames() as $class) {
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
     * Answers an array of classes for image alignment.
     *
     * @return array
     */
    public function getImageAlignmentClassNames()
    {
        $classes = [];
        
        $alignment = $this->owner->dbObject('ImageAlignment');
        
        foreach ($alignment->getViewports() as $viewport) {
            $classes[] = $this->getImageAlignmentClass($viewport, $alignment->getField($viewport));
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
            self::ALIGN_LEFT    => _t(__CLASS__ . '.LEFT', 'Left'),
            self::ALIGN_CENTER  => _t(__CLASS__ . '.CENTER', 'Center'),
            self::ALIGN_RIGHT   => _t(__CLASS__ . '.RIGHT', 'Right'),
            self::ALIGN_JUSTIFY => _t(__CLASS__ . '.JUSTIFY', 'Justify')
        ];
    }
    
    /**
     * Answers an array of options for image alignment fields.
     *
     * @return array
     */
    public function getImageAlignmentOptions()
    {
        return [
            self::ALIGN_LEFT   => _t(__CLASS__ . '.LEFT', 'Left'),
            self::ALIGN_CENTER => _t(__CLASS__ . '.CENTER', 'Center'),
            self::ALIGN_RIGHT  => _t(__CLASS__ . '.RIGHT', 'Right')
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
    
    /**
     * Answers the image alignment class for the specified viewport and value.
     *
     * @param string $viewport
     * @param string $value
     *
     * @return string
     */
    protected function getImageAlignmentClass($viewport, $value)
    {
        return $this->grid()->getImageAlignmentClass($viewport, $value);
    }
}
