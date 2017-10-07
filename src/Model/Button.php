<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Model
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\Model;

use SilverWare\Extensions\Model\LinkToExtension;
use SilverWare\Extensions\Style\ButtonStyle;
use SilverWare\Extensions\Style\CornerStyle;
use SilverWare\FontIcons\Extensions\FontIconExtension;

/**
 * An extension of the component class for a button.
 *
 * @package SilverWare\Model
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class Button extends Component
{
    /**
     * Human-readable singular name.
     *
     * @var string
     * @config
     */
    private static $singular_name = 'Button';
    
    /**
     * Human-readable plural name.
     *
     * @var string
     * @config
     */
    private static $plural_name = 'Buttons';
    
    /**
     * Description of this object.
     *
     * @var string
     * @config
     */
    private static $description = 'A component which represents a button';
    
    /**
     * Icon file for this object.
     *
     * @var string
     * @config
     */
    private static $icon = 'silverware/silverware: admin/client/dist/images/icons/Button.png';
    
    /**
     * Defines an ancestor class to hide from the admin interface.
     *
     * @var string
     * @config
     */
    private static $hide_ancestor = Component::class;
    
    /**
     * Defines the allowed children for this object.
     *
     * @var array|string
     * @config
     */
    private static $allowed_children = 'none';
    
    /**
     * Defines the extension classes to apply to this object.
     *
     * @var array
     * @config
     */
    private static $extensions = [
        ButtonStyle::class,
        CornerStyle::class,
        LinkToExtension::class,
        FontIconExtension::class
    ];
    
    /**
     * Answers an array of HTML tag attributes for the object.
     *
     * @return array
     */
    public function getAttributes()
    {
        return array_merge(
            parent::getAttributes(),
            $this->getLinkAttributes(),
            $this->getButtonAttributes()
        );
    }
    
    /**
     * Answers an array of attributes for the button.
     *
     * @return array
     */
    public function getButtonAttributes()
    {
        $attributes = ['role' => 'button'];
        
        $this->extend('updateButtonAttributes', $attributes);
        
        return $attributes;
    }
}
