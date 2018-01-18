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
 * @copyright 2018 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\Forms;

use SilverStripe\Core\Injector\Injector;
use SilverStripe\ORM\DataObjectInterface;
use SilverWare\Select2\Forms\Select2AjaxField;

/**
 * An extension of the Select2 Ajax field for a has-one field.
 *
 * @package SilverWare\Forms
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2018 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class HasOneField extends Select2AjaxField
{
    /**
     * An array which defines the default configuration for instances.
     *
     * @var array
     * @config
     */
    private static $default_config = [
        'minimum-input-length' => 0
    ];
    
    /**
     * Defines whether Ajax is enabled or disabled for the field.
     *
     * @var boolean
     */
    protected $ajaxEnabled = false;
    
    /**
     * The data class to search via Ajax.
     *
     * @var string
     */
    protected $dataClass;
    
    /**
     * The default values for new instances of the data class.
     *
     * @var array
     */
    protected $dataDefaults = [];
    
    /**
     * The ID field for the data class.
     *
     * @var string
     */
    protected $idField = 'ID';
    
    /**
     * The text field for the data class.
     *
     * @var string
     */
    protected $textField = 'Title';
    
    /**
     * Defines whether the field can create new objects.
     *
     * @var boolean
     */
    protected $canCreate = true;
    
    /**
     * Defines whether the field can handle multiple options.
     *
     * @var boolean
     */
    protected $multiple = false;
    
    /**
     * Constructs the object upon instantiation.
     *
     * @param string $name
     * @param string $title
     * @param array|ArrayAccess $source
     * @param mixed $value
     */
    public function __construct($name, $title = null, $source = [], $value = null)
    {
        // Construct Parent:
        
        parent::__construct($name, $title, $source, $value);
    }
    
    /**
     * Answers the field type for the template.
     *
     * @return string
     */
    public function Type()
    {
        return sprintf('hasonefield %s', parent::Type());
    }
    
    /**
     * Defines the value of the canCreate attribute.
     *
     * @param boolean $canCreate
     *
     * @return $this
     */
    public function setCanCreate($canCreate)
    {
        $this->canCreate = (boolean) $canCreate;
        
        return $this;
    }
    
    /**
     * Answers the value of the canCreate attribute.
     *
     * @return boolean
     */
    public function getCanCreate()
    {
        return $this->canCreate;
    }
    
    /**
     * Defines the value of an individual data default.
     *
     * @param string $name
     * @param mixed $value
     *
     * @return $this
     */
    public function setDataDefault($name, $value)
    {
        $this->dataDefaults[$name] = $value;
        
        return $this;
    }
    
    /**
     * Answers the value of an individual data default.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function getDataDefault($name)
    {
        return isset($this->dataDefaults[$name]) ? $this->dataDefaults[$name] : null;
    }
    
    /**
     * Defines the value of the dataDefaults attribute.
     *
     * @param array $dataDefaults
     *
     * @return $this
     */
    public function setDataDefaults($dataDefaults)
    {
        $this->dataDefaults = (array) $dataDefaults;
        
        return $this;
    }
    
    /**
     * Answers the value of the dataDefaults attribute.
     *
     * @return array
     */
    public function getDataDefaults()
    {
        return $this->dataDefaults;
    }
    
    /**
     * Saves the value of the field into the given data object.
     *
     * @param DataObjectInterface $record
     *
     * @return void
     */
    public function saveInto(DataObjectInterface $record)
    {
        // Obtain Field Name:
        
        $fieldName = $this->getName();
        
        // Bail Early (if needed):
        
        if (empty($fieldName) || empty($record)) {
            return;
        }
        
        // Obtain Value:
        
        $value = $this->Value();
        
        // Value Not Empty / Does Not Exist?
        
        if (!empty($value) && !in_array($value, $this->getSourceValues())) {
            
            // Create New Object (if enabled):
            
            if ($this->getCanCreate() && $this->dataClass) {
                $object = Injector::inst()->create($this->dataClass, $this->dataDefaults);
                $object->setField($this->getTextField(), $value);
                $this->setValue($object->write());
            }
            
        }
        
        // Call Parent Method:
        
        return parent::saveInto($record);
    }
    
    /**
     * Answers the field config for the receiver.
     *
     * @return array
     */
    protected function getFieldConfig()
    {
        $config = parent::getFieldConfig();
        
        $config['tags'] = (boolean) $this->getCanCreate();
        
        return $config;
    }
}
