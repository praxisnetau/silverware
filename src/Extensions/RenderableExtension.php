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

use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\CompositeField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\SelectionGroup;
use SilverStripe\Forms\SelectionGroup_Item;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataExtension;

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
                CompositeField::create([
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
                ])->setName('ComponentStyle')->setTitle($this->owner->fieldLabel('ComponentStyle'))
            ]
        );
        
        // Create Options Fields:
        
        $fields->addFieldsToTab(
            'Root.Options',
            [
                CompositeField::create([
                    CheckboxField::create(
                        'Disabled',
                        $this->owner->fieldLabel('Disabled')
                    )
                ])->setName('ComponentOptions')->setTitle($this->owner->fieldLabel('ComponentOptions')),
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
        $labels['StyleID'] = _t(__CLASS__ . '.STYLEID', 'Style ID');
        $labels['Options'] = _t(__CLASS__ . '.OPTIONS', 'Options');
        $labels['Enabled'] = _t(__CLASS__ . '.ENABLED', 'Enabled');
        $labels['StyleClasses'] = _t(__CLASS__ . '.STYLECLASSES', 'Style Classes');
        $labels['CacheLifetime'] = _t(__CLASS__ . '.CACHELIFETIMEINSECONDS', 'Cache lifetime (in seconds)');
        $labels['Disabled'] = $labels['Disabled.Nice'] = _t(__CLASS__ . '.DISABLED', 'Disabled');
        $labels['ComponentStyle'] = $labels['ComponentOptions'] = _t(__CLASS__ . '.COMPONENT', 'Component');
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
}
