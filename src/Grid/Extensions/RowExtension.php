<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Grid\Extensions
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\Grid\Extensions;

/**
 * A grid extension class which forms an abstract base for row extensions.
 *
 * @package SilverWare\Grid\Extensions
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
abstract class RowExtension extends GridExtension
{
    /**
     * Answers the container class names for the extended object.
     *
     * @return array
     */
    abstract public function getClassNamesForContainer();
    
    /**
     * Updates the container class names of the extended object.
     *
     * @param array $classes Array of container class names from the extended object.
     *
     * @return void
     */
    public function updateContainerClassNames(&$classes)
    {
        $classes += $this->owner->getClassNamesForContainer();
    }
}
