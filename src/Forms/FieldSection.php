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

use SilverStripe\Forms\CompositeField;

/**
 * An extension of the composite field class for a named section of fields.
 *
 * @package SilverWare\Forms
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class FieldSection extends CompositeField
{
    /**
     * Constructs the object upon instantiation.
     *
     * @param string $name Name of field.
     * @param string $title Title of field.
     * @param array|FieldList $children Child fields.
     */
    public function __construct($name, $title, $children)
    {
        // Construct Parent:
        
        parent::__construct($children);
        
        // Define Attributes:
        
        $this->setName($name);
        $this->setTitle($title);
    }
    
    /**
     * Merges the given array or list of fields with the receiver.
     *
     * @param array|ArrayAccess $with
     *
     * @return $this
     */
    public function merge($with)
    {
        $this->children->merge($with);
        
        return $this;
    }
}
