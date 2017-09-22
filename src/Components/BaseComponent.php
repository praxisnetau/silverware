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
use SilverStripe\Forms\DropdownField;
use SilverWare\Forms\FieldSection;
use SilverWare\Model\Component;

/**
 * An extension of the component class for a base component.
 *
 * @package SilverWare\Components
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class BaseComponent extends Component
{
    /**
     * Defines an ancestor class to hide from the admin interface.
     *
     * @var string
     * @config
     */
    private static $hide_ancestor = Component::class;
    
    /**
     * Maps field names to field types for this object.
     *
     * @var array
     * @config
     */
    private static $db = [
        'TitleLevel' => 'Varchar(2)',
        'HideTitle' => 'Boolean'
    ];
    
    /**
     * Maps field and method names to the class names of casting objects.
     *
     * @var array
     * @config
     */
    private static $casting = [
        'TitleTag' => 'HTMLFragment'
    ];
    
    /**
     * Defines the default values for the fields of this object.
     *
     * @var array
     * @config
     */
    private static $defaults = [
        'HideTitle' => 0
    ];
    
    /**
     * Defines the available title level options.
     *
     * @var array
     * @config
     */
    private static $title_level_options = [
        'h1' => 'h1',
        'h2' => 'h2',
        'h3' => 'h3',
        'h4' => 'h4',
        'h5' => 'h5',
        'h6' => 'h6'
    ];
    
    /**
     * Defines the default title level to use.
     *
     * @var array
     * @config
     */
    private static $title_level_default = 'h3';
    
    /**
     * Answers a list of field objects for the CMS interface.
     *
     * @return FieldList
     */
    public function getCMSFields()
    {
        // Obtain Field Objects (from parent):
        
        $fields = parent::getCMSFields();
        
        // Define Placeholder:
        
        $placeholder = _t(__CLASS__ . '.DROPDOWNDEFAULT', '(default)');
        
        // Create Style Fields:
        
        $fields->addFieldsToTab(
            'Root.Style',
            [
                FieldSection::create(
                    'TitleStyle',
                    $this->fieldLabel('TitleStyle'),
                    [
                        DropdownField::create(
                            'TitleLevel',
                            $this->fieldLabel('TitleLevel'),
                            $this->getTitleLevelOptions()
                        )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder)
                    ]
                )
            ]
        );
        
        // Create Options Fields:
        
        $fields->addFieldsToTab(
            'Root.Options',
            [
                FieldSection::create(
                    'TitleOptions',
                    $this->fieldLabel('TitleOptions'),
                    [
                        CheckboxField::create(
                            'HideTitle',
                            $this->fieldLabel('HideTitle')
                        )
                    ]
                )
            ]
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
        
        $labels['TitleLevel'] = _t(__CLASS__ . '.LEVEL', 'Level');
        $labels['HideTitle'] = _t(__CLASS__ . '.HIDETITLE', 'Hide title');
        $labels['TitleStyle'] = $labels['TitleOptions'] = _t(__CLASS__ . '.TITLE', 'Title');
        
        // Answer Field Labels:
        
        return $labels;
    }
    
    /**
     * Answers null to avoid problems with '$Content' double-ups in the template.
     *
     * @return null
     */
    public function getContent()
    {
        return null;
    }
    
    /**
     * Answers an array of content class names for the HTML template.
     *
     * @return array
     */
    public function getContentClassNames()
    {
        $classes = $this->styles('content');
        
        $this->extend('updateContentClassNames', $classes);
        
        return $classes;
    }
    
    /**
     * Answers an array of form class names for the HTML template.
     *
     * @return array
     */
    public function getFormClassNames()
    {
        $classes = ['form'];
        
        $this->extend('updateFormClassNames', $classes);
        
        return $classes;
    }
    
    /**
     * Answers an array of options for the title level field.
     *
     * @return array
     */
    public function getTitleLevelOptions()
    {
        return $this->config()->title_level_options;
    }
    
    /**
     * Answers the title tag for the receiver.
     *
     * @return string
     */
    public function getTitleTag()
    {
        if ($tag = $this->getField('TitleLevel')) {
            return $tag;
        }
        
        return $this->config()->title_level_default;
    }
    
    /**
     * Answers the title of the component for the template.
     *
     * @return string
     */
    public function getTitleText()
    {
        return $this->Title;
    }
    
    /**
     * Answers true if the title is to be shown in the template.
     *
     * @return boolean
     */
    public function getShowTitle()
    {
        return !$this->HideTitle && $this->Title;
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
        return $this->getController()->customise([
            'Content' => $this->renderContent($layout, $title)
        ])->renderWith(self::class);
    }
    
    /**
     * Renders the content for the HTML template.
     *
     * @param string $layout Page layout passed from template.
     * @param string $title Page title passed from template.
     *
     * @return DBHTMLText|string
     */
    public function renderContent($layout = null, $title = null)
    {
        if ($this->getTemplate() != self::class) {
            return parent::renderSelf($layout, $title);
        }
    }
}
