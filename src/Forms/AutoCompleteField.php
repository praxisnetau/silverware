<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Forms
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\Forms;

use SilverStripe\Control\HTTPRequest;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\Map;
use SilverStripe\ORM\SS_List;
use ArrayAccess;

/**
 * An extension of the text field class for an auto-complete field.
 *
 * @package SilverWare\Forms
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class AutoCompleteField extends TextField
{
    /**
     * Defines the allowed actions for this field.
     *
     * @var array
     * @config
     */
    private static $allowed_actions = [
        'suggest'
    ];
    
    /**
     * Source options used to return suggestions to the field.
     *
     * @var array
     */
    protected $source;
    
    /**
     * The source URL used to return suggestions to the field.
     *
     * @var string
     */
    protected $sourceURL;
    
    /**
     * If true, free text entry is permitted in the text field.
     *
     * @var boolean
     */
    protected $allowFreeText = false;
    
    /**
     * The minimum length of the term which triggers the suggestions.
     *
     * @var integer
     */
    protected $termMinLength = 2;
    
    /**
     * The delay in milliseconds between when a key is pressed and suggestions are returned.
     *
     * @var integer
     */
    protected $delay = 800;
    
    /**
     * Constructs the object upon instantiation.
     *
     * @param string $name Name of field.
     * @param string $title Title of field.
     * @param array|ArrayAccess $source A map of options used as the data source.
     * @param mixed $value Value of field.
     */
    public function __construct($name, $title = null, $source = [], $value = null)
    {
        // Define Source:
        
        $this->setSource($source);
        
        // Define Empty Value:
        
        $this->setEmptyValue(_t(__CLASS__ . '.DEFAULTEMPTYVALUE', '(none)'));
        
        // Construct Parent:
        
        parent::__construct($name, $title, $value);
    }
    
    /**
     * Answers an array of HTML attributes for the field.
     *
     * @return array
     */
    public function getAttributes()
    {
        $attributes = array_merge(
            parent::getAttributes(),
            [
                'data-source-url'  => $this->getSourceURL(),
                'data-min-length'  => $this->getTermMinLength(),
                'data-free-text'   => $this->getAllowFreeText(),
                'data-empty-value' => $this->getEmptyValue(),
                'data-delay'       => $this->getDelay()
            ]
        );
        
        $attributes['name']  = $this->getAutoCompleteName();
        $attributes['value'] = null;
        
        return $attributes;
    }
    
    /**
     * Answers the name for the auto-complete field.
     *
     * @return string
     */
    public function getAutoCompleteName()
    {
        return sprintf('%s_autocomplete', $this->getName());
    }
    
    /**
     * Defines the value of the source attribute.
     *
     * @param array $source
     *
     * @return $this
     */
    public function setSource($source)
    {
        $this->source = $this->getSourceAsArray($source);
        
        return $this;
    }
    
    /**
     * Answers the value of the source attribute.
     *
     * @return array
     */
    public function getSource()
    {
        return $this->source;
    }
    
    /**
     * Answers the source URL of the field.
     *
     * @return string
     */
    public function getSourceURL()
    {
        return $this->hasSourceURL() ? $this->sourceURL : $this->Link('suggest');
    }
    
    /**
     * Answers true if a source URL is defined.
     *
     * @return boolean
     */
    public function hasSourceURL()
    {
        return (boolean) $this->sourceURL;
    }
    
    /**
     * Defines the value of the allowFreeText attribute.
     *
     * @param boolean $allowFreeText
     *
     * @return $this
     */
    public function setAllowFreeText($allowFreeText)
    {
        $this->allowFreeText = (boolean) $allowFreeText;
        
        return $this;
    }
    
    /**
     * Answers the value of the allowFreeText attribute.
     *
     * @return boolean
     */
    public function getAllowFreeText()
    {
        return $this->allowFreeText;
    }
    
    /**
     * Defines the value of the termMinLength attribute.
     *
     * @param integer $termMinLength
     *
     * @return $this
     */
    public function setTermMinLength($termMinLength)
    {
        $this->termMinLength = (integer) $termMinLength;
        
        return $this;
    }
    
    /**
     * Answers the value of the termMinLength attribute.
     *
     * @return integer
     */
    public function getTermMinLength()
    {
        return $this->termMinLength;
    }
    
    /**
     * Defines the value of the delay attribute.
     *
     * @param integer $delay
     *
     * @return $this
     */
    public function setDelay($delay)
    {
        $this->delay = (integer) $delay;
        
        return $this;
    }
    
    /**
     * Answers the value of the delay attribute.
     *
     * @return integer
     */
    public function getDelay()
    {
        return $this->delay;
    }
    
    /**
     * Defines the value of the emptyValue attribute.
     *
     * @param string $emptyValue
     *
     * @return $this
     */
    public function setEmptyValue($emptyValue)
    {
        $this->emptyValue = (string) $emptyValue;
        
        return $this;
    }
    
    /**
     * Answers the value of the emptyValue attribute.
     *
     * @return string
     */
    public function getEmptyValue()
    {
        return $this->emptyValue;
    }
    
    /**
     * Answers the field type for the template.
     *
     * @return string
     */
    public function Type()
    {
        return 'autocomplete text';
    }
    
    /**
     * Answers a string of JSON-encoded suggestions for the entered term.
     *
     * @param HTTPRequest $request
     *
     * @return string
     */
    public function suggest(HTTPRequest $request)
    {
        $data = [];
        
        if ($request->isAjax()) {
            
            $term = $request->getVar('term');
            
            foreach ($this->getMatches($term) as $key => $value) {
                $data[] = [
                    'value' => $key,
                    'label' => $value
                ];
            }
            
        }
        
        return json_encode($data);
    }
    
    /**
     * Answers an array of source items matching the given term.
     *
     * @param string $term
     *
     * @return array
     */
    public function getMatches($term)
    {
        return array_filter($this->getSource(), function ($item) use ($term) {
            return ( strpos($item, $term) !== false );
        });
    }
    
    /**
     * Converts the given source parameter to an array.
     *
     * @param array|ArrayAccess $source
     *
     * @return array
     */
    protected function getSourceAsArray($source)
    {
        if (!is_array($source) && !($source instanceof ArrayAccess)) {
            user_error('$source passed in as invalid type', E_USER_ERROR);
        }
        
        if ($source instanceof SS_List || $source instanceof Map) {
            $source = $source->toArray();
        }
        
        return $source;
    }
}
