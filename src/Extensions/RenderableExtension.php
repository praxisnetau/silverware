<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Extensions
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\Extensions;

use SilverStripe\Core\Convert;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\CheckboxSetField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\SelectionGroup;
use SilverStripe\Forms\SelectionGroup_Item;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\ArrayLib;
use SilverStripe\ORM\DataExtension;
use SilverWare\Forms\FieldSection;

/**
 * A data extension class which allows extended objects to become template renderable.
 *
 * @package SilverWare\Extensions
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class RenderableExtension extends DataExtension
{
    /**
     * Maps field names to field types for the extended object.
     *
     * @var array
     * @config
     */
    private static $db = [
        'StyleID' => 'Varchar(255)',
        'StyleClasses' => 'Varchar(255)',
        'CustomStyles' => 'Varchar(255)',
        'CacheLifetime' => 'Int',
        'Cached' => 'Boolean',
        'Disabled' => 'Boolean'
    ];
    
    /**
     * Defines the default values for the fields of the extended object.
     *
     * @var array
     * @config
     */
    private static $defaults = [
        'Cached' => 0,
        'Disabled' => 0,
        'CacheLifetime' => 300
    ];
    
    /**
     * Maps field and method names to the class names of casting objects.
     *
     * @var array
     * @config
     */
    private static $casting = [
        'render' => 'HTMLFragment',
        'AttributesHTML' => 'HTMLFragment'
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
        // Create Tabs:
        
        $fields->findOrMakeTab('Root.Style', $this->owner->fieldLabel('Style'));
        $fields->findOrMakeTab('Root.Options', $this->owner->fieldLabel('Options'));
        
        // Create Style Fields:
        
        $fields->addFieldsToTab(
            'Root.Style',
            [
                $selectors = FieldSection::create(
                    'SelectorStyle',
                    $this->owner->fieldLabel('Selectors'),
                    [
                        TextField::create(
                            'StyleID',
                            $this->owner->fieldLabel('StyleID')
                        )->setRightTitle(
                            _t(
                                __CLASS__ . '.STYLEIDRIGHTTITLE',
                                'Allows you to define a custom style ID for the component.'
                            )
                        ),
                        TextField::create(
                            'StyleClasses',
                            $this->owner->fieldLabel('StyleClasses')
                        )->setRightTitle(
                            _t(
                                __CLASS__ . '.STYLECLASSESRIGHTTITLE',
                                'Allows you to add additional style classes for the component (separated by spaces).'
                            )
                        )
                    ]
                )
            ]
        );
        
        // Create Custom Styles Field (if available):
        
        if ($this->owner->hasCustomStylesConfig()) {
            
            $selectors->push(
                CheckboxSetField::create(
                    'CustomStyles',
                    $this->owner->fieldLabel('CustomStyles'),
                    $this->owner->getCustomStylesOptions()
                )->setRightTitle(
                    _t(
                        __CLASS__ . '.CUSTOMSTYLESRIGHTTITLE',
                        'This component supports custom styles. Select one or more of the options above.'
                    )
                )
            );
            
        }
        
        // Create Options Fields:
        
        $fields->addFieldsToTab(
            'Root.Options',
            [
                FieldSection::create(
                    'StatusOptions',
                    $this->owner->fieldLabel('Status'),
                    [
                        CheckboxField::create(
                            'Disabled',
                            $this->owner->fieldLabel('Disabled')
                        )
                    ]
                ),
                SelectionGroup::create(
                    'Cached',
                    [
                        SelectionGroup_Item::create(
                            '0',
                            null,
                            $this->owner->fieldLabel('Disabled')
                        ),
                        SelectionGroup_Item::create(
                            '1',
                            NumericField::create(
                                'CacheLifetime',
                                $this->owner->fieldLabel('CacheLifetime')
                            ),
                            $this->owner->fieldLabel('Enabled')
                        )
                    ]
                )->setTitle($this->owner->fieldLabel('Cached'))
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
        $labels['Style'] = _t(__CLASS__ . '.STYLE', 'Style');
        $labels['Cached'] = _t(__CLASS__ . '.CACHE', 'Cache');
        $labels['Status'] = _t(__CLASS__ . '.STATUS', 'Status');
        $labels['StyleID'] = _t(__CLASS__ . '.STYLEID', 'Style ID');
        $labels['Options'] = _t(__CLASS__ . '.OPTIONS', 'Options');
        $labels['Enabled'] = _t(__CLASS__ . '.ENABLED', 'Enabled');
        $labels['Selectors'] = _t(__CLASS__ . '.SELECTORS', 'Selectors');
        $labels['CustomStyles'] = _t(__CLASS__ . '.CUSTOMSTYLES', 'Custom Styles');
        $labels['StyleClasses'] = _t(__CLASS__ . '.STYLECLASSES', 'Style Classes');
        $labels['CacheLifetime'] = _t(__CLASS__ . '.CACHELIFETIMEINSECONDS', 'Cache lifetime (in seconds)');
        $labels['Disabled'] = $labels['Disabled.Nice'] = _t(__CLASS__ . '.DISABLED', 'Disabled');
    }
    
    /**
     * Updates the CMS tree classes of the extended object.
     *
     * @param string $classes String of CMS tree classes from the extended object.
     *
     * @return void
     */
    public function updateCMSTreeClasses(&$classes)
    {
        if ($this->owner->getField('Disabled')) {
            $classes .= ' is-disabled';
        } elseif ($this->owner->getField('Cached')) {
            $classes .= ' is-cached';
        }
    }
    
    /**
     * Event method called before the extended object is written to the database.
     *
     * @return void
     */
    public function onBeforeWrite()
    {
        $this->owner->StyleID = $this->owner->cleanStyleID($this->owner->StyleID);
        $this->owner->StyleClasses = $this->owner->cleanStyleClasses($this->owner->StyleClasses);
    }
    
    /**
     * Event method called after the extended object is written to the database.
     *
     * @return void
     */
    public function onAfterWrite()
    {
        $this->owner->clearRenderCache();
    }
    
    /**
     * Answers a sorted array of any custom styles configured for the extended object.
     *
     * @return array
     */
    public function getCustomStylesConfig()
    {
        $styles = [];
        
        if (($config = $this->owner->config()->custom_styles) && is_array($config)) {
            $styles = $config;
        }
        
        ksort($styles);
        
        array_walk($styles, function (&$item) {
            $item = $this->owner->cleanStyleClasses($item);
        });
        
        return $styles;
    }
    
    /**
     * Answers true if the extended object has custom styles configured.
     *
     * @return boolean
     */
    public function hasCustomStylesConfig()
    {
        return !empty($this->owner->getCustomStylesConfig());
    }
    
    /**
     * Answers an array of selected custom styles mapped to their class names.
     *
     * @return array
     */
    public function getCustomStylesMappings()
    {
        $config = $this->owner->getCustomStylesConfig();
        
        if ($values = $this->owner->getField('CustomStyles')) {
            
            $styles = Convert::json2array($values);
            
            return array_intersect_key($config, array_flip($styles));
            
        }
        
        return [];
    }
    
    /**
     * Answers an array of options for the custom styles field.
     *
     * @return array
     */
    public function getCustomStylesOptions()
    {
        $keys = array_keys($this->owner->getCustomStylesConfig());
        
        return ArrayLib::valuekey($keys, $keys);
    }
}
