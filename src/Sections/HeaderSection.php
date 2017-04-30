<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Sections
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\Sections;

use SilverWare\Grid\Section;
use SilverWare\Model\Template;

/**
 * An extension of the section class for a header section.
 *
 * @package SilverWare\Sections
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class HeaderSection extends Section
{
    /**
     * Human-readable singular name.
     *
     * @var string
     * @config
     */
    private static $singular_name = 'Header Section';
    
    /**
     * Human-readable plural name.
     *
     * @var string
     * @config
     */
    private static $plural_name = 'Header Sections';
    
    /**
     * Description of this object.
     *
     * @var string
     * @config
     */
    private static $description = 'A header section within a SilverWare template';
    
    /**
     * Icon file for this object.
     *
     * @var string
     * @config
     */
    private static $icon = 'silverware/admin/client/dist/images/icons/HeaderSection.png';
    
    /**
     * Defines the allowed parents for this object.
     *
     * @var array
     * @config
     */
    private static $allowed_parents = [
        Template::class
    ];
    
    /**
     * Tag name to use when rendering this object.
     *
     * @var string
     * @config
     */
    private static $tag = 'header';
}
