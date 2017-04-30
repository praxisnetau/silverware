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

use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\CompositeField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldGroup;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\ArrayLib;
use SilverStripe\ORM\DataList;
use SilverWare\Colorpicker\Forms\ColorField;
use SilverWare\Tags\Tag;
use SilverWare\Tags\TagSource;
use SilverWare\Tools\ClassTools;

/**
 * An extension of the base component class for a tag cloud component.
 *
 * @package SilverWare\Components
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class TagCloudComponent extends BaseComponent
{
    /**
     * Human-readable singular name.
     *
     * @var string
     * @config
     */
    private static $singular_name = 'Tag Cloud Component';
    
    /**
     * Human-readable plural name.
     *
     * @var string
     * @config
     */
    private static $plural_name = 'Tag Cloud Components';
    
    /**
     * Description of this object.
     *
     * @var string
     * @config
     */
    private static $description = 'A component which show tags in an interactive cloud';
    
    /**
     * Icon file for this object.
     *
     * @var string
     * @config
     */
    private static $icon = 'silverware/admin/client/dist/images/icons/TagCloudComponent.png';
    
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
        'Depth' => 'Decimal',
        'Zoom' => 'Decimal',
        'ZoomMin' => 'Decimal',
        'ZoomMax' => 'Decimal',
        'TextColor' => 'Color',
        'OutlineColor' => 'Color',
        'InitialRotationH' => 'Decimal',
        'InitialRotationV' => 'Decimal',
        'WeightSizeMin' => 'AbsoluteInt',
        'WeightSizeMax' => 'AbsoluteInt',
        'Weight' => 'Boolean'
    ];
    
    /**
     * Defines the has-one associations for this object.
     *
     * @var array
     * @config
     */
    private static $has_one = [
        'TagSource' => SiteTree::class
    ];
    
    /**
     * Defines the default values for the fields of this object.
     *
     * @var array
     * @config
     */
    private static $defaults = [
        'Depth' => 0.2,
        'Zoom' => 1.0,
        'ZoomMin' => 0.5,
        'ZoomMax' => 2.0,
        'InitialRotationH' => 0,
        'InitialRotationV' => 0,
        'WeightSizeMin' => 15,
        'WeightSizeMax' => 30,
        'Weight' => 1
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
        
        $placeholder = _t(__CLASS__ . '.DROPDOWNSELECT', 'Select');
        
        // Create Main Fields:
        
        $fields->addFieldsToTab(
            'Root.Main',
            [
                DropdownField::create(
                    'TagSourceID',
                    $this->fieldLabel('TagSourceID'),
                    $this->getTagSourceOptions()
                )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder)
            ]
        );
        
        // Create Style Fields:
        
        $fields->addFieldToTab(
            'Root.Style',
            CompositeField::create([
                ColorField::create(
                    'TextColor',
                    $this->fieldLabel('TextColor')
                ),
                ColorField::create(
                    'OutlineColor',
                    $this->fieldLabel('OutlineColor')
                )
            ])->setName('TagCloudComponentStyle')->setTitle($this->i18n_singular_name())
        );
        
        // Create Options Fields:
        
        $fields->addFieldToTab(
            'Root.Options',
            CompositeField::create([
                FieldGroup::create(
                    $this->fieldLabel('Zoom'),
                    [
                        DropdownField::create(
                            'Zoom',
                            $this->fieldLabel('ZoomInitial'),
                            $this->getRangeOptions(0.5, 3, 0.1)
                        ),
                        DropdownField::create(
                            'ZoomMin',
                            $this->fieldLabel('ZoomMin'),
                            $this->getRangeOptions(0.5, 3, 0.1)
                        ),
                        DropdownField::create(
                            'ZoomMax',
                            $this->fieldLabel('ZoomMax'),
                            $this->getRangeOptions(0.5, 3, 0.1)
                        ),
                        DropdownField::create(
                            'Depth',
                            $this->fieldLabel('Depth'),
                            $this->getRangeOptions(0.1, 1, 0.1)
                        )
                    ]
                ),
                FieldGroup::create(
                    $this->fieldLabel('InitialRotation'),
                    [
                        DropdownField::create(
                            'InitialRotationH',
                            $this->fieldLabel('InitialRotationH'),
                            $this->getRangeOptions(-1, 1, 0.1)
                        ),
                        DropdownField::create(
                            'InitialRotationV',
                            $this->fieldLabel('InitialRotationV'),
                            $this->getRangeOptions(-1, 1, 0.1)
                        )
                    ]
                ),
                FieldGroup::create(
                    $this->fieldLabel('WeightSize'),
                    [
                        TextField::create(
                            'WeightSizeMin',
                            $this->fieldLabel('WeightSizeMin')
                        ),
                        TextField::create(
                            'WeightSizeMax',
                            $this->fieldLabel('WeightSizeMax')
                        )
                    ]
                ),
                CheckboxField::create(
                    'Weight',
                    $this->fieldLabel('Weight')
                )
            ])->setName('TagCloudComponentOptions')->setTitle($this->i18n_singular_name())
        );
        
        // Answer Field Objects:
        
        return $fields;
    }
    
    /**
     * Answers a validator for the CMS interface.
     *
     * @return RequiredFields
     */
    public function getCMSValidator()
    {
        return RequiredFields::create([
            'TagSourceID'
        ]);
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
        
        $labels['Depth'] = _t(__CLASS__ . '.DEPTH', 'Depth');
        $labels['Zoom'] = _t(__CLASS__ . '.ZOOM', 'Zoom');
        $labels['ZoomMin'] = _t(__CLASS__ . '.MINIMUM', 'Minimum');
        $labels['ZoomMax'] = _t(__CLASS__ . '.MAXIMUM', 'Maximum');
        $labels['ZoomInitial'] = _t(__CLASS__ . '.INITIAL', 'Initial');
        $labels['TextColor'] = _t(__CLASS__ . '.TEXTCOLOR', 'Text color');
        $labels['OutlineColor'] = _t(__CLASS__ . '.OUTLINECOLOR', 'Outline color');
        $labels['InitialRotation'] = _t(__CLASS__ . '.INITIALROTATION', 'Initial rotation');
        $labels['InitialRotationV'] = _t(__CLASS__ . '.VERTICAL', 'Vertical');
        $labels['InitialRotationH'] = _t(__CLASS__ . '.HORIZONTAL', 'Horizontal');
        $labels['WeightSizeMin'] = _t(__CLASS__ . '.MINIMUM', 'Minimum');
        $labels['WeightSizeMax'] = _t(__CLASS__ . '.MAXIMUM', 'Maximum');
        $labels['WeightSize'] = _t(__CLASS__ . '.WEIGHTEDFONTSIZES', 'Weighted font sizes');
        $labels['Weight'] = _t(__CLASS__ . '.ENABLETAGWEIGHTING', 'Enable tag weighting');
        $labels['TagSource'] = $labels['TagSourceID'] = _t(__CLASS__ . '.TAGSOURCE', 'Tag source');
        
        // Answer Field Labels:
        
        return $labels;
    }
    
    /**
     * Answers a list of tags for the template.
     *
     * @return SS_List
     */
    public function getTags()
    {
        if ($this->TagSource()->isInDB()) {
            return $this->TagSource()->getTags();
        }
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
                'data-depth' => $this->Depth,
                'data-zoom' => $this->Zoom,
                'data-zoom-min' => $this->ZoomMin,
                'data-zoom-max' => $this->ZoomMax,
                'data-canvas' => $this->CanvasCSSID,
                'data-tag-list' => $this->TagListID,
                'data-color-text' => $this->TextColor,
                'data-color-outline' => $this->OutlineColor,
                'data-weight-size-min' => $this->WeightSizeMin,
                'data-weight-size-max' => $this->WeightSizeMax,
                'data-initial' => $this->Initial,
                'data-weight' => $this->dbObject('Weight')->NiceAsBoolean()
            ]
        );
        
        return $attributes;
    }
    
    /**
     * Answers the canvas element ID for the template.
     *
     * @return string
     */
    public function getCanvasID()
    {
        return sprintf('%s_Canvas', $this->getHTMLID());
    }
    
    /**
     * Answers the tag list element ID for the template.
     *
     * @return string
     */
    public function getTagListID()
    {
        return sprintf('%s_TagList', $this->getHTMLID());
    }
    
    /**
     * Answers the canvas element ID for a stylesheet.
     *
     * @return string
     */
    public function getCanvasCSSID()
    {
        return $this->getCSSID($this->getCanvasID());
    }
    
    /**
     * Answers the initial rotation settings for the tag cloud as a string.
     *
     * @return string
     */
    public function getInitial()
    {
        $initial = [
            (float) $this->InitialRotationH,
            (float) $this->InitialRotationV
        ];
        
        return json_encode($initial);
    }
    
    /**
     * Answers true if the object is disabled within the template.
     *
     * @return boolean
     */
    public function isDisabled()
    {
        if (!$this->TagSourceID || !$this->getTags()->exists()) {
            return true;
        }
        
        return parent::isDisabled();
    }
    
    /**
     * Answers an array of range options for a dropdown field.
     *
     * @param float $min
     * @param float $max
     * @param float $step
     *
     * @return array
     */
    public function getRangeOptions($min, $max, $step)
    {
        $options = [];
        
        foreach (range($min, $max, $step) as $value) {
            $options[number_format($value, 1)] = number_format($value, 1);
        }
        
        return $options;
    }
    
    /**
     * Answers an array of options for the tag source field.
     *
     * @return array
     */
    public function getTagSourceOptions()
    {
        return ClassTools::singleton()->getImplementorMap(TagSource::class);
    }
}
