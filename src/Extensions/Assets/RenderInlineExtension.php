<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Extensions\Assets
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\Extensions\Assets;

use SilverStripe\Core\Extension;
use SilverStripe\ORM\FieldType\DBField;
use DOMDocument;

/**
 * An extension which adds inline rendering for vector images to the file class.
 *
 * @package SilverWare\Extensions\Assets
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class RenderInlineExtension extends Extension
{
    /**
     * Renders the content of the extended object inline.
     *
     * @return DBHTMLText
     */
    public function getRenderInline()
    {
        // Check File Exists / File Extension:
        
        if ($this->owner->exists() && $this->owner->getExtension() == 'svg') {
            
            // Create DOM Document:
            
            $dom = new DOMDocument();
            
            // Load SVG Data into DOM:
            
            $dom->load(BASE_PATH . $this->owner->URL);
            
            // Normalise SVG Data:
            
            $dom->normalizeDocument();
            
            // Render SVG as HTML:
            
            return DBField::create_field('HTMLFragment', $dom->saveHTML($dom->documentElement));
            
        }
    }
}
