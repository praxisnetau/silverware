<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Model
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\Model;

use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Forms\CheckboxSetField;
use SilverStripe\Forms\SelectionGroup;
use SilverStripe\Forms\SelectionGroup_Item;
use SilverStripe\Forms\TreeMultiselectField;
use SilverWare\Components\AreaComponent;
use SilverWare\Components\BaseComponent;
use SilverWare\Folders\PanelFolder;
use Page;

/**
 * An extension of the component class for a panel.
 *
 * @package SilverWare\Model
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class Panel extends Component
{
    /**
     * Human-readable singular name.
     *
     * @var string
     * @config
     */
    private static $singular_name = 'Panel';
    
    /**
     * Human-readable plural name.
     *
     * @var string
     * @config
     */
    private static $plural_name = 'Panels';
    
    /**
     * Description of this object.
     *
     * @var string
     * @config
     */
    private static $description = 'An individual SilverWare panel';
    
    /**
     * Icon file for this object.
     *
     * @var string
     * @config
     */
    private static $icon = 'silverware/admin/client/dist/images/icons/Panel.png';
    
    /**
     * Defines an ancestor class to hide from the admin interface.
     *
     * @var string
     * @config
     */
    private static $hide_ancestor = Component::class;
    
    /**
     * Maps field names to field types for this object.
     *
     * @var array
     * @config
     */
    private static $db = [
        'ShowOn' => "Enum('AllPages, OnlyThesePages', 'AllPages')"
    ];
    
    /**
     * Defines the default values for the fields of this object.
     *
     * @var array
     * @config
     */
    private static $defaults = [
        'ShowOn' => 'AllPages'
    ];
    
    /**
     * Defines the many-many associations for this object.
     *
     * @var array
     * @config
     */
    private static $many_many = [
        'Pages' => Page::class,
        'Areas' => AreaComponent::class
    ];
    
    /**
     * Defines the allowed children for this object.
     *
     * @var array|string
     * @config
     */
    private static $allowed_children = [
        BaseComponent::class
    ];
    
    /**
     * Defines the allowed parents for this object.
     *
     * @var array
     * @config
     */
    private static $allowed_parents = [
        Page::class,
        PanelFolder::class
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
        
        // Remove Field Objects:
        
        $fields->fieldByName('Root')->removeByName('Style');
        
        // Create Main Fields:
        
        $fields->addFieldsToTab(
            'Root.Main',
            [
                CheckboxSetField::create(
                    'Areas',
                    $this->fieldLabel('Areas'),
                    AreaComponent::get()->map()
                )
            ]
        );
        
        // Insert Show On Field (within folder only):
        
        if ($this->getParent() instanceof PanelFolder) {
            
            $fields->insertAfter(
                SelectionGroup::create(
                    'ShowOn',
                    [
                        SelectionGroup_Item::create(
                            'AllPages',
                            null,
                            $this->fieldLabel('AllPages')
                        ),
                        SelectionGroup_Item::create(
                            'OnlyThesePages',
                            TreeMultiselectField::create('Pages', '', 'Page'),
                            $this->fieldLabel('OnlyThesePages')
                        )
                    ]
                )->setTitle($this->fieldLabel('ShowOn')),
                'Areas'
            );
            
        }
        
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
        
        $labels['Areas'] = _t(__CLASS__ . '.AREAS', 'Areas');
        $labels['Title'] = _t(__CLASS__ . '.PANELNAME', 'Panel name');
        $labels['ShowOn'] = _t(__CLASS__ . '.SHOWON', 'Show on');
        $labels['AllPages'] = _t(__CLASS__ . '.ALLPAGES', 'All pages');
        $labels['OnlyThesePages'] = _t(__CLASS__ . '.ONLYTHESEPAGES', 'Only these pages');
        
        // Answer Field Labels:
        
        return $labels;
    }
    
    /**
     * Answers true if the panel is associated with the given area component.
     *
     * @return boolean
     */
    public function hasArea(AreaComponent $area)
    {
        return array_key_exists($area->ID, $this->Areas()->getIDList());
    }
    
    /**
     * Answers true if the panel is shown on all pages.
     *
     * @return boolean
     */
    public function isShownOnAll()
    {
        return ($this->ShowOn == 'AllPages');
    }
}
