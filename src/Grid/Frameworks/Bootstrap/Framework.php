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

use SilverWare\Grid\Column as GridColumn;
use SilverWare\Grid\Framework as GridFramework;
use SilverWare\Grid\Row as GridRow;
use SilverWare\Grid\Section as GridSection;

/**
 * An extension of the framework class for the Bootstrap framework.
 *
 * @package SilverWare\Grid\Frameworks\Bootstrap
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class Framework extends GridFramework
{
    /**
     * Maps grid classes to the extensions applied by this framework.
     *
     * @var array
     * @config
     */
    private static $class_extensions = [
        GridRow::class => Row::class,
        GridColumn::class => Column::class,
        GridSection::class => Section::class
    ];
    
    /**
     * Determines whether the framework uses column offset values.
     *
     * @var boolean
     * @config
     */
    private static $use_column_offset = false;
    
    /**
     * Answers the text alignment class for the specified viewport and value.
     *
     * @param string $viewport
     * @param string $value
     *
     * @return string
     */
    public function getTextAlignmentClass($viewport, $value)
    {
        if ($value) {
            
            $class = [$this->getStyle('text')];
            
            $class[] = $this->getStyle('viewport', $viewport);
            
            $class[] = $this->getStyle('align', $value);
            
            return implode('-', array_filter($class));
            
        }
    }
    
    /**
     * Answers the image alignment class for the specified viewport and value.
     *
     * @param string $viewport
     * @param string $value
     *
     * @return string
     */
    public function getImageAlignmentClass($viewport, $value)
    {
        if ($value) {
            
            $class = [$this->getStyle('image')];
            
            $class[] = $this->getStyle('viewport', $viewport);
            
            $class[] = $this->getStyle('align', $value);
            
            return implode('-', array_filter($class));
            
        }
    }
}
