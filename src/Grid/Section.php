<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Grid
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\Grid;

use SilverStripe\Forms\CheckboxField;
use SilverWare\Forms\FieldSection;

/**
 * An extension of the grid class for a section.
 *
 * @package SilverWare\Grid
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class Section extends Grid
{
    /**
     * Human-readable singular name.
     *
     * @var string
     * @config
     */
    private static $singular_name = 'Section';
    
    /**
     * Human-readable plural name.
     *
     * @var string
     * @config
     */
    private static $plural_name = 'Sections';
    
    /**
     * Description of this object.
     *
     * @var string
     * @config
     */
    private static $description = 'A section within a SilverWare template or layout';
    
    /**
     * Icon file for this object.
     *
     * @var string
     * @config
     */
    private static $icon = 'silverware/admin/client/dist/images/icons/Section.png';
    
    /**
     * Defines an ancestor class to hide from the admin interface.
     *
     * @var string
     * @config
     */
    private static $hide_ancestor = Grid::class;
    
    /**
     * Defines the default child class for this object.
     *
     * @var string
     * @config
     */
    private static $default_child = Row::class;
    
    /**
     * Maps field names to field types for this object.
     *
     * @var array
     * @config
     */
    private static $db = [
        'FullWidth' => 'Boolean'
    ];
    
    /**
     * Defines the default values for the fields of this object.
     *
     * @var array
     * @config
     */
    private static $defaults = [
        'FullWidth' => 0
    ];
    
    /**
     * Defines the allowed children for this object.
     *
     * @var array|string
     * @config
     */
    private static $allowed_children = [
        Row::class
    ];
    
    /**
     * Answers a list of field objects for the CMS interface.
     *
     * @return FieldList
     */
    public function getCMSFields()
    {
        // Obtain Field Objects (from parent):
        
        $fields = parent::getCMSFields();
        
        // Create Options Fields:
        
        $fields->addFieldToTab(
            'Root.Options',
            FieldSection::create(
                'SectionOptions',
                $this->fieldLabel('SectionOptions'),
                [
                    CheckboxField::create(
                        'FullWidth',
                        $this->fieldLabel('FullWidth')
                    )
                ]
            )
        );
        
        // Answer Field Objects:
        
        return $fields;
    }
    
    /**
     * Answers the labels for the fields of the receiver.
     *
     * @param boolean $includerelations Include labels for relations.
     *
     * @return array
     */
    public function fieldLabels($includerelations = true)
    {
        // Obtain Field Labels (from parent):
        
        $labels = parent::fieldLabels($includerelations);
        
        // Define Field Labels:
        
        $labels['FullWidth'] = _t(__CLASS__ . '.USEFULLWIDTHCONTAINER', 'Use full width container');
        $labels['SectionOptions'] = _t(__CLASS__ . '.SECTION', 'Section');
        
        // Answer Field Labels:
        
        return $labels;
    }
    
    /**
     * Answers an array of container class names for the HTML template.
     *
     * @return array
     */
    public function getContainerClassNames()
    {
        $classes = [];
        
        $this->extend('updateContainerClassNames', $classes);
        
        return $classes;
    }
    
    /**
     * Answers true if the section uses a full width container.
     *
     * @return boolean
     */
    public function isFullWidth()
    {
        return (boolean) $this->FullWidth;
    }
    
    /**
     * Renders the component for the HTML template.
     *
     * @param string $layout Page layout passed from template.
     * @param string $title Page title passed from template.
     *
     * @return DBHTMLText|string
     */
    public function renderSelf($layout = null, $title = null)
    {
        return $this->tag($this->renderContainer($layout, $title));
    }
    
    /**
     * Renders the container for the HTML template.
     *
     * @param string $layout Page layout passed from template.
     * @param string $title Page title passed from template.
     *
     * @return string
     */
    public function renderContainer($layout = null, $title = null)
    {
        return sprintf(
            "<div class=\"%s\">\n%s</div>\n",
            $this->getContainerClass(),
            $this->renderChildren($layout, $title)
        );
    }
}
