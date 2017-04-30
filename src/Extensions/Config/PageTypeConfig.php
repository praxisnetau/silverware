<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Extensions\Config
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\Extensions\Config;

use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverWare\Extensions\ConfigExtension;
use SilverWare\Model\Layout;
use SilverWare\Model\PageType;
use SilverWare\Model\Template;
use SilverWare\Tools\ClassTools;

/**
 * A config extension which adds page type settings to site configuration.
 *
 * @package SilverWare\Extensions\Config
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class PageTypeConfig extends ConfigExtension
{
    /**
     * Defines the has-many associations for this object.
     *
     * @var array
     * @config
     */
    private static $has_many = [
        'AppPageTypes' => PageType::class
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
        // Update Field Objects (from parent):
        
        parent::updateCMSFields($fields);
        
        // Create Page Types Grid Field:
        
        $types = GridField::create(
            'AppPageTypes',
            $this->owner->fieldLabel('AppPageTypes'),
            $this->owner->AppPageTypes(),
            GridFieldConfig_RecordEditor::create()
        );
        
        // Create Page Types Tab:
        
        $fields->findOrMakeTab('Root.SilverWare.PageTypes', $this->owner->fieldLabel('AppPageTypes'));
        
        // Add Grid Field to Page Types Tab:
        
        $fields->addFieldToTab('Root.SilverWare.PageTypes', $types);
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
        // Update Field Labels (from parent):
        
        parent::updateFieldLabels($labels);
        
        // Update Field Labels:
        
        $labels['AppPageTypes'] = _t(__CLASS__ . '.PAGETYPES', 'Page Types');
    }
    
    /**
     * Answers the template for the given page object.
     *
     * @param SiteTree $page
     *
     * @return Template
     */
    public function getTemplateForPage(SiteTree $page)
    {
        return $this->getTemplateForClass($page->class);
    }
    
    /**
     * Answers the layout for the given page object.
     *
     * @param SiteTree $page
     *
     * @return Layout
     */
    public function getLayoutForPage(SiteTree $page)
    {
        return $this->getLayoutForClass($page->class);
    }
    
    /**
     * Answers the template for the given page class.
     *
     * @param string $class
     *
     * @return Template
     */
    public function getTemplateForClass($class)
    {
        foreach (ClassTools::singleton()->getReverseAncestry($class) as $name) {
            
            if ($type = PageType::findByClass($name)) {
                
                if ($type->hasPageTemplate()) {
                    return $type->getPageTemplate();
                }
                
            }
            
            if ($name == SiteTree::class) {
                break;
            }
            
        }
    }
    
    /**
     * Answers the layout for the given page class.
     *
     * @param string $class
     *
     * @return Layout
     */
    public function getLayoutForClass($class)
    {
        foreach (ClassTools::singleton()->getReverseAncestry($class) as $name) {
            
            if ($type = PageType::findByClass($name)) {
                
                if ($type->hasPageLayout()) {
                    return $type->getPageLayout();
                }
                
            }
            
            if ($name == SiteTree::class) {
                break;
            }
            
        }
    }
}
