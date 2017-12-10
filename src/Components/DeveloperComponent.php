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

use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\RequiredFields;
use SilverWare\Extensions\Model\TokenMappingExtension;
use SilverWare\Extensions\Style\AlignmentStyle;
use SilverWare\Forms\FieldSection;
use SilverWare\Forms\PageDropdownField;
use Page;

/**
 * An extension of the base component class for a developer component.
 *
 * @package SilverWare\Components
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class DeveloperComponent extends BaseComponent
{
    /**
     * Human-readable singular name.
     *
     * @var string
     * @config
     */
    private static $singular_name = 'Developer Component';
    
    /**
     * Human-readable plural name.
     *
     * @var string
     * @config
     */
    private static $plural_name = 'Developer Components';
    
    /**
     * Description of this object.
     *
     * @var string
     * @config
     */
    private static $description = 'A component for showing attribution to the developer';
    
    /**
     * Icon file for this object.
     *
     * @var string
     * @config
     */
    private static $icon = 'silverware/silverware: admin/client/dist/images/icons/DeveloperComponent.png';
    
    /**
     * Defines the table name to use for this object.
     *
     * @var string
     * @config
     */
    private static $table_name = 'SilverWare_DeveloperComponent';
    
    /**
     * Defines an ancestor class to hide from the admin interface.
     *
     * @var string
     * @config
     */
    private static $hide_ancestor = BaseComponent::class;
    
    /**
     * Maps field names to field types for this object.
     *
     * @var array
     * @config
     */
    private static $db = [
        'DeveloperName' => 'Varchar(255)',
        'DeveloperURL' => 'Varchar(2048)',
        'DeveloperText' => 'Varchar(255)',
        'OpenLinkInNewTab' => 'Boolean',
        'LinkDisabled' => 'Boolean'
    ];
    
    /**
     * Defines the has-one associations for this object.
     *
     * @var array
     * @config
     */
    private static $has_one = [
        'DeveloperPage' => Page::class
    ];
    
    /**
     * Defines the default values for the fields of this object.
     *
     * @var array
     * @config
     */
    private static $defaults = [
        'HideTitle' => 1,
        'LinkDisabled' => 0,
        'OpenLinkInNewTab' => 1,
        'TextAlignmentTiny' => 'center',
        'TextAlignmentMedium' => 'right'
    ];
    
    /**
     * Maps field and method names to the class names of casting objects.
     *
     * @var array
     * @config
     */
    private static $casting = [
        'Developer' => 'HTMLFragment'
    ];
    
    /**
     * Defines the extension classes to apply to this object.
     *
     * @var array
     * @config
     */
    private static $extensions = [
        AlignmentStyle::class,
        TokenMappingExtension::class
    ];
    
    /**
     * Defines the allowed children for this object.
     *
     * @var array|string
     * @config
     */
    private static $allowed_children = 'none';
    
    /**
     * Maps token names to properties and methods for this object.
     *
     * @var array|string
     * @config
     */
    private static $token_mappings = [
        'developer' => [
            'property' => 'DeveloperNameLink'
        ]
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
                    'DeveloperName',
                    $this->fieldLabel('DeveloperName')
                ),
                PageDropdownField::create(
                    'DeveloperPageID',
                    $this->fieldLabel('DeveloperPageID')
                ),
                TextField::create(
                    'DeveloperURL',
                    $this->fieldLabel('DeveloperURL')
                )
            ]
        );
        
        // Create Options Fields:
        
        $fields->addFieldsToTab(
            'Root.Options',
            [
                FieldSection::create(
                    'DeveloperOptions',
                    $this->fieldLabel('DeveloperOptions'),
                    [
                        TextField::create(
                            'DeveloperText',
                            $this->fieldLabel('DeveloperText')
                        ),
                        CheckboxField::create(
                            'OpenLinkInNewTab',
                            $this->fieldLabel('OpenLinkInNewTab')
                        ),
                        CheckboxField::create(
                            'LinkDisabled',
                            $this->fieldLabel('LinkDisabled')
                        )
                    ]
                )
            ]
        );
        
        // Answer Field Objects:
        
        return $fields;
    }
    
    /**
     * Answers a validator for the CMS interface.
     *
     * @return RequiredFields
     */
    public function getCMSValidator()
    {
        return RequiredFields::create([
            'DeveloperName',
            'DeveloperText'
        ]);
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
        
        $labels['DeveloperName'] = _t(__CLASS__ . '.DEVELOPERNAME', 'Developer name');
        $labels['DeveloperPageID'] = _t(__CLASS__ . '.DEVELOPERPAGE', 'Developer page');
        $labels['DeveloperURL'] = _t(__CLASS__ . '.DEVELOPERURL',  'Developer URL');
        $labels['DeveloperText'] = _t(__CLASS__ . '.DEVELOPERTEXT', 'Developer text');
        $labels['DeveloperOptions'] = _t(__CLASS__ . '.DEVELOPER', 'Developer');
        
        // Define Checkbox Field Labels:
        
        $labels['LinkDisabled'] = _t(__CLASS__ . '.LINKDISABLED', 'Link disabled');
        $labels['OpenLinkInNewTab'] = _t(__CLASS__ . '.OPENLINKINNEWTAB', 'Open link in new tab');
        
        // Define Relation Labels:
        
        if ($includerelations) {
            $labels['DeveloperPage'] = _t(__CLASS__ . '.has_one_DeveloperPage', 'Developer Page');
        }
        
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
        
        $this->DeveloperText = _t(
            __CLASS__ . '.DEFAULTDEVELOPERTEXT',
            'Developed by {developer}.'
        );
    }
    
    /**
     * Answers an array of wrapper class names for the HTML template.
     *
     * @return array
     */
    public function getWrapperClassNames()
    {
        $classes = ['developer'];
        
        $this->extend('updateWrapperClassNames', $classes);
        
        return $classes;
    }
    
    /**
     * Answers the link to the developer page.
     *
     * @return string
     */
    public function getDeveloperLink()
    {
        if ($this->DeveloperURL) {
            return $this->dbObject('DeveloperURL')->URL();
        }
        
        if ($this->DeveloperPageID) {
            return $this->DeveloperPage()->Link();
        }
    }
    
    /**
     * Answers true if the receiver has a developer link.
     *
     * @return boolean
     */
    public function hasDeveloperLink()
    {
        return (boolean) $this->getDeveloperLink();
    }
    
    /**
     * Answers the developer name link for the template.
     *
     * @return string
     */
    public function getDeveloperNameLink()
    {
        if ($this->hasDeveloperLink() && !$this->LinkDisabled) {
            
            $target = $this->OpenLinkInNewTab ? ' target="_blank"' : '';
            
            return sprintf(
                '<a href="%s" rel="nofollow"%s>%s</a>',
                $this->DeveloperLink,
                $target,
                $this->DeveloperName
            );
            
        }
        
        return $this->DeveloperName;
    }
    
    /**
     * Answers the developer text for the template.
     *
     * @return string
     */
    public function getDeveloper()
    {
        return $this->replaceTokens($this->DeveloperText);
    }
}
