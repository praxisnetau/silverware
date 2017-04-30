<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Forms\GridField
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\Forms\GridField;

use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\GridFieldExtensions\GridFieldOrderableRows;

/**
 * An extension of the grid field config record editor class for an orderable editor config.
 *
 * @package SilverWare\Forms\GridField
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class GridFieldConfig_OrderableEditor extends GridFieldConfig_RecordEditor
{
    /**
     * Constructs the object upon instantiation.
     *
     * @param integer $itemsPerPage
     */
    public function __construct($itemsPerPage = null)
    {
        // Construct Parent:
        
        parent::__construct($itemsPerPage);
        
        // Construct Object:
        
        $this->addComponent(GridFieldOrderableRows::create());
        
        // Apply Extensions:
        
        $this->extend('updateConfig');
    }
}
