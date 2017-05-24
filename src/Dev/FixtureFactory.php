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

use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Dev\FixtureFactory as BaseFactory;

/**
 * An extension of the SilverStripe fixture factory class for building SilverWare objects from fixtures.
 *
 * @package SilverWare\Dev
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class FixtureFactory extends BaseFactory
{
    use Configurable;
    
    /**
     * Defines the default blueprint class to use when creating objects.
     *
     * @var string
     * @config
     */
    private static $default_blueprint;
    
    /**
     * Writes the fixture into the database using data objects.
     *
     * @param string $name Class name (subclass of DataObject).
     * @param string $identifier Unique identifier for this fixture type.
     * @param array $data Optional map of properties which overrides the default data.
     * @param array $filter Optional map of properties to use as a filter.
     *
     * @return DataObject
     */
    public function createObject($name, $identifier, $data = null, $filter = [])
    {
        // Create Object:
        
        $object = $this->getBlueprintOrDefault($name)->createObject($identifier, $data, $this->fixtures, $filter);
        
        // Answer Object:
        
        return $object;
    }
    
    /**
     * Answers either an existing blueprint or a new instance of the default blueprint for the specified class.
     *
     * @param string $name Class name (subclass of DataObject).
     *
     * @return FixtureBlueprint
     */
    public function getBlueprintOrDefault($name)
    {
        if (!$this->getBlueprint($name)) {
            $this->blueprints[$name] = $this->getDefaultBlueprint($name);
        }
        
        return $this->blueprints[$name]->setFactory($this);
    }
    
    /**
     * Answers the default blueprint defined for this factory.
     *
     * @param string $name Class name (subclass of DataObject).
     *
     * @return FixtureBlueprint
     */
    public function getDefaultBlueprint($name)
    {
        return Injector::inst()->create($this->config()->get('default_blueprint'), $name);
    }
    
    /**
     * Adds the identifier and ID of the given object to the fixtures map.
     *
     * @param DataObject $object
     * @param string $identifier
     *
     * @return void
     */
    public function addFixture($object, $identifier)
    {
        if (!isset($this->fixtures[get_class($object)])) {
            $this->fixtures[get_class($object)] = [];
        }
        
        $this->fixtures[get_class($object)][$identifier] = $object->ID;
    }
    
    /**
     * Answers the ID for the fixture with the given class and identifier.
     *
     * @param string $class
     * @param string $identifier
     *
     * @return integer
     */
    public function getFixture($class, $identifier)
    {
        if ($this->hasFixture($class, $identifier)) {
            return $this->fixtures[$class][$identifier];
        }
    }
    
    /**
     * Answers true if a fixture exists with the given class and identifier.
     *
     * @param string $class
     * @param string $identifier
     *
     * @return boolean
     */
    public function hasFixture($class, $identifier)
    {
        return isset($this->fixtures[$class][$identifier]);
    }
}
