<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Extensions\Model
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\Extensions\Model;

use SilverStripe\Core\Extension;
use SilverStripe\ORM\ArrayList;
use SilverStripe\View\ArrayData;
use SilverWare\Tools\ViewTools;

/**
 * An extension class which adds detail fields functionality to the extended object.
 *
 * @package SilverWare\Extensions\Model
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class DetailFieldsExtension extends Extension
{
    /**
     * Defines the default detail fields heading level to use.
     *
     * @var string
     * @config
     */
    private static $detail_fields_heading_level_default = 'h3';
    
    /**
     * Answers an array list object containing the detail fields for the template.
     *
     * @return ArrayList
     */
    public function getDetailFields()
    {
        // Create Details List:
        
        $details = ArrayList::create();
        
        // Define Details List:
        
        foreach ($this->owner->getDetailFieldsConfig() as $name => $spec) {
            
            if ($spec) {
                
                foreach ($spec as $item => $value) {
                    
                    $args = [];
                    
                    if (is_array($value)) {
                        $args  = $value;
                        $value = array_shift($args);
                    }
                    
                    $spec[$item] = $this->owner->processDetailFieldValue($value, $args);
                    
                }
                
                $text = isset($spec['text']) ? $this->owner->processDetailFieldValue($spec['text']) : null;
                
                if ($text) {
                    
                    if (isset($spec['show']) && !$this->owner->{$spec['show']}) {
                        continue;
                    }
                    
                    $details->push(
                        ArrayData::create([
                            'Name' => isset($spec['name']) ? $spec['name'] : $name,
                            'Icon' => isset($spec['icon']) ? $spec['icon'] : null,
                            'Text' => $text
                        ])
                    );
                    
                }
                
            }
            
        }
        
        // Answer Details List:
        
        return $details;
    }
    
    /**
     * Answers true if the extended object has detail fields available.
     *
     * @return boolean
     */
    public function hasDetailFields()
    {
        return $this->owner->getDetailFields()->exists();
    }
    
    /**
     * Answers the detail fields config for the extended object.
     *
     * @return array
     */
    public function getDetailFieldsConfig()
    {
        $config = $this->owner->getDefaultDetailFieldsConfig();
        
        if (is_array($this->owner->config()->detail_fields)) {
            
            foreach ($this->owner->config()->detail_fields as $name => $spec) {
                
                if (!$spec) {
                    unset($config[$name]);
                }
                
            }
            
            $config = array_merge_recursive($config, $this->owner->config()->detail_fields);
            
        }
        
        return $config;
    }
    
    /**
     * Answers the default detail fields config for the extended object.
     *
     * @return array
     */
    public function getDefaultDetailFieldsConfig()
    {
        if (is_array($this->owner->config()->default_detail_fields)) {
            return $this->owner->config()->default_detail_fields;
        }
        
        return [];
    }
    
    /**
     * Answers the heading tag for the detail fields section.
     *
     * @return string
     */
    public function getDetailFieldsHeadingTag()
    {
        $tag = $this->owner->config()->detail_fields_heading_level;
        
        return $tag ? $tag : $this->owner->config()->detail_fields_heading_level_default;
    }
    
    /**
     * Answers the text for the detail fields heading.
     *
     * @return string
     */
    public function getDetailFieldsHeadingText()
    {
        return _t(__CLASS__ . '.DEFAULTHEADING', 'Details');
    }
    
    /**
     * Answers a string of class names for the detail fields wrapper.
     *
     * @return string
     */
    public function getDetailFieldsClass()
    {
        return ViewTools::singleton()->array2att($this->owner->getDetailFieldsClassNames());
    }
    
    /**
     * Answers an array of class names for the detail fields wrapper.
     *
     * @return array
     */
    public function getDetailFieldsClassNames()
    {
        $classes = ['detail-fields'];
        
        $this->owner->extend('updateDetailFieldsClassNames', $classes);
        
        return $classes;
    }
    
    /**
     * Processes the given detail field value which references methods and/or fields of the extended object.
     *
     * @param string $value
     * @param array $args
     *
     * @return string
     */
    public function processDetailFieldValue($value, $args = [])
    {
        $parent = $this->owner->hasMethod('getParent') ? $this->owner->getParent() : null;
        
        return ViewTools::singleton()->processAttribute($value, $this->owner, $parent, $args);
    }
}
