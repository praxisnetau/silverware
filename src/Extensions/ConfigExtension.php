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

use SilverStripe\Control\Director;
use SilverStripe\Core\Convert;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TabSet;
use SilverStripe\ORM\DataExtension;
use SilverStripe\SiteConfig\SiteConfig;
use SilverWare\Tools\ViewTools;
use Page;

/**
 * A data extension which adds SilverWare settings to site configuration.
 *
 * @package SilverWare\Extensions
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class ConfigExtension extends DataExtension
{
    /**
     * Updates the CMS fields of the extended object.
     *
     * @param FieldList $fields List of CMS fields from the extended object.
     *
     * @return void
     */
    public function updateCMSFields(FieldList $fields)
    {
        // Create SilverWare Tab Set:
        
        if (!$fields->fieldByName('Root.SilverWare')) {
            
            $fields->addFieldToTab(
                'Root',
                TabSet::create(
                    'SilverWare',
                    $this->owner->fieldLabel('SilverWare')
                )
            );
            
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
        $labels['SilverWare'] = _t(__CLASS__ . '.SILVERWARE', 'SilverWare');
    }
    
    /**
     * Answers the HTML tag attributes for the body as an array.
     *
     * @return array
     */
    public function getBodyAttributes()
    {
        return [];
    }
    
    /**
     * Answers all body attributes from config extension subclasses as an array.
     *
     * @return array
     */
    public function getAllBodyAttributes()
    {
        $attributes = ['class' => $this->getCurrentPageAncestry()];
        
        foreach ($this->owner->getExtensionInstances() as $class => $extension) {
            
            if ($extension instanceof ConfigExtension) {
                $attributes = array_merge($attributes, $extension->getBodyAttributes());
            }
            
        }
        
        return $attributes;
    }
    
    /**
     * Answers the HTML tag attributes for the body as a string.
     *
     * @return string
     */
    public function getBodyAttributesHTML()
    {
        return ViewTools::singleton()->getAttributesHTML($this->owner->getAllBodyAttributes());
    }
    
    /**
     * Answers the current site config object.
     *
     * @return SiteConfig
     */
    public function getSiteConfig()
    {
        return SiteConfig::current_site_config();
    }
    
    /**
     * Answers the asset folder used by the receiver.
     *
     * @return string
     */
    protected function getAssetFolder()
    {
        $folders = $this->owner->config()->asset_folders;
        
        if (is_array($folders) && isset($folders[static::class])) {
            return $folders[static::class];
        }
    }
    
    /**
     * Answers a string containing the current page ancestry for the HTML template.
     *
     * @return string
     */
    protected function getCurrentPageAncestry()
    {
        $tools = ViewTools::singleton();
        
        if (($page = Director::get_current_page()) && $page instanceof Page) {
            return $tools->array2att($tools->getAncestorClassNames($page, Page::class));
        }
    }
}
