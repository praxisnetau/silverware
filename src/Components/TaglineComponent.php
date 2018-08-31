<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Components
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2018 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\Components;

/**
 * An extension of the base component class for a tagline component.
 *
 * @package SilverWare\Components
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2018 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class TaglineComponent extends BaseComponent
{
    /**
     * Human-readable singular name.
     *
     * @var string
     * @config
     */
    private static $singular_name = 'Tagline Component';
    
    /**
     * Human-readable plural name.
     *
     * @var string
     * @config
     */
    private static $plural_name = 'Tagline Components';
    
    /**
     * Description of this object.
     *
     * @var string
     * @config
     */
    private static $description = 'A component which shows the tagline of the site';
    
    /**
     * Icon file for this object.
     *
     * @var string
     * @config
     */
    private static $icon = 'silverware/silverware: admin/client/dist/images/icons/TaglineComponent.png';
    
    /**
     * Defines the table name to use for this object.
     *
     * @var string
     * @config
     */
    private static $table_name = 'SilverWare_TaglineComponent';
    
    /**
     * Defines an ancestor class to hide from the admin interface.
     *
     * @var string
     * @config
     */
    private static $hide_ancestor = BaseComponent::class;
    
    /**
     * Defines the extension classes to apply to this object.
     *
     * @var array
     * @config
     */
    private static $extensions = [
        AlignmentStyle::class
    ];
    
    /**
     * Defines the allowed children for this object.
     *
     * @var array|string
     * @config
     */
    private static $allowed_children = 'none';
    
    /**
     * Defines the default classes to use when rendering this object.
     *
     * @var array
     * @config
     */
    private static $default_classes = [
        'typography'
    ];
    
    /**
     * Defines the default title level to use.
     *
     * @var array
     * @config
     */
    private static $title_level_default = 'h2';
    
    /**
     * Answers a list of field objects for the CMS interface.
     *
     * @return FieldList
     */
    public function getCMSFields()
    {
        // Obtain Field Objects (from parent):
        
        $fields = parent::getCMSFields();
        
        // Remove Field Objects:
        
        $fields->removeFieldFromTab('Root.Options', 'TitleOptions');
        
        // Answer Field Objects:
        
        return $fields;
    }
    
    /**
     * Overrides the parent method so style extensions can add classes to the component class names.
     *
     * @return array
     */
    public function getClassNames()
    {
        $classes = parent::getClassNames();
        
        $this->extend('updateContentClassNames', $classes);
        
        return $classes;
    }
    
    /**
     * Answers the title of the component for the template.
     *
     * @return string
     */
    public function getTitleText()
    {
        return $this->getSiteConfig()->Tagline;
    }
}
