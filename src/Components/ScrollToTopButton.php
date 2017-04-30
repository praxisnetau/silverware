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
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\Components;

use SilverStripe\Forms\CompositeField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\TextField;
use SilverWare\FontIcons\Forms\FontIconField;

/**
 * An extension of the base component class for a scroll to top button.
 *
 * @package SilverWare\Components
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class ScrollToTopButton extends BaseComponent
{
    /**
     * Define corner constants.
     */
    const CORNER_ROUNDED  = 'rounded';
    const CORNER_CIRCULAR = 'circular';
    
    /**
     * Human-readable singular name.
     *
     * @var string
     * @config
     */
    private static $singular_name = 'Scroll to Top Button';
    
    /**
     * Human-readable plural name.
     *
     * @var string
     * @config
     */
    private static $plural_name = 'Scroll to Top Buttons';
    
    /**
     * Description of this object.
     *
     * @var string
     * @config
     */
    private static $description = 'A button which scrolls to the top of the page when pressed';
    
    /**
     * Icon file for this object.
     *
     * @var string
     * @config
     */
    private static $icon = 'silverware/admin/client/dist/images/icons/ScrollToTopButton.png';
    
    /**
     * Defines an ancestor class to hide from the admin interface.
     *
     * @var string
     * @config
     */
    private static $hide_ancestor = BaseComponent::class;
    
    /**
     * Defines the allowed children for this object.
     *
     * @var array|string
     * @config
     */
    private static $allowed_children = 'none';
    
    /**
     * Maps field names to field types for this object.
     *
     * @var array
     * @config
     */
    private static $db = [
        'Label' => 'Varchar(128)',
        'ButtonIcon' => 'FontIcon',
        'OffsetShow' => 'Int',
        'OffsetOpacity' => 'Int',
        'ScrollDuration' => 'Int',
        'CornerStyle' => 'Varchar(16)'
    ];
    
    /**
     * Defines the default values for the fields of this object.
     *
     * @var array
     * @config
     */
    private static $defaults = [
        'ButtonIcon' => 'chevron-up',
        'OffsetShow' => 300,
        'OffsetOpacity' => 1000,
        'ScrollDuration' => 800
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
        
        // Define Placeholder:
        
        $placeholder = _t(__CLASS__ . '.DROPDOWNDEFAULT', '(default)');
        
        // Create Main Fields:
        
        $fields->addFieldsToTab(
            'Root.Main',
            [
                TextField::create(
                    'Label',
                    $this->fieldLabel('Label')
                ),
                FontIconField::create(
                    'ButtonIcon',
                    $this->fieldLabel('ButtonIcon')
                )
            ]
        );
        
        // Create Style Fields:
        
        $fields->addFieldToTab(
            'Root.Style',
            CompositeField::create([
                DropdownField::create(
                    'CornerStyle',
                    $this->fieldLabel('CornerStyle'),
                    $this->getCornerStyleOptions()
                )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder)
            ])->setName('ScrollToTopButtonStyle')->setTitle($this->i18n_singular_name())
        );
        
        // Create Options Fields:
        
        $fields->addFieldToTab(
            'Root.Options',
            CompositeField::create([
                NumericField::create(
                    'OffsetShow',
                    $this->fieldLabel('OffsetShow')
                ),
                NumericField::create(
                    'OffsetOpacity',
                    $this->fieldLabel('OffsetOpacity')
                ),
                NumericField::create(
                    'ScrollDuration',
                    $this->fieldLabel('ScrollDuration')
                )
            ])->setName('ScrollToTopButtonOptions')->setTitle($this->i18n_singular_name())
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
        
        $labels['Label'] = _t(__CLASS__ . '.LABEL', 'Label');
        $labels['ButtonIcon'] = _t(__CLASS__ . '.ICON', 'Icon');
        $labels['CornerStyle'] = _t(__CLASS__ . '.CORNERSTYLE', 'Corner style');
        $labels['OffsetShow'] = _t(__CLASS__ . '.OFFSETSHOWINPIXELS', 'Show offset (in pixels)');
        $labels['OffsetOpacity'] = _t(__CLASS__ . '.OFFSETOPACITYINPIXELS', 'Opacity offset (in pixels)');
        $labels['ScrollDuration'] = _t(__CLASS__ . '.SCROLLDURATIONINMS', 'Scroll duration (in milliseconds)');
        
        // Answer Field Labels:
        
        return $labels;
    }
    
    /**
     * Populates the default values for the fields of the receiver.
     *
     * @return void
     */
    public function populateDefaults()
    {
        // Populate Defaults (from parent):
        
        parent::populateDefaults();
        
        // Populate Defaults:
        
        $this->Label = _t(__CLASS__ . 'DEFAULTLABEL', 'Scroll to Top');
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
                'href' => '#top',
                'title' => $this->Label,
                'data-offset-show' => $this->OffsetShow,
                'data-offset-opacity' => $this->OffsetOpacity,
                'data-scroll-duration' => $this->ScrollDuration
            ]
        );
        
        return $attributes;
    }
    
    /**
     * Answers an array of class names for the HTML template.
     *
     * @return array
     */
    public function getClassNames()
    {
        $classes = parent::getClassNames();
        
        switch ($this->CornerStyle) {
            case self::CORNER_ROUNDED:
                $classes[] = $this->style('rounded');
                break;
            case self::CORNER_CIRCULAR:
                $classes[] = $this->style('rounded.circle');
                break;
        }
        
        return $classes;
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
        return $this->getController()->renderWith(self::class);
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
