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
 * @copyright 2018 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\Tools;

use SilverStripe\Core\Convert;
use SilverStripe\Core\Injector\Injectable;
use SilverStripe\ORM\FieldType\DBHTMLText;

/**
 * A singleton providing utility functions for use with strings.
 *
 * @package SilverWare\Tools
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2018 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class StringTools
{
    use Injectable;
    
    /**
     * Answers a summary of the given content field using an improved method of removing tags.
     *
     * @param DBHTMLText $content
     * @param integer $maxWords
     * @param string $add
     *
     * @return string
     */
    public function getContentSummary(DBHTMLText $content, $maxWords = 50, $add = '...')
    {
        // Get Plain Text Version:
        
        $value = $this->getContentAsPlainText($content);
        
        // Bail Early (if empty):
        
        if (!$value) {
            return '';
        }
        
        // Split on Sentences (do not remove period):
        
        $sentences = array_filter(array_map(function ($str) {
            return trim($str);
        }, preg_split('@(?<=\.)@', $value)));
        
        $wordCount = count(preg_split('#\s+#u', $sentences[0]));
        
        // If the First Sentence is Too Long, Show Only the First $maxWords Words:
        
        if ($wordCount > $maxWords) {
            return implode(' ', array_slice(explode(' ', $sentences[0]), 0, $maxWords)) . $add;
        }
        
        // Add Each Sentence (while there are enough words to do so):
        
        $result = '';
        
        do {
            
            // Add Next Sentence:
            
            $result .= ' ' . array_shift($sentences);
            
            // If More Sentences, Count Number of Words:
            
            if ($sentences) {
                $wordCount += count(preg_split('#\s+#u', $sentences[0]));
            }
            
        } while ($wordCount < $maxWords && $sentences && trim($sentences[0]));
        
        // Answer Result String:
        
        return trim($result);
    }
    
    /**
     * Converts the given content field to a plain text string using an improved method of removing tags.
     *
     * @param DBHTMLText $content
     *
     * @return string
     */
    public function getContentAsPlainText(DBHTMLText $content)
    {
        // Preserve Line Breaks:
        
        $text = preg_replace('/\<br(\s*)?\/?\>/i', "\n", $content->RAW());
        
        // Convert Paragraph Breaks to Multi-Lines:
        
        $text = preg_replace('/\<\/p\>/i', "\n\n", $text);
        
        // Remove HTML Tags:
        
        $text = $this->removeTags($text);
        
        // Implode >3 Consecutive Linebreaks into 2:
        
        $text = preg_replace('~(\R){2,}~', "\n\n", $text);
        
        // Decode HTML Entities Back to Plain Text:
        
        return trim(Convert::xml2raw($text));
    }
    
    /**
     * Removes HTML tags from the given string while maintaining whitespace.
     *
     * @param string $string
     *
     * @return string
     */
    public function removeTags($string)
    {
        // Remove HTML Tags:
        
        $string = preg_replace('/<[^>]*>/', ' ', $string);
        
        // Remove Control Characters:
        
        $string = str_replace("\r", '',  $string);  // replace with empty space
        $string = str_replace("\n", ' ', $string); // replace with single space
        $string = str_replace("\t", ' ', $string); // replace with single space
        
        // Remove Multiple Spaces:
        
        $string = trim(preg_replace('/ {2,}/', ' ', $string));
        
        // Answer String:
        
        return $string;
    }
}
