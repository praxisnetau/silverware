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

use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\TreeDropdownField;

/**
 * An extension of the base component class for a virtual component.
 *
 * @package SilverWare\Components
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2018 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class VirtualComponent extends BaseComponent
{
    /**
     * Human-readable singular name.
     *
     * @var string
     * @config
     */
    private static $singular_name = 'Virtual Component';
    
    /**
     * Human-readable plural name.
     *
     * @var string
     * @config
     */
    private static $plural_name = 'Virtual Components';
    
    /**
     * Description of this object.
     *
     * @var string
     * @config
     */
    private static $description = 'Displays the content of another component';
    
    /**
     * Icon file for this object.
     *
     * @var string
     * @config
     */
    private static $icon = 'silverware/silverware: admin/client/dist/images/icons/VirtualComponent.png';
    
    /**
     * Defines the table name to use for this object.
     *
     * @var string
     * @config
     */
    private static $table_name = 'SilverWare_VirtualComponent';
    
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
        'UseSourceTitle' => 'Boolean'
    ];
    
    /**
     * Defines the default values for the fields of this object.
     *
     * @var array
     * @config
     */
    private static $defaults = [
        'UseSourceTitle' => 1
    ];
    
    /**
     * Defines the has-one associations for this object.
     *
     * @var array
     * @config
     */
    private static $has_one = [
        'SourceComponent' => BaseComponent::class
    ];
    
    /**
     * Defines the ownership of associations for this object.
     *
     * @var array
     * @config
     */
    private static $owns = [
        'SourceComponent'
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
                TreeDropdownField::create(
                    'SourceComponentID',
                    $this->fieldLabel('SourceComponentID'),
                    SiteTree::class
                )->setDisableFunction(function ($node) {
                    return (!($node instanceof BaseComponent) || $node->ID === $this->ID);
                })
            ]
        );
        
        // Create Option Fields:
        
        $fields->fieldByName('Root.Options.TitleOptions')->push(
            CheckboxField::create(
                'UseSourceTitle',
                $this->fieldLabel('UseSourceTitle')
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
        
        $labels['UseSourceTitle'] = _t(__CLASS__ . '.USESOURCETITLE', 'Use source title');
        $labels['SourceComponentID'] = _t(__CLASS__ . '.SOURCECOMPONENT', 'Source Component');
        
        // Define Relation Labels:
        
        if ($includerelations) {
            $labels['SourceComponent'] = _t(__CLASS__ . '.has_one_SourceComponent', 'Source Component');
        }
        
        // Answer Field Labels:
        
        return $labels;
    }
    
    /**
     * Answers true if a source component is associated with the receiver.
     *
     * @return boolean
     */
    public function hasSourceComponent()
    {
        return $this->SourceComponent()->exists();
    }
    
    /**
     * Answers an array of HTML tag attributes for the object.
     *
     * @return array
     */
    public function getAttributes()
    {
        if ($this->hasSourceComponent()) {
            
            $attributes = $this->SourceComponent()->getAttributes();
            
            if ($this->StyleID) {
                $attributes['id'] = $this->StyleID;
            }
            
            if ($this->StyleClasses) {
                $attributes['class'] .= sprintf(' %s', $this->StyleClasses);
            }
            
            return $attributes;
            
        }
        
        return parent::getAttributes();
    }
    
    /**
     * Answers the title of the component for the template.
     *
     * @return string
     */
    public function getTitleText()
    {
        if ($this->UseSourceTitle && $this->hasSourceComponent()) {
            return $this->SourceComponent()->getTitleText();
        }
        
        return parent::getTitleText();
    }
    
    /**
     * Answers true if the object is disabled within the template.
     *
     * @return boolean
     */
    public function isDisabled()
    {
        if (!$this->hasSourceComponent() || $this->SourceComponent()->isDisabled()) {
            return true;
        }
        
        return parent::isDisabled();
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
        if ($this->hasSourceComponent()) {
            
            $source = $this->SourceComponent();
            
            if ($this->StyleID) {
                $source->StyleID = $this->StyleID;
            }
            
            $source->loadRequirements();
            
        }
        
        return parent::renderSelf($layout, $title);
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
        if ($this->hasSourceComponent()) {
            return $this->SourceComponent()->renderContent($layout, $title);
        }
        
        return parent::renderContent($layout, $title);
    }
}
