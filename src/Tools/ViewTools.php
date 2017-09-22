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

use SilverStripe\Assets\File;
use SilverStripe\Control\Director;
use SilverStripe\Core\Convert;
use SilverStripe\Core\Injector\Injectable;
use SilverStripe\View\Requirements;
use SilverStripe\View\SSViewer;
use SilverStripe\View\ViewableData;

/**
 * A singleton providing utility functions for use with views.
 *
 * @package SilverWare\Tools
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class ViewTools
{
    use Injectable;
    
    /**
     * Converts the given array of strings into an HTML attribute string.
     *
     * @param array $array
     *
     * @return string
     */
    public function array2att($array)
    {
        return Convert::raw2att(implode(' ', array_filter($array)));
    }
    
    /**
     * Converts the given value to a string suitable for HTML class attributes.
     *
     * @param string|array $val
     *
     * @return string
     */
    public function convertClass($val)
    {
        if (is_array($val)) {
            
            foreach ($val as $k => $v) {
                $val[$k] = $this->convertClass($v);
            }
            
            return $val;
            
        }
        
        return strtolower(trim(str_replace('\\', '-', $val), '-'));
    }
    
    /**
     * Answers an array of ancestor class names for the given object.
     *
     * @param string|object $nameOrObject Class name or object.
     * @param string $stopAt Ancestor class to stop at.
     * @param boolean $removeNamespaces If true, remove namespaces from class names.
     *
     * @return array
     */
    public function getAncestorClassNames($nameOrObject, $stopAt = null, $removeNamespaces = true)
    {
        return $this->convertClass(
            ClassTools::singleton()->getObjectAncestry(
                $nameOrObject,
                $stopAt,
                $removeNamespaces
            )
        );
    }
    
    /**
     * Loads the specified JavaScript template (with a provision for handling JSON variables).
     *
     * @param string $file
     * @param array $vars
     * @param string $uniquenessID
     *
     * @return void
     */
    public function loadJSTemplate($file, $vars, $uniquenessID = null)
    {
        // Load File Contents:
        
        $script = file_get_contents(Director::getAbsFile($file));
        
        // Initialise Search / Replace Arrays:
        
        $s = [];
        $r = [];
        
        // Process Variable Array:
        
        if ($vars) {
            
            foreach ($vars as $k => $v) {
                
                if (strtolower(substr($k, 0, 5)) == 'json:') {
                    $s[] = '$' . substr($k, 5);
                    $r[] = Convert::raw2json($v);
                } else {
                    $s[] = '$' . $k;
                    $r[] = str_replace("\\'", "'", Convert::raw2js($v));
                }
                
            }
            
        }
        
        // Replace Script Variables:
        
        $script = str_replace($s, $r, $script);
        
        // Load Custom Script:
        
        Requirements::customScript($script, $uniquenessID);
    }
    
    /**
     * Combines the given array of files into a single file within the specified name and answers the URL.
     *
     * @param string $name
     * @param array $files
     *
     * @return string
     */
    public function combineFiles($name, $files)
    {
        $backend = Requirements::backend();
        
        $path = File::join_paths($backend->getCombinedFilesFolder(), $name);
        
        return $backend->getAssetHandler()->getContentURL($path, function () use ($files) {
            
            $output = [];
            
            foreach ($files as $file => $data) {
                $output[] = "/***** FILE: $file *****/";
                $output[] = $data;
            }
            
            return implode("\n", $output);
            
        });
    }
    
    /**
     * Minifies and wraps the given string of CSS.
     *
     * @param string $css
     * @param integer $wrap
     *
     * @return string
     */
    public function minifyCSS($css, $wrap = 200)
    {
        // Remove Comments:
        
        $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
        
        // Remove Space Following Colons:
        
        $css = str_replace(': ', ':', $css);
        
        // Remove Whitespace:
        
        $css = str_replace(["\r\n", "\r", "\n", "\t", '  ', '    ', '    '], '', $css);
        
        // Wrap and Answer:
        
        return wordwrap($css, $wrap);
    }
    
    /**
     * Converts the given associative array of attributes to a string of HTML.
     *
     * @param array $attributes
     *
     * @return string
     */
    public function getAttributesHTML($attributes)
    {
        // Initialise:
        
        $markup = [];
        
        // Remove Empty Attributes:
        
        $attributes = array_filter($attributes, function ($v) {
            return ($v || $v === 0 || $v === '0');
        });
        
        // Render Remaining Attributes:
        
        foreach ($attributes as $name => $value) {
            $markup[] = sprintf('%s="%s"', $name, ($value === true) ? $name : Convert::raw2att($value));
        }
        
        // Answer Markup:
        
        return implode(' ', $markup);
    }
    
    /**
     * Processes the given attribute value which references methods / fields of the given objects.
     *
     * @param string $value
     * @param ViewableData $object
     * @param ViewableData $parent
     * @param array $args
     *
     * @return string
     */
    public function processAttribute($value, ViewableData $object, ViewableData $parent = null, $args = [])
    {
        // Does the value refer to a field or method?
        
        if (strpos($value, '$') === 0) {
            
            // Obtain Field Name:
            
            $field = ltrim($value, '$');
            
            // Detect Object Relationship Format:
            
            if (strpos($field, '.') !== false) {
                
                list($name, $prop) = explode('.', $field);
                
                if ($object->hasMethod("get{$name}")) {
                    return $object->{"get{$name}"}()->{$prop};
                } elseif ($object->hasField($name) || $object->hasField($name . 'ID')) {
                    return $object->relField($field);
                }
                
            }
            
            // Obtain Field Value:
            
            if ($object->hasMethod("get{$field}")) {
                
                // First, answer the result of a method call on the receiver:
                
                return call_user_func_array(
                    [$object, "get{$field}"],
                    $this->processAttributeArgs($args, $object, $parent)
                );
                
            } elseif ($object->hasField($field)) {
                
                // Next, answer a field value from the given object:
                
                return $object->$field;
                
            } elseif (is_object($parent) && $parent->hasField($field)) {
                
                // Finally, answer a field value from the given parent object:
                
                return $parent->$field;
                
            }
            
        }
        
        // Answer Value:
        
        return $value;
    }
    
    /**
     * Processes the given array of arguments for an attribute.
     *
     * @param string|array $stringOrArray
     * @param ViewableData $object
     * @param ViewableData $parent
     *
     * @return array
     */
    public function processAttributeArgs($stringOrArray, ViewableData $object, ViewableData $parent = null)
    {
        $args = (array) $stringOrArray;
        
        foreach ($args as $key => $arg) {
            $args[$key] = $this->processAttribute($arg, $object, $parent);
        }
        
        return $args;
    }
    
    /**
     * Removes empty lines from the given string.
     *
     * @param string $string
     *
     * @return string
     */
    public function removeEmptyLines($string)
    {
        return preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $string);
    }
    
    /**
     * Renders the given object using the given CSS template.
     *
     * @param ViewableData $object
     * @param string $template
     * @param array $css
     *
     * @return array
     */
    public function renderCSS(ViewableData $object, $template, $css = [])
    {
        if (SSViewer::hasTemplate($template)) {
            
            return array_merge(
                $css,
                preg_split('/\r\n|\n|\r/', (string) $object->renderWith($template))
            );
            
        }
        
        return $css;
    }
}
