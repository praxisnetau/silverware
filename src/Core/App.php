<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Core
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\Core;

use SilverStripe\Control\Director;
use SilverStripe\Dev\YamlFixture;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DB;
use SilverStripe\SiteConfig\SiteConfig;
use SilverWare\Model\Folder;
use InvalidArgumentException;
use Exception;

/**
 * The core SilverWare application object.
 *
 * @package SilverWare\Core
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class App extends DataObject
{
    /**
     * Defines the injector dependencies for this object.
     *
     * @var array
     * @config
     */
    private static $dependencies = [
        'factory' => '%$AppFixtureFactory'
    ];
    
    /**
     * Maps field names to field types for this object.
     *
     * @var array
     * @config
     */
    private static $db = [
        'FixturesLoaded' => 'Boolean'
    ];
    
    /**
     * Defines the default values for the fields of this object.
     *
     * @var array
     * @config
     */
    private static $defaults = [
        'FixturesLoaded' => 0
    ];
    
    /**
     * Determines whether the app will load fixtures upon build.
     *
     * @var boolean
     * @config
     */
    private static $load_fixtures = false;
    
    /**
     * Determines whether the app will load fixtures upon build only once, or upon each build.
     *
     * @var boolean
     * @config
     */
    private static $load_fixtures_once = true;
    
    /**
     * Fixtures to be loaded by the app.
     *
     * @var array
     * @config
     */
    private static $fixtures = [];
    
    /**
     * Answers the current app instance and creates one if none is found.
     *
     * @return App
     */
    public static function instance()
    {
        if ($app = self::get_one(self::class)) {
            return $app;
        }
        
        return self::make();
    }
    
    /**
     * Creates a new app instance and answers it.
     *
     * @return App
     */
    public static function make()
    {
        $app = self::create();
        
        $app->write();
        
        return $app;
    }
    
    /**
     * Creates any required default records (if they do not already exist).
     *
     * @return void
     */
    public function requireDefaultRecords()
    {
        // Require Default Records (from parent):
        
        parent::requireDefaultRecords();
        
        // Create Default Folders:
        
        Folder::singleton()->createDefaultFolders();
        
        // Obtain App Instance:
        
        $app = self::instance();
        
        // Load Defaults:
        
        $app->loadDefaults();
        
        // Load Fixtures:
        
        $app->loadFixtures();
    }
    
    /**
     * Loads the defaults defined by application configuration.
     *
     * @return void
     */
    public function loadDefaults()
    {
        // Obtain Default and Current Site Config:
        
        $default = SiteConfig::create();
        $current = SiteConfig::current_site_config();
        
        // Check App Title:
        
        if (!is_null($this->config()->title)) {
            
            // Update Site Title:
            
            if ($current->Title == $default->Title) {
                $current->Title = $this->config()->title;
            }
            
        }
        
        // Check App Tagline:
        
        if (!is_null($this->config()->tagline)) {
            
            // Update Site Tagline:
            
            if ($current->Tagline == $default->Tagline) {
                $current->Tagline = $this->config()->tagline;
            }
            
        }
        
        // Save Site Config Changes:
        
        if ($current->isChanged('Title') || $current->isChanged('Tagline')) {
            
            if ($current->isChanged('Title')) {
                
                DB::alteration_message(
                    sprintf(
                        'Updating App Title to "%s"',
                        $current->Title
                    ),
                    'changed'
                );
                
            }
            
            if ($current->isChanged('Tagline')) {
                
                DB::alteration_message(
                    sprintf(
                        'Updating App Tagline to "%s"',
                        $current->Tagline
                    ),
                    'changed'
                );
                
            }
            
            $current->write();
            
        }
        
    }
    
    /**
     * Loads the fixtures defined by application configuration.
     *
     * @return void
     */
    public function loadFixtures()
    {
        // Using Live Environment?
        
        if ($this->loadFixturesDisabled()) {
            return;
        }
        
        // Initialise:
        
        $errored = false;
        
        // Load Fixture Files:
        
        foreach ($this->config()->fixtures as $file) {
            
            try {
                
                // Attempt Fixture Loading:
                
                YamlFixture::create($file)->writeInto($this->factory);
                
            } catch (Exception $e) {
                
                $errored = true;
                
                $message = $e->getMessage();
                
                if (strpos($message, 'YamlFixture::') === 0 && strpos($message, 'not found') !== false) {
                    $message = sprintf('Cannot load fixture file: %s', $file);
                }
                
                DB::alteration_message(
                    sprintf(
                        'App fixture loading failed with exception: "%s"',
                        $message
                    ),
                    'error'
                );
                
            }
            
        }
        
        if (!$errored) {
            
            // Update App Status:
            
            $this->FixturesLoaded = true;
            
            // Record App Status:
            
            $this->write();
            
        }
    }
    
    /**
     * Answers true if fixture loading is disabled.
     *
     * @return boolean
     */
    protected function loadFixturesDisabled()
    {
        if (!$this->config()->load_fixtures) {
            return true;
        }
        
        if ($this->config()->load_fixtures_once && $this->FixturesLoaded) {
            return true;
        }
        
        return false;
    }
}
