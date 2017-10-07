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
use SilverStripe\Forms\FieldGroup;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\RequiredFields;
use SilverWare\Extensions\Model\TokenMappingExtension;
use SilverWare\Extensions\Style\AlignmentStyle;
use SilverWare\Forms\FieldSection;
use SilverWare\Forms\PageDropdownField;
use Page;

/**
 * An extension of the base component class for a copyright component.
 *
 * @package SilverWare\Components
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class CopyrightComponent extends BaseComponent
{
    /**
     * Human-readable singular name.
     *
     * @var string
     * @config
     */
    private static $singular_name = 'Copyright Component';
    
    /**
     * Human-readable plural name.
     *
     * @var string
     * @config
     */
    private static $plural_name = 'Copyright Components';
    
    /**
     * Description of this object.
     *
     * @var string
     * @config
     */
    private static $description = 'A component for showing a copyright notice';
    
    /**
     * Icon file for this object.
     *
     * @var string
     * @config
     */
    private static $icon = 'silverware/silverware: admin/client/dist/images/icons/CopyrightComponent.png';
    
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
        'YearStart' => 'Varchar(8)',
        'YearFinish' => 'Varchar(8)',
        'EntityName' => 'Varchar(255)',
        'CopyrightNoun' => 'Varchar(128)',
        'CopyrightText' => 'Varchar(255)',
        'EntityURL' => 'Varchar(2048)',
        'CopyrightURL' => 'Varchar(2048)',
        'EntityLinkDisabled' => 'Boolean',
        'CopyrightLinkDisabled' => 'Boolean',
        'OpenEntityLinkInNewTab' => 'Boolean',
        'OpenCopyrightLinkInNewTab' => 'Boolean'
    ];
    
    /**
     * Defines the has-one associations for this object.
     *
     * @var array
     * @config
     */
    private static $has_one = [
        'EntityPage' => Page::class,
        'CopyrightPage' => Page::class
    ];
    
    /**
     * Defines the default values for the fields of this object.
     *
     * @var array
     * @config
     */
    private static $defaults = [
        'HideTitle' => 1,
        'EntityLinkDisabled' => 0,
        'CopyrightLinkDisabled' => 0,
        'OpenEntityLinkInNewTab' => 0,
        'OpenCopyrightLinkInNewTab' => 0,
        'TextAlignmentTiny' => 'center',
        'TextAlignmentMedium' => 'left'
    ];
    
    /**
     * Maps field and method names to the class names of casting objects.
     *
     * @var array
     * @config
     */
    private static $casting = [
        'Copyright' => 'HTMLFragment'
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
        'copyright' => [
            'property' => 'CopyrightNounLink'
        ],
        'entity' => [
            'property' => 'EntityNameLink'
        ],
        'year' => [
            'property' => 'CopyrightYear'
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
                    'EntityName',
                    $this->fieldLabel('EntityName')
                ),
                FieldGroup::create(
                    $this->fieldLabel('Years'),
                    [
                        TextField::create(
                            'YearStart',
                            ''
                        )->setAttribute('placeholder', $this->fieldLabel('YearStart')),
                        TextField::create(
                            'YearFinish',
                            ''
                        )->setAttribute('placeholder', $this->fieldLabel('YearFinish'))
                    ]
                ),
                PageDropdownField::create(
                    'CopyrightPageID',
                    $this->fieldLabel('CopyrightPageID')
                ),
                TextField::create(
                    'CopyrightURL',
                    $this->fieldLabel('CopyrightURL')
                ),
                PageDropdownField::create(
                    'EntityPageID',
                    $this->fieldLabel('EntityPageID')
                ),
                TextField::create(
                    'EntityURL',
                    $this->fieldLabel('EntityURL')
                )
            ]
        );
        
        // Create Options Fields:
        
        $fields->addFieldsToTab(
            'Root.Options',
            [
                FieldSection::create(
                    'CopyrightOptions',
                    $this->fieldLabel('CopyrightOptions'),
                    [
                        TextField::create(
                            'CopyrightNoun',
                            $this->fieldLabel('CopyrightNoun')
                        ),
                        TextField::create(
                            'CopyrightText',
                            $this->fieldLabel('CopyrightText')
                        ),
                        CheckboxField::create(
                            'OpenCopyrightLinkInNewTab',
                            $this->fieldLabel('OpenCopyrightLinkInNewTab')
                        ),
                        CheckboxField::create(
                            'OpenEntityLinkInNewTab',
                            $this->fieldLabel('OpenEntityLinkInNewTab')
                        ),
                        CheckboxField::create(
                            'CopyrightLinkDisabled',
                            $this->fieldLabel('CopyrightLinkDisabled')
                        ),
                        CheckboxField::create(
                            'EntityLinkDisabled',
                            $this->fieldLabel('EntityLinkDisabled')
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
            'EntityName',
            'CopyrightNoun',
            'CopyrightText'
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
        
        $labels['EntityName'] = _t(__CLASS__ . '.ENTITYNAME', 'Entity name');
        $labels['Years'] = _t(__CLASS__ . '.YEARS', 'Years');
        $labels['YearStart'] = _t(__CLASS__ . '.START', 'Start');
        $labels['YearFinish'] = _t(__CLASS__ . '.FINISH', 'Finish');
        $labels['EntityPageID'] = _t(__CLASS__ . '.ENTITYPAGE', 'Entity page');
        $labels['EntityURL'] = _t(__CLASS__ . '.ENTITYURL', 'Entity URL');
        $labels['CopyrightPageID'] = _t(__CLASS__ . '.COPYRIGHTPAGE', 'Copyright page');
        $labels['CopyrightURL'] = _t(__CLASS__ . '.COPYRIGHTURL', 'Copyright URL');
        $labels['CopyrightNoun'] = _t(__CLASS__ . '.COPYRIGHTNOUN', 'Copyright noun');
        $labels['CopyrightText'] = _t(__CLASS__ . '.COPYRIGHTTEXT', 'Copyright text');
        $labels['CopyrightOptions'] = _t(__CLASS__ . '.COPYRIGHT', 'Copyright');
        
        // Define Checkbox Field Labels:
        
        $labels['EntityLinkDisabled'] = _t(
            __CLASS__ . '.ENTITYLINKDISABLED',
            'Entity link disabled'
        );
        
        $labels['CopyrightLinkDisabled'] = _t(
            __CLASS__ . '.COPYRIGHTLINKDISABLED',
            'Copyright link disabled'
        );
        
        $labels['OpenEntityLinkInNewTab'] = _t(
            __CLASS__ . '.OPENENTITYLINKINNEWTAB',
            'Open entity link in new tab'
        );
        
        $labels['OpenCopyrightLinkInNewTab'] = _t(
            __CLASS__ . '.OPENCOPYRIGHTLINKINNEWTAB',
            'Open copyright link in new tab'
        );
        
        // Define Relation Labels:
        
        if ($includerelations) {
            $labels['EntityPage'] = _t(__CLASS__ . '.has_one_EntityPage', 'Entity Page');
            $labels['CopyrightPage'] = _t(__CLASS__ . '.has_one_CopyrightPage', 'Copyright Page');
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
        
        $this->CopyrightNoun = _t(
            __CLASS__ . '.DEFAULTCOPYRIGHTNOUN',
            'Copyright'
        );
        
        $this->CopyrightText = _t(
            __CLASS__ . '.DEFAULTCOPYRIGHTTEXT',
            '{copyright} &copy; {year} {entity}. All Rights Reserved.'
        );
    }
    
    /**
     * Answers an array of wrapper class names for the HTML template.
     *
     * @return array
     */
    public function getWrapperClassNames()
    {
        $classes = ['copyright'];
        
        $this->extend('updateWrapperClassNames', $classes);
        
        return $classes;
    }
    
    /**
     * Answers the link to the entity page.
     *
     * @return string
     */
    public function getEntityLink()
    {
        if ($this->EntityURL) {
            return $this->dbObject('EntityURL')->URL();
        }
        
        if ($this->EntityPageID) {
            return $this->EntityPage()->Link();
        }
    }
    
    /**
     * Answers the link to the copyright page.
     *
     * @return string
     */
    public function getCopyrightLink()
    {
        if ($this->CopyrightURL) {
            return $this->dbObject('CopyrightURL')->URL();
        }
        
        if ($this->CopyrightPageID) {
            return $this->CopyrightPage()->Link();
        }
    }
    
    /**
     * Answers true if the receiver has an entity link.
     *
     * @return boolean
     */
    public function hasEntityLink()
    {
        return (boolean) $this->getEntityLink();
    }
    
    /**
     * Answers true if the receiver has a copyright link.
     *
     * @return boolean
     */
    public function hasCopyrightLink()
    {
        return (boolean) $this->getCopyrightLink();
    }
    
    /**
     * Answers the entity name link for the template.
     *
     * @return string
     */
    public function getEntityNameLink()
    {
        if ($this->hasEntityLink() && !$this->EntityLinkDisabled) {
            
            $target = $this->OpenEntityLinkInNewTab ? ' target="_blank"' : '';
            
            return sprintf(
                '<a href="%s" rel="nofollow"%s>%s</a>',
                $this->EntityLink,
                $target,
                $this->EntityName
            );
            
        }
        
        return $this->EntityName;
    }
    
    /**
     * Answers the copyright noun link for the template.
     *
     * @return string
     */
    public function getCopyrightNounLink()
    {
        if ($this->hasCopyrightLink() && !$this->CopyrightLinkDisabled) {
            
            $target = $this->OpenCopyrightLinkInNewTab ? ' target="_blank"' : '';
            
            return sprintf(
                '<a href="%s" rel="nofollow"%s>%s</a>',
                $this->CopyrightLink,
                $target,
                $this->CopyrightNoun
            );
            
        }
        
        return $this->CopyrightNoun;
    }
    
    /**
     * Answers the copyright year for the template.
     *
     * @return string
     */
    public function getCopyrightYear()
    {
        if ($this->YearStart && $this->YearFinish) {
            return sprintf('%d-%d', $this->YearStart, $this->YearFinish);
        } elseif ($this->YearStart && !$this->YearFinish) {
            return sprintf('%d-%d', $this->YearStart, date('Y'));
        } elseif (!$this->YearStart && $this->YearFinish) {
            return $this->YearFinish;
        }
        
        return (string) date('Y');
    }
    
    /**
     * Answers the copyright text for the template.
     *
     * @return string
     */
    public function getCopyright()
    {
        return $this->replaceTokens($this->CopyrightText);
    }
}
