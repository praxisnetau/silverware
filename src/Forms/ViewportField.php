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

use SilverStripe\Forms\DropdownField;
use SilverWare\ORM\FieldType\DBViewports;

/**
 * An extension of the dropdown field class for a viewport field.
 *
 * @package SilverWare\Forms
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class ViewportField extends DropdownField
{
    /**
     * Extra attributes for the HTML tag.
     *
     * @var array
     */
    protected $extraClasses = [
        'dropdown'
    ];
    
    /**
     * Defines the source options for the receiver.
     *
     * @return $this
     */
    public function setSource($source)
    {
        return parent::setSource($source ? $source : $this->getDefaultSource());
    }
    
    /**
     * Answers the default source options for the receiver.
     *
     * @return array
     */
    public function getDefaultSource()
    {
        return DBViewports::singleton()->getViewportOptions();
    }
}
