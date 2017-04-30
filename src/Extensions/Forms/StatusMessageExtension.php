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
use SilverStripe\Forms\LiteralField;

/**
 * An extension which allows status messages to be added to field lists.
 *
 * @package SilverWare\Extensions\Forms
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class StatusMessageExtension extends Extension
{
    /**
     * Adds a status message as a literal field to the extended object.
     *
     * @param string|array $textOrArray
     * @param string $type
     * @param string $icon
     * @param string $insertBefore
     *
     * @return void
     */
    public function addStatusMessage($textOrArray, $type = 'warning', $icon = 'warning', $insertBefore = 'Root')
    {
        if (!is_null($textOrArray)) {
            
            // Obtain Arguments:
            
            if (is_array($textOrArray)) {
                $text = isset($textOrArray['text']) ? $textOrArray['text'] : null;
                $type = isset($textOrArray['type']) ? $textOrArray['type'] : null;
                $icon = isset($textOrArray['icon']) ? $textOrArray['icon'] : null;
            } else {
                $text = $textOrArray;
            }
            
            // Add Literal Field:
            
            $this->owner->insertBefore(
                $insertBefore,
                LiteralField::create(
                    'StatusMessageLiteral',
                    sprintf(
                        '<p class="message status %s"><i class="fa fa-fw fa-%s"></i> %s</p>',
                        $type,
                        $icon,
                        $text
                    )
                )
            );
            
        }
    }
}
