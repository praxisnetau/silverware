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

use SilverStripe\Forms\FieldGroup;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\FormField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataObjectInterface;
use SilverStripe\ORM\FieldType\DBField;
use SilverWare\ORM\FieldType\DBDimensions;

/**
 * An extension of the form field class for a dimensions field.
 *
 * @package SilverWare\Forms
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class DimensionsField extends FormField
{
    /**
     * Array of dimension fields.
     *
     * @var array
     */
    protected $fields = [];
    
    /**
     * Labels for dimension fields.
     *
     * @var array
     */
    protected $labels = [];
    
    /**
     * Use placeholders for dimension fields?
     *
     * @var boolean
     */
    protected $usePlaceholders = true;
    
    /**
     * Defines the schema data type.
     *
     * @var string
     */
    protected $schemaDataType = self::SCHEMA_DATA_TYPE_STRUCTURAL;
    
    /**
     * Defines the schema component.
     *
     * @var string
     */
    protected $schemaComponent = 'DimensionsField';
    
    /**
     * Constructs the object upon instantiation.
     *
     * @param string $name Name of field.
     * @param string $title Title of field.
     * @param mixed $value Value of field.
     */
    public function __construct($name, $title = null, $value = null)
    {
        // Define Name:
        
        $this->setName($name);
        
        // Build Dimension Fields:
        
        foreach (DBDimensions::singleton()->getDimensions() as $dim) {
            $this->fields[$dim] = $this->buildDimensionField($dim);
        }
        
        // Construct Parent:
        
        parent::__construct($name, $title, $value);
        
        // Update Dimension Fields:
        
        $this->updateDimensionFields();
    }
    
    /**
     * Called when the receiver is cloned (ensures associated objects are also cloned).
     *
     * @return void
     */
    public function __clone()
    {
        foreach ($this->fields as $dim => $field) {
            $this->fields[$dim] = clone $field;
        }
    }
    
    /**
     * Defines the form for the receiver and dimension fields.
     *
     * @param Form $form
     *
     * @return $this
     */
    public function setForm($form)
    {
        foreach ($this->fields as $field) {
            $field->setForm($form);
        }
        
        return parent::setForm($form);
    }
    
    /**
     * Defines the value of the usePlaceholders attribute.
     *
     * @param boolean $usePlaceholders
     *
     * @return $this
     */
    public function setUsePlaceholders($usePlaceholders)
    {
        $this->usePlaceholders = (boolean) $usePlaceholders;
        
        $this->updateDimensionFields();
        
        return $this;
    }
    
    /**
     * Answers the value of the usePlaceholders attribute.
     *
     * @return boolean
     */
    public function getUsePlaceholders()
    {
        return $this->usePlaceholders;
    }
    
    /**
     * Defines the value of the receiver.
     *
     * @param mixed $value
     * @param array|DataObject $data
     *
     * @return $this
     */
    public function setValue($value, $data = null)
    {
        // Define Value Property:
        
        $this->value = $value;
        
        // Determine Value Type:
        
        if (is_array($value)) {
            
            // Define from Array:
            
            foreach ($this->fields as $dim => $field) {
                
                if (isset($value[$dim])) {
                    $field->setValue($value[$dim]);
                }
                
            }
            
        } elseif ($value instanceof DBDimensions) {
            
            // Define from Field:
            
            foreach ($this->fields as $dim => $field) {
                $field->setValue($value->{$dim});
            }
            
        }
        
        // Answer Receiver:
        
        return $this;
    }
    
    /**
     * Saves the receiver into the given data object.
     *
     * @param DataObjectInterface|DataObject $dataObject
     */
    public function saveInto(DataObjectInterface $record)
    {
        $name = $this->getName();
        
        if ($record->hasMethod("set{$name}")) {
            
            $record->$name = DBField::create_field('Dimensions', $this->getDimensionValues());
            
        } else {
            
            foreach ($this->getDimensionValues() as $dim => $value) {
                $record->{"{$name}$dim"} = $value;
            }
            
        }
    }
    
    /**
     * Sets a disabled flag on the receiver and dimension fields.
     *
     * @param boolean $disabled
     *
     * @return $this
     */
    public function setDisabled($disabled)
    {
        $self = parent::setDisabled($disabled);
        
        $this->updateDimensionFields();
        
        return $self;
    }
    
    /**
     * Sets a read-only flag on the receiver and dimension fields.
     *
     * @param boolean $readonly
     *
     * @return $this
     */
    public function setReadonly($readonly)
    {
        $self = parent::setReadonly($readonly);
        
        $this->updateDimensionFields();
        
        return $self;
    }
    
    /**
     * Returns a read-only version of the receiver.
     *
     * @return DimensionsField
     */
    public function performReadonlyTransformation()
    {
        $clone = clone $this;
        
        foreach ($clone->fields as $dim => $field) {
            $clone->fields[$dim] = $field->performReadonlyTransformation();
        }
        
        $clone->setReadonly(true);
        
        return $clone;
    }
    
    /**
     * Defines the label for the specified dimension.
     *
     * @param string $dim
     * @param string $label
     *
     * @return $this
     */
    public function setDimensionLabel($dim, $label)
    {
        $this->labels[$dim] = $label;
        
        $this->updateDimensionFields();
        
        return $this;
    }
    
    /**
     * Answers the label for the specified dimension.
     *
     * @param string $dim
     *
     * @return string
     */
    public function getDimensionLabel($dim)
    {
        if (isset($this->labels[$dim])) {
            return $this->labels[$dim];
        }
        
        return DBDimensions::singleton()->getDimensionLabel($dim);
    }
    
    /**
     * Answers an array containing the values of the dimension fields.
     *
     * @return array
     */
    public function getDimensionValues()
    {
        $values = [];
        
        foreach ($this->fields as $dim => $field) {
            $values[$dim] = is_numeric($field->dataValue()) ? (int) $field->dataValue() : null;
        }
        
        return $values;
    }
    
    /**
     * Answers the dimension fields for the field holder.
     *
     * @return FieldList
     */
    public function getFields()
    {
        $fields = FieldList::create($this->fields);
        
        foreach ($fields as $field) {
            
            $fields->insertAfter(
                $field->getName(),
                LiteralField::create(
                    sprintf('%s_ByLiteral', $field->getName()),
                    '<div class="by"><span class="icon"></span></div>'
                )
            );
            
        }
        
        return $fields;
    }
    
    /**
     * Renders the field holder for the receiver.
     *
     * @param array $properties
     *
     * @return DBHTMLText
     */
    public function FieldHolder($properties = [])
    {
        return FieldGroup::create(
            $this->Title(),
            $this->Fields
        )->addExtraClass('dimensions')->FieldHolder($properties);
    }
    
    /**
     * Builds and answers a form field for the specified dimension.
     *
     * @param string $dim
     *
     * @return FormField
     */
    protected function buildDimensionField($dim)
    {
        $field = TextField::create(
            sprintf('%s[%s]', $this->getName(), $dim),
            $this->getDimensionLabel($dim)
        );
        
        return $field;
    }
    
    /**
     * Updates the dimension fields after a state change.
     *
     * @return void
     */
    protected function updateDimensionFields()
    {
        foreach ($this->fields as $dim => $field) {
            
            // Update Title / Placeholders:
            
            if ($this->usePlaceholders) {
                $field->setTitle(null)->setAttribute('placeholder', $this->getDimensionLabel($dim));
            } else {
                $field->setTitle($this->getDimensionLabel($dim))->setAttribute('placeholder', null);
            }
            
            // Update Disabled Flags:
            
            $field->setDisabled($this->disabled);
            
            // Update Readonly Flags:
            
            $field->setReadonly($this->readonly);
            
        }
    }
}
