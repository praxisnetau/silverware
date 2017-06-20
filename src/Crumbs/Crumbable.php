<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Crumbs
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\Crumbs;

/**
 * Interface for classes which can be rendered as breadcrumbs within the template.
 *
 * @package SilverWare\Crumbs
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
interface Crumbable
{
    /**
     * Answers the menu title for the receiver.
     *
     * @return string
     */
    public function getMenuTitle();
    
    /**
     * Answers the link for the receiver.
     *
     * @param string $action
     *
     * @return string
     */
    public function getLink($action = null);
}
