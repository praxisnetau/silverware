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

use SilverStripe\Core\Config\Config;
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
    private static $default_detail_fields_heading_level = 'h3';
    
    /**
     * Defines the default detail field heading level to use.
     *
     * @var string
     * @config
     */
    private static $default_detail_field_heading_level = 'h4';
    
    /**
     * Defines the default setting for showing the detail fields inline.
     *
     * @var boolean
     * @config
     */
    private static $default_detail_fields_inline = false;
    
    /**
     * Defines the default setting for hiding the detail fields header.
     *
     * @var boolean
     * @config
     */
    private static $default_detail_fields_hide_header = false;
    
    /**
     * Defines the default setting for hiding the detail field icons.
     *
     * @var boolean
     * @config
     */
    private static $default_detail_fields_hide_icons = false;
    
    /**
     * Defines the default setting for hiding the detail field names.
     *
     * @var boolean
     * @config
     */
    private static $default_detail_fields_hide_names = false;
    
    /**
     * Defines the default setting for using a heading tag for each detail field.
     *
     * @var boolean
     * @config
     */
    private static $default_detail_fields_use_heading = false;
    
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
                
                if (!is_array($spec)) {
                    $spec = ['text' => $spec];
                }
                
                foreach ($spec as $item => $value) {
                    
                    $args = [];
                    
                    if (is_array($value)) {
                        $args  = $value;
                        $value = array_shift($args);
                    }
                    
                    $spec[$item] = $this->owner->processDetailFieldValue($value, $args);
                    
                }
                
                $text = isset($spec['text']) ? $spec['text'] : null;
                
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
        
        return $tag ? $tag : Config::inst()->get(static::class, 'default_detail_fields_heading_level');
    }
    
    /**
     * Answers the heading tag for an individual detail field.
     *
     * @return string
     */
    public function getDetailFieldHeadingTag()
    {
        $tag = $this->owner->config()->detail_field_heading_level;
        
        return $tag ? $tag : Config::inst()->get(static::class, 'default_detail_field_heading_level');
    }
    
    /**
     * Answers true if heading tags are to be used for each detail field.
     *
     * @return boolean
     */
    public function getDetailFieldsUseHeading()
    {
        if ($this->owner->config()->detail_fields_use_heading) {
            return true;
        }
        
        return Config::inst()->get(static::class, 'default_detail_fields_use_heading');
    }
    
    /**
     * Answers true if the detail fields are to be shown inline.
     *
     * @return boolean
     */
    public function getDetailFieldsInline()
    {
        if ($this->owner->config()->detail_fields_inline) {
            return true;
        }
        
        return Config::inst()->get(static::class, 'default_detail_fields_inline');
    }
    
    /**
     * Answers true if the detail fields header is to be hidden.
     *
     * @return boolean
     */
    public function getDetailFieldsHideHeader()
    {
        if ($this->owner->config()->detail_fields_hide_header) {
            return true;
        }
        
        return Config::inst()->get(static::class, 'default_detail_fields_hide_header');
    }
    
    /**
     * Answers true if the detail fields icons are to be hidden.
     *
     * @return boolean
     */
    public function getDetailFieldsHideIcons()
    {
        if ($this->owner->config()->detail_fields_hide_icons) {
            return true;
        }
        
        return Config::inst()->get(static::class, 'default_detail_fields_hide_icons');
    }
    
    /**
     * Answers true if the detail fields names are to be hidden.
     *
     * @return boolean
     */
    public function getDetailFieldsHideNames()
    {
        if ($this->owner->config()->detail_fields_hide_names) {
            return true;
        }
        
        return Config::inst()->get(static::class, 'default_detail_fields_hide_names');
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
        
        $classes[] = $this->owner->DetailFieldsInline ? 'inline' : 'block';
        
        if ($this->owner->DetailFieldsHideIcons) {
            $classes[] = 'hide-icons';
        }
        
        if ($this->owner->DetailFieldsHideNames) {
            $classes[] = 'hide-names';
        }
        
        if ($this->owner->DetailFieldsHideHeader) {
            $classes[] = 'hide-header';
        }
        
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
