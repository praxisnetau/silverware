<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Extensions
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\Extensions;

use SilverStripe\Control\Controller;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Security\Permission;
use SilverStripe\Security\PermissionProvider;
use SilverStripe\SiteConfig\SiteConfig;
use SilverWare\Forms\FieldSection;
use SilverWare\Grid\ColumnSpan;
use SilverWare\Model\Layout;
use SilverWare\Model\Link;
use SilverWare\Model\Template;
use SilverWare\Tools\ViewTools;
use SilverWare\View\GridAware;
use Page;

/**
 * A data extension which allows pages to use SilverWare.
 *
 * @package SilverWare\Extensions
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class PageExtension extends DataExtension implements PermissionProvider
{
    use GridAware;
    
    /**
     * Defines the has-one associations for the extended object.
     *
     * @var array
     * @config
     */
    private static $has_one = [
        'MyLayout' => Layout::class,
        'MyTemplate' => Template::class
    ];
    
    /**
     * Defines the reciprocal many-many associations for the extended object.
     *
     * @var array
     * @config
     */
    private static $belongs_many_many = [
        'Spans' => ColumnSpan::class
    ];
    
    /**
     * Maps field and method names to the class names of casting objects.
     *
     * @var array
     * @config
     */
    private static $casting = [
        'BodyAttributesHTML' => 'HTMLFragment'
    ];
    
    /**
     * Updates the CMS fields of the extended object.
     *
     * @param FieldList $fields List of CMS fields from the extended object.
     *
     * @return void
     */
    public function updateCMSFields(FieldList $fields)
    {
        // Update Field Objects:
        
        $fields->fieldByName('Root.Main')->setTitle(_t(__CLASS__ . '.TABMAIN', 'Main'));
        
        // Remove Field Objects:
        
        if ($this->owner->config()->disable_metadata_toggle) {
            $fields->removeFieldFromTab('Root', 'Metadata');
        }
        
        // Add Tab Number Badges:
        
        if ($badges = $this->owner->getNumberBadgeData()) {
            $fields->fieldByName('Root')->setAttribute('data-number-badges', json_encode($badges));
        }
    }
    
    /**
     * Updates the CMS settings fields of the extended object.
     *
     * @param FieldList $fields List of CMS settings fields from the extended object.
     *
     * @return void
     */
    public function updateSettingsFields(FieldList $fields)
    {
        // Define Placeholder:
        
        $placeholder = _t(__CLASS__ . '.DROPDOWNDEFAULT', '(default)');
        
        // Create Settings Fields:
        
        $fields->addFieldToTab(
            'Root.Settings',
            FieldSection::create(
                'AppearanceSettings',
                $this->owner->fieldLabel('AppearanceSettings'),
                [
                    $template = DropdownField::create(
                        'MyTemplateID',
                        $this->owner->fieldLabel('MyTemplateID'),
                        Template::get()->map()
                    )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder),
                    $layout = DropdownField::create(
                        'MyLayoutID',
                        $this->owner->fieldLabel('MyLayoutID'),
                        Layout::get()->map()
                    )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder)
                ]
            )
        );
        
        // Check Permissions and Modify Fields:
        
        if (!Permission::check(['ADMIN', 'SILVERWARE_PAGE_TEMPLATE_CHANGE'])) {
            $fields->makeFieldReadonly($template);
        }
        
        if (!Permission::check(['ADMIN', 'SILVERWARE_PAGE_TEMPLATE_CHANGE'])) {
            $fields->makeFieldReadonly($layout);
        }
    }
    
    /**
     * Updates the field labels of the extended object.
     *
     * @param array $labels Array of field labels from the extended object.
     *
     * @return void
     */
    public function updateFieldLabels(&$labels)
    {
        $labels['MyLayoutID'] = _t(__CLASS__ . '.LAYOUT', 'Layout');
        $labels['MyTemplateID'] = _t(__CLASS__ . '.TEMPLATE', 'Template');
        $labels['AppearanceSettings'] = _t(__CLASS__ . '.APPEARANCE', 'Appearance');
    }
    
    /**
     * Updates the status flags of the extended object.
     *
     * @param array $flags
     *
     * @return void
     */
    public function updateStatusFlags(&$flags)
    {
        if ($value = $this->owner->getNumberBadgeValue()) {
            
            $text = sprintf($this->owner->getNumberBadgeText(), $value);
            
            $flags['number-badge'] = ['text' => $text, 'title' => $text];
            $flags['number-badge-value'] = ['text' => '', 'title' => $value];
            
        }
    }
    
    /**
     * Provides the permissions for the security interface.
     *
     * @return array
     */
    public function providePermissions()
    {
        $category = _t(__CLASS__ . '.PERMISSION_CATEGORY', 'SilverWare pages');
        
        return [
            'SILVERWARE_PAGE_TEMPLATE_CHANGE' => [
                'category' => $category,
                'name' => _t(__CLASS__ . '.PERMISSION_TEMPLATE_CHANGE_NAME', 'Change templates for pages'),
                'help' => _t(__CLASS__ . '.PERMISSION_TEMPLATE_CHANGE_HELP', 'Ability to change templates for pages.'),
                'sort' => 100
            ],
            'SILVERWARE_PAGE_LAYOUT_CHANGE' => [
                'category' => $category,
                'name' => _t(__CLASS__ . '.PERMISSION_LAYOUT_CHANGE_NAME', 'Change layouts for pages'),
                'help' => _t(__CLASS__ . '.PERMISSION_LAYOUT_CHANGE_HELP', 'Ability to change layouts for pages.'),
                'sort' => 200
            ],
            'SILVERWARE_PAGE_SETTINGS_CHANGE' => [
                'category' => $category,
                'name' => _t(__CLASS__ . '.PERMISSION_SETTINGS_CHANGE_NAME', 'Change settings for pages'),
                'help' => _t(__CLASS__ . '.PERMISSION_SETTINGS_CHANGE_HELP', 'Ability to change settings for pages.'),
                'sort' => 300
            ]
        ];
    }
    
    /**
     * Answers an array of number badge data for the CMS tabs.
     *
     * @return array
     */
    public function getNumberBadgeData()
    {
        return [];
    }
    
    /**
     * Answers the text to display as a site tree number badge.
     *
     * @return string
     */
    public function getNumberBadgeText()
    {
        return '%d';
    }
    
    /**
     * Answers the value to display as a site tree number badge.
     *
     * @return integer
     */
    public function getNumberBadgeValue()
    {
        return 0;
    }
    
    /**
     * Answers true if the extended object has a page layout defined.
     *
     * @return boolean
     */
    public function hasPageLayout()
    {
        return (boolean) $this->owner->MyLayoutID;
    }
    
    /**
     * Answers the layout defined for the extended object.
     *
     * @return Layout
     */
    public function getPageLayout()
    {
        // Obtain Page Layout:
        
        if ($this->hasPageLayout()) {
            $layout = $this->owner->MyLayout();
        } else {
            $layout = $this->getDefaultLayout();
        }
        
        // Answer Page Layout (if available and enabled):
        
        if ($layout && $layout->isEnabled()) {
            return $layout;
        }
    }
    
    /**
     * Answers the default layout defined for the extended object.
     *
     * @return Layout
     */
    public function getDefaultLayout()
    {
        return SiteConfig::current_site_config()->getLayoutForPage($this->owner);
    }
    
    /**
     * Answers true if the extended object has a page template defined.
     *
     * @return boolean
     */
    public function hasPageTemplate()
    {
        return (boolean) $this->owner->MyTemplateID;
    }
    
    /**
     * Answers the template defined for the extended object.
     *
     * @return Template
     */
    public function getPageTemplate()
    {
        // Obtain Page Template:
        
        if ($this->hasPageTemplate()) {
            $template = $this->owner->MyTemplate();
        } else {
            $template = $this->getDefaultTemplate();
        }
        
        // Answer Page Template (if available and enabled):
        
        if ($template && $template->isEnabled()) {
            return $template;
        }
    }
    
    /**
     * Answers the default template defined for the extended object.
     *
     * @return Template
     */
    public function getDefaultTemplate()
    {
        return SiteConfig::current_site_config()->getTemplateForPage($this->owner);
    }
    
    /**
     * Answers a string of content class names for the HTML template.
     *
     * @return string
     */
    public function getContentClass()
    {
        return ViewTools::singleton()->array2att($this->owner->getContentClassNames());
    }
    
    /**
     * Answers an array of content class names for the HTML template.
     *
     * @return array
     */
    public function getContentClassNames()
    {
        $classes = $this->styles('content');
        
        $this->owner->extend('updateContentClassNames', $classes);
        
        return $classes;
    }
    
    /**
     * Answers an array of custom CSS required for the template.
     *
     * @return array
     */
    public function getCustomCSS()
    {
        // Create CSS Array:
        
        $css = [];
        
        // Apply Extensions:
        
        $this->owner->extend('updateCustomCSS', $css);
        
        // Answer CSS Array:
        
        return $css;
    }
    
    /**
     * Answers a list of the enabled components within the extended object.
     *
     * @return ArrayList
     */
    public function getEnabledComponents()
    {
        // Create Components List:
        
        $components = ArrayList::create();
        
        // Merge Template Components:
        
        if ($template = $this->getPageTemplate()) {
            $components->merge($template->getEnabledComponents());
        }
        
        // Merge Layout Components:
        
        if ($layout = $this->getPageLayout()) {
            $components->merge($layout->getEnabledComponents());
        }
        
        // Answer Components List:
        
        return $components;
    }
    
    /**
     * Answers the HTML tag attributes for the body as a string.
     *
     * @return string
     */
    public function getBodyAttributesHTML()
    {
        return SiteConfig::current_site_config()->getBodyAttributesHTML();
    }
    
    /**
     * Answers the value of the specified attribute from the extended object or an ancestor.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function getFieldFromHierarchy($name)
    {
        return !is_null($this->owner->$name) ? $this->owner->$name : $this->owner->getFieldFromParent($name);
    }
    
    /**
     * Converts the extended object into a link object.
     *
     * @param string $nameField
     *
     * @return Link
     */
    public function toLink($nameField = 'MenuTitle')
    {
        return Link::create()->fromPage($this->owner, $nameField);
    }
}
