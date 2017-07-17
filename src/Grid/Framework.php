<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Grid
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\Grid;

use SilverStripe\ORM\ArrayLib;
use SilverStripe\View\ViewableData;

/**
 * An extension of the viewable data class for the parent class of grid framework implementations.
 *
 * @package SilverWare\Grid
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
abstract class Framework extends ViewableData
{
    /**
     * Number of columns used by the framework.
     *
     * @var integer
     * @config
     */
    private static $columns = 12;
    
    /**
     * Maps viewport sizes to breakpoints for this framework.
     *
     * @var array
     * @config
     */
    private static $breakpoints = [];
    
    /**
     * Maps style names to the appropriate style classes provided by this framework.
     *
     * @var array
     * @config
     */
    private static $style_mappings = [];
    
    /**
     * Maps grid classes to the extensions applied by this framework.
     *
     * @var array
     * @config
     */
    private static $class_extensions = [];
    
    /**
     * Answers the text alignment class for the specified viewport and value.
     *
     * @param string $viewport
     * @param string $value
     *
     * @return string
     */
    abstract public function getTextAlignmentClass($viewport, $value);
    
    /**
     * Answers the image alignment class for the specified viewport and value.
     *
     * @param string $viewport
     * @param string $value
     *
     * @return string
     */
    abstract public function getImageAlignmentClass($viewport, $value);
    
    /**
     * Answers the style mapped to the given name.
     *
     * @param string $name Name of style.
     * @param string $subname Subname of style (optional).
     *
     * @return string
     */
    public function getStyle($name, $subname = null)
    {
        $name    = strtolower($name);
        $subname = strtolower($subname);
        
        if (strpos($name, '.') !== false && !$subname) {
            list($name, $subname) = explode('.', $name);
        }
        
        if (($mappings = $this->getStyleMappings()) && isset($mappings[$name])) {
            
            if (!$subname) {
                $subname = $name;
            }
            
            if (is_array($mappings[$name])) {
                return isset($mappings[$name][$subname]) ? $mappings[$name][$subname] : null;
            } else {
                return $mappings[$name];
            }
            
        }
        
        return $name;
    }
    
    /**
     * Answers the styles mapped to the given names.
     *
     * @param array $names Names of styles.
     *
     * @return array
     */
    public function getStyles($names = [])
    {
        $styles = [];
        
        foreach ($names as $name) {
            $styles[] = $this->getStyle($name);
        }
        
        return array_filter($styles);
    }
    
    /**
     * Answers the breakpoint for the specified viewport.
     *
     * @param string $viewport
     *
     * @return string
     */
    public function getBreakpoint($viewport)
    {
        $viewport = strtolower($viewport);
        
        if (($breakpoints = $this->getBreakpoints()) && isset($breakpoints[$viewport])) {
            return $breakpoints[$viewport];
        }
    }
    
    /**
     * Answers an array of breakpoints defined for the framework.
     *
     * @return array
     */
    public function getBreakpoints()
    {
        return $this->config()->breakpoints;
    }
    
    /**
     * Answers an array of style mappings defined for the framework.
     *
     * @return array
     */
    public function getStyleMappings()
    {
        return $this->config()->style_mappings;
    }
    
    /**
     * Answers the number of columns used by the framework.
     *
     * @return integer
     */
    public function getNumberOfColumns()
    {
        return $this->config()->columns;
    }
    
    /**
     * Answers an array of numeric options for column size fields.
     *
     * @param integer $max Maximum number of columns (optional, defaults to number of columns from config).
     *
     * @return array
     */
    public function getColumnSizeOptions($max = null)
    {
        return ArrayLib::valuekey(range(1, $max ? $max : $this->getNumberOfColumns()));
    }
    
    /**
     * Answers an array of numeric options for column span fields.
     *
     * @return array
     */
    public function getColumnSpanOptions()
    {
        return $this->getColumnSizeOptions($this->getNumberOfColumns());
    }
    
    /**
     * Answers an array of numeric options for column offset fields.
     *
     * @return array
     */
    public function getColumnOffsetOptions()
    {
        return $this->getColumnSizeOptions($this->getNumberOfColumns() - 1);
    }
    
    /**
     * Initialises the framework (with extension hooks).
     *
     * @return void
     */
    public function doInit()
    {
        // Trigger Before Init Hook:
        
        $this->extend('onBeforeInit');
        
        // Perform Initialisation:
        
        $this->init();
        
        // Trigger After Init Hook:
        
        $this->extend('onAfterInit');
    }
    
    /**
     * Initialises the framework.
     *
     * @return void
     */
    protected function init()
    {
        $this->addExtensions();
    }
    
    /**
     * Adds extensions for this framework to the appropriate data objects.
     *
     * @return void
     */
    protected function addExtensions()
    {
        foreach ($this->config()->class_extensions as $class => $extension) {
            self::add_extension($class, $extension);
        }
    }
}
