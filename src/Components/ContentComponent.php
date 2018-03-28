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
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Forms\TextField;
use SilverWare\Extensions\Model\LinkToExtension;
use SilverWare\Extensions\Style\AlignmentStyle;
use SilverWare\Extensions\Style\ButtonStyle;
use SilverWare\Extensions\Style\CornerStyle;
use SilverWare\Extensions\Style\ThemeStyle;
use SilverWare\FontIcons\Extensions\FontIconExtension;
use SilverWare\Forms\FieldSection;

/**
 * An extension of the base component class for a content component.
 *
 * @package SilverWare\Components
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class ContentComponent extends BaseComponent
{
    /**
     * Define link mode constants.
     */
    const LINK_MODE_BOTH   = 'both';
    const LINK_MODE_TITLE  = 'title';
    const LINK_MODE_BUTTON = 'button';
    
    /**
     * Human-readable singular name.
     *
     * @var string
     * @config
     */
    private static $singular_name = 'Content Component';
    
    /**
     * Human-readable plural name.
     *
     * @var string
     * @config
     */
    private static $plural_name = 'Content Components';
    
    /**
     * Description of this object.
     *
     * @var string
     * @config
     */
    private static $description = 'A component to show a block of editable content';
    
    /**
     * Icon file for this object.
     *
     * @var string
     * @config
     */
    private static $icon = 'silverware/silverware: admin/client/dist/images/icons/ContentComponent.png';
    
    /**
     * Defines the table name to use for this object.
     *
     * @var string
     * @config
     */
    private static $table_name = 'SilverWare_ContentComponent';
    
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
        'LinkMode' => 'Varchar(16)',
        'ButtonLabel' => 'Varchar(128)'
    ];
    
    /**
     * Defines the extension classes to apply to this object.
     *
     * @var array
     * @config
     */
    private static $extensions = [
        AlignmentStyle::class,
        ButtonStyle::class,
        CornerStyle::class,
        FontIconExtension::class,
        LinkToExtension::class,
        ThemeStyle::class
    ];
    
    /**
     * Defines the style extension classes to apply to this object.
     *
     * @var array
     * @config
     */
    private static $apply_styles = [
        AlignmentStyle::class,
        CornerStyle::class
    ];
    
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
     * Answers a list of field objects for the CMS interface.
     *
     * @return FieldList
     */
    public function getCMSFields()
    {
        // Obtain Field Objects (from parent):
        
        $fields = parent::getCMSFields();
        
        // Create Main Fields:
        
        $fields->addFieldToTab(
            'Root.Main',
            HTMLEditorField::create(
                'Content',
                $this->fieldLabel('Content')
            ),
            'LinkTo'
        );
        
        // Create Options Fields:
        
        $fields->addFieldToTab(
            'Root.Options',
            FieldSection::create(
                'ContentOptions',
                $this->fieldLabel('ContentOptions'),
                [
                    DropdownField::create(
                        'LinkMode',
                        $this->fieldLabel('LinkMode'),
                        $this->getLinkModeOptions()
                    ),
                    TextField::create(
                        'ButtonLabel',
                        $this->fieldLabel('ButtonLabel')
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
        
        $labels['LinkMode'] = _t(__CLASS__ . '.LINKMODE', 'Link mode');
        $labels['ButtonLabel'] = _t(__CLASS__ . '.BUTTONLABEL', 'Button label');
        $labels['ContentOptions'] = _t(__CLASS__ . '.CONTENT', 'Content');
        
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
        
        $this->ButtonLabel = _t(__CLASS__ . '.DEFAULTBUTTONLABEL', 'More');
    }
    
    /**
     * Answers the HTML content of the receiver (overrides method in BaseComponent).
     *
     * @return DBHTMLText
     */
    public function getContent()
    {
        return $this->dbObject('Content');
    }
    
    /**
     * Answers the link for the content component.
     *
     * @return string
     */
    public function getContentLink()
    {
        return $this->getLink();
    }
    
    /**
     * Answers true if the button is to be shown.
     *
     * @return boolean
     */
    public function getButtonShown()
    {
        return ($this->LinkMode == self::LINK_MODE_BOTH || $this->LinkMode == self::LINK_MODE_BUTTON);
    }
    
    /**
     * Answers true if the footer is to be shown in the template.
     *
     * @return boolean
     */
    public function getFooterShown()
    {
        return ($this->hasLink() && $this->ButtonShown);
    }
    
    /**
     * Answers true if the title link is to be shown in the template.
     *
     * @return boolean
     */
    public function getLinkTitle()
    {
        return ($this->hasLink() && $this->TitleLinked);
    }
    
    /**
     * Answers true if the title link is to be shown.
     *
     * @return boolean
     */
    public function getTitleLinked()
    {
        return ($this->LinkMode == self::LINK_MODE_BOTH || $this->LinkMode == self::LINK_MODE_TITLE);
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
    
    /**
     * Answers an array of options for the link mode field.
     *
     * @return array
     */
    public function getLinkModeOptions()
    {
        return [
            self::LINK_MODE_TITLE  => _t(__CLASS__ . '.TITLE', 'Title'),
            self::LINK_MODE_BUTTON => _t(__CLASS__ . '.BUTTON', 'Button'),
            self::LINK_MODE_BOTH   => _t(__CLASS__ . '.BOTHTITLEANDBUTTON', 'Both Title and Button')
        ];
    }
}
