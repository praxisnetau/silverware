<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Dev
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\Dev;

use Composer\Console\HtmlOutputFormatter;
use Composer\IO\IOInterface;
use Composer\Json\JsonFile;
use Composer\Script\Event;
use ErrorException;
use InvalidArgumentException;

/**
 * Singleton class which handles Composer script events and installation tasks.
 *
 * @package SilverWare\Dev
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class Installer
{
    /**
     * Name of installer configuration file.
     *
     * @var string
     * @const
     */
    const CONFIG_FILE = 'installer.json';
    
    /**
     * Singleton instance of Installer.
     *
     * @var Installer
     */
    private static $instance;
    
    /**
     * Default configuration.
     *
     * Create a JSON config file (installer.json) in the app root folder to customise.
     *
     * Symfony console colors:
     * - available: black, red, green, yellow, blue, magenta, cyan and white.
     * - use a value of 'none' to disable color for an individual item.
     *
     * @var array
     */
    private static $default_config = [
        'text-header' => 'SilverWare App Installer',
        'text-install-success' => 'Installation successful!',
        'text-install-failure' => 'Installation failed!',
        'text-file-success' => '✔',
        'text-file-failure' => '✘',
        'fg-header' => 'cyan',
        'fg-question' => 'green',
        'fg-default' => 'none',
        'fg-type' => 'magenta',
        'fg-writing' => 'white',
        'fg-success' => 'green',
        'fg-failure' => 'red',
        'default-name' => 'app',
        'default-locale' => 'en_GB',
        'default-database' => '{app-name}',
        'default-vendor' => 'vendor',
        'default-repo-name' => '{default-vendor}/app-{app-name}',
        'default-repo-url' => 'git@bitbucket.org:{repo-name}.git',
        'default-user' => 'serverpilot',
        'default-host-staging' => '{app-name}-staging.example.com',
        'default-host-production' => '{app-name}-production.example.com',
        'default-path-staging' => '/srv/users/{user-staging}/apps/{app-name}-staging/deploy',
        'default-path-production' => '/srv/users/{user-production}/apps/{app-name}-production/deploy',
        'default-branch-staging' => 'master',
        'default-branch-production' => 'master',
        'default-app-namespace' => 'SilverWare\App',
        'default-app-title' => 'SilverWare App',
        'default-app-tagline' => '',
        'format-type' => ' [%s]',
        'format-default' => ' (default "%s")',
        'use-colors' => true,
        'files' => [
            'app/_config/config.yml',
            'app/_config.php',
            'deploy.php',
            'deploy.yml'
        ],
        'namespace-files' => [
            'composer.json' => 1,
            'app/fixtures/types.yml' => 0,
            'app/src/Pages/HomePage.php' => 0,
            'app/src/Pages/HomePageController.php' => 0
        ]
    ];
    
    /**
     * Composer script event instance.
     *
     * @var Event
     */
    protected $event;
    
    /**
     * Installer configuration.
     *
     * @var array
     */
    protected $config;
    
    /**
     * Maps script event names to method names.
     *
     * @var array
     */
    protected $handlers = [
        'post-create-project-cmd' => 'onPostCreateProject'
    ];
    
    /**
     * Answers the singleton instance of Installer.
     *
     * @return Installer
     */
    public static function inst()
    {
        if (!self::$instance) {
            self::$instance = new Installer();
        }
        
        return self::$instance;
    }
    
    /**
     * Event handler for script events triggered by Composer.
     *
     * @param Event $event
     *
     * @return void
     */
    public static function handleEvent(Event $event)
    {
        self::inst()->handle($event);
    }
    
    /**
     * Handles the given Composer script event.
     *
     * @param Event $event
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    public function handle(Event $event)
    {
        // Define Event:
        
        $this->setEvent($event);
        
        // Check Event Name:
        
        if (!$this->hasHandler($event->getName())) {
            
            throw new InvalidArgumentException(
                sprintf(
                    'An event handler does not exist for "%s"',
                    $event->getName()
                )
            );
            
        }
        
        // Handle Event:
        
        $this->{$this->handlers[$event->getName()]}();
    }
    
    /**
     * Handles a Composer 'post-create-project-cmd' script event.
     *
     * @return void
     */
    public function onPostCreateProject()
    {
        // Initialise:
        
        $data = [];
        
        // Output Header:
        
        $this->header($this->getConfig('text-header'));
        
        // Obtain App Name:
        
        $data['app-name'] = $this->ask(
            'App name',
            $this->getConfig('default-name'),
            $data
        );
        
        // Obtain Locale:
        
        $data['locale'] = $this->ask(
            'Default locale',
            $this->getConfig('default-locale'),
            $data
        );
        
        // Obtain Repository Name:
        
        $data['repo-name'] = $this->ask(
            'Repository name',
            $this->getConfig('default-repo-name'),
            $data
        );
        
        // Obtain Repository URL:
        
        $data['repo-url'] = $this->ask(
            'Repository URL',
            $this->getConfig('default-repo-url'),
            $data
        );
        
        // Obtain Repository Branches:
        
        $data['branch-staging'] = $this->ask(
            'Repository branch:staging',
            $this->getConfig('default-branch-staging'),
            $data
        );
        
        $data['branch-production'] = $this->ask(
            'Repository branch:production',
            $this->getConfig('default-branch-production'),
            $data
        );
        
        // Obtain User Names:
        
        $data['user-staging'] = $this->ask(
            'User name:staging',
            $this->getConfig('default-user'),
            $data
        );
        
        $data['user-production'] = $this->ask(
            'User name:production',
            $this->getConfig('default-user'),
            $data
        );
        
        // Obtain Host Names:
        
        $data['host-staging'] = $this->ask(
            'Host name:staging',
            $this->getConfig('default-host-staging'),
            $data
        );
        
        $data['host-production'] = $this->ask(
            'Host name:production',
            $this->getConfig('default-host-production'),
            $data
        );
        
        // Obtain Deployment Paths:
        
        $data['path-staging'] = $this->ask(
            'Deployment path:staging',
            $this->getConfig('default-path-staging'),
            $data
        );
        
        $data['path-production'] = $this->ask(
            'Deployment path:production',
            $this->getConfig('default-path-production'),
            $data
        );
        
        // Obtain App Namespace:
        
        $data['app-namespace'] = $this->ask(
            'App namespace',
            $this->getConfig('default-app-namespace'),
            $data
        );
        
        // Obtain App Title and Tagline:
        
        $data['app-title'] = $this->ask(
            'App title',
            $this->getConfig('default-app-title'),
            $data
        );
        
        $data['app-tagline'] = $this->ask(
            'App tagline',
            $this->getConfig('default-app-tagline'),
            $data
        );
        
        // Write Namespace and Data:
        
        $result = false;
        
        if ($this->writeNamespace($data['app-namespace'], $this->getConfig('namespace-files'))) {
            $result = $this->writeData($data, $this->getConfig('files'));
        }
        
        // Handle Installation Result:
        
        if ($result) {
            
            // Report Success:
            
            $this->io()->write(
                $this->color(
                    $this->getConfig('text-install-success'),
                    $this->getConfig('fg-success')
                )
            );
            
        } else {
            
            // Report Failure:
            
            $this->io()->write(
                $this->color(
                    $this->getConfig('text-install-failure'),
                    $this->getConfig('fg-failure')
                )
            );
            
        }
    }
    
    /**
     * Defines the value of the event attribute.
     *
     * @param Event $event
     *
     * @return $this
     */
    public function setEvent(Event $event)
    {
        $this->event = $event;
        
        return $this;
    }
    
    /**
     * Answers the value of the event attribute.
     *
     * @return Event
     */
    public function getEvent()
    {
        return $this->event;
    }
    
    /**
     * Defines either the named config value, or the config array.
     *
     * @param string|array $arg1
     * @param string $arg2
     *
     * @return $this
     */
    public function setConfig($arg1, $arg2 = null)
    {
        if (is_array($arg1)) {
            $this->config = $arg1;
        } else {
            $this->config[$arg1] = $arg2;
        }
        
        return $this;
    }
    
    /**
     * Answers either the named config value, or the config array.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function getConfig($name = null)
    {
        if (!is_null($name)) {
            return isset($this->config[$name]) ? $this->config[$name] : null;
        }
        
        return $this->config;
    }
    
    /**
     * Merges the given array of configuration with the existing configuration.
     *
     * @param array $config
     *
     * @return $this
     */
    public function mergeConfig($config = [])
    {
        $this->config = array_merge($this->config, $config);
        
        return $this;
    }
    
    /**
     * Answers true if a handler is defined for the given event name.
     *
     * @param string $name
     *
     * @return boolean
     */
    public function hasHandler($name)
    {
        return isset($this->handlers[$name]);
    }
    
    /**
     * Answers the name of the current script event.
     *
     * @return string
     */
    public function getEventName()
    {
        return $this->event->getName();
    }
    
    /**
     * Constructs the object upon instantiation (private, in accordance with singleton pattern).
     */
    private function __construct()
    {
        $this->init();
    }
    
    /**
     * Initialises the receiver from configuration.
     *
     * @return void
     */
    private function init()
    {
        // Define Default Config:
        
        $this->setConfig(self::$default_config);
        
        // Load JSON Config File:
        
        $file = new JsonFile(self::CONFIG_FILE);
        
        // Merge JSON Config (if file exists):
        
        if ($file->exists()) {
            $this->mergeConfig($file->read());
        }
    }
    
    /**
     * Answers the IO interface instance from the current script event.
     *
     * @return IOInterface
     */
    private function io()
    {
        return $this->event->getIO();
    }
    
    /**
     * Asks a question with an optional default value to the developer.
     *
     * @param string $question Question to ask the developer (with optional type).
     * @param string $default Optional default value for the answer.
     * @param array $data Array of data values for token replacement.
     *
     * @return string
     */
    private function ask($question, $default = null, $data = [])
    {
        // Initialise:
        
        $text = [];
        
        // Process Question:
        
        list($question, $type) = array_pad(explode(':', $question), 2, null);
        
        // Add Question:
        
        $text[] = $this->color(
            $question,
            $this->getConfig('fg-question')
        );
        
        // Add Type:
        
        if ($type) {
            
            $text[] = $this->color(
                sprintf($this->getConfig('format-type'), $type),
                $this->getConfig('fg-type')
            );
            
        }
        
        // Add Default:
        
        if ($default) {
            
            // Replace Tokens:
            
            $default = $this->replace($default, $data);
            
            // Add Default Text:
            
            $text[] = $this->color(
                sprintf($this->getConfig('format-default'), $default),
                $this->getConfig('fg-default')
            );
            
        }
        
        // Add Colon:
        
        $text[] = ': ';
        
        // Ask Question and Answer:
        
        if ($answer = $this->io()->ask(implode('', $text))) {
            return $answer;
        }
        
        // Answer Default:
        
        return $default;
    }
    
    /**
     * Applies the given foreground and background colors to the provided text.
     *
     * @param string $text Text to color.
     * @param string $fg Foreground color.
     * @param string $bg Background color (optional).
     *
     * @return string
     */
    private function color($text, $fg, $bg = null)
    {
        if ($this->getConfig('use-colors')) {
            
            $colors = [];
            
            if ($fg != 'none') {
                $colors[] = sprintf('fg=%s', $fg);
            }
            
            if ($bg && $bg != 'none') {
                $colors[] = sprintf('bg=%s', $bg);
            }
            
            if (!empty($colors)) {
                return sprintf('<%s>%s</>', implode(';', $colors), $text);
            }
        }
        
        return $text;
    }
    
    /**
     * Outputs a header block with the given text.
     *
     * @param string $text
     *
     * @return void
     */
    private function header($text)
    {
        $this->io()->write($this->line(strlen($text)));
        $this->io()->write($this->color($text, $this->getConfig('fg-header')));
        $this->io()->write($this->line(strlen($text)));
    }
    
    /**
     * Answers a line string of the specified length consisting of the given character.
     *
     * @param integer $length
     * @param string $char
     *
     * @return string
     */
    private function line($length, $char = '=')
    {
        return str_pad('', $length, $char);
    }
    
    /**
     * Replaces named tokens within the given text with values from the provided array and configuration.
     *
     * @param string $text
     * @param array $data
     *
     * @return string
     */
    private function replace($text, $data)
    {
        // Replace Data Tokens:
        
        foreach ($data as $key => $value) {
            
            if (is_scalar($value)) {
                $text = str_replace("{{$key}}", $value, $text);
            }
            
        }
        
        // Replace Config Tokens:
        
        foreach ($this->config as $key => $value) {
            
            if (is_scalar($value)) {
                $text = str_replace("{{$key}}", $value, $text);
            }
            
        }
        
        // Answer Text:
        
        return $text;
    }
    
    /**
     * Replaces the default namespace within the given text with the given namespace.
     *
     * @param string $text
     * @param string $namespace
     * @param boolean $escape
     *
     * @return string
     */
    private function replaceNamespace($text, $namespace, $escape = false)
    {
        // Clean Values:
        
        $default   = rtrim($this->getConfig('default-app-namespace'), '\\');
        $namespace = rtrim($namespace, '\\');
        
        // Handle Escaping:
        
        if ($escape) {
            $default   = addslashes($default);
            $namespace = addslashes($namespace);
        }
        
        // Replace Namespace:
        
        $text = str_replace($default, $namespace, $text);
        
        // Answer Text:
        
        return $text;
    }
    
    /**
     * Writes values from the given data array to the specified files.
     *
     * @param array $data
     * @param array $files
     *
     * @throws ErrorException
     *
     * @return boolean
     */
    private function writeData($data, $files)
    {
        // Initialise:
        
        $result = true;
        
        // Check Parameters:
        
        if (is_array($data) && is_array($files)) {
            
            // Iterate Files:
            
            foreach ($files as $file) {
                
                // Attempt to Process File:
                
                try {
                    
                    // Read File Contents:
                    
                    $contents = file_get_contents($file);
                    
                    // Replace Tokens in Contents:
                    
                    $contents = $this->replace($contents, $data);
                    
                    // Write File Contents:
                    
                    file_put_contents($file, $contents);
                    
                    // Define Status:
                    
                    $status = 'success';
                    
                } catch (ErrorException $e) {
                    
                    // Write Debug Info:
                    
                    $this->io()->writeError(
                        sprintf(
                            '<warning>Exception: %s</warning>',
                            $e->getMessage()
                        ),
                        true,
                        IOInterface::DEBUG
                    );
                    
                    // Define Result:
                    
                    $result = false;
                    
                    // Define Status:
                    
                    $status = 'failure';
                    
                }
                
                // Output Status:
                
                $this->io()->write(
                    sprintf(
                        '[%s] %s',
                        $this->color($this->getConfig("text-file-{$status}"), $this->getConfig("fg-{$status}")),
                        $this->color("Writing to '{$file}'...", $this->getConfig('fg-writing'))
                    )
                );
                
            }
            
        }
        
        // Answer Result:
        
        return $result;
    }
    
    /**
     * Writes the app namespace to the specified files.
     *
     * @param string $namespace
     * @param array $files
     *
     * @throws ErrorException
     *
     * @return boolean
     */
    private function writeNamespace($namespace, $files)
    {
        // Initialise:
        
        $result = true;
        
        // Check Namespace Change:
        
        if ($namespace == $this->getConfig('default-app-namespace')) {
            return $result;
        }
        
        // Check Parameters:
        
        if ($namespace && is_array($files)) {
            
            // Iterate Files:
            
            foreach ($files as $file => $escape) {
                
                // Attempt to Process File:
                
                try {
                    
                    // Read File Contents:
                    
                    $contents = file_get_contents($file);
                    
                    // Replace Namespace in Contents:
                    
                    $contents = $this->replaceNamespace($contents, $namespace, $escape);
                    
                    // Write File Contents:
                    
                    file_put_contents($file, $contents);
                    
                    // Define Status:
                    
                    $status = 'success';
                    
                } catch (ErrorException $e) {
                    
                    // Write Debug Info:
                    
                    $this->io()->writeError(
                        sprintf(
                            '<warning>Exception: %s</warning>',
                            $e->getMessage()
                        ),
                        true,
                        IOInterface::DEBUG
                    );
                    
                    // Define Result:
                    
                    $result = false;
                    
                    // Define Status:
                    
                    $status = 'failure';
                    
                }
                
                // Output Status:
                
                $this->io()->write(
                    sprintf(
                        '[%s] %s',
                        $this->color($this->getConfig("text-file-{$status}"), $this->getConfig("fg-{$status}")),
                        $this->color("Writing namespace to '{$file}'...", $this->getConfig('fg-writing'))
                    )
                );
                
            }
            
        }
        
        // Answer Result:
        
        return $result;
    }
}
