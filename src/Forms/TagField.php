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

use SilverStripe\Core\Injector\Injector;
use SilverStripe\ORM\DataObjectInterface;
use SilverStripe\ORM\Relation;
use SilverWare\Select2\Forms\Select2AjaxField;
use SilverWare\Tags\Tag;
use Exception;

/**
 * An extension of the Select2 Ajax field for a tag field.
 *
 * @package SilverWare\Forms
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class TagField extends Select2AjaxField
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
     * The tag class to search via Ajax.
     *
     * @var string
     */
    protected $dataClass = Tag::class;
    
    /**
     * The ID field for the tag class.
     *
     * @var string
     */
    protected $idField = 'Title';
    
    /**
     * The text field for the tag class.
     *
     * @var string
     */
    protected $textField = 'Title';
    
    /**
     * Defines whether the field can create new tags.
     *
     * @var boolean
     */
    protected $canCreate = true;
    
    /**
     * Defines whether the field can handle multiple tags.
     *
     * @var boolean
     */
    protected $multiple = true;
    
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
        
        // Define Object:
        
        $this->setHasEmptyDefault(false);
    }
    
    /**
     * Answers the field type for the template.
     *
     * @return string
     */
    public function Type()
    {
        return sprintf('tagfield %s', parent::Type());
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
     * Saves the value of the field into the given data object.
     *
     * @param DataObjectInterface $record
     *
     * @throws Exception
     *
     * @return void
     */
    public function saveInto(DataObjectInterface $record)
    {
        // Initialise:
        
        $ids = [];
        
        // Obtain Field Name:
        
        $fieldName = $this->getName();
        
        // Bail Early (if needed):
        
        if (empty($fieldName) || empty($record)) {
            return;
        }
        
        // Obtain Relation:
        
        if (!($relation = $this->getNamedRelation($record))) {
            
            throw new Exception(
                sprintf(
                    '%s does not have a relation named "%s"',
                    get_class($record),
                    $this->Name
                )
            );
            
        }
        
        // Iterate Value Array:
        
        foreach ($this->getValueArray() as $title) {
            
            // Obtain or Create Tag:
            
            if ($tag = $this->findOrMakeTag($relation, $title)) {
                $ids[] = $tag->ID;
            }
            
        }
        
        // Update Relation:
        
        $relation->setByIDList($ids);
    }
    
    /**
     * Obtains or creates a tag object with the given title.
     *
     * @param Relation $relation
     * @param string $title
     *
     * @return Tag
     */
    protected function findOrMakeTag(Relation $relation, $title)
    {
        // Obtain Data List:
        
        $list = $this->getList();
        
        // Obtain Field Name:
        
        $field = $this->getIDField();
        
        // Obtain Existing Tag:
        
        if ($tag = $list->find($field, $title)) {
            return $tag;
        }
        
        // Create New Tag (if enabled):
        
        if ($this->getCanCreate()) {
            $tag = Injector::inst()->create($this->getTagClass($relation));
            $tag->setField($field, $title)->write();
            return $tag;
        }
    }
    
    /**
     * Answers the tag class used by the field (uses the relation to identify if no source list defined).
     *
     * @param Relation $relation
     *
     * @return string
     */
    protected function getTagClass(Relation $relation)
    {
        return ($this->dataClass === Tag::class) ? $relation->dataClass() : $this->dataClass;
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
