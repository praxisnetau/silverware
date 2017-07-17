<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Lists
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\Lists;

use SilverStripe\ORM\ArrayList;
use SilverStripe\View\ArrayData;
use SilverStripe\View\SSViewer;
use SilverWare\Model\Component;
use SilverWare\Tools\ViewTools;
use SilverWare\View\ViewClasses;

/**
 * Allows an object to become renderable within a list component.
 *
 * @package SilverWare\Lists
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
trait ListItem
{
    use ViewClasses;
    
    /**
     * The component instance responsible for rendering the list item.
     *
     * @var Component
     */
    protected $renderer;
    
    /**
     * Defines the value of the renderer attribute.
     *
     * @param Component $renderer
     *
     * @return $this
     */
    public function setRenderer(Component $renderer)
    {
        $this->renderer = $renderer;
        
        return $this;
    }
    
    /**
     * Answers the value of the renderer attribute.
     *
     * @return Component
     */
    public function getRenderer()
    {
        return $this->renderer;
    }
    
    /**
     * Answers an array of list item class names for the HTML template.
     *
     * @return array
     */
    public function getListItemClassNames()
    {
        $classes = ['item'];
        
        $classes = array_merge($classes, ViewTools::singleton()->getAncestorClassNames($this, self::class));
        
        if ($this->hasMethod('getMetaClassNames')) {
            $classes = array_merge($classes, $this->getMetaClassNames());
        }
        
        return $classes;
    }
    
    /**
     * Answers the name of the list item template.
     *
     * @return string
     */
    public function getListItemTemplate()
    {
        $template = sprintf('%s\ListItem', static::class);
        
        if ($this->getRenderer()->hasMethod('getListItemTemplate')) {
            $template = $this->getRenderer()->getListItemTemplate(static::class);
        }
        
        if (SSViewer::hasTemplate($template)) {
            return $template;
        }
        
        return __TRAIT__;
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
    public function renderListItem($isFirst = false, $isMiddle = false, $isLast = false)
    {
        return $this->customise([
            'isFirst' => $isFirst,
            'isMiddle' => $isMiddle,
            'isLast' => $isLast
        ])->renderWith($this->getListItemTemplate());
    }
    
    /**
     * Answers an array list object containing the item details for the template.
     *
     * @return ArrayList
     */
    public function getListItemDetails()
    {
        $details = ArrayList::create();
        
        foreach ($this->getListItemDetailsConfig() as $name => $spec) {
            
            if ($spec) {
                
                foreach ($spec as $item => $value) {
                    
                    $args = [];
                    
                    if (is_array($value)) {
                        $args  = $value;
                        $value = array_shift($args);
                    }
                    
                    $spec[$item] = $this->processListItemValue($value, $args);
                    
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
        
        if (is_array($this->config()->default_list_item_details)) {
            $config = $this->config()->default_list_item_details;
        }
        
        if (is_array($this->config()->list_item_details)) {
            
            foreach ($this->config()->list_item_details as $name => $spec) {
                
                if (!$spec) {
                    unset($config[$name]);
                }
                
            }
            
            $config = array_merge_recursive($config, $this->config()->list_item_details);
            
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
        
        foreach ($this->getListItemButtonsConfig() as $name => $spec) {
            
            if ($spec) {
                
                foreach ($spec as $item => $value) {
                    
                    $args = [];
                    
                    if (is_array($value)) {
                        $args  = $value;
                        $value = array_shift($args);
                    }
                    
                    $spec[$item] = $this->processListItemValue($value, $args);
                    
                }
                
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
        
        if (is_array($this->config()->default_list_item_buttons)) {
            $config = $this->config()->default_list_item_buttons;
        }
        
        if (is_array($this->config()->list_item_buttons)) {
            
            foreach ($this->config()->list_item_buttons as $name => $spec) {
                
                if (!$spec) {
                    unset($config[$name]);
                }
                
            }
            
            $config = array_merge_recursive($config, $this->config()->list_item_buttons);
            
        }
            
        return $config;
    }
    
    /**
     * Processes the given value which references methods / fields of the receiver and List Component.
     *
     * @param string $value
     * @param array $args
     *
     * @return string
     */
    public function processListItemValue($value, $args = [])
    {
        return ViewTools::singleton()->processAttribute($value, $this, $this->getRenderer(), $args);
    }
}
