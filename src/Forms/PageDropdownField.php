<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Forms
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\Forms;

use SilverStripe\Forms\TreeDropdownField;
use Page;

/**
 * An extension of the tree dropdown field which shows pages only.
 *
 * @package SilverWare\Forms
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class PageDropdownField extends TreeDropdownField
{
    /**
     * Constructs the object upon instantiation.
     *
     * @param string $name
     * @param string $title
     */
    public function __construct($name, $title = null)
    {
        // Construct Parent:
        
        parent::__construct($name, $title, Page::class);
        
        // Define Filter Function:
        
        $this->setFilterFunction(function ($node) {
            return ($node instanceof Page);
        });
    }
}
