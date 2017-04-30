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

/**
 * Allows an object to filter the items within a list component.
 *
 * @package SilverWare\Lists
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
trait ListFilter
{
    /**
     * Defines the filters to be applied to the list source.
     *
     * @var array
     */
    protected $listFilters = [];
    
    /**
     * Defines the value of the listFilters attribute.
     *
     * @param array $listFilters
     *
     * @return $this
     */
    public function setListFilters($listFilters)
    {
        $this->listFilters = (array) $listFilters;
        
        return $this;
    }
    
    /**
     * Answers the value of the listFilters attribute.
     *
     * @return array
     */
    public function getListFilters()
    {
        return $this->listFilters;
    }
    
    /**
     * Adds the given filter array to the array of filters.
     *
     * @param array $filter
     *
     * @return $this
     */
    public function addListFilter($filter = [])
    {
        $this->listFilters[] = $filter;
        
        return $this;
    }
}
