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
use SilverStripe\Forms\FieldList;
use SilverWare\Extensions\StyleExtension;
use SilverWare\Forms\FieldSection;

/**
 * A style extension which adds button styles to the extended object.
 *
 * @package SilverWare\Extensions\Style
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class ButtonStyle extends StyleExtension
{
    /**
     * Define size constants.
     */
    const SIZE_SMALL  = 'small';
    const SIZE_MEDIUM = 'medium';
    const SIZE_LARGE  = 'large';
    
    /**
     * Define style constants.
     */
    const STYLE_FILLED  = 'filled';
    const STYLE_OUTLINE = 'outline';
    
    /**
     * Maps field names to field types for the extended object.
     *
     * @var array
     * @config
     */
    private static $db = [
        'ButtonType' => 'Varchar(16)',
        'ButtonSize' => 'Varchar(16)',
        'ButtonStyle' => 'Varchar(16)'
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
                    'ButtonStyle',
                    $this->owner->fieldLabel('Button'),
                    [
                        DropdownField::create(
                            'ButtonType',
                            $this->owner->fieldLabel('ButtonType'),
                            $this->owner->getButtonTypeOptions()
                        )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder),
                        DropdownField::create(
                            'ButtonSize',
                            $this->owner->fieldLabel('ButtonSize'),
                            $this->owner->getButtonSizeOptions()
                        )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder),
                        DropdownField::create(
                            'ButtonStyle',
                            $this->owner->fieldLabel('ButtonStyle'),
                            $this->owner->getButtonStyleOptions()
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
        $labels['Button'] = _t(__CLASS__ . '.BUTTON', 'Button');
        $labels['ButtonType'] = _t(__CLASS__ . '.BUTTONTYPE', 'Button type');
        $labels['ButtonSize'] = _t(__CLASS__ . '.BUTTONSIZE', 'Button size');
        $labels['ButtonStyle'] = _t(__CLASS__ . '.BUTTONSTYLE', 'Button style');
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
        
        $classes[] = $this->style('button');
        
        if ($class = $this->owner->ButtonTypeClass) {
            $classes[] = $class;
        }
        
        if ($class = $this->owner->ButtonSizeClass) {
            $classes[] = $class;
        }
    }
    
    /**
     * Answers the type class for the button.
     *
     * @return string
     */
    public function getButtonTypeClass()
    {
        return $this->style('button', $this->owner->getButtonTypeStyle());
    }
    
    /**
     * Answers the type style for the button.
     *
     * @return string
     */
    public function getButtonTypeStyle()
    {
        $type = $this->owner->ButtonTypeOrDefault;
        
        if ($this->owner->ButtonStyleOrDefault == self::STYLE_OUTLINE) {
            return sprintf('outline-%s', $type);
        }
        
        return $type;
    }
    
    /**
     * Answers the defined button size or the default size.
     *
     * @return string
     */
    public function getButtonSizeOrDefault()
    {
        if ($this->owner->ButtonSize) {
            return $this->owner->ButtonSize;
        }
        
        return Config::inst()->get(static::class, 'default_button_size');
    }
    
    /**
     * Answers the defined button style or the default style.
     *
     * @return string
     */
    public function getButtonStyleOrDefault()
    {
        if ($this->owner->ButtonStyle) {
            return $this->owner->ButtonStyle;
        }
        
        return Config::inst()->get(static::class, 'default_button_style');
    }
    
    /**
     * Answers the defined button type or the default type.
     *
     * @return string
     */
    public function getButtonTypeOrDefault()
    {
        if ($this->owner->ButtonType) {
            return $this->owner->ButtonType;
        }
        
        return Config::inst()->get(static::class, 'default_button_type');
    }
    
    /**
     * Answers the size class for the button.
     *
     * @return string
     */
    public function getButtonSizeClass()
    {
        if ($size = $this->owner->ButtonSizeOrDefault) {
            return $this->style('button', $size);
        }
    }
    
    /**
     * Answers a string of extra classes for a button include.
     *
     * @return string
     */
    public function getButtonExtraClass()
    {
        $classes = [];
        
        if ($class = $this->owner->ButtonSizeClass) {
            $classes[] = $class;
        }
        
        return implode(' ', $classes);
    }
    
    /**
     * Answers an array of options for the button type field.
     *
     * @return array
     */
    public function getButtonTypeOptions()
    {
        return Config::inst()->get(static::class, 'button_types');
    }
    
    /**
     * Answers an array of options for the button size field.
     *
     * @return array
     */
    public function getButtonSizeOptions()
    {
        return [
            self::SIZE_SMALL  => _t(__CLASS__ . '.SMALL', 'Small'),
            self::SIZE_MEDIUM => _t(__CLASS__ . '.MEDIUM', 'Medium'),
            self::SIZE_LARGE  => _t(__CLASS__ . '.LARGE', 'Large')
        ];
    }
    
    /**
     * Answers an array of options for the button style field.
     *
     * @return array
     */
    public function getButtonStyleOptions()
    {
        return [
            self::STYLE_FILLED  => _t(__CLASS__ . '.FILLED', 'Filled'),
            self::STYLE_OUTLINE => _t(__CLASS__ . '.OUTLINE', 'Outline')
        ];
    }
}
