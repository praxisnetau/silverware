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

use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldGroup;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\FormField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataObjectInterface;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\ORM\Map;
use SilverStripe\ORM\SS_List;
use SilverWare\ORM\FieldType\DBViewports;
use ArrayAccess;

/**
 * An extension of the form field class for a viewports field.
 *
 * @package SilverWare\Forms
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class ViewportsField extends FormField
{
    /**
     * Source options for viewport fields.
     *
     * @var array
     */
    protected $source = [];
    
    /**
     * Array of viewport fields.
     *
     * @var array
     */
    protected $fields = [];
    
    /**
     * Labels for viewport fields.
     *
     * @var array
     */
    protected $labels = [];
    
    /**
     * Defines viewport fields to hide.
     *
     * @var array
     */
    protected $hidden = [];
    
    /**
     * String to show for an empty viewport field.
     *
     * @var string
     */
    protected $emptyString;
    
    /**
     * If true, text fields are used instead of dropdown fields.
     *
     * @var boolean
     */
    protected $useTextInput = false;
    
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
    protected $schemaComponent = 'ViewportsField';
    
    /**
     * Constructs the object upon instantiation.
     *
     * @param string $name Name of field.
     * @param string $title Title of field.
     * @param array|ArrayAccess $source A map of options for the viewport fields.
     * @param mixed $value Value of field.
     */
    public function __construct($name, $title = null, $source = [], $value = null)
    {
        // Define Name:
        
        $this->setName($name);
        
        // Define Source:
        
        $this->setSource($source);
        
        // Build Viewport Fields:
        
        $this->buildViewportFields();
        
        // Define Empty String:
        
        $this->setEmptyString(_t(__CLASS__ . '.DROPDOWNDEFAULT', '(default)'));
        
        // Construct Parent:
        
        parent::__construct($name, $title, $value);
        
        // Update Viewport Fields:
        
        $this->updateViewportFields();
    }
    
    /**
     * Called when the receiver is cloned (ensures associated objects are also cloned).
     *
     * @return void
     */
    public function __clone()
    {
        foreach ($this->fields as $viewport => $field) {
            $this->fields[$viewport] = clone $field;
        }
    }
    
    /**
     * Defines the form for the receiver and viewport fields.
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
            
            foreach ($this->fields as $viewport => $field) {
                
                if (isset($value[$viewport])) {
                    $field->setValue($value[$viewport]);
                }
                
            }
            
        } elseif ($value instanceof DBViewports) {
            
            // Define from Field:
            
            foreach ($this->fields as $viewport => $field) {
                $field->setValue($value->{$viewport});
            }
            
        }
        
        // Answer Receiver:
        
        return $this;
    }
    
    /**
     * Saves the receiver into the given data object.
     *
     * @param DataObjectInterface|DataObject $record Data object to save into.
     *
     * @return void
     */
    public function saveInto(DataObjectInterface $record)
    {
        $name = $this->getName();
        
        if ($record->hasMethod("set{$name}")) {
            
            $record->$name = DBField::create_field('Viewport', $this->getViewportValues());
            
        } else {
            
            foreach ($this->getViewportValues() as $viewport => $value) {
                $record->{"{$name}$viewport"} = $value;
            }
            
        }
    }
    
    /**
     * Defines the value of the source attribute.
     *
     * @param array|ArrayAccess $source
     *
     * @return $this
     */
    public function setSource($source)
    {
        $this->source = $this->getSourceAsArray($source);
        
        $this->updateViewportFields();
        
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
     * Sets a disabled flag on the receiver and dimension fields.
     *
     * @param boolean $disabled
     *
     * @return $this
     */
    public function setDisabled($disabled)
    {
        $self = parent::setDisabled($disabled);
        
        $this->updateViewportFields();
        
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
        
        $this->updateViewportFields();
        
        return $self;
    }
    
    /**
     * Defines the value of the useTextInput attribute.
     *
     * @param boolean $useTextInput
     *
     * @return $this
     */
    public function setUseTextInput($useTextInput)
    {
        $this->useTextInput = (boolean) $useTextInput;
        
        $this->buildViewportFields();
        
        return $this;
    }
    
    /**
     * Answers the value of the useTextInput attribute.
     *
     * @return boolean
     */
    public function getUseTextInput()
    {
        return $this->useTextInput;
    }
    
    /**
     * Returns a read-only version of the receiver.
     *
     * @return ViewportsField
     */
    public function performReadonlyTransformation()
    {
        $clone = clone $this;
        
        foreach ($clone->fields as $viewport => $field) {
            $clone->fields[$viewport] = $field->performReadonlyTransformation();
        }
        
        $clone->setReadonly(true);
        
        return $clone;
    }
    
    /**
     * Defines the label for the specified viewport.
     *
     * @param string $viewport
     * @param string $label
     *
     * @return $this
     */
    public function setViewportLabel($viewport, $label)
    {
        $this->labels[$viewport] = $label;
        
        $this->updateViewportFields();
        
        return $this;
    }
    
    /**
     * Answers the label for the specified viewport.
     *
     * @param string $viewport
     *
     * @return string
     */
    public function getViewportLabel($viewport)
    {
        if (isset($this->labels[$viewport])) {
            return $this->labels[$viewport];
        }
        
        return DBViewports::singleton()->getViewportLabel($viewport);
    }
    
    /**
     * Answers an array containing the values of the viewport fields.
     *
     * @return array
     */
    public function getViewportValues()
    {
        $values = [];
        
        foreach ($this->fields as $viewport => $field) {
            $values[$viewport] = $field->dataValue();
        }
        
        return $values;
    }
    
    /**
     * Defines the viewport fields to hide from display.
     *
     * @return $this
     */
    public function setHiddenViewports()
    {
        $args = func_get_args();
        
        if (count($args) == 1 && is_array($args[0])) {
            $viewports = $args[0];
        } else {
            $viewports = $args;
        }
        
        foreach ($viewports as $viewport) {
            
            if (isset($this->fields[$viewport])) {
                $this->hidden[$viewport] = true;
            }
            
        }
        
        $this->updateViewportFields();
        
        return $this;
    }
    
    /**
     * Answers true if the specified viewport is hidden.
     *
     * @param string $viewport
     *
     * @return boolean
     */
    public function isViewportHidden($viewport)
    {
        return (isset($this->hidden[$viewport]) && $this->hidden[$viewport]);
    }
    
    /**
     * Defines the value of the emptyString attribute.
     *
     * @param string $string
     *
     * @return $this
     */
    public function setEmptyString($string)
    {
        $this->emptyString = (string) $string;
        
        $this->updateViewportFields();
        
        return $this;
    }
    
    /**
     * Answers the value of the emptyString attribute.
     *
     * @return string
     */
    public function getEmptyString()
    {
        return $this->emptyString;
    }
    
    /**
     * Answers the viewport fields for the field holder.
     *
     * @return FieldList
     */
    public function getFields()
    {
        return FieldList::create($this->fields);
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
        )->addExtraClass('viewports')->FieldHolder($properties);
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
    
    /**
     * Builds the viewport fields for the receiver.
     *
     * @return void
     */
    protected function buildViewportFields()
    {
        foreach (DBViewports::singleton()->getViewports() as $viewport) {
            $this->fields[$viewport] = $this->buildViewportField($viewport);
        }
    }
    
    /**
     * Builds and answers a form field for the specified viewport.
     *
     * @param string $viewport
     *
     * @return FormField
     */
    protected function buildViewportField($viewport)
    {
        if ($this->useTextInput) {
            
            $field = TextField::create(
                sprintf('%s[%s]', $this->getName(), $viewport),
                $this->getViewportLabel($viewport)
            );
            
        } else {
            
            $field = DropdownField::create(
                sprintf('%s[%s]', $this->getName(), $viewport),
                $this->getViewportLabel($viewport),
                $this->getSource()
            );
            
        }
        
        return $field;
    }
    
    /**
     * Updates the viewport fields after a state change.
     *
     * @return void
     */
    protected function updateViewportFields()
    {
        foreach ($this->fields as $viewport => $field) {
            
            // Update Title:
            
            $field->setTitle($this->getViewportLabel($viewport));
            
            // Update Hidden Status:
            
            if ($this->isViewportHidden($viewport)) {
                $field->addExtraClass('hidden');
            } else {
                $field->removeExtraClass('hidden');
            }
            
            // Update Dropdown Fields:
            
            if ($field instanceof DropdownField) {
                
                // Update Empty Strings:
                
                if ($field->hasMethod('setEmptyString')) {
                    $field->setEmptyString(' ')->setAttribute('data-placeholder', $this->emptyString);
                }
                
                // Update Source:
                
                $field->setSource($this->getSource());
                
            }
            
            // Update Disabled Flags:
            
            $field->setDisabled($this->disabled);
            
            // Update Readonly Flags:
            
            $field->setReadonly($this->readonly);
            
        }
    }
}
