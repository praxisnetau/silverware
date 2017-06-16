<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Lists
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\Lists;

use SilverStripe\ORM\SS_List;
use SilverStripe\View\ViewableData;

/**
 * A wrapper class to allow regular list objects to operate as list sources.
 *
 * @package SilverWare\Lists
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class ListWrapper extends ViewableData implements ListSource
{
    /**
     * List object being wrapped.
     *
     * @var SS_List
     */
    protected $list;
    
    /**
     * Constructs the object upon instantiation.
     *
     * @param SS_List $list
     */
    public function __construct(SS_List $list)
    {
        $this->list = $list;
        
        parent::__construct();
    }
    
    /**
     * Answers a list of items.
     *
     * @return SS_List
     */
    public function getListItems()
    {
        return $this->list;
    }
}
