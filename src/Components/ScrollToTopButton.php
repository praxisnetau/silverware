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
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\Components;

use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\TextField;
use SilverWare\Extensions\Style\CornerStyle;
use SilverWare\Extensions\Style\LinkColorStyle;
use SilverWare\Extensions\Style\ThemeStyle;
use SilverWare\FontIcons\Forms\FontIconField;
use SilverWare\Forms\FieldSection;

/**
 * An extension of the base component class for a scroll to top button.
 *
 * @package SilverWare\Components
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class ScrollToTopButton extends BaseComponent
{
    /**
     * Human-readable singular name.
     *
     * @var string
     * @config
     */
    private static $singular_name = 'Scroll to Top Button';
    
    /**
     * Human-readable plural name.
     *
     * @var string
     * @config
     */
    private static $plural_name = 'Scroll to Top Buttons';
    
    /**
     * Description of this object.
     *
     * @var string
     * @config
     */
    private static $description = 'A button which scrolls to the top of the page when pressed';
    
    /**
     * Icon file for this object.
     *
     * @var string
     * @config
     */
    private static $icon = 'silverware/silverware: admin/client/dist/images/icons/ScrollToTopButton.png';
    
    /**
     * Defines an ancestor class to hide from the admin interface.
     *
     * @var string
     * @config
     */
    private static $hide_ancestor = BaseComponent::class;
    
    /**
     * Defines the allowed children for this object.
     *
     * @var array|string
     * @config
     */
    private static $allowed_children = 'none';
    
    /**
     * Maps field names to field types for this object.
     *
     * @var array
     * @config
     */
    private static $db = [
        'Label' => 'Varchar(128)',
        'ButtonIcon' => 'FontIcon',
        'OffsetShow' => 'Int',
        'OffsetOpacity' => 'Int',
        'ScrollDuration' => 'Int'
    ];
    
    /**
     * Defines the default values for the fields of this object.
     *
     * @var array
     * @config
     */
    private static $defaults = [
        'ButtonIcon' => 'chevron-up',
        'OffsetShow' => 300,
        'OffsetOpacity' => 1000,
        'ScrollDuration' => 800,
        'ColorBackgroundTheme' => 'background.primary',
        'ColorForegroundTheme' => 'text.white'
    ];
    
    /**
     * Defines the extension classes to apply to this object.
     *
     * @var array
     * @config
     */
    private static $extensions = [
        ThemeStyle::class,
        LinkColorStyle::class,
        CornerStyle::class
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
        
        // Create Main Fields:
        
        $fields->addFieldsToTab(
            'Root.Main',
            [
                TextField::create(
                    'Label',
                    $this->fieldLabel('Label')
                ),
                FontIconField::create(
                    'ButtonIcon',
                    $this->fieldLabel('ButtonIcon')
                )
            ]
        );
        
        // Create Options Fields:
        
        $fields->addFieldToTab(
            'Root.Options',
            FieldSection::create(
                'ScrollToTopOptions',
                $this->fieldLabel('ScrollToTopOptions'),
                [
                    NumericField::create(
                        'OffsetShow',
                        $this->fieldLabel('OffsetShow')
                    ),
                    NumericField::create(
                        'OffsetOpacity',
                        $this->fieldLabel('OffsetOpacity')
                    ),
                    NumericField::create(
                        'ScrollDuration',
                        $this->fieldLabel('ScrollDuration')
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
        
        $labels['Label'] = _t(__CLASS__ . '.LABEL', 'Label');
        $labels['ButtonIcon'] = _t(__CLASS__ . '.ICON', 'Icon');
        $labels['OffsetShow'] = _t(__CLASS__ . '.OFFSETSHOWINPIXELS', 'Show offset (in pixels)');
        $labels['OffsetOpacity'] = _t(__CLASS__ . '.OFFSETOPACITYINPIXELS', 'Opacity offset (in pixels)');
        $labels['ScrollDuration'] = _t(__CLASS__ . '.SCROLLDURATIONINMS', 'Scroll duration (in milliseconds)');
        $labels['ScrollToTopOptions'] = _t(__CLASS__ . '.SCROLLTOTOP', 'Scroll to Top');
        
        // Answer Field Labels:
        
        return $labels;
    }
    
    /**
     * Populates the default values for the fields of the receiver.
     *
     * @return void
     */
    public function populateDefaults()
    {
        // Populate Defaults (from parent):
        
        parent::populateDefaults();
        
        // Populate Defaults:
        
        $this->Label = _t(__CLASS__ . 'DEFAULTLABEL', 'Scroll to Top');
    }
    
    /**
     * Answers an array of HTML tag attributes for the object.
     *
     * @return array
     */
    public function getAttributes()
    {
        $attributes = array_merge(
            parent::getAttributes(),
            [
                'href' => '#top',
                'title' => $this->Label,
                'data-offset-show' => $this->OffsetShow,
                'data-offset-opacity' => $this->OffsetOpacity,
                'data-scroll-duration' => $this->ScrollDuration
            ]
        );
        
        return $attributes;
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
        return $this->getController()->renderWith(self::class);
    }
}
