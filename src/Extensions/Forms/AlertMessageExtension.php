<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Extensions\Forms
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\Extensions\Forms;

use SilverStripe\Core\Extension;
use SilverStripe\ORM\ValidationResult;
use SilverWare\Tools\ViewTools;
use SilverWare\View\GridAware;

/**
 * An extension which translates form message types into alert classes.
 *
 * @package SilverWare\Extensions\Forms
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class AlertMessageExtension extends Extension
{
    use GridAware;
    
    /**
     * Answers a string of alert classes for the message type of the extended object.
     *
     * @return string
     */
    public function getAlertMessageType()
    {
        // Obtain Base Alert Class:
        
        $classes = $this->styles('alert');
        
        // Determine Message Type:
        
        switch ($this->owner->MessageType) {
            case ValidationResult::TYPE_GOOD:
                $classes[] = $this->style('alert.success');
                break;
            case ValidationResult::TYPE_INFO:
                $classes[] = $this->style('alert.info');
                break;
            case ValidationResult::TYPE_ERROR:
                $classes[] = $this->style('alert.danger');
                break;
            default:
                $classes[] = $this->style('alert.warning');
        }
        
        // Answer Classes:
        
        return ViewTools::singleton()->array2att($classes);
    }
}
