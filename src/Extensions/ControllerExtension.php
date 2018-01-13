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
use SilverStripe\Core\ClassInfo;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Extension;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Core\Manifest\ModuleResourceLoader;
use SilverStripe\Security\Security;
use SilverStripe\View\Requirements;
use SilverWare\Grid\Grid;
use SilverWare\Tools\ClassTools;
use SilverWare\Tools\ViewTools;
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
     * Answers an array of files required by given configuration array.
     *
     * @param array $config
     *
     * @return array
     */
    public function getRequiredFiles($config)
    {
        $files = [];
        
        foreach ($config as $key => $name) {
            
            if (is_numeric($key) && is_string($name)) {
                
                $files[] = $name;
                
            } elseif (is_numeric($key) && is_array($name)) {
                
                foreach ($name as $module => $file) {
                    $files[] = sprintf('%s: %s', $module, $file);
                }
                
            } elseif (!is_numeric($key) && is_array($name)) {
                
                foreach ($name as $file) {
                    $files[] = sprintf('%s: %s', $key, $file);
                }
                
            }
            
        }
        
        return $files;
    }
    
    /**
     * Answers an array of JavaScript files required by the extended object.
     *
     * @return array
     */
    public function getRequiredJS()
    {
        return $this->owner->getRequiredFiles($this->owner->config()->required_js);
    }
    
    /**
     * Answers an array of all JavaScript files required by the content controllers of the app.
     *
     * @return array
     */
    public function getRequiredJSFiles()
    {
        $files = [];
        
        foreach (ClassInfo::subclassesFor(ContentController::class) as $controller) {
            
            if ($required_js = Injector::inst()->get($controller)->getRequiredJS()) {
                
                foreach ($required_js as $file) {
                    
                    $file = ModuleResourceLoader::singleton()->resolvePath($file);
                    
                    if (!isset($files[$file])) {
                        $files[$file] = file_get_contents(Director::getAbsFile($file));
                    }
                    
                }
                
            }
            
        }
        
        return $files;
    }
    
    /**
     * Answers an array of CSS files required by the extended object.
     *
     * @return array
     */
    public function getRequiredCSS()
    {
        return $this->owner->getRequiredFiles($this->owner->config()->required_css);
    }
    
    /**
     * Answers an array of all CSS files required by the content controllers of the app.
     *
     * @return array
     */
    public function getRequiredCSSFiles()
    {
        $files = [];
        
        foreach (ClassInfo::subclassesFor(ContentController::class) as $controller) {
            
            if ($required_css = Injector::inst()->get($controller)->getRequiredCSS()) {
                
                foreach ($required_css as $file) {
                    
                    $file = ModuleResourceLoader::singleton()->resolvePath($file);
                    
                    if (!isset($files[$file])) {
                        $files[$file] = file_get_contents(Director::getAbsFile($file));
                    }
                    
                }
                
            }
            
        }
        
        return $files;
    }
    
    /**
     * Answers the custom CSS required for the template as a string.
     *
     * @return string
     */
    public function getCustomCSSAsString()
    {
        // Create CSS Array:
        
        $css = [];
        
        // Merge Custom CSS from Page Controller:
        
        if ($this->owner instanceof PageController) {
            $css = array_merge($css, $this->owner->getCustomCSS());
        }
        
        // Create CSS String:
        
        $css = implode("\n", $css);
        
        // Remove Empty Lines:
        
        $css = ViewTools::singleton()->removeEmptyLines($css);
        
        // Trim CSS String:
        
        $css = trim($css);
        
        // Minify CSS String:
        
        if (!Director::isDev()) {
            $css = ViewTools::singleton()->minifyCSS($css);
        }
        
        // Answer CSS String:
        
        return $css;
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
                
                $protocol = function ($config) {
                    if (isset($config['https']) && $config['https'] !== 'auto') {
                        return sprintf('%s:', $config['https'] ? 'https' : 'http');
                    }
                };
                
                // Answer URL String:
                
                return sprintf(
                    '%s//%s:%d/%s',
                    $protocol($config),
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
        
        if ($this->owner->config()->load_themed_css || $this->isDevServerFallback()) {
            
            // Load Themed CSS:
            
            foreach ($this->owner->config()->required_themed_css as $name) {
                $this->loadThemedCSS($name);
            }
            
        }
        
        // Regular JavaScript Enabled?
        
        if ($this->owner->config()->load_js) {
            
            // Load Regular JavaScript:
            
            foreach ($this->owner->getRequiredJS() as $name) {
                $this->loadJS($name);
            }
            
        }
        
        // Regular CSS Enabled?
        
        if ($this->owner->config()->load_css) {
            
            // Load Regular CSS:
            
            foreach ($this->owner->getRequiredCSS() as $name) {
                $this->loadCSS($name);
            }
            
        }
        
        // Load Page Controller Requirements:
        
        if ($this->owner instanceof PageController) {
            
            // Custom CSS Enabled?
            
            if ($this->owner->config()->load_custom_css) {
                
                // Load Custom CSS:
                
                if ($css = $this->getCustomCSSAsString()) {
                    $this->loadCustomCSS($css);
                }
                
            }
            
        }
        
        // Combine Files (dev only):
        
        $this->combineFiles();
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
     * Answers true if the dev server is not active and fallback mode is enabled.
     *
     * @return boolean
     */
    protected function isDevServerFallback()
    {
        $config = $this->getDevServerConfig();
        
        return (!$this->isDevServerActive() && isset($config['fallback']) && $config['fallback']);
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
     * Loads the given custom CSS string.
     *
     * @param string $css Custom CSS.
     *
     * @return void
     */
    protected function loadCustomCSS($css)
    {
        Requirements::customCSS($css);
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
     * Combines required files together for bundling with the theme.
     *
     * @return void
     */
    protected function combineFiles()
    {
        if (Director::isDev() && $this->owner->config()->combine_files) {
            
            // Obtain Tools:
            
            $tools = ViewTools::singleton();
            
            // Combine Files:
            
            $tools->combineFiles($this->owner->config()->combined_js,  $this->owner->getRequiredJSFiles());
            $tools->combineFiles($this->owner->config()->combined_css, $this->owner->getRequiredCSSFiles());
            
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
