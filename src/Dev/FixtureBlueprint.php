<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Dev
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\Dev;

use SilverStripe\Assets\File;
use SilverStripe\Assets\Folder;
use SilverStripe\Assets\Upload;
use SilverStripe\Control\Director;
use SilverStripe\Core\ClassInfo;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Dev\FixtureBlueprint as BaseBlueprint;
use SilverStripe\Dev\FixtureFactory as BaseFactory;
use SilverStripe\Forms\FileHandleField;
use SilverStripe\Forms\FormField;
use SilverStripe\ORM\ArrayLib;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DB;
use SilverStripe\ORM\FieldType\DBComposite;
use SilverStripe\ORM\Hierarchy\Hierarchy;
use SilverStripe\Versioned\Versioned;
use Exception;
use InvalidArgumentException;

/**
 * An extension of the SilverStripe fixture blueprint class for building SilverWare objects.
 *
 * @package SilverWare\Dev
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class FixtureBlueprint extends BaseBlueprint
{
    use Configurable;
    
    /**
     * Maps class names to their default parents.
     *
     * @var array
     * @config
     */
    private static $default_parents = [];
    
    /**
     * Maps class names to their default relations.
     *
     * @var array
     * @config
     */
    private static $default_relations = [];
    
    /**
     * Maps class names to their default identifier fields.
     *
     * @var array
     * @config
     */
    private static $default_identifiers = [];
    
    /**
     * If true, more detailed output is shown during the build process.
     *
     * @var boolean
     * @config
     */
    private static $verbose = false;
    
    /**
     * Defines the name of the MatchOn filter.
     *
     * @var string
     * @config
     */
    private static $match_on_name = 'MatchOn';
    
    /**
     * The factory associated with this blueprint.
     *
     * @var FixtureFactory
     */
    protected $factory;
    
    /**
     * Creates a data object using the provided data.
     *
     * @param string $identifier Unique identifier for this fixture type.
     * @param array $data Map of property names with associated values.
     * @param array $fixtures Map of fixture names to an array of identifiers and IDs.
     * @param array $filter Map of property names and associated values to filter by.
     *
     * @return DataObject
     */
    public function createObject($identifier, $data = null, $fixtures = null, $filter = [])
    {
        // Trigger Before Create Event:
        
        $this->onBeforeCreate($identifier, $data, $fixtures);
        
        // Attempt Object Creation:
        
        try {
            
            // Obtain Object:
            
            $object = $this->findOrMakeObject($identifier, $data, $fixtures, $filter);
            
            // Populate Object:
            
            $this->populateObject($object, $data, $fixtures);
            
            // Write Object to Database:
            
            $this->writeObject($object);
            
        } catch (Exception $e) {
            
            // Enable Validation (on failure):
            
            $this->enableValidation();
            
            // Throw Exception Again:
            
            throw $e;
            
        }
        
        // Trigger After Create Event:
        
        $this->onAfterCreate($object, $identifier, $data, $fixtures);
        
        // Answer Object:
        
        return $object;
    }
    
    /**
     * Answers either an existing instance or a new instance of the data object.
     *
     * @param string $identifier
     * @param array $data
     * @param array $fixtures
     * @param array $filter
     *
     * @return DataObject
     */
    public function findOrMakeObject($identifier, $data = null, $fixtures = null, $filter = [])
    {
        // Handle Default Parent:
        
        if (($ID = $this->getDefaultParent()) && !isset($data['ParentID'])) {
            $data['ParentID'] = $filter['ParentID'] = $ID;
        }
        
        // Obtain Existing Object:
        
        $object = $this->findObject($identifier, $data, $filter);
        
        // Create Object (if does not exist):
        
        if (!$object) {
            
            // Create Object Instance:
            
            $object = $this->makeObject();
            
            // Populate Identifier:
            
            $this->populateIdentifier($object, $identifier);
            
            // Populate Defaults:
            
            $this->populateDefaults($object, $identifier, $data, $fixtures);
            
            // Populate Parent:
            
            $this->populateParent($object, $data);
            
            // Show Create Message:
            
            $this->createMessage($object, $identifier);
            
        } else {
            
            // Show Update Message:
            
            $this->updateMessage($object, $identifier);
            
        }
        
        // Mutate Class (if required):
        
        $object = $this->mutateClass($object, $identifier, $data);
        
        // Write Object to Database:
        
        $object->write();
        
        // Record Object in Fixtures:
        
        $this->addFixture($object, $identifier);
        
        // Answer Object:
        
        return $object;
    }
    
    /**
     * Answers an existing instance of the data object.
     *
     * @param string $identifier
     * @param array $data
     * @param array $filter
     *
     * @return DataObject
     */
    public function findObject($identifier, $data, $filter)
    {
        if ($objects = $this->findObjects($identifier, $data, $filter)) {
            
            if ($objects->exists()) {
                return $objects->first();
            }
            
        }
    }
    
    /**
     * Answers a list of the existing objects in the database.
     *
     * @param string $identifier
     * @param array $data
     * @param array $filter
     *
     * @return DataList
     */
    public function findObjects($identifier, $data, $filter)
    {
        $objects = DataList::create($this->getClass());
        
        if (!$objects->exists()) {
            $objects = DataList::create($this->getBaseClass());
        }
        
        if (is_array($filter)) {
            $objects = $objects->filter($filter);
        }
        
        if (isset($data[$this->getMatchOnName()])) {
            return $this->matchObjects($objects, $data);
        }
        
        if ($field = $this->getDefaultIdentifier()) {
            return $objects->filter($field, $identifier);
        }
        
        $title = $this->getIdentifierOrTitle($identifier, $data);
        
        return $objects->filterByCallback(function ($item, $list) use ($title) {
            return ($item->Title == $title);
        });
    }
    
    /**
     * Answers those objects within the given data list matching the provided filter.
     *
     * @param DataList $objects
     * @param array $data
     *
     * @return DataList
     */
    public function matchObjects(DataList $objects, $data)
    {
        // Detect Filter:
        
        $name = $this->getMatchOnName();
        
        if (isset($data[$name])) {
            
            $filter  = [];
            $matchOn = $data[$name];
            
            if (is_array($matchOn)) {
                
                // Handle Array Filter:
                
                if (ArrayLib::is_associative($matchOn)) {
                    
                    $filter = $matchOn;
                    
                } else {
                    
                    foreach ($matchOn as $field) {
                        
                        if (isset($data[$field])) {
                            $filter[$field] = $data[$field];
                        }
                        
                    }
                    
                }
                
            } elseif (isset($data[$matchOn])) {
                
                // Handle String Filter:
                
                $filter[$matchOn] = $data[$matchOn];
                
            }
            
            foreach ($filter as $k => $v) {
                $filter[$k] = $this->processValue($v);
            }
            
            return $objects->filter($filter);
            
        }
        
        return $objects;
    }
    
    /**
     * Creates a new instance of the data object.
     *
     * @return DataObject
     */
    public function makeObject()
    {
        return Injector::inst()->create($this->getClass());
    }
    
    /**
     * Writes the given object to the database.
     *
     * @param DataObject $object
     *
     * @return DataObject
     */
    public function writeObject(DataObject $object)
    {
        // Write Object:
        
        $object->write();
        
        // Handle Versioning:
        
        if ($object->hasExtension(Versioned::class)) {
            
            // Copy Draft to Live:
            
            $object->copyVersionToStage(
                Versioned::DRAFT,
                Versioned::LIVE
            );
            
            // Clear Cache:
            
            $object->flushCache();
            
        }
        
        // Answer Object:
        
        return $object;
    }
    
    /**
     * Mutates the class of the given object, if required.
     *
     * @param DataObject $object
     * @param string $identifier
     * @param array $data
     *
     * @return DataObject
     */
    public function mutateClass(DataObject $object, $identifier, $data)
    {
        $class = $this->getClass();
        
        if ($object->ClassName != $class) {
            
            // Show Notice Message:
            
            $this->noticeMessage(
                sprintf(
                    'Mutating %s (%s to %s)',
                    $identifier,
                    get_class($object),
                    $class
                )
            );
            
            // Mutate Object:
            
            $object = $object->newClassInstance($class);
            
            // Write Object to Database:
            
            $object->write();
            $object->flushCache();
            
        }
        
        return $object;
    }
    
    /**
     * Populates the given object using fixture data.
     *
     * @param DataObject $object
     * @param array $data
     * @param array $fixtures
     *
     * @return DataObject
     */
    public function populateObject(DataObject $object, $data, $fixtures)
    {
        if (is_array($data)) {
            
            // Prepare Data:
            
            $this->prepareData($data);
            
            // Populate Fields:
            
            $this->populateFields($object, $data, $fixtures);
            
            // Populate Children:
            
            $this->populateChildren($object, $data, $fixtures);
            
            // Populate Relations:
            
            $this->populateRelations($object, $data, $fixtures);
            
            // Populate Objects:
            
            $this->populateObjects($object, $data, $fixtures);
            
        }
        
        // Answer Object:
        
        return $object;
    }
    
    /**
     * Populates the fields of the given object.
     *
     * @param DataObject $object
     * @param array $data
     * @param array $fixtures
     *
     * @return DataObject
     */
    public function populateFields(DataObject $object, $data, $fixtures)
    {
        // Iterate Data:
        
        foreach ($data as $name => $value) {
            
            if ($this->isField($object, $name)) {
                $this->populateField($object, $name, $value);
            }
            
        }
        
        // Answer Object:
        
        return $object;
    }
    
    /**
     * Populates the specified field of the given object.
     *
     * @param DataObject $object
     * @param string $name
     * @param mixed $value
     *
     * @return void
     */
    public function populateField(DataObject $object, $name, $value)
    {
        // Detect Value Type:
        
        if (is_array($value) && $object->hasField($name)) {
            
            // Handle Array Value:
            
            if ($object->dbObject($name) instanceof DBComposite) {
                
                // Handle Composite Field:
                
                foreach ($value as $k => $v) {
                    $object->dbObject($name)->setField($k, $this->processValue($v));
                }
                
            } else {
                
                // Handle Regular Field (JSON-encode):
                
                $object->setField($name, $this->processArray($value, true));
                
            }
            
        } else {
            
            // Handle Regular Value:
            
            $object->setField($name, $this->processValue($value));
            
        }
    }
    
    /**
     * Populates the children of the given object.
     *
     * @param DataObject $object
     * @param array $data
     * @param array $fixtures
     *
     * @return DataObject
     */
    public function populateChildren(DataObject $object, $data, $fixtures)
    {
        // Iterate Data:
        
        foreach ($data as $name => $value) {
            
            if ($this->isChild($object, $name)) {
                
                // Process Child Identifier:
                
                list($class, $identifier) = $this->processChildIdentifier($object, $name);
                
                // Define Child Data:
                
                $child = (array) $value;
                
                if (!isset($child['ParentID'])) {
                    $child['ParentID'] = $object->ID;
                }
                
                // Define Child Filter:
                
                $filter = ['ParentID' => $child['ParentID']];
                
                // Create Child Object:
                
                $this->getFactory()->createObject($class, $identifier, $child, $filter);
                
            }
            
        }
        
        // Answer Object:
        
        return $object;
    }
    
    /**
     * Populates the associated objects of the given object.
     *
     * @param DataObject $object
     * @param array $data
     * @param array $fixtures
     *
     * @return DataObject
     */
    public function populateObjects(DataObject $object, $data, $fixtures)
    {
        // Populate Objects:
        
        foreach ($data as $name => $value) {
            
            // Detect Object:
            
            if ($this->isObject($object, $name)) {
                
                // Trim Name:
                
                $name = ltrim($name, '->');
                
                // Populate Object:
                
                if ($subObject = $object->$name) {
                    
                    foreach ($value as $k => $v) {
                        $subObject->$k = $v;
                    }
                    
                    $subObject->write();
                    
                }
                
            }
            
        }
    }
    
    /**
     * Populates the relations of the given object.
     *
     * @param DataObject $object
     * @param array $data
     * @param array $fixtures
     *
     * @return DataObject
     */
    public function populateRelations(DataObject $object, $data, $fixtures)
    {
        // Populate Default Relations:
        
        $this->populateDefaultRelations($object);
        
        // Populate Relations:
        
        foreach ($data as $name => $value) {
            
            // Skip Object Identifiers:
            
            if ($this->isObject($object, $name)) {
                continue;
            }
            
            // Detect Relation:
            
            if ($this->isRelation($object, $name)) {
                
                if ($this->isHasOneRelation($object, $name)) {
                    
                    // Define Has-One Association:
                    
                    $this->verboseMessage(sprintf('%s is a has-one relation', $name));
                    
                    $this->populateOneRelation($object, $name, $value);
                    
                } elseif ($this->isHasManyRelation($object, $name) || $this->isManyManyRelation($object, $name)) {
                    
                    // Define Has-Many / Many-Many Association:
                    
                    $this->verboseMessage(sprintf('%s is a has-many/many-many relation', $name));
                    
                    $this->populateManyRelation($object, $name, $value);
                    
                }
                
            }
            
        }
        
        // Answer Object:
        
        return $object;
    }
    
    /**
     * Populates a has-one relation for the given object.
     *
     * @param DataObject $object
     * @param string $field
     * @param mixed $value
     *
     * @return DataObject
     */
    public function populateOneRelation(DataObject $object, $field, $value)
    {
        // Trim Field ID:
        
        $field = $this->trimID($field);
        
        // Obtain Relation Class:
        
        if ($hasOneClass = $this->getSchema()->hasOneComponent($object, $field)) {
            
            // Define Relation:
            
            if ($this->isFile($hasOneClass)) {
                $object->{$field . 'ID'} = $this->processFile($object, $field, $value, $hasOneClass);
            } else {
                $object->{$field . 'ID'} = $this->processValue($value);
            }
            
            // Handle Polymorphic Relation:
            
            if ($this->isReference($value)) {
                
                list($class, $identifier) = $this->processTypedIdentifier($value);
                
                if ($hasOneClass === DataObject::class) {
                    $object->{$field . 'Class'} = $class;
                }
                
            }
            
        }
        
        // Answer Object:
        
        return $object;
    }
    
    /**
     * Populates a has-many/many-many relation for the given object.
     *
     * @param DataObject $object
     * @param string $field
     * @param mixed $value
     *
     * @return DataObject
     */
    public function populateManyRelation(DataObject $object, $field, $value)
    {
        // Initialise:
        
        $ids = [];
        
        // Process Value as Array:
        
        if ($value = $this->processValueAsArray($value)) {
            
            // Is Value an Associative Array?
            
            $isAssoc = ArrayLib::is_associative($value);
            
            // Output Verbose Message:
            
            $this->verboseMessage(sprintf('%s has an %s array value', $field, ($isAssoc ? 'associative' : 'indexed')));
            
            // Handle Array Values:
            
            if ($isAssoc) {
                
                foreach ($value as $k => $v) {
                    
                    if ($this->isTypedIdentifier($k)) {
                        
                        // Process Typed Identifier:
                        
                        list($class, $identifier) = $this->processTypedIdentifier($k);
                        
                        // Obtain Remote Join Field:
                        
                        if ($join = $this->getSchema()->getRemoteJoinField($object, $field)) {
                            
                            // Define Item Data:
                            
                            $data = (array) $v;
                            
                            if (!isset($data[$join])) {
                                $data[$join] = $object->ID;
                            }
                            
                            // Define Item Filter:
                            
                            $filter = [$join => $data[$join]];
                            
                            // Create Item Object:
                            
                            $this->getFactory()->createObject($class, $identifier, $data, $filter);
                            
                        }
                        
                    }
                    
                }
                
            } else {
                
                // Obtain ID List:
                
                foreach ($value as $k => $v) {
                    $ids[] = $this->processValue($v);
                }
                
                // Populate Relation:
                
                $this->populateManyRelationByIDs($object, $field, $ids);
                
            }
            
        }
        
        // Answer Object:
        
        return $object;
    }
    
    /**
     * Populates a has-many/many-many relation for the given object using the given array of IDs.
     *
     * @param DataObject $object
     * @param string $field
     * @param array $ids
     *
     * @return DataObject
     */
    public function populateManyRelationByIDs(DataObject $object, $field, $ids = [])
    {
        // Define Relation:
        
        if (!empty($ids)) {
            $this->getRelationList($object, $field)->setByIDList($ids);
        }
        
        // Answer Object:
        
        return $object;
    }
    
    /**
     * Populates the default relations of the given object.
     *
     * @param DataObject $object
     *
     * @return DataObject
     */
    public function populateDefaultRelations(DataObject $object)
    {
        foreach ($object->hasOne() as $name => $class) {
            
            if ($id = $this->getDefaultRelation($class)) {
                $object->{$name . 'ID'} = $id;
            }
            
        }
        
        return $object;
    }
    
    /**
     * Populates the identifier field for the given object.
     *
     * @param DataObject $object
     * @param string $identifier
     *
     * @return DataObject
     */
    public function populateIdentifier(DataObject $object, $identifier)
    {
        if ($field = $this->getDefaultIdentifier()) {
            
            return $object->setField($field, $identifier);
            
        } else {
            
            if ($this->getSchema()->fieldSpec($object, 'Title')) {
                return $object->setField('Title', $this->getIdentifierAsTitle($identifier));
            }
            
            if ($this->getSchema()->fieldSpec($object, 'Name')) {
                return $object->setField('Name', $this->getIdentifierAsTitle($identifier));
            }
            
        }
        
        return $object;
    }
    
    /**
     * Populates the defaults for the given object.
     *
     * @param DataObject $object
     * @param string $identifier
     * @param array $data
     * @param array $fixtures
     *
     * @return DataObject
     */
    public function populateDefaults(DataObject $object, $identifier, $data, $fixtures)
    {
        // Iterate Defaults:
        
        foreach ($this->getDefaults() as $name => $value) {
            
            // Is Value Callable?
            
            if (is_callable($value)) {
                $object->$name = $value($object, $data, $fixtures);
            } else {
                $object->$name = $value;
            }
            
        }
        
        // Answer Object:
        
        return $object;
    }
    
    /**
     * Populates the parent relation for the given object.
     *
     * @param DataObject $object
     * @param array $data
     *
     * @return DataObject
     */
    public function populateParent(DataObject $object, $data)
    {
        // Define Parent:
        
        if (isset($data['ParentID'])) {
            $object->ParentID = $data['ParentID'];
        }
        
        // Answer Object:
        
        return $object;
    }
    
    /**
     * Answers true if the specified field is a child of the given object.
     *
     * @param DataObject $object
     * @param string $name
     *
     * @return boolean
     */
    public function isChild(DataObject $object, $name)
    {
        return ($this->isChildIdentifier($name) && $object->hasExtension(Hierarchy::class));
    }
    
    /**
     * Answers true if the specified field is a regular field of the given object.
     *
     * @param DataObject $object
     * @param string $name
     *
     * @return boolean
     */
    public function isField(DataObject $object, $name)
    {
        return (
            !$this->isRelation($object, $name) &&
            !$this->isObject($object, $name) &&
            !$this->isChild($object, $name)
        );
    }
    
    /**
     * Answers true if the specified field is an object associated with the given object.
     *
     * @param DataObject $object
     * @param string $name
     *
     * @return boolean
     */
    public function isObject(DataObject $object, $name)
    {
        $identifier = ltrim($name, '->');
        
        return ($this->isObjectIdentifier($name) && is_object($object->$identifier));
    }
    
    /**
     * Answers true if the specified field name is a child identifier string.
     *
     * @param string $name
     *
     * @return boolean
     */
    public function isChildIdentifier($name)
    {
        return (strpos($name, '+') === 0);
    }
    
    /**
     * Answers true if the specified field name is an object identifier string.
     *
     * @param string $name
     *
     * @return boolean
     */
    public function isObjectIdentifier($name)
    {
        return (strpos($name, '->') === 0);
    }
    
    /**
     * Answers true if the specified class is a file.
     *
     * @param string $class
     *
     * @return boolean
     */
    public function isFile($class)
    {
        return in_array($class, ClassInfo::subclassesFor(File::class));
    }
    
    /**
     * Answers true if the given identifier string is a 'typed' identifier (with class name).
     *
     * @param string $identifier
     *
     * @return boolean
     */
    public function isTypedIdentifier($identifier)
    {
        return (boolean) preg_match('/^[(\w)|\\\]+\.(\w+)$/', ltrim($identifier, '+=>'));
    }
    
    /**
     * Answers true if the given value is a fixture reference string.
     *
     * @param string $value
     *
     * @return boolean
     */
    public function isReference($value)
    {
        return (!is_array($value) && substr($value, 0, 2) == '=>');
    }
    
    /**
     * Answers true if the given value is a callback array.
     *
     * @param array $value
     *
     * @return boolean
     */
    public function isCallbackArray($value)
    {
        return (is_array($value) && isset($value['class']) && isset($value['method']));
    }
    
    /**
     * Answers true if the specified field is a relation of the given object.
     *
     * @param DataObject $object
     * @param string $name
     *
     * @return boolean
     */
    public function isRelation(DataObject $object, $name)
    {
        return (
            $this->isHasOneRelation($object, $name) ||
            $this->isHasManyRelation($object, $name) ||
            $this->isManyManyRelation($object, $name)
        );
    }
    
    /**
     * Answers true if the specified field is a has-one relation of the given object.
     *
     * @param DataObject $object
     * @param string $name
     *
     * @return boolean
     */
    public function isHasOneRelation(DataObject $object, $name)
    {
        return (
            $this->getSchema()->hasOneComponent($object, $name) ||
            $this->getSchema()->hasOneComponent($object, $this->trimID($name))
        );
    }
    
    /**
     * Answers true if the specified field is a has-many relation of the given object.
     *
     * @param DataObject $object
     * @param string $name
     *
     * @return boolean
     */
    public function isHasManyRelation(DataObject $object, $name)
    {
        return (boolean) $this->getSchema()->hasManyComponent($object, $name);
    }
    
    /**
     * Answers true if the specified field is a many-many relation of the given object.
     *
     * @param DataObject $object
     * @param string $name
     *
     * @return boolean
     */
    public function isManyManyRelation(DataObject $object, $name)
    {
        return (boolean) $this->getSchema()->manyManyComponent($object, $name);
    }
    
    /**
     * Answers true if verbose mode is enabled.
     *
     * @return boolean
     */
    public function isVerbose()
    {
        return (boolean) $this->config()->verbose;
    }
    
    /**
     * Converts the given identifier string to a title string.
     *
     * @param string $identifier
     *
     * @return string
     */
    public function getIdentifierAsTitle($identifier)
    {
        return FormField::name_to_label($identifier);
    }
    
    /**
     * Answers either the identifier string or the title (if it exists in the given data array).
     *
     * @param string $identifier
     * @param array $data
     *
     * @return string
     */
    public function getIdentifierOrTitle($identifier, $data)
    {
        return isset($data['Title']) ? $data['Title'] : $this->getIdentifierAsTitle($identifier);
    }
    
    /**
     * Answers the data object schema defined by configuration.
     *
     * @return DataObjectSchema
     */
    public function getSchema()
    {
        return DataObject::getSchema();
    }
    
    /**
     * Answers the base data class for the blueprint.
     *
     * @return string
     */
    public function getBaseClass()
    {
        return $this->getSchema()->baseDataClass($this->getClass());
    }
    
    /**
     * Defines the value of the factory attribute.
     *
     * @param BaseFactory $factory
     *
     * @return $this
     */
    public function setFactory(BaseFactory $factory)
    {
        $this->factory = $factory;
        
        return $this;
    }
    
    /**
     * Answers the value of the factory attribute.
     *
     * @return BaseFactory
     */
    public function getFactory()
    {
        return $this->factory;
    }
    
    /**
     * Adds the identifier string and ID of the given object to the fixtures map.
     *
     * @param DataObject $object
     * @param string $identifier
     *
     * @return void
     */
    public function addFixture(DataObject $object, $identifier)
    {
        $this->getFactory()->addFixture($object, $identifier);
    }
    
    /**
     * Answers the default parent for the associated class.
     *
     * @return integer
     */
    public function getDefaultParent()
    {
        if ($defaultParents = $this->config()->default_parents) {
            
            if (isset($defaultParents[$this->getClass()])) {
                
                $result = $this->processValue($defaultParents[$this->getClass()]);
                
                if ($result instanceof DataObject) {
                    return $result->ID;
                }
                
                return $result;
                
            }
            
        }
    }
    
    /**
     * Answers the default relation for the specified class.
     *
     * @param string $class
     *
     * @return integer
     */
    public function getDefaultRelation($class)
    {
        if ($defaultRelations = $this->config()->default_relations) {
            
            if (isset($defaultRelations[$class])) {
                
                $result = $this->processValue($defaultRelations[$class]);
                
                if ($result instanceof DataObject) {
                    return $result->ID;
                }
                
                return $result;
                
            }
            
        }
    }
    
    /**
     * Answers the default identifier for the associated class.
     *
     * @return string
     */
    public function getDefaultIdentifier()
    {
        if ($defaultIdentifiers = $this->config()->default_identifiers) {
            
            if (isset($defaultIdentifiers[$this->getClass()])) {
                return $defaultIdentifiers[$this->getClass()];
            }
            
        }
    }
    
    /**
     * Answers the relation list from the given object with the specified field name.
     *
     * @param DataObject $object
     * @param string $field
     *
     * @return RelationList
     */
    public function getRelationList(DataObject $object, $field)
    {
        if ($this->isHasManyRelation($object, $field)) {
            return $object->getComponents($field);
        } elseif ($this->isManyManyRelation($object, $field)) {
            return $object->getManyManyComponents($field);
        }
    }
    
    /**
     * Shows a database create message for the given object.
     *
     * @param DataObject $object
     * @param string $identifier
     *
     * @return void
     */
    public function createMessage(DataObject $object, $identifier)
    {
        $this->changeMessage(sprintf('Creating %s.%s', get_class($object), $identifier), 'created');
    }
    
    /**
     * Shows a database update message for the given object.
     *
     * @param DataObject $object
     * @param string $identifier
     *
     * @return void
     */
    public function updateMessage(DataObject $object, $identifier)
    {
        $this->changeMessage(sprintf('Updating %s.%s', get_class($object), $identifier), 'changed');
    }
    
    /**
     * Shows the given notice message.
     *
     * @param string $message
     *
     * @return void
     */
    public function noticeMessage($message)
    {
        $this->changeMessage($message, 'notice');
    }
    
    /**
     * Shows the given notice message if verbose mode is enabled.
     *
     * @param string $message
     *
     * @return void
     */
    public function verboseMessage($message)
    {
        if ($this->isVerbose()) {
            $this->noticeMessage($message);
        }
    }
    
    /**
     * Shows a database alteration message with the given details.
     *
     * @param string $message
     * @param string $type
     *
     * @return void
     */
    public function changeMessage($message, $type = '')
    {
        DB::alteration_message($message, $type);
    }
    
    /**
     * Disables data object validation while importing fixtures.
     *
     * @return $this
     */
    protected function disableValidation()
    {
        // Nest Configuration:
        
        Config::nest();
        
        // Update DataObject / File Configuration:
        
        Config::inst()->update('SilverStripe\ORM\DataObject', 'validation_enabled', false);
        Config::inst()->update('SilverStripe\Assets\File', 'update_filesystem', false);
        
        // Answer Self:
        
        return $this;
    }
    
    /**
     * Enables data object validation after importing fixtures.
     *
     * @return $this
     */
    protected function enableValidation()
    {
        // Un-Nest Configuration:
        
        Config::unnest();
        
        // Answer Self:
        
        return $this;
    }
    
    /**
     * Event method called before the object is created.
     *
     * @param string $identifier
     * @param array $data
     * @param array $fixtures
     *
     * @return void
     */
    protected function onBeforeCreate($identifier, &$data, &$fixtures)
    {
        $this->disableValidation()->invokeCallbacks('beforeCreate', [$identifier, $data, $fixtures]);
    }
    
    /**
     * Event method called after the object is created.
     *
     * @param DataObject $object
     * @param string $identifier
     * @param array $data
     * @param array $fixtures
     *
     * @return void
     */
    protected function onAfterCreate(DataObject $object, $identifier, &$data, &$fixtures)
    {
        $this->enableValidation()->invokeCallbacks('afterCreate', [$object, $identifier, $data, $fixtures]);
    }
    
    /**
     * Processes the given value and answers the resulting data.
     *
     * @param string|array $value
     *
     * @return mixed
     */
    protected function processValue($value)
    {
        if (is_array($value)) {
            
            // Handle Array Value:
            
            $value = $this->processArray($value);
            
        } else {
            
            // Handle String Value:
            
            $value = trim($value);
            
            if ($this->isReference($value)) {
                return $this->processReference($value);
            }
            
        }
        
        // Answer Value:
        
        return $value;
    }
    
    /**
     * Processes the given array value and answers the resulting data.
     *
     * @param array $value
     * @param boolean $json
     *
     * @return mixed
     */
    protected function processArray($value, $json = false)
    {
        if (is_array($value)) {
            
            if ($this->isCallbackArray($value)) {
                return $this->processCallbackArray($value);
            }
            
            return $json ? json_encode($value) : implode(', ', $value);
            
        }
        
        return $value;
    }
    
    /**
     * Processes the given callback array value and answers the resulting data.
     *
     * @param array $value
     *
     * @return mixed
     */
    protected function processCallbackArray($value)
    {
        if ($this->isCallbackArray($value)) {
            
            $class  = $value['class'];
            $method = $value['method'];
            
            if ($result = call_user_func("{$class}::{$method}")) {
                
                if (isset($value['property'])) {
                    return $result->{$value['property']};
                }
                
                return $result;
                
            }
            
        }
    }
    
    /**
     * Processes the given value as a file and answers the ID of the file record.
     *
     * @param DataObject $object
     * @param string $field
     * @param mixed $value
     * @param string $class
     *
     * @return integer
     */
    protected function processFile(DataObject $object, $field, $value, $class)
    {
        // Initialise:
        
        $source = null;
        $folder = null;
        
        // Detect Value Type:
        
        if (is_array($value)) {
            
            // Handle Array Value:
            
            $source = isset($value['Source']) ? $value['Source'] : null;
            $folder = isset($value['Folder']) ? $value['Folder'] : null;
            
        } else {
            
            // Handle String Value:
            
            $source = $value;
            
        }
        
        // Obtain Folder (if required):
        
        if (!$folder) {
            $folder = $this->getFolderName($object, $field);
        }
        
        // Obtain Source Path:
        
        $path = Director::getAbsFile($source);
        
        // Does Source Exist?
        
        if (file_exists($path)) {
            
            // Obtain Base Name:
            
            $basename = basename($path);
            
            // Define Asset Filename:
            
            $filename = sprintf('%s/%s', $folder, $basename);
            
            // Obtain or Create File:
            
            if (!($file = File::find($filename))) {
                $file = Injector::inst()->create($class);
            }
            
            // Define File Contents:
            
            $file->setFromLocalFile($path, $filename);
            
            // Write and Publish File:
            
            $file->write();
            $file->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);
            
            // Answer File ID:
            
            return $file->ID;
        }
    }
    
    /**
     * Attempts to answer the folder name for the given object and field name.
     *
     * @param DataObject $object
     * @param string $field
     *
     * @return string
     */
    protected function getFolderName(DataObject $object, $field)
    {
        // Initialise:
        
        $folder = null;
        
        // Locate File Handle Field:
        
        if ($fileField = $object->getCMSFields()->dataFieldByName($field)) {
            
            if ($fileField instanceof FileHandleField) {
                $folder = $fileField->getFolderName();
            }
            
        }
        
        // Answer Folder Name:
        
        return ($folder ? $folder : Upload::config()->uploads_folder);
    }
    
    /**
     * Processes the given reference string and answers the ID of the matching fixture.
     *
     * @param string $value
     *
     * @throws InvalidArgumentException
     *
     * @return integer
     */
    protected function processReference($value)
    {
        if ($this->isReference($value)) {
            
            list($class, $identifier) = $this->processTypedIdentifier($value);
            
            if (!$this->getFactory()->hasFixture($class, $identifier)) {
                
                throw new InvalidArgumentException(
                    sprintf(
                        'No fixture definition found for %s.%s',
                        $class,
                        $identifier
                    )
                );
                
            }
            
            return $this->getFactory()->getFixture($class, $identifier);
        }
    }
    
    /**
     * Processes the given value as an array and answers the result.
     *
     * @param mixed $value
     *
     * @return array
     */
    protected function processValueAsArray($value)
    {
        if (!is_array($value)) {
            return preg_split('/[\s,]+/', $value, -1, PREG_SPLIT_NO_EMPTY);
        }
        
        return $value;
    }
    
    /**
     * Answers an array containing the class and name of the given child identifier.
     *
     * @param DataObject $object
     * @param string $name
     *
     * @return array
     */
    protected function processChildIdentifier(DataObject $object, $name)
    {
        if ($this->isChildIdentifier($name)) {
            
            $name = ltrim($name, '+');
            
            if ($this->isTypedIdentifier($name)) {
                return $this->processTypedIdentifier($name);
            }
            
            return [$object->defaultChild(), $name];
            
        }
    }
    
    /**
     * Answers an array containing the class and name from the given typed identifier.
     *
     * @param string $identifier
     *
     * @return array
     */
    protected function processTypedIdentifier($identifier)
    {
        return array_pad(explode('.', ltrim($identifier, '+=>')), 2, null);
    }
    
    /**
     * Answers the name of the MatchOn filter.
     *
     * @return string
     */
    protected function getMatchOnName()
    {
        return $this->config()->match_on_name;
    }
    
    /**
     * Prepares the given array of data for object population.
     *
     * @param array $data
     *
     * @return array
     */
    protected function prepareData(&$data)
    {
        // Remove MatchOn Filter:
        
        $name = $this->getMatchOnName();
        
        if (isset($data[$name])) {
            unset($data[$name]);
        }
    }
    
    /**
     * Removes the 'ID' from the end of the given field name.
     *
     * @param string $name
     *
     * @return string
     */
    protected function trimID($name)
    {
        return preg_replace('/ID$/', '', $name);
    }
}
