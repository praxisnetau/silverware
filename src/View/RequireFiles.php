<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\View
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\View;

use SilverStripe\Core\Injector\Injector;
use SilverStripe\ORM\ArrayLib;
use SilverStripe\View\Requirements;
use SilverWare\Tools\ViewTools;

/**
 * Allows an object to use the configuration system to require files.
 *
 * @package SilverWare\View
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
trait RequireFiles
{
    /**
     * An array of required JavaScript files.
     *
     * @var array
     * @config
     */
    private static $required_js = [];
    
    /**
     * An array of required CSS files.
     *
     * @var array
     * @config
     */
    private static $required_css = [];
    
    /**
     * An array of required themed JavaScript files.
     *
     * @var array
     * @config
     */
    private static $required_themed_js = [];
    
    /**
     * An array of required themed CSS files.
     *
     * @var array
     * @config
     */
    private static $required_themed_css = [];
    
    /**
     * An array of required JavaScript template files.
     *
     * @var array
     * @config
     */
    private static $required_js_templates = [];
    
    /**
     * Loads the requirements for the object.
     *
     * @return void
     */
    public function loadRequirements()
    {
        // Load Required CSS:
        
        foreach ($this->getRequiredCSS() as $css => $media) {
            Requirements::css($css, $media);
        }
        
        // Load Required Themed CSS:
        
        foreach ($this->getRequiredThemedCSS() as $css => $media) {
            Requirements::themedCSS($css, $media);
        }
        
        // Load Required JavaScript:
        
        foreach ($this->getRequiredJS() as $js) {
            Requirements::javascript($js);
        }
        
        // Load Required Themed JavaScript:
        
        foreach ($this->getRequiredThemedJS() as $js) {
            Requirements::themedJavascript($js);
        }
        
        // Load Required JavaScript Templates:
        
        foreach ($this->getRequiredJSTemplates() as $file => $params) {
            ViewTools::singleton()->loadJSTemplate($file, $params['vars'], $params['id']);
        }
    }
    
    /**
     * Answers an array of JavaScript files required by the object.
     *
     * @return array
     */
    public function getRequiredJS()
    {
        $js = $this->config()->required_js;
        
        $this->extend('updateRequiredJS', $js);
        
        return $js;
    }
    
    /**
     * Answers an array of CSS files required by the object.
     *
     * @return array
     */
    public function getRequiredCSS()
    {
        $css = $this->config()->required_css;
        
        $this->extend('updateRequiredCSS', $css);
        
        return $this->processCSSConfig($css);
    }
    
    /**
     * Answers an array of themed JavaScript files required by the object.
     *
     * @return array
     */
    public function getRequiredThemedJS()
    {
        $js =  $this->config()->required_themed_js;
        
        $this->extend('updateRequiredThemedJS', $js);
        
        return $js;
    }
    
    /**
     * Answers an array of themed CSS files required by the object.
     *
     * @return array
     */
    public function getRequiredThemedCSS()
    {
        $css =  $this->config()->required_themed_css;
        
        $this->extend('updateRequiredThemedCSS', $css);
        
        return $this->processCSSConfig($css);
    }
    
    /**
     * Answers an array of JavaScript templates required by the object.
     *
     * @return array
     */
    public function getRequiredJSTemplates()
    {
        $js = $this->config()->required_js_templates;
        
        $this->extend('updateRequiredJSTemplates', $js);
        
        return $this->processJSTemplateConfig($js);
    }
    
    /**
     * Answers an array of variables required by a JavaScript template.
     *
     * @return array
     */
    public function getJSVars()
    {
        if (in_array(Renderable::class, class_uses(self::class))) {
            return ['HTMLID' => $this->getHTMLID(), 'CSSID' => $this->getCSSID()];
        }
        
        return [];
    }
    
    /**
     * Processes the given CSS config and answers an array suitable for loading requirements.
     *
     * @param array $config
     *
     * @return array
     */
    protected function processCSSConfig($config)
    {
        if (!ArrayLib::is_associative($config)) {
            return array_fill_keys(array_values($config), null); 
        }
        
        return $config;
    }
    
    /**
     * Processes the given JavaScript template config and answers an array suitable for loading requirements.
     *
     * @param array $config
     *
     * @return array
     */
    protected function processJSTemplateConfig($config)
    {
        $templates = [];
        
        foreach ($config as $key => $value) {
            
            if (is_integer($key)) {
                
                $templates[$value] = [
                    'vars' => $this->getJSVars(),
                    'id'   => $this->getHTMLID()
                ];
                
            } else {
                
                $templates[$key] = [
                    'vars' => $this->getJSTemplateVars(array_shift($value)),
                    'id'   => $this->getJSTemplateID(array_shift($value))
                ];
                
            }
            
        }
        
        return $templates;
    }
    
    /**
     * Answers an array of variables for a required JavaScript template.
     *
     * @param string|array $vars Array of variables or method name.
     *
     * @return array
     */
    protected function getJSTemplateVars($vars)
    {
        if (is_array($vars)) {
            return $vars;
        }
        
        if ($this->hasMethod($vars)) {
            return $this->{$vars}();
        }
        
        return [];
    }
    
    /**
     * Answers the ID for a required JavaScript template.
     *
     * @param string $id ID or method name.
     *
     * @return string
     */
    protected function getJSTemplateID($id)
    {
        if ($this->hasMethod($id)) {
            return $this->{$id}();
        }
        
        return $id;
    }
}
