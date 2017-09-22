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

use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;
use SilverStripe\View\SSViewer;
use SilverWare\Tools\ViewTools;
use SilverWare\View\GridAware;

/**
 * A data extension which adds style settings to the extended object.
 *
 * @package SilverWare\Extensions
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class StyleExtension extends DataExtension
{
    use GridAware;
    
    /**
     * Maps field and method names to the class names of casting objects.
     *
     * @var array
     * @config
     */
    private static $casting = [
        'CustomCSSPrefix' => 'HTMLFragment'
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
        // Create Tab:
        
        $fields->findOrMakeTab('Root.Style', $this->owner->fieldLabel('Style'));
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
        $labels['Style'] = _t(__CLASS__ . '.STYLE', 'Style');
    }
    
    /**
     * Updates the array of custom CSS for the extended object.
     *
     * @param array $css
     *
     * @return void
     */
    public function updateCustomCSS(&$css)
    {
        $template = $this->owner->getStyleExtensionTemplate(static::class);
        
        if (SSViewer::hasTemplate($template)) {
            $css = ViewTools::singleton()->renderCSS($this->owner, $template, $css);
        }
    }
    
    /**
     * Answers the CSS prefix used for the custom CSS template.
     *
     * @return string
     */
    public function getCustomCSSPrefix()
    {
        return $this->owner->CSSID;
    }
    
    /**
     * Answers the template for the style extension with the given class.
     *
     * @param string $class
     *
     * @return string
     */
    public function getStyleExtensionTemplate($class)
    {
        return sprintf('%s\CustomCSS', $class);
    }
    
    /**
     * Answers true if this extension should apply styles to the extended object.
     *
     * @return boolean
     */
    protected function apply()
    {
        return $this->hasAppliedStyles() ? in_array(static::class, $this->getAppliedStyles()) : true;
    }
    
    /**
     * Answers true if the extended object has applied styles configuration.
     *
     * @return boolean
     */
    public function hasAppliedStyles()
    {
        return is_array($this->getAppliedStyles());
    }
    
    /**
     * Answers an array of the style extension classes to be applied to the extended object.
     *
     * @return array
     */
    protected function getAppliedStyles()
    {
        $applyStyles = $this->owner->config()->apply_styles;
        
        if (is_array($applyStyles)) {
            return $applyStyles;
        } elseif ($applyStyles == 'none') {
            return [];
        }
    }
}
