<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Model
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\Model;

use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Core\ClassInfo;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\ORM\DB;
use SilverStripe\Security\Permission;
use SilverStripe\Security\PermissionProvider;
use SilverStripe\Versioned\Versioned;

/**
 * An extension of the site tree class for a SilverWare folder.
 *
 * @package SilverWare\Model
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class Folder extends SiteTree implements PermissionProvider
{
    /**
     * Icon file for this object.
     *
     * @var string
     * @config
     */
    private static $icon = 'silverware/admin/client/dist/images/icons/Folder.png';
    
    /**
     * Defines the default values for the fields of this object.
     *
     * @var array
     * @config
     */
    private static $defaults = [
        'ShowInMenus' => 0,
        'ShowInSearch' => 0
    ];
    
    /**
     * Answers the folder instance (only one of each type of folder should exist).
     *
     * @param string $class Folder class to locate (optional).
     *
     * @return Folder
     */
    public static function find($class = null)
    {
        return self::get($class ? $class : static::class)->first();
    }
    
    /**
     * Answers a list of field objects for the CMS interface.
     *
     * @return FieldList
     */
    public function getCMSFields()
    {
        // Obtain Field Objects (from parent):
        
        $fields = parent::getCMSFields();
        
        // Remove Field Objects:
        
        $fields->removeFieldsFromTab('Root.Main', ['Content', 'Metadata']);
        
        // Modify Field Objects:
        
        $fields->dataFieldByName('MenuTitle')->addExtraClass('hidden');
        
        // Answer Field Objects:
        
        return $fields;
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
        
        $labels['Title'] = _t(__CLASS__ . '.NAME', 'Name');
        
        // Answer Field Labels:
        
        return $labels;
    }
    
    /**
     * Creates any required default folders (if they do not already exist).
     *
     * @return void
     */
    public function createDefaultFolders()
    {
        foreach ($this->config()->default_folders as $class) {
            
            if (!self::find($class)) {
                
                $folder = Injector::inst()->create($class);
                
                $folder->Sort = 0;
                
                $folder->write();
                $folder->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);
                $folder->flushCache();
                
                DB::alteration_message(sprintf('Creating %s.%s', $class, $folder->Title), 'created');
                
            }
            
        }
    }
    
    /**
     * Answers a string of CSS classes to apply to the receiver in the CMS tree.
     *
     * @return string
     */
    public function CMSTreeClasses()
    {
        $classes = parent::CMSTreeClasses();
        
        if (!$this->canEdit()) {
            $classes .= ' hidden';
        }
        
        $this->extend('updateCMSTreeClasses', $classes);
        
        return $classes;
    }
    
    /**
     * Answers true if the member can create a new instance of the receiver.
     *
     * @param Member $member Optional member object.
     * @param array $context Context-specific data.
     *
     * @return boolean
     */
    public function canCreate($member = null, $context = [])
    {
        // Allow Only Root Level:
        
        if (isset($context['Parent'])) {
            return false;
        }
        
        // Answer Permission Check:
        
        return Permission::checkMember($member, ['ADMIN', 'SILVERWARE_FOLDER_CREATE']);
    }
    
    /**
     * Answers true if the member can delete the receiver.
     *
     * @param Member $member
     *
     * @return boolean
     */
    public function canDelete($member = null)
    {
        return Permission::checkMember($member, ['ADMIN', 'SILVERWARE_FOLDER_DELETE']);
    }
    
    /**
     * Answers true if the member can edit the receiver.
     *
     * @param Member $member
     *
     * @return boolean
     */
    public function canEdit($member = null)
    {
        return Permission::checkMember($member, ['ADMIN', 'SILVERWARE_FOLDER_EDIT']);
    }
    
    /**
     * Provides the permissions for the security interface.
     *
     * @return array
     */
    public function providePermissions()
    {
        $category = _t(__CLASS__ . '.PERMISSION_CATEGORY', 'SilverWare folders');
        
        return [
            'SILVERWARE_FOLDER_CREATE' => [
                'category' => $category,
                'name' => _t('.PERMISSION_CREATE_NAME', 'Create folders'),
                'help' => _t('.PERMISSION_CREATE_HELP', 'Ability to create SilverWare folders.'),
                'sort' => 100
            ],
            'SILVERWARE_FOLDER_EDIT' => [
                'category' => $category,
                'name' => _t('.PERMISSION_EDIT_NAME', 'Edit folders'),
                'help' => _t('.PERMISSION_EDIT_HELP', 'Ability to edit SilverWare folders.'),
                'sort' => 200
            ],
            'SILVERWARE_FOLDER_DELETE' => [
                'category' => $category,
                'name' => _t('.PERMISSION_DELETE_NAME', 'Delete folders'),
                'help' => _t('.PERMISSION_DELETE_HELP', 'Ability to delete SilverWare folders.'),
                'sort' => 300
            ]
        ];
    }
}
