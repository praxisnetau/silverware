<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Grid\Frameworks\Bootstrap
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\Grid\Frameworks\Bootstrap;

use SilverWare\Grid\Extensions\ColumnExtension;

/**
 * A column extension for Bootstrap columns.
 *
 * @package SilverWare\Grid\Frameworks\Bootstrap
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class Column extends ColumnExtension
{
    /**
     * Defines the column span prefix.
     *
     * @var string
     */
    protected $spanPrefix = 'col';
    
    /**
     * Defines the column offset prefix.
     *
     * @var string
     */
    protected $offsetPrefix = 'offset';
    
    /**
     * Defines the default column span values.
     *
     * @var array
     */
    protected $columnSpanDefaults = [
        'Medium' => 'fill'
    ];
    
    /**
     * Defines additional column span options.
     *
     * @var array
     */
    protected $columnSpanOptions = [
        'auto' => 'Auto',
        'fill' => 'Fill'
    ];
    
    /**
     * Defines additional column offset options.
     *
     * @var array
     */
    protected $columnOffsetOptions = [
        'reset' => 'Reset'
    ];
    
    /**
     * Answers the span class names for the extended object.
     *
     * @return array
     */
    public function getSpanClassNames()
    {
        $classes = [];
        
        $span = $this->owner->dbObject('Span');
        
        if ($span->allEqualTo('auto')) {
            
            // Add Span Prefix Only:
            
            $classes[] = $this->spanPrefix;
            
        } elseif ($span->allEmpty()) {
            
            // Add Column Span Defaults:
            
            foreach ($this->columnSpanDefaults as $viewport => $value) {
                $classes[] = $this->getSpanClass($viewport, $value);
            }
            
        } else {
            
            // Add Column Span Field Values:
            
            foreach ($span->getViewports() as $viewport) {
                $classes[] = $this->getSpanClass($viewport, $span->getField($viewport));
            }
            
        }
        
        return $classes;
    }
    
    /**
     * Answers the offset class names for the extended object.
     *
     * @return array
     */
    public function getOffsetClassNames()
    {
        $classes = [];
        
        $offset = $this->owner->dbObject('Offset');
        
        foreach ($offset->getViewports() as $viewport) {
            $classes[] = $this->getOffsetClass($viewport, $offset->getField($viewport));
        }
        
        return $classes;
    }
    
    /**
     * Updates the options for the column span field.
     *
     * @param array $options
     *
     * @return void
     */
    public function updateColumnSpanOptions(&$options)
    {
        $options = $this->columnSpanOptions + $options;
    }
    
    /**
     * Updates the options for the column offset field.
     *
     * @param array $options
     *
     * @return void
     */
    public function updateColumnOffsetOptions(&$options)
    {
        $options = $this->columnOffsetOptions + $options;
    }
    
    /**
     * Answers the span class for the given viewport and value.
     *
     * @param string $viewport
     * @param string $value
     *
     * @return string
     */
    protected function getSpanClass($viewport, $value)
    {
        if ($value) {
            
            $class = [$this->spanPrefix];
            
            $class[] = $this->grid()->getStyle('viewport', $viewport);
            
            if ($value != 'fill') {
                $class[] = $value;
            }
            
            return implode('-', array_filter($class));
            
        }
    }
    
    /**
     * Answers the offset class for the specified viewport and value.
     *
     * @param string $viewport
     * @param string $value
     *
     * @return string
     */
    protected function getOffsetClass($viewport, $value)
    {
        if ($value) {
            
            $class = [$this->offsetPrefix];
            
            $class[] = $this->grid()->getStyle('viewport', $viewport);
            
            if ($value == 'reset') {
                $class[] = 0;
            } else {
                $class[] = $value;
            }
            
            return implode('-', array_filter($class));
            
        }
    }
}
