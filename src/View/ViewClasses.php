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

use SilverWare\Tools\ViewTools;

/**
 * Allows an object to make use of classes for the HTML template.
 *
 * @package SilverWare\View
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
trait ViewClasses
{
    /**
     * Magic method to handle access to named properties.
     *
     * @param string $name Property name.
     *
     * @return mixed
     */
    public function __get($name)
    {
        // Match HTML Class Name Methods (converts array to string attribute):
        
        if (substr_compare($name, 'Class', -5) === 0 && $this->hasMethod("get{$name}Names")) {
            return ViewTools::singleton()->array2att($this->{"get{$name}Names"}());
        }
        
        // Answer Parent Result:
        
        return parent::__get($name);
    }
    
    /**
     * Magic method to handle access to named methods.
     *
     * @param string $name Method name.
     * @param array $args Method arguments.
     *
     * @return mixed
     */
    public function __call($name, $args)
    {
        // Match HTML Class Name Methods (converts array to string attribute):
        
        if (substr_compare($name, 'Class', -5) === 0 && $this->hasMethod("{$name}Names")) {
            return ViewTools::singleton()->array2att($this->{"{$name}Names"}());
        }
        
        // Answer Parent Result:
        
        return parent::__call($name, $args);
    }
}
