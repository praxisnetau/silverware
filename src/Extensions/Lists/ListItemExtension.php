<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Extensions\Lists
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\Extensions\Lists;

use SilverStripe\Core\Extension;
use SilverStripe\ORM\ArrayList;
use SilverStripe\View\ArrayData;
use SilverStripe\View\SSViewer;
use SilverWare\Components\BaseListComponent;
use SilverWare\Model\Component;
use SilverWare\Tools\ViewTools;
use SilverWare\View\GridAware;

/**
 * An extension class to add list item functionality to the extended object.
 *
 * @package SilverWare\Extensions\Lists
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class ListItemExtension extends Extension
{
    use GridAware;
    
    /**
     * Define constants.
     */
    const DEFAULT_LIST_ITEM_TEMPLATE = 'SilverWare\Lists\ListItem';
    
    /**
     * Defines the value of the renderer attribute.
     *
     * @param Component $renderer
     *
     * @return $this
     */
    public function setRenderer(Component $renderer)
    {
        $this->owner->setField('renderer', $renderer);
        
        return $this;
    }
    
    /**
     * Answers the value of the renderer attribute.
     *
     * @return Component
     */
    public function getRenderer()
    {
        return $this->owner->getField('renderer');
    }
    
    /**
     * Answers the renderer object only if it is a list component.
     *
     * @return BaseListComponent
     */
    public function getListComponent()
    {
        if ($this->owner->hasListComponent()) {
            return $this->owner->getRenderer();
        }
    }
    
    /**
     * Answers true if the renderer is a list component.
     *
     * @return boolean
     */
    public function hasListComponent()
    {
        return ($this->owner->getRenderer() instanceof BaseListComponent);
    }
    
    /**
     * Answers an string of list item class names for the HTML template.
     *
     * @return string
     */
    public function getListItemClass()
    {
        return ViewTools::singleton()->array2att($this->owner->getListItemClassNames());
    }
    
    /**
     * Answers an array of list item class names for the HTML template.
     *
     * @return array
     */
    public function getListItemClassNames()
    {
        // Initialise:
        
        $classes = ['item'];
        
        // Merge Ancestor Class Names:
        
        $classes = array_merge(
            $classes,
            ViewTools::singleton()->getAncestorClassNames(
                $this->owner,
                $this->owner->baseClass()
            )
        );
        
        // Merge Meta Class Names:
        
        if ($this->owner->hasMethod('getMetaClassNames')) {
            $classes = array_merge($classes, $this->owner->getMetaClassNames());
        }
        
        // Update Class Names via Renderer:
        
        if ($this->owner->getRenderer()->hasMethod('updateListItemClassNames')) {
            $this->owner->getRenderer()->updateListItemClassNames($classes);
        }
        
        // Answer Classes:
        
        return $classes;
    }
    
    /**
     * Answers an string of list item content class names for the HTML template.
     *
     * @return string
     */
    public function getListItemContentClass()
    {
        return ViewTools::singleton()->array2att($this->owner->getListItemContentClassNames());
    }
    
    /**
     * Answers an string of list item image class names for the HTML template.
     *
     * @return string
     */
    public function getListItemImageClass()
    {
        return ViewTools::singleton()->array2att($this->owner->getListItemImageClassNames());
    }
    
    /**
     * Answers an array of list item content class names for the HTML template.
     *
     * @return array
     */
    public function getListItemContentClassNames()
    {
        return $this->styles('content');
    }
    
    /**
     * Answers an array of list item image class names for the HTML template.
     *
     * @return array
     */
    public function getListItemImageClassNames()
    {
        return $this->styles('image.fluid');
    }
    
    /**
     * Answers the name of the list item template.
     *
     * @return string
     */
    public function getListItemTemplate()
    {
        // Define Template by Class:
        
        $template = sprintf('%s\ListItem', get_class($this->owner));
        
        // Define Template via Renderer:
        
        if ($this->owner->getRenderer()->hasMethod('getListItemTemplate')) {
            $template = $this->owner->getRenderer()->getListItemTemplate(get_class($this->owner));
        }
        
        // Verify Template Exists:
        
        if (SSViewer::hasTemplate($template)) {
            return $template;
        }
        
        // Answer Default Template:
        
        return $this->owner->getDefaultListItemTemplate();
    }
    
    /**
     * Answers the name of the default list item template.
     *
     * @return string
     */
    public function getDefaultListItemTemplate()
    {
        return self::DEFAULT_LIST_ITEM_TEMPLATE;
    }
    
    /**
     * Renders the object as a list item for the HTML template.
     *
     * @param boolean $isFirst Item is first in the list.
     * @param boolean $isMiddle Item is in the middle of the list.
     * @param boolean $isLast Item is last in the list.
     *
     * @return DBHTMLText
     */
    public function renderListItem($isFirst = false, $isMiddle = false, $isLast = false, $wtf = false)
    {
        return $this->owner->customise([
            'isFirst' => $isFirst,
            'isMiddle' => $isMiddle,
            'isLast' => $isLast
        ])->renderWith($this->owner->getListItemTemplate());
    }
    
    /**
     * Answers an array list object containing the item details for the template.
     *
     * @return ArrayList
     */
    public function getListItemDetails()
    {
        $details = ArrayList::create();
        
        foreach ($this->owner->getListItemDetailsConfig() as $name => $spec) {
            
            if ($spec) {
                
                foreach ($spec as $item => $value) {
                    
                    $args = [];
                    
                    if (is_array($value)) {
                        $args  = $value;
                        $value = array_shift($args);
                    }
                    
                    $spec[$item] = $this->processListItemValue($value, $args);
                    
                }
                
                if (isset($spec['show']) && !$this->owner->{$spec['show']}) {
                    continue;
                }
                
                $details->push(
                    ArrayData::create([
                        'Name' => $name,
                        'Icon' => isset($spec['icon']) ? $spec['icon'] : null,
                        'Text' => isset($spec['text']) ? $spec['text'] : null
                    ])
                );
                
            }
            
        }
        
        return $details;
    }
    
    /**
     * Answers the list item details config for the receiver.
     *
     * @return array
     */
    public function getListItemDetailsConfig()
    {
        $config = [];
        
        if (is_array($this->owner->config()->default_list_item_details)) {
            $config = $this->owner->config()->default_list_item_details;
        }
        
        if (is_array($this->owner->config()->list_item_details)) {
            
            foreach ($this->owner->config()->list_item_details as $name => $spec) {
                
                if (!$spec) {
                    unset($config[$name]);
                }
                
            }
            
            $config = array_merge_recursive($config, $this->owner->config()->list_item_details);
            
        }
        
        return $config;
    }
    
    /**
     * Answers an array list object containing the item buttons for the template.
     *
     * @return ArrayList
     */
    public function getListItemButtons()
    {
        $buttons = ArrayList::create();
        
        foreach ($this->owner->getListItemButtonsConfig() as $name => $spec) {
            
            if ($spec) {
                
                foreach ($spec as $item => $value) {
                    
                    $args = [];
                    
                    if (is_array($value)) {
                        $args  = $value;
                        $value = array_shift($args);
                    }
                    
                    $spec[$item] = $this->processListItemValue($value, $args);
                    
                }
                
                if (isset($spec['show']) && !$this->owner->{$spec['show']}) {
                    continue;
                }
                
                $href = isset($spec['href']) ? $spec['href'] : null;
                
                if ($href) {
                    
                    $buttons->push(
                        ArrayData::create([
                            'Icon' => isset($spec['icon']) ? $spec['icon'] : null,
                            'Type' => isset($spec['type']) ? $spec['type'] : null,
                            'HREF' => isset($spec['href']) ? $spec['href'] : null,
                            'Text' => isset($spec['text']) ? $spec['text'] : null,
                            'ExtraClass' => isset($spec['extraClass']) ? $spec['extraClass'] : null
                        ])
                    );
                    
                }
                
            }
            
        }
        
        return $buttons;
    }
    
    /**
     * Answers the list item buttons config for the receiver.
     *
     * @return array
     */
    public function getListItemButtonsConfig()
    {
        $config = [];
        
        if (is_array($this->owner->config()->default_list_item_buttons)) {
            $config = $this->owner->config()->default_list_item_buttons;
        }
        
        if (is_array($this->owner->config()->list_item_buttons)) {
            
            foreach ($this->owner->config()->list_item_buttons as $name => $spec) {
                
                if (!$spec) {
                    unset($config[$name]);
                }
                
            }
            
            $config = array_merge_recursive($config, $this->owner->config()->list_item_buttons);
            
        }
            
        return $config;
    }
    
    /**
     * Processes the given value which references methods / fields of the receiver and renderer.
     *
     * @param string $value
     * @param array $args
     *
     * @return string
     */
    protected function processListItemValue($value, $args = [])
    {
        return ViewTools::singleton()->processAttribute($value, $this->owner, $this->owner->getRenderer(), $args);
    }
}
