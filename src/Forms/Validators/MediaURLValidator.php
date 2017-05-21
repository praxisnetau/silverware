<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Forms\Validators
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\Forms\Validators;

use Embed\Embed;
use Embed\Exceptions\EmbedException;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\ORM\ValidationException;
use SilverStripe\ORM\ValidationResult;

/**
 * An extension of the required fields validator for a media URL validator.
 *
 * @package SilverWare\Forms\Validators
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class MediaURLValidator extends RequiredFields
{
    /**
     * The name of the media URL field.
     *
     * @var string
     */
    protected $urlField = 'MediaURL';
    
    /**
     * Defines the value of the urlField attribute.
     *
     * @param string $urlField
     *
     * @return $this
     */
    public function setURLField($urlField)
    {
        $this->urlField = (string) $urlField;
        
        return $this;
    }
    
    /**
     * Answers the value of the urlField attribute.
     *
     * @return string
     */
    public function getURLField()
    {
        return $this->urlField;
    }
    
    /**
     * Validates the given array of form data and answers the result.
     *
     * @param array $data
     *
     * @throws ValidationException
     *
     * @return boolean
     */
    public function php($data)
    {
        // Obtain Result (from parent):
        
        $valid = parent::php($data);
        
        // Obtain URL Field:
        
        if ($field = $this->form->Fields()->dataFieldByName($this->urlField)) {
            
            // Detect Value:
            
            if (!$field->Value()) {
                return $valid;
            }
            
            // Attempt Adapter Creation:
            
            try {
                
                $adapter = Embed::create($field->Value());
                
                $valid = true;
                
            } catch (EmbedException $e) {
                
                $this->validationError($this->urlField, $e->getMessage(), ValidationResult::TYPE_ERROR);
                
                $valid = false;
                
            }
            
        } else {
            
            // Cannot Find URL Field:
            
            throw new ValidationException(
                sprintf(
                    _t(__CLASS__ . '.CANNOTFINDURLFIELD', 'Cannot find a field to validate with name "%s"'),
                    $this->urlField
                )
            );
            
        }
        
        // Answer Result:
        
        return $valid;
    }
}
