<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Forms\GridField
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\Forms\GridField;

use SilverStripe\Core\ClassInfo;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverWare\Tools\ClassTools;
use Symbiote\GridFieldExtensions\GridFieldAddNewMultiClass;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;

/**
 * An extension of the grid field config record editor class for a multi-class editor config.
 *
 * @package SilverWare\Forms\GridField
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class GridFieldConfig_MultiClassEditor extends GridFieldConfig_RecordEditor
{
    /**
     * Constructs the object upon instantiation.
     *
     * @param integer $itemsPerPage
     */
    public function __construct($itemsPerPage = null)
    {
        // Construct Parent:
        
        parent::__construct($itemsPerPage);
        
        // Construct Object:
        
        $this->addComponents(
            new GridFieldOrderableRows(),
            new GridFieldAddNewMultiClass()
        )->removeComponentsByType(GridFieldAddNewButton::class);
        
        // Apply Extensions:
        
        $this->extend('updateConfig');
    }
    
    /**
     * Defines the classes that can be created using the grid field.
     *
     * @param array $classes Class names optionally mapped to titles.
     * @param string $default Optional default class name.
     *
     * @return $this
     */
    public function setClasses($classes, $default = null)
    {
        if ($component = $this->getComponentByType(GridFieldAddNewMultiClass::class)) {
            $component->setClasses($classes, $default);
        }
        
        return $this;
    }
    
    /**
     * Defines the creatable classes as descendants of the specified class (does not include the specified class).
     *
     * @param string $class Parent class name.
     * @param string $default Optional default class name.
     *
     * @return $this
     */
    public function useDescendantsOf($class, $default = null)
    {
        return $this->setClasses(array_values(ClassTools::singleton()->getDescendantsOf($class)), $default);
    }
    
    /**
     * Defines the creatable classes as subclasses of the specified class (includes the specified class).
     *
     * @param string $class Parent class name.
     * @param string $default Optional default class name.
     *
     * @return $this
     */
    public function useSubclassesOf($class, $default = null)
    {
        return $this->setClasses(array_values(ClassInfo::subclassesFor($class)), $default);
    }
}
