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

use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\CompositeField;

/**
 * An extension of the composite field class for a toggleable group of fields.
 *
 * @package SilverWare\Forms
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class ToggleGroup extends CompositeField
{
    /**
     * Maps field and method names to the class names of casting objects.
     *
     * @var array
     * @config
     */
    private static $casting = [
        'ShowWhenChecked' => 'Boolean'
    ];
    
    /**
     * The name of the toggle field.
     *
     * @var string
     */
    protected $toggleName;
    
    /**
     * The title of the toggle field.
     *
     * @var string
     */
    protected $toggleTitle;
    
    /**
     * The toggle field instance.
     *
     * @var CheckboxField
     */
    protected $toggleField;
    
    /**
     * Determines whether the fields are to be shown when the field is checked or unchecked.
     *
     * @var boolean
     */
    protected $showWhenChecked = true;
    
    /**
     * Constructs the object upon instantiation.
     *
     * @param string $name Name of field.
     * @param string $title Title of field.
     * @param array|FieldList $children Child fields.
     */
    public function __construct($name, $title, $children)
    {
        // Construct Parent:
        
        parent::__construct($children);
        
        // Define Attributes:
        
        $this->setToggleName($name);
        $this->setToggleTitle($title);
        
        // Create Toggle Field:
        
        $this->setToggleField(
            CheckboxField::create(
                $name,
                $title
            )
        );
    }
    
    /**
     * Defines the value of the toggleName attribute.
     *
     * @param string $toggleName
     *
     * @return $this
     */
    public function setToggleName($toggleName)
    {
        $this->toggleName = (string) $toggleName;
        
        return $this;
    }
    
    /**
     * Answers the value of the toggleName attribute.
     *
     * @return string
     */
    public function getToggleName()
    {
        return $this->toggleName;
    }
    
    /**
     * Defines the value of the toggleTitle attribute.
     *
     * @param string $toggleTitle
     *
     * @return $this
     */
    public function setToggleTitle($toggleTitle)
    {
        $this->toggleTitle = (string) $toggleTitle;
        
        return $this;
    }
    
    /**
     * Answers the value of the toggleTitle attribute.
     *
     * @return string
     */
    public function getToggleTitle()
    {
        return $this->toggleTitle;
    }
    
    /**
     * Defines the value of the toggleField attribute.
     *
     * @param CheckboxField $toggleField
     *
     * @return $this
     */
    public function setToggleField(CheckboxField $toggleField)
    {
        $this->toggleField = $toggleField;
        
        return $this;
    }
    
    /**
     * Answers the value of the toggleField attribute.
     *
     * @return CheckboxField
     */
    public function getToggleField()
    {
        return $this->toggleField;
    }
    
    /**
     * Defines the container form instance for the receiver.
     *
     * @param Form $form
     *
     * @return $this
     */
    public function setForm($form)
    {
        $this->toggleField->setForm($form);
        
        return parent::setForm($form);
    }
    
    /**
     * Defines the value of the showWhenChecked attribute.
     *
     * @param boolean $showWhenChecked
     *
     * @return $this
     */
    public function setShowWhenChecked($showWhenChecked)
    {
        $this->showWhenChecked = (boolean) $showWhenChecked;
        
        return $this;
    }
    
    /**
     * Answers the value of the showWhenChecked attribute.
     *
     * @return boolean
     */
    public function getShowWhenChecked()
    {
        return $this->showWhenChecked;
    }
    
    /**
     * Collates all data fields within the receiver into the given list.
     *
     * @param array $list
     * @param boolean $saveableOnly
     *
     * @return void
     */
    public function collateDataFields(&$list, $saveableOnly = false)
    {
        $list[$this->ToggleName] = $this->getToggleField();
        
        parent::collateDataFields($list, $saveableOnly);
    }
}
