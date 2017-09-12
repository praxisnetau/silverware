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

use SilverWare\Grid\Extensions\RowExtension;

/**
 * A row extension for Bootstrap rows.
 *
 * @package SilverWare\Grid\Frameworks\Bootstrap
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class Row extends RowExtension
{
    /**
     * Updates the given array of class names from the extended object.
     *
     * @param array $classes
     *
     * @return void
     */
    public function updateClassNames(&$classes)
    {
        if ($this->owner->isNoGutters()) {
            $classes[] = $this->style('row.no-gutters');
        }
    }
}
