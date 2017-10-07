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

use SilverStripe\ORM\ArrayList;
use SilverWare\Folders\PanelFolder;
use SilverWare\Grid\Column;
use SilverWare\Model\Panel;
use Page;

/**
 * An extension of the base component class for an area component.
 *
 * @package SilverWare\Components
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class AreaComponent extends BaseComponent
{
    /**
     * Human-readable singular name.
     *
     * @var string
     * @config
     */
    private static $singular_name = 'Area Component';
    
    /**
     * Human-readable plural name.
     *
     * @var string
     * @config
     */
    private static $plural_name = 'Area Components';
    
    /**
     * Description of this object.
     *
     * @var string
     * @config
     */
    private static $description = 'Defines an area of the template for panels to be added';
    
    /**
     * Icon file for this object.
     *
     * @var string
     * @config
     */
    private static $icon = 'silverware/silverware: admin/client/dist/images/icons/AreaComponent.png';
    
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
     * Defines the allowed parents for this object.
     *
     * @var array
     * @config
     */
    private static $allowed_parents = [
        Column::class
    ];
    
    /**
     * Defines the default values for the fields of this object.
     *
     * @var array
     * @config
     */
    private static $defaults = [
        'HideTitle' => 1
    ];
    
    /**
     * Defines the reciprocal many-many associations for this object.
     *
     * @var array
     * @config
     */
    private static $belongs_many_many = [
        'Panels' => Panel::class
    ];
    
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
        
        $labels['Title'] = _t(__CLASS__ . '.AREANAME', 'Area name');
        
        // Answer Field Labels:
        
        return $labels;
    }
    
    /**
     * Answers a list of the enabled children within the area and panel for the current page.
     *
     * @return ArrayList
     */
    public function getEnabledChildren()
    {
        if ($page = $this->getCurrentPage(Page::class)) {
            
            if (($panel = $page->getPanelForArea($this)) && $panel->isEnabled()) {
                return $panel->getEnabledChildren();
            }
            
        }
        
        return ArrayList::create();
    }
    
    /**
     * Answers only the panels associated with the receiver from the panels folder.
     *
     * @return DataList
     */
    public function getFolderPanels()
    {
        return $this->Panels()->filter('ParentID', PanelFolder::find()->ID);
    }
    
    /**
     * Answers true if no enabled children are available.
     *
     * @return boolean
     */
    public function isDisabled()
    {
        return !$this->getEnabledChildren()->exists();
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
        return $this->renderChildren($layout, $title);
    }
}
