<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\ORM
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\ORM;

use SilverStripe\Core\ClassInfo;
use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\ReadonlyField;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\TabSet;
use SilverStripe\ORM\DataObject;
use SilverWare\Security\CMSMainPermissions;

/**
 * An extension of the data object class for a multi-class object.
 *
 * @package SilverWare\ORM
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class MultiClassObject extends DataObject
{
    use CMSMainPermissions;
    
    /**
     * Defines the table name to use for this object.
     *
     * @var string
     * @config
     */
    private static $table_name = 'SilverWare_MultiClassObject';
    
    /**
     * Defines the summary fields of this object.
     *
     * @var array
     * @config
     */
    private static $summary_fields = [
        'Type'
    ];
    
    /**
     * Defines an ancestor class to hide from the admin interface.
     *
     * @var string
     * @config
     */
    private static $hide_ancestor;
    
    /**
     * Answers a list of field objects for the CMS interface.
     *
     * @return FieldList
     */
    public function getCMSFields()
    {
        // Create Field Tab Set:
        
        $fields = FieldList::create(TabSet::create('Root'));
        
        // Create Main Tab:
        
        $fields->findOrMakeTab('Root.Main', $this->fieldLabel('Main'));
        
        // Create Class Field:
        
        if (!$this->isInDB()) {
            
            // Define Placeholder:
            
            $placeholder = _t(__CLASS__ . '.SELECTTYPETOCREATE', '(Select type to create)');
            
            // Create Dropdown Field:
            
            $classField = DropdownField::create(
                'ClassName',
                $this->fieldLabel('ClassName'),
                $this->getClassNameOptions()
            )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder);
            
        } else {
            
            // Create Read-Only Field:
            
            $classField = ReadonlyField::create(
                'Type',
                $this->fieldLabel('Type')
            );
            
        }
        
        // Add Class Field to Main Tab:
        
        $fields->addFieldToTab('Root.Main', $classField);
        
        // Apply Extensions:
        
        if ($this->isInDB()) {
            
            // Extend Field Objects:
            
            $this->extend('updateCMSFields', $fields);
            
        }
        
        // Answer Field Objects:
        
        return $fields;
    }
    
    /**
     * Answers a validator for the CMS interface.
     *
     * @return RequiredFields
     */
    public function getCMSValidator()
    {
        return RequiredFields::create([
            'ClassName'
        ]);
    }
    
    /**
     * Answers the labels for the fields of the receiver.
     *
     * @param boolean $includerelations Include labels for relations.
     *
     * @return array
     */
    public function fieldLabels($includerelations = true)
    {
        // Obtain Field Labels (from parent):
        
        $labels = parent::fieldLabels($includerelations);
        
        // Define Field Labels:
        
        $labels['ClassName'] = $labels['Type'] = _t(__CLASS__ . '.TYPE', 'Type');
        
        // Answer Field Labels:
        
        return $labels;
    }
    
    /**
     * Answers the title of the receiver for the CMS interface.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getType();
    }
    
    /**
     * Answers a string describing the type of object (defaults to singular name).
     *
     * @return string
     */
    public function getType()
    {
        return $this->i18n_singular_name();
    }
    
    /**
     * Answers an array of options for the class name field.
     *
     * @return array
     */
    protected function getClassNameOptions()
    {
        $hidden  = [];
        $classes = [];
        
        foreach ($this->dbObject('ClassName')->enumValues() as $class) {
            
            if ($class == self::class || !in_array($class, ClassInfo::subclassesFor($this))) {
                continue;
            }
            
            if ($hide = Config::inst()->get($class, 'hide_ancestor')) {
                $hidden[$hide] = true;
            }
            
            if (!$class::singleton()->canCreate()) {
                continue;
            }
            
            $classes[$class] = $class::singleton()->i18n_singular_name();
            
        }
        
        foreach ($hidden as $class => $hide) {
            unset($classes[$class]);
        }
        
        return $classes;
    }
}
