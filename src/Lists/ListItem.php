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
use SilverWare\Components\BaseListComponent;
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
     * @var BaseListComponent
     */
    protected $listComponent;
    
    /**
     * Defines the value of the listComponent attribute.
     *
     * @param BaseListComponent $listComponent
     *
     * @return $this
     */
    public function setListComponent(BaseListComponent $listComponent)
    {
        $this->listComponent = $listComponent;
        
        return $this;
    }
    
    /**
     * Answers the value of the listComponent attribute.
     *
     * @return BaseListComponent
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
     * Processes the given string of text which references methods / fields of the receiver and List Component.
     *
     * @param string $text
     * @param array $args
     *
     * @return string
     */
    public function processListItemText($text, $args = [])
    {
        return ViewTools::singleton()->processAttribute($text, $this, $this->getListComponent(), $args);
    }
}
