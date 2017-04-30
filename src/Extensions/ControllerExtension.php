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

use SilverStripe\CMS\Controllers\ContentController;
use SilverStripe\Control\Director;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Extension;
use SilverStripe\Security\Security;
use SilverStripe\View\Requirements;
use SilverWare\Grid\Grid;
use SilverWare\Tools\ClassTools;
use PageController;

/**
 * An extension which allows controllers to use SilverWare.
 *
 * @package SilverWare\Extensions
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class ControllerExtension extends Extension
{
    /**
     * Configuration for the webpack development server.
     *
     * @var array
     * @config
     */
    private static $dev_server = [];
    
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
     * Is there a development server currently active?
     *
     * @var boolean
     */
    protected $devServerActive = false;
    
    /**
     * Has the development server connection been tested?
     *
     * @var boolean
     */
    protected $devServerTested = false;
    
    /**
     * Answers true if the render cache is disabled.
     *
     * @return boolean
     */
    public static function isCacheDisabled()
    {
        return (boolean) Config::inst()->get(ContentController::class, 'disable_cache');
    }
    
    /**
     * Event handler method triggered before the extended controller has initialised.
     *
     * @return void
     */
    public function onBeforeInit()
    {
        // Ignore Ajax Requests:
        
        if ($this->isAjaxRequest()) {
            return;
        }
        
        // Initialise Grid:
        
        $this->initGrid();
        
        // Initialise Requirements:
        
        $this->initRequirements();
    }
    
    /**
     * Answers true if a webpack development server is currently being used.
     *
     * @return boolean
     */
    public function isDevServerActive()
    {
        // Using Development Environment?
        
        if (!Director::isDev()) {
            return false;
        }
        
        // Already Checked Server?
        
        if ($this->devServerTested) {
            return $this->devServerActive;
        }
        
        // Test Server Connection:
        
        return $this->testDevServer();
    }
    
    /**
     * Answers the URL for the webpack development server.
     *
     * @param string $path Path to append to the URL.
     *
     * @return string
     */
    public function getDevServerURL($path = null)
    {
        // Using Development Environment?
        
        if (!Director::isDev()) {
            return;
        }
        
        // Obtain Development Server Config:
        
        if ($config = $this->getDevServerConfig()) {
            
            if (isset($config['host']) && isset($config['port'])) {
                
                // Define Protocol:
                
                $protocol = (isset($config['https']) && $config['https']) ? 'https' : 'http';
                
                // Answer URL String:
                
                return sprintf(
                    '%s://%s:%d/%s',
                    $protocol,
                    $config['host'],
                    $config['port'],
                    $path
                );
                
            }
            
        }
    }
    
    /**
     * Answers true if the HTTP request is an Ajax request.
     *
     * @return boolean
     */
    protected function isAjaxRequest()
    {
        return $this->owner->getRequest()->isAjax();
    }
    
    /**
     * Initialises the grid framework defined by configuration.
     *
     * @return void
     */
    protected function initGrid()
    {
        Grid::framework()->doInit();
    }
    
    /**
     * Initialises the requirements for the extended controller.
     *
     * @return void
     */
    protected function initRequirements()
    {
        // Themed JavaScript Enabled?
        
        if ($this->owner->config()->load_themed_js) {
            
            // Load Themed JavaScript:
            
            foreach ($this->owner->config()->required_themed_js as $name) {
                $this->loadThemedJS($name);
            }
            
        }
        
        // Themed CSS Enabled?
        
        if ($this->owner->config()->load_themed_css) {
            
            // Load Themed CSS:
            
            foreach ($this->owner->config()->required_themed_css as $name) {
                $this->loadThemedCSS($name);
            }
            
        }
        
        // Regular JavaScript Enabled?
        
        if ($this->owner->config()->load_js) {
            
            // Load Regular JavaScript:
            
            foreach ($this->owner->config()->required_js as $name) {
                $this->loadJS($name);
            }
            
        }
        
        // Regular CSS Enabled?
        
        if ($this->owner->config()->load_css) {
            
            // Load Regular CSS:
            
            foreach ($this->owner->config()->required_css as $name) {
                $this->loadCSS($name);
            }
            
        }
        
        // Component Requirements Enabled?
        
        if ($this->owner->config()->load_component_requirements) {
            
            // Load Component Requirements:
            
            if ($this->owner instanceof PageController) {
                
                foreach ($this->owner->getEnabledComponents() as $component) {
                    $component->loadRequirements();
                }
                
            }
            
        }
    }
    
    /**
     * Tests whether a connection can be established to the configured development server.
     *
     * @return boolean
     */
    protected function testDevServer()
    {
        if ($config = $this->getDevServerConfig()) {
            
            if (isset($config['host']) && isset($config['port'])) {
                
                // Define Timeout:
                
                $timeout = isset($config['timeout']) ? $config['timeout'] : 10;
                
                // Attempt to Open Connection:
                
                $socket = @fsockopen(
                    $config['host'],
                    $config['port'],
                    $errno,
                    $error,
                    $timeout
                );
                
                // Update Status Attributes:
                
                $this->devServerActive = (!$socket ? false : true);
                $this->devServerTested = true;
                
            }
            
        }
        
        return $this->devServerActive;
    }
    
    /**
     * Answers the development server configuration array.
     *
     * @return array
     */
    protected function getDevServerConfig()
    {
        return $this->owner->config()->dev_server;
    }
    
    /**
     * Loads the regular JavaScript with the given name.
     *
     * @param string $name Name of JavaScript file.
     *
     * @return void
     */
    protected function loadJS($name)
    {
        Requirements::javascript($name);
    }
    
    /**
     * Loads the regular CSS with the given name.
     *
     * @param string $name Name of CSS file.
     *
     * @return void
     */
    protected function loadCSS($name)
    {
        Requirements::css($name);
    }
    
    /**
     * Loads the themed JavaScript with the given name.
     *
     * @param string $name Name of themed JavaScript file.
     *
     * @return void
     */
    protected function loadThemedJS($name)
    {
        if ($this->isDevServerActive()) {
            Requirements::javascript($this->getDevServerURL($this->ext($name, 'js')));
        } else {
            Requirements::themedJavascript($name);
        }
    }
    
    /**
     * Loads the themed CSS with the given name.
     *
     * @param string $name Name of themed CSS file.
     *
     * @return void
     */
    protected function loadThemedCSS($name)
    {
        if ($this->isDevServerActive()) {
            Requirements::css($this->getDevServerURL($this->ext($name, 'css')));
        } else {
            Requirements::themedCSS($name);
        }
    }
    
    /**
     * Applies the given extension to the given file name if it is not already present.
     *
     * @param string $name Filename to process.
     * @param string $ext Required file extension.
     *
     * @return string
     */
    protected function ext($name, $ext)
    {
        // Obtain Info:
        
        $info = pathinfo($name);
        
        // Check Extension:
        
        if (!isset($info['extension']) || $info['extension'] !== $ext) {
            $name = sprintf('%s.%s', $name, $ext);
        }
        
        // Answer Name:
        
        return $name;
    }
}
