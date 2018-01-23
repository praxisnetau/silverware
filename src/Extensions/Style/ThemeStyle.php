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

use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldGroup;
use SilverStripe\Forms\FieldList;
use SilverWare\Extensions\StyleExtension;
use SilverWare\Forms\FieldSection;
use SilverWare\View\GridAware;

/**
 * A style extension which adds theme styles to the extended object.
 *
 * @package SilverWare\Extensions\Style
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class ThemeStyle extends StyleExtension
{
    use GridAware;
    
    /**
     * Maps field names to field types for the extended object.
     *
     * @var array
     * @config
     */
    private static $db = [
        'ColorBorderTheme' => 'Varchar(64)',
        'ColorBackgroundTheme' => 'Varchar(64)',
        'ColorForegroundTheme' => 'Varchar(64)'
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
                    'ThemeStyle',
                    $this->owner->fieldLabel('Theme'),
                    [
                        DropdownField::create(
                            'ColorBackgroundTheme',
                            $this->owner->fieldLabel('ColorBackgroundTheme'),
                            $this->getDropdownOptions('ColorBackground')
                        )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder),
                        DropdownField::create(
                            'ColorForegroundTheme',
                            $this->owner->fieldLabel('ColorForegroundTheme'),
                            $this->getDropdownOptions('ColorForeground')
                        )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder),
                        DropdownField::create(
                            'ColorBorderTheme',
                            $this->owner->fieldLabel('ColorBorderTheme'),
                            $this->getDropdownOptions('ColorBorder')
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
        $labels['ColorBorderTheme']     = _t(__CLASS__ . '.BORDERCOLOR', 'Border color');
        $labels['ColorBackgroundTheme'] = _t(__CLASS__ . '.BACKGROUNDCOLOR', 'Background color');
        $labels['ColorForegroundTheme'] = _t(__CLASS__ . '.FOREGROUNDCOLOR', 'Foreground color');
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
        
        // Apply Background Color:
        
        if ($class = $this->owner->ColorBackgroundTheme) {
            $classes[] = $this->style($class);
        }
        
        // Apply Foreground Color:
        
        if ($class = $this->owner->ColorForegroundTheme) {
            $classes[] = $this->style($class);
        }
        
        // Apply Border Color:
        
        if ($class = $this->owner->ColorBorderTheme) {
            $classes[] = $this->style($class);
        }
    }
    
    /**
     * Answers an array of options with the given style name for a dropdown field.
     *
     * @param string $name
     *
     * @return array
     */
    public function getDropdownOptions($name)
    {
        $config = Config::inst()->get(static::class, 'theme_styles');
        
        return (isset($config[$name]) && is_array($config[$name])) ? $config[$name] : [];
    }
}
