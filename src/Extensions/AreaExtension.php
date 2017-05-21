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

use SilverStripe\ORM\DataExtension;
use SilverWare\Components\AreaComponent;
use SilverWare\Model\Panel;
use Page;

/**
 * A data extension class which adds area and panel functionality to extended objects.
 *
 * @package SilverWare\Extensions
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class AreaExtension extends DataExtension
{
    /**
     * Defines the reciprocal many-many associations for the extended object.
     *
     * @var array
     * @config
     */
    private static $belongs_many_many = [
        'Panels' => Panel::class
    ];
    
    /**
     * Answers the panel associated with the given area component.
     *
     * @param AreaComponent $area
     *
     * @return Panel
     */
    public function getPanelForArea(AreaComponent $area)
    {
        // Answer Panel from Children:
        
        foreach ($this->owner->getChildPanels() as $panel) {
            
            if ($panel->hasArea($area)) {
                return $panel;
            }
            
        }
        
        // Answer Panel from Owner:
        
        foreach ($this->owner->Panels() as $panel) {
            
            if ($panel->hasArea($area)) {
                return $panel;
            }
            
        }
        
        // Answer Panel from Parent:
        
        if (($parent = $this->owner->getParent()) && $parent instanceof Page) {
            return $parent->getPanelForArea($area);
        }
        
        // Answer Panel for All Pages:
        
        return $area->getFolderPanels()->find('ShowOn', 'AllPages');
    }
    
    /**
     * Answers a list of child panels from the extended object.
     *
     * @return DataList
     */
    public function getChildPanels()
    {
        return $this->owner->AllChildren()->filter('ClassName', Panel::class);
    }
}
