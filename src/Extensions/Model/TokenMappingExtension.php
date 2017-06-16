<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Extensions\Model
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\Extensions\Model;

use SilverStripe\Core\Extension;
use Exception;

/**
 * An extension class which allows extended objects to use token mappings.
 *
 * @package SilverWare\Extensions\Model
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class TokenMappingExtension extends Extension
{
    /**
     * Answers the token mappings for the extended object.
     *
     * @return array
     */
    public function getTokenMappings()
    {
        // Create Mappings Array:
        
        $mappings = [];
        
        // Define Mappings Array:
        
        if (is_array($this->owner->config()->token_mappings)) {
            
            foreach ($this->owner->config()->token_mappings as $name => $spec) {
                
                if (!is_array($spec)) {
                    $spec = ['property' => $spec];
                }
                
                $mappings[$name] = $spec;
                
            }
            
        }
        
        // Answer Mappings Array:
        
        return $mappings;
    }
    
    /**
     * Replaces tokens found within the given text with their mapped value.
     *
     * @param string $text Text with tokens to replace.
     * @param array $tokens Array of tokens mapped to values (optional).
     *
     * @throws Exception
     *
     * @return string
     */
    public function replaceTokens($text, $tokens = [])
    {
        // Obtain Token Mappings:
        
        $mappings = $this->getTokenMappings();
        
        // Iterate Token Mappings:
        
        foreach ($mappings as $name => $spec) {
            
            // Ignore Custom Mappings:
            
            if (isset($spec['custom']) && $spec['custom']) {
                continue;
            }
            
            // Check Property Defined:
                
            if (!isset($spec['property'])) {
                throw new Exception(sprintf('Property is undefined for token mapping "%s"', $name));
            }
            
            // Obtain Value for Token:
            
            $value = isset($tokens[$name]) ? $tokens[$name] : $this->getPropertyValue($spec['property']);
            
            // Perform Token Replacement:
            
            $text = str_ireplace("{{$name}}", $value, $text);
            
        }
        
        // Answer Processed Text:
        
        return $text;
    }
    
    /**
     * Answers the value of the property with the given name from the extended object (either via field or method).
     *
     * @param string $name Name of property.
     *
     * @return mixed
     */
    protected function getPropertyValue($name)
    {
        if (strpos($name, '.') !== false) {
            return $this->owner->relField($name);
        }
        
        if ($this->owner->hasMethod($name)) {
            return $this->owner->{$name}();
        }
        
        return $this->owner->{$name};
    }
}
