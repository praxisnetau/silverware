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

use SilverStripe\Forms\FieldGroup;
use SilverStripe\Forms\FieldList;
use SilverWare\Colorpicker\Forms\ColorField;
use SilverWare\Extensions\StyleExtension;
use SilverWare\Forms\FieldSection;

/**
 * A style extension which adds link color styles to the extended object.
 *
 * @package SilverWare\Extensions\Style
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class LinkColorStyle extends StyleExtension
{
    /**
     * Maps field names to field types for the extended object.
     *
     * @var array
     * @config
     */
    private static $db = [
        'ColorBackgroundLink' => 'Color',
        'ColorForegroundLink' => 'Color',
        'ColorBackgroundHover' => 'Color',
        'ColorForegroundHover' => 'Color',
        'ColorBackgroundActive' => 'Color',
        'ColorForegroundActive' => 'Color'
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
                    'LinkColorStyle',
                    $this->owner->fieldLabel('LinkColorStyle'),
                    [
                        FieldGroup::create(
                            $this->owner->fieldLabel('Background'),
                            [
                                ColorField::create(
                                    'ColorBackgroundLink',
                                    $this->owner->fieldLabel('Link')
                                ),
                                ColorField::create(
                                    'ColorBackgroundHover',
                                    $this->owner->fieldLabel('Hover')
                                ),
                                ColorField::create(
                                    'ColorBackgroundActive',
                                    $this->owner->fieldLabel('Active')
                                )
                            ]
                        ),
                        FieldGroup::create(
                            $this->owner->fieldLabel('Foreground'),
                            [
                                ColorField::create(
                                    'ColorForegroundLink',
                                    $this->owner->fieldLabel('Link')
                                ),
                                ColorField::create(
                                    'ColorForegroundHover',
                                    $this->owner->fieldLabel('Hover')
                                ),
                                ColorField::create(
                                    'ColorForegroundActive',
                                    $this->owner->fieldLabel('Active')
                                )
                            ]
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
        $labels['Link'] = _t(__CLASS__ . '.LINK', 'Link');
        $labels['Hover'] = _t(__CLASS__ . '.HOVER', 'Hover');
        $labels['Active'] = _t(__CLASS__ . '.ACTIVE', 'Active');
        $labels['LinkColorStyle'] = _t(__CLASS__ . '.LINKCOLOR', 'Link color');
    }
    
    /**
     * Answers true if the extended object has link colors defined.
     *
     * @return boolean
     */
    public function hasLinkColors()
    {
        return ($this->owner->ColorBackgroundLink || $this->owner->ColorForegroundLink);
    }
    
    /**
     * Answers true if the extended object has hover colors defined.
     *
     * @return boolean
     */
    public function hasHoverColors()
    {
        return ($this->owner->ColorBackgroundHover || $this->owner->ColorForegroundHover);
    }
    
    /**
     * Answers true if the extended object has active colors defined.
     *
     * @return boolean
     */
    public function hasActiveColors()
    {
        return ($this->owner->ColorBackgroundActive || $this->owner->ColorForegroundActive);
    }
}
