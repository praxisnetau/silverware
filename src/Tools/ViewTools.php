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

use SilverStripe\Control\Director;
use SilverStripe\Core\Convert;
use SilverStripe\Core\Object;
use SilverStripe\View\Requirements;

/**
 * A singleton providing utility functions for use with views.
 *
 * @package SilverWare\Tools
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class ViewTools extends Object
{
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
}
