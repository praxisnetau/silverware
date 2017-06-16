<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\View
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\View;

use SilverWare\Grid\Grid;
use SilverWare\Tools\ViewTools;

/**
 * Allows an object to become aware of the grid framework defined by configuration.
 *
 * @package SilverWare\View
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
trait GridAware
{
    /**
     * Answers the grid framework defined by configuration.
     *
     * @return SilverWare\Grid\Framework
     */
    public function grid()
    {
        return Grid::framework();
    }
    
    /**
     * Answers the style mapped to the given name from the grid framework.
     *
     * @param string $name Name of style.
     * @param string $subname Subname of style (optional).
     *
     * @return string
     */
    public function style($name, $subname = null)
    {
        return $this->grid()->getStyle($name, $subname);
    }
    
    /**
     * Answers the styles mapped to the given names from the grid framework.
     *
     * @return array|string
     */
    public function styles()
    {
        // Obtain Arguments:
        
        $args = func_get_args();
        
        // Array Argument Passed?
        
        if (is_array($args[0])) {
            
            // Obtain Styles:
            
            $styles = $this->grid()->getStyles($args[0]);
            
            // Answer as Array or String? (2nd param is true)
            
            return (isset($args[1]) && $args[1]) ? ViewTools::singleton()->array2att($styles) : $styles;
            
        }
        
        // Answer Styles Array:
        
        return $this->grid()->getStyles($args);
    }
}
