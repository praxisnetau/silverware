<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Extensions\Model
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\Extensions\Model;

use SilverStripe\Core\Extension;

/**
 * An extension which works around a URL ampersand-encoding bug with paginated lists.
 *
 * @package SilverWare\Extensions\Model
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class PaginatedListExtension extends Extension
{
    /**
     * Answers a summarised pagination for the extended object (fixes a bug with URL ampersands).
     *
     * @param integer $context
     *
     * @return ArrayList
     */
    public function PageSummary($context = 4)
    {
        $summary = $this->owner->PaginationSummary($context);
        
        foreach ($summary as $page) {
            
            if ($page->Link) {
                $page->Link = str_replace('&amp;', '&', $page->Link);
            }
            
        }
        
        return $summary;
    }
}
