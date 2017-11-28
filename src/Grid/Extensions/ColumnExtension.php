<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Grid\Extensions
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\Grid\Extensions;

use SilverWare\ORM\FieldType\DBViewports;

/**
 * A grid extension class which forms an abstract base for column extensions.
 *
 * @package SilverWare\Grid\Extensions
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
abstract class ColumnExtension extends GridExtension
{
    /**
     * Answers the span class names for the extended object.
     *
     * @param DBViewports $span Optional viewports field to use instead of extended object field.
     *
     * @return array
     */
    abstract public function getSpanClassNames(DBViewports $span = null);
    
    /**
     * Answers the offset class names for the extended object.
     *
     * @param DBViewports $offset Optional viewports field to use instead of extended object field.
     *
     * @return array
     */
    abstract public function getOffsetClassNames(DBViewports $offset = null);
    
    /**
     * Answers the combined class names for the extended object.
     *
     * @return array
     */
    public function getColumnClassNames()
    {
        return array_merge(
            $this->owner->getSpanClassNames(),
            $this->owner->getOffsetClassNames()
        );
    }
    
    /**
     * Updates the class names of the extended object.
     *
     * @param array $classes Array of class names from the extended object.
     *
     * @return void
     */
    public function updateClassNames(&$classes)
    {
        $classes = array_merge(
            $classes,
            $this->getColumnClassNames()
        );
    }
}
