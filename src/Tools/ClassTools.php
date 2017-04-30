<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Tools
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\Tools;

use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Core\ClassInfo;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Convert;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Core\Object;
use SilverStripe\ORM\HiddenClass;
use ReflectionException;

/**
 * A singleton providing utility functions for use with classes.
 *
 * @package SilverWare\Tools
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class ClassTools extends Object
{
    /**
     * Answers an array containing the class names of the ancestors of the object.
     *
     * @param string|object $nameOrObject Class name or object.
     * @param string $stopAt Ancestor class to stop at.
     * @param boolean $removeNamespaces If true, remove namespaces from class names.
     *
     * @return array
     */
    public function getObjectAncestry($nameOrObject, $stopAt = null, $removeNamespaces = false)
    {
        $classes = [];
        
        foreach ($this->getReverseAncestry($nameOrObject) as $className) {
            
            if ($removeNamespaces) {
                $classes[] = $this->getClassWithoutNamespace($className);
            } else {
                $classes[] = $className;
            }
            
            if ($className == $stopAt) {
                break;
            }
            
        }
        
        return $classes;
    }
    
    /**
     * Answers the ancestry of the specified class name or object in reverse order.
     *
     * @param string|object $nameOrObject Class name or object.
     *
     * @return array
     */
    public function getReverseAncestry($nameOrObject)
    {
        return array_reverse(ClassInfo::ancestry($nameOrObject));
    }
    
    /**
     * Answers the visible subclasses of the specified class name.
     *
     * @param string $name Name of parent class.
     *
     * @return array
     */
    public function getVisibleSubClasses($name)
    {
        $remove  = [];
        $classes = ClassInfo::getValidSubClasses($name);
        
        foreach ($classes as $class) {
            
            $instance = singleton($class);
            
            if ($ancestor = $instance->stat('hide_ancestor')) {
                $remove[$ancestor] = $ancestor;
            }
            
            if ($instance instanceof HiddenClass) {
                $remove[$class] = $class;
            }
            
        }
        
        return array_diff_key($classes, $remove);
    }
    
    /**
     * Answers a map of class names to singular names for implementors of the specified interface.
     *
     * @param string $interface Name of interface to implement.
     * @param string $baseClass Base class to filter (defaults to SiteTree).
     *
     * @return array
     */
    public function getImplementorMap($interface, $baseClass = null)
    {
        // Create Map Array:
        
        $map = [];
        
        // Define Base Class (if none given):
        
        if (!$baseClass) {
            $baseClass = SiteTree::class;
        }
        
        // Obtain Implementors:
        
        if ($implementors = ClassInfo::implementorsOf($interface)) {
            
            // Filter Implementors:
            
            $records = $baseClass::get()->filter([
                'ClassName' => $implementors
            ])->sort('ClassName');
            
            // Define Map Array:
            
            foreach ($records as $record) {
                $map[$record->ID] = sprintf(
                    '%s (%s)',
                    $record->Title,
                    $record->i18n_singular_name()
                );
            }
            
        }
        
        // Answer Map Array:
        
        return $map;
    }
    
    /**
     * Removes the namespace from the given class name.
     *
     * @param string $name
     *
     * @return string
     */
    public function getClassWithoutNamespace($name)
    {
        return substr($name, strrpos($name, '\\') + 1);
    }
    
    /**
     * Replaces the specified class name with a style-friendly version within the given string of classes.
     *
     * @param string $classes
     * @param string $name
     *
     * @return string
     */
    public function getStyleClasses($classes, $name)
    {
        return str_replace($name, Convert::raw2htmlid($name), $classes);
    }
}
