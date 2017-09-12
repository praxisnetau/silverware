<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Grid\Frameworks\Bootstrap
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\Grid\Frameworks\Bootstrap;

use SilverWare\Grid\Extensions\SectionExtension;

/**
 * A section extension for Bootstrap sections.
 *
 * @package SilverWare\Grid\Frameworks\Bootstrap
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class Section extends SectionExtension
{
    /**
     * Updates the given array of class names from the extended object.
     *
     * @param array $classes
     *
     * @return void
     */
    public function updateClassNames(&$classes)
    {
        if ($position = $this->owner->Position) {
            $classes[] = $this->style('position', $position);
        }
    }
    
    /**
     * Answers the container class names for the extended object.
     *
     * @return array
     */
    public function getClassNamesForContainer()
    {
        $classes = [$this->owner->isFullWidth() ? 'container-fluid' : 'container'];
        
        if ($this->owner->isEdgeToEdge()) {
            $classes[] = $this->style('section.edge-to-edge');
        }
        
        return $classes;
    }
}
