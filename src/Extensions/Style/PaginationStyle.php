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
 * A style extension which adds pagination styles to the extended object.
 *
 * @package SilverWare\Extensions\Style
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class PaginationStyle extends StyleExtension
{
    /**
     * Define size constants.
     */
    const SIZE_SMALL = 'small';
    const SIZE_LARGE = 'large';
    
    /**
     * Define button mode constants.
     */
    const BUTTON_MODE_ICON = 'icon';
    const BUTTON_MODE_TEXT = 'text';
    const BUTTON_MODE_BOTH = 'both';
    
    /**
     * Maps field names to field types for the extended object.
     *
     * @var array
     * @config
     */
    private static $db = [
        'PaginationSize' => 'Varchar(16)',
        'PaginationButtonMode' => 'Varchar(16)'
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
                    'PaginationStyle',
                    $this->owner->fieldLabel('PaginationStyle'),
                    [
                        DropdownField::create(
                            'PaginationSize',
                            $this->owner->fieldLabel('PaginationSize'),
                            $this->owner->getPaginationSizeOptions()
                        )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder),
                        DropdownField::create(
                            'PaginationButtonMode',
                            $this->owner->fieldLabel('PaginationButtonMode'),
                            $this->owner->getPaginationButtonModeOptions()
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
        $labels['PaginationSize']  = _t(__CLASS__ . '.SIZE', 'Size');
        $labels['PaginationStyle'] = _t(__CLASS__ . '.PAGINATION', 'Pagination');
        $labels['PaginationButtonMode'] = _t(__CLASS__ . '.BUTTONMODE', 'Button mode');
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
        
        if ($class = $this->owner->PaginationSizeClass) {
            $classes[] = $class;
        }
        
        if ($class = $this->owner->PaginationButtonModeClass) {
            $classes[] = $class;
        }
    }
    
    /**
     * Answers the pagination size class of the extended object.
     *
     * @return string
     */
    public function getPaginationSizeClass()
    {
        switch ($this->owner->PaginationSize) {
            case self::SIZE_SMALL:
                return $this->style('pagination.small');
            case self::SIZE_LARGE:
                return $this->style('pagination.large');
        }
    }
    
    /**
     * Answers the pagination button mode class of the extended object.
     *
     * @return string
     */
    public function getPaginationButtonModeClass()
    {
        switch ($this->owner->PaginationButtonMode) {
            case self::BUTTON_MODE_ICON:
                return 'icon-only';
            case self::BUTTON_MODE_TEXT:
                return 'text-only';
            case self::BUTTON_MODE_BOTH:
                return 'icon-text';
        }
    }
    
    /**
     * Answers an array of options for the pagination size field.
     *
     * @return array
     */
    public function getPaginationSizeOptions()
    {
        return [
            self::SIZE_SMALL => _t(__CLASS__ . '.SMALL', 'Small'),
            self::SIZE_LARGE => _t(__CLASS__ . '.LARGE', 'Large'),
        ];
    }
    
    /**
     * Answers an array of options for the pagination button mode field.
     *
     * @return array
     */
    public function getPaginationButtonModeOptions()
    {
        return [
            self::BUTTON_MODE_ICON => _t(__CLASS__ . '.ICON', 'Icon Only'),
            self::BUTTON_MODE_TEXT => _t(__CLASS__ . '.TEXT', 'Text Only'),
            self::BUTTON_MODE_BOTH => _t(__CLASS__ . '.BOTH', 'Both Icon and Text')
        ];
    }
}
