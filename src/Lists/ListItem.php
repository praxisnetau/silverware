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
use SilverWare\Components\ListComponent;
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
     * The list component instance responsible for rendering the list item.
     *
     * @var ListComponent
     */
    protected $listComponent;
    
    /**
     * Defines the value of the listComponent attribute.
     *
     * @param ListComponent $listComponent
     *
     * @return $this
     */
    public function setListComponent(ListComponent $listComponent)
    {
        $this->listComponent = $listComponent;
        
        return $this;
    }
    
    /**
     * Answers the value of the listComponent attribute.
     *
     * @return ListComponent
     */
    public function getListComponent()
    {
        return $this->listComponent;
    }
    
    /**
     * Answers an array of list item class names for the HTML template.
     *
     * @return array
     */
    public function getListItemClassNames()
    {
        $classes = ['item'];
        
        $classes[] = $this->getAncestorClass();
        
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
        $template = sprintf('%s\ListItem', $this->class);
        
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
        
        if (is_array($this->config()->list_item_details)) {
            
            foreach ($this->config()->list_item_details as $name => $spec) {
                
                $icon = isset($spec['icon']) ? $spec['icon'] : null;
                $text = isset($spec['text']) ? $spec['text'] : null;
                
                $text = $this->processListItemText($text, isset($spec['args']) ? $spec['args'] : []);
                
                $details->push(
                    ArrayData::create([
                        'Name' => $name,
                        'Icon' => $icon,
                        'Text' => $text
                    ])
                );
                
            }
            
        }
        
        return $details;
    }
    
    /**
     * Processes the given string of text which references methods / fields of the receiver and list component.
     *
     * @param string $text
     * @param array $args
     *
     * @return string
     */
    public function processListItemText($text, $args = [])
    {
        // Does the text refer to a field or method?
        
        if (strpos($text, '$') === 0) {
            
            // Obtain Field Name:
            
            $field = ltrim($text, '$');
            
            // Obtain Field Value:
            
            if ($this->hasMethod("get{$field}")) {
                
                // First, answer the result of a method call on the receiver:
                
                $text = call_user_func_array([$this, "get{$field}"], $this->processListItemArgs($args));
                
            } elseif ($this->hasField($field)) {
                
                // Next, answer a field value from the receiver:
                
                return $this->$field;
                
            } elseif ($this->getListComponent()->hasField($field)) {
                
                // Finally, answer a field value from the associated List Component:
                
                return $this->getListComponent()->$field;
                
            }
            
        }
        
        // Answer Text Value:
        
        return $text;
    }
    
    /**
     * Processes the given array of arguments for a list item detail.
     *
     * @param string|array $stringOrArray
     *
     * @return array
     */
    public function processListItemArgs($stringOrArray)
    {
        $args = (array) $stringOrArray;
        
        foreach ($args as $key => $arg) {
            $args[$key] = $this->processListItemText($arg);
        }
        
        return $args;
    }
}
