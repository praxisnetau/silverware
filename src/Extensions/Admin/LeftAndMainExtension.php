<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Extensions\Admin
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\Extensions\Admin;

use SilverStripe\Admin\LeftAndMainExtension as BaseExtension;
use SilverWare\Grid\Grid;

/**
 * A left and main extension to add SilverWare functionality to the admin.
 *
 * @package SilverWare\Extensions\Admin
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class LeftAndMainExtension extends BaseExtension
{
    /**
     * Event handler method triggered when the CMS initialises.
     *
     * @return void
     */
    public function init()
    {
        // Initialise Grid Framework:
        
        Grid::framework()->doInit();
    }
}
