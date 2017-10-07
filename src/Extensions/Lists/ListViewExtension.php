<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Extensions\Lists
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\Extensions\Lists;

use SilverStripe\Control\Controller;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Convert;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Versioned\Versioned;
use SilverWare\Colorpicker\Forms\ColorField;
use SilverWare\Components\BaseListComponent;
use SilverWare\Components\ListComponent;
use SilverWare\Folders\ComponentFolder;
use SilverWare\FontIcons\Forms\FontIconField;
use SilverWare\Forms\DimensionsField;
use SilverWare\Forms\FieldSection;
use SilverWare\Forms\ViewportsField;
use SilverWare\ORM\FieldType\DBViewports;
use SilverWare\Tools\ClassTools;
use SilverWare\Tools\ImageTools;
use SilverWare\Tools\ViewTools;

/**
 * A data extension class to add list view functionality to the extended object.
 *
 * @package SilverWare\Extensions\Lists
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class ListViewExtension extends DataExtension
{
    /**
     * Define constants.
     */
    const FIELD_WRAPPER = 'ListObject';
    
    /**
     * Maps field names to field types for the extended object.
     *
     * @var array
     * @config
     */
    private static $db = [
        'ListClass' => 'Varchar(255)',
        'ListInherit' => 'Boolean'
    ];
    
    /**
     * Defines the has-one associations for the extended object.
     *
     * @var array
     * @config
     */
    private static $has_one = [
        'ListObject' => BaseListComponent::class
    ];
    
    /**
     * Defines the default list component class to use.
     *
     * @var string
     * @config
     */
    private static $default_list_component_class = ListComponent::class;
    
    /**
     * Updates the CMS fields of the extended object.
     *
     * @param FieldList $fields List of CMS fields from the extended object.
     *
     * @return void
     */
    public function updateCMSFields(FieldList $fields)
    {
        // Create Tabs:
        
        $fields->findOrMakeTab('Root.Style', $this->owner->fieldLabel('Style'));
        $fields->findOrMakeTab('Root.Options', $this->owner->fieldLabel('Options'));
        
        // Add Extension Class:
        
        $fields->fieldByName('Root')->addExtraClass(ViewTools::singleton()->convertClass(self::class));
        
        // Define Placeholder:
        
        $placeholder = _t(__CLASS__ . '.DROPDOWNDEFAULT', '(default)');
        
        // Obtain List Object:
        
        $list = $this->owner->getListObject();
        
        // Create Style Fields:
        
        $fields->addFieldToTab(
            'Root.Style',
            $styleOpts = FieldSection::create(
                'ListViewStyle',
                $this->owner->fieldLabel('ListView'),
                [
                    DropdownField::create(
                        $this->nestName('HeadingLevel'),
                        $this->owner->fieldLabel('HeadingLevel'),
                        $list->getTitleLevelOptions(),
                        $list->HeadingLevel
                    )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder),
                    ViewportsField::create(
                        $this->nestName('TextAlignment'),
                        $this->owner->fieldLabel('TextAlignment'),
                        $list->getTextAlignmentOptions(),
                        $list->TextAlignment
                    ),
                    DropdownField::create(
                        $this->nestName('ImageAlign'),
                        $this->owner->fieldLabel('ImageAlignment'),
                        $list->getImageAlignOptions(),
                        $list->ImageAlign
                    )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder),
                    DimensionsField::create(
                        $this->nestName('ImageResize'),
                        $this->owner->fieldLabel('ImageResize'),
                        $list->ImageResize
                    ),
                    DropdownField::create(
                        $this->nestName('ImageResizeMethod'),
                        $this->owner->fieldLabel('ImageResizeMethod'),
                        ImageTools::singleton()->getResizeMethods(),
                        $list->ImageResizeMethod
                    )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder),
                    FontIconField::create(
                        $this->nestName('OverlayIcon'),
                        $this->owner->fieldLabel('OverlayIcon')
                    )->setValue($list->OverlayIcon),
                    ColorField::create(
                        $this->nestName('OverlayIconColor'),
                        $this->owner->fieldLabel('OverlayIconColor'),
                        $list->OverlayIconColor
                    ),
                    FontIconField::create(
                        $this->nestName('ButtonIcon'),
                        $this->owner->fieldLabel('ButtonIcon')
                    )->setValue($list->ButtonIcon)
                ]
            )
        );
        
        // Add List Style Fields:
        
        $fields->addFieldsToTab('Root.Style', $this->getListStyleFields());
        
        // Create Options Fields:
        
        $fields->addFieldsToTab(
            'Root.Options',
            [
                $inheritOpts = FieldSection::create(
                    'ListInheritanceOptions',
                    $this->owner->fieldLabel('ListInheritance'),
                    [
                        CheckboxField::create(
                            'ListInherit',
                            $this->owner->fieldLabel('ListInherit')
                        )
                    ]
                ),
                $sourceOpts = FieldSection::create(
                    'ListSourceOptions',
                    $this->owner->fieldLabel('ListSource'),
                    [
                        TextField::create(
                            $this->nestName('NumberOfItems'),
                            $this->owner->fieldLabel('NumberOfItems'),
                            $list->NumberOfItems
                        ),
                        DropdownField::create(
                            $this->nestName('ReverseItems'),
                            $this->owner->fieldLabel('ReverseItems'),
                            $this->getToggleOptions(),
                            $list->ReverseItems
                        ),
                        DropdownField::create(
                            $this->nestName('ImageItems'),
                            $this->owner->fieldLabel('ImageItems'),
                            $this->getToggleOptions(),
                            $list->ImageItems
                        )
                    ]
                ),
                $viewOpts = FieldSection::create(
                    'ListViewOptions',
                    $this->owner->fieldLabel('ListView'),
                    [
                        DropdownField::create(
                            'ListClass',
                            $this->owner->fieldLabel('ListClass'),
                            $this->owner->getListClassOptions()
                        )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder),
                        DropdownField::create(
                            $this->nestName('ShowImage'),
                            $this->owner->fieldLabel('ShowImage'),
                            $list->getShowOptions(),
                            $list->ShowImage
                        ),
                        DropdownField::create(
                            $this->nestName('ShowHeader'),
                            $this->owner->fieldLabel('ShowHeader'),
                            $list->getShowOptions(),
                            $list->ShowHeader
                        ),
                        DropdownField::create(
                            $this->nestName('ShowDetails'),
                            $this->owner->fieldLabel('ShowDetails'),
                            $list->getShowOptions(),
                            $list->ShowDetails
                        ),
                        DropdownField::create(
                            $this->nestName('ShowSummary'),
                            $this->owner->fieldLabel('ShowSummary'),
                            $list->getShowOptions(),
                            $list->ShowSummary
                        ),
                        DropdownField::create(
                            $this->nestName('ShowContent'),
                            $this->owner->fieldLabel('ShowContent'),
                            $list->getShowOptions(),
                            $list->ShowContent
                        ),
                        DropdownField::create(
                            $this->nestName('ShowFooter'),
                            $this->owner->fieldLabel('ShowFooter'),
                            $list->getShowOptions(),
                            $list->ShowFooter
                        ),
                        TextField::create(
                            $this->nestName('ButtonLabel'),
                            $this->owner->fieldLabel('ButtonLabel'),
                            $list->ButtonLabel
                        ),
                        DropdownField::create(
                            $this->nestName('LinkTitles'),
                            $this->owner->fieldLabel('LinkTitles'),
                            $this->getToggleOptions(),
                            $list->LinkTitles
                        )
                    ]
                ),
                $imageOpts = FieldSection::create(
                    'ListImageOptions',
                    $this->owner->fieldLabel('ListImages'),
                    [
                        DropdownField::create(
                            $this->nestName('LinkImages'),
                            $this->owner->fieldLabel('LinkImages'),
                            $this->getToggleOptions(),
                            $list->LinkImages
                        ),
                        DropdownField::create(
                            $this->nestName('ImageLinksTo'),
                            $this->owner->fieldLabel('ImageLinksTo'),
                            $list->getImageLinksToOptions(),
                            $list->ImageLinksTo
                        ),
                        DropdownField::create(
                            $this->nestName('OverlayImages'),
                            $this->owner->fieldLabel('OverlayImages'),
                            $this->getToggleOptions(),
                            $list->OverlayImages
                        )
                    ]
                )
            ]
        );
        
        // Add List Option Fields:
        
        $fields->addFieldsToTab('Root.Options', $this->getListOptionFields());
        
        // Merge Pagination Fields:
        
        if ($list->canPaginate()) {
            
            $sourceOpts->merge([
                DropdownField::create(
                    $this->nestName('PaginateItems'),
                    $this->owner->fieldLabel('PaginateItems'),
                    $this->getToggleOptions(),
                    $list->PaginateItems
                ),
                TextField::create(
                    $this->nestName('ItemsPerPage'),
                    $this->owner->fieldLabel('ItemsPerPage'),
                    $list->ItemsPerPage
                )
            ]);
            
        }
        
        // Hide Fields (if required):
        
        if (!$this->owner->canListInherit()) {
            $inheritOpts->addExtraClass('hidden');
        }
        
        if ($this->owner->ListInherit) {
            $viewOpts->addExtraClass('hidden');
            $imageOpts->addExtraClass('hidden');
            $styleOpts->addExtraClass('hidden');
            $sourceOpts->addExtraClass('hidden');
        }
    }
    
    /**
     * Updates the field labels of the extended object.
     *
     * @param array $labels Array of field labels from the extended object.
     *
     * @return void
     */
    public function updateFieldLabels(&$labels)
    {
        $labels['Style'] = _t(__CLASS__ . '.STYLE', 'Style');
        $labels['Options'] = _t(__CLASS__ . '.OPTIONS', 'Options');
        $labels['Enabled'] = _t(__CLASS__ . '.ENABLED', 'Enabled');
        $labels['Disabled'] = _t(__CLASS__ . '.DISABLED', 'Disabled');
        $labels['ListView'] = _t(__CLASS__ . '.LISTVIEW', 'List View');
        $labels['ListImages'] = _t(__CLASS__ . '.LISTIMAGES', 'List Images');
        $labels['ListTitle'] = _t(__CLASS__ . '.TITLE', 'List title');
        $labels['ShowImage'] = _t(__CLASS__ . '.SHOWIMAGE', 'Show image');
        $labels['ShowHeader'] = _t(__CLASS__ . '.SHOWHEADER', 'Show header');
        $labels['ShowDetails'] = _t(__CLASS__ . '.SHOWDETAILS', 'Show details');
        $labels['ShowSummary'] = _t(__CLASS__ . '.SHOWSUMMARY', 'Show summary');
        $labels['ShowContent'] = _t(__CLASS__ . '.SHOWCONTENT', 'Show content');
        $labels['ShowFooter'] = _t(__CLASS__ . '.SHOWFOOTER', 'Show footer');
        $labels['LinkTitles'] = _t(__CLASS__ . '.LINKTITLES', 'Link titles');
        $labels['LinkImages'] = _t(__CLASS__ . '.LINKIMAGES', 'Link images');
        $labels['ButtonIcon'] = _t(__CLASS__ . '.BUTTONLABEL', 'Button icon');
        $labels['ButtonLabel'] = _t(__CLASS__ . '.BUTTONLABEL', 'Button label');
        $labels['HeadingLevel'] = _t(__CLASS__ . '.HEADINGLEVEL', 'Heading level');
        $labels['ItemsPerPage'] = _t(__CLASS__ . '.ITEMSPERPAGE', 'Items per page');
        $labels['PaginateItems'] = _t(__CLASS__ . '.PAGINATEITEMS', 'Paginate items');
        $labels['ImageLinksTo'] = _t(__CLASS__ . '.IMAGELINKSTO', 'Image links to');
        $labels['ImageResize'] = _t(__CLASS__ . '.IMAGEDIMENSIONS', 'Image dimensions');
        $labels['ImageResizeMethod'] = _t(__CLASS__ . '.IMAGERESIZEMETHOD', 'Image resize method');
        $labels['ImageAlignment'] = _t(__CLASS__ . '.IMAGEALIGNMENT', 'Image alignment');
        $labels['TextAlignment'] = _t(__CLASS__ . '.TEXTALIGNMENT', 'Text alignment');
        $labels['OverlayImages'] = _t(__CLASS__ . '.OVERLAYIMAGES', 'Overlay images');
        $labels['OverlayIcon'] = _t(__CLASS__ . '.OVERLAYICON', 'Overlay icon');
        $labels['OverlayIconColor'] = _t(__CLASS__ . '.OVERLAYICONCOLOR', 'Overlay icon color');
        $labels['NumberOfItems'] = _t(__CLASS__ . '.NUMBEROFITEMS', 'Number of items');
        $labels['ReverseItems'] = _t(__CLASS__ . '.REVERSEITEMS', 'Reverse items');
        $labels['ImageItems'] = _t(__CLASS__ . '.IMAGEITEMS', 'Show only items with images');
        $labels['ListClass'] = _t(__CLASS__ . '.LISTCLASS', 'List class');
        $labels['ListInherit'] = _t(__CLASS__ . '.INHERITFROMPARENT', 'Inherit from parent');
        $labels['ListInheritance'] = _t(__CLASS__ . '.LISTINHERITANCE', 'List Inheritance');
    }
    
    /**
     * Event method called before the extended object is written to the database.
     *
     * @return void
     */
    public function onBeforeWrite()
    {
        $this->owner->updateListObject();
    }
    
    /**
     * Event method called after the extended object is published.
     *
     * @return void
     */
    public function onAfterPublish()
    {
        $this->owner->getListObject()->publishSingle();
    }
    
    /**
     * Event method called after the extended object is version published.
     *
     * @param int|string $fromStage
     * @param string $toStage
     *
     * @return void
     */
    public function onAfterVersionedPublish($fromStage, $toStage)
    {
        $this->owner->getListObject()->copyVersionToStage($fromStage, $toStage);
    }
    
    /**
     * Event method called after the extended object is unpublished.
     *
     * @return void
     */
    public function onAfterUnpublish()
    {
        $this->owner->getListObject()->doUnpublish();
    }
    
    /**
     * Event method called after the extended object is archived.
     *
     * @return void
     */
    public function onAfterArchive()
    {
        $this->owner->getListObject()->doArchive();
    }
    
    /**
     * Event method called after the extended object is reverted to live.
     *
     * @return void
     */
    public function onAfterRevertToLive()
    {
        $this->owner->getListObject()->doRevertToLive();
    }
    
    /**
     * Answers true if the extended object can inherit a list object from the parent.
     *
     * @return boolean
     */
    public function canListInherit()
    {
        if ($parent = $this->owner->getParent()) {
            return $parent->hasExtension(self::class);
        }
        
        return false;
    }
    
    /**
     * Answers the source for the list component.
     *
     * @return ListSource|SS_List
     */
    public function getListSource()
    {
        return $this->owner;
    }
    
    /**
     * Answers the class to use for the list component.
     *
     * @return string
     */
    public function getListComponentClass()
    {
        if ($class = $this->owner->ListClass) {
            return $class;
        }
        
        return $this->owner->getDefaultListComponentClass();
    }
    
    /**
     * Answers the default class to use for the list component.
     *
     * @return string
     */
    public function getDefaultListComponentClass()
    {
        if ($class = $this->owner->config()->list_component_class) {
            return $class;
        }
        
        return Config::inst()->get(self::class, 'default_list_component_class');
    }
    
    /**
     * Answers the default values for the list component.
     *
     * @return array
     */
    public function getListComponentDefaults()
    {
        return $this->owner->config()->list_view_defaults ?: [];
    }
    
    /**
     * Answers the list component for the template.
     *
     * @return BaseListComponent
     */
    public function getListComponent()
    {
        // Obtain List Object:
        
        $list = $this->owner->getListObjectInherited();
        
        // Define List Parent:
        
        $list->setParentInstance($this->owner);
        
        // Define List Source:
        
        $list->setSource($this->owner->getListSource());
        
        // Initialise Component:
        
        $list->doInit();
        
        // Answer List Component:
        
        return $list;
    }
    
    /**
     * Answers true if the extended object provides a list component for the template.
     *
     * @return boolean
     */
    public function hasListComponent()
    {
        return ($this->owner->ListComponent instanceof BaseListComponent);
    }
    
    /**
     * Answers an array of options for the list class field.
     *
     * @return array
     */
    public function getListClassOptions()
    {
        $options = [];
        
        foreach (ClassTools::singleton()->getVisibleSubClasses(BaseListComponent::class) as $class) {
            $options[$class] = Injector::inst()->get($class)->i18n_singular_name();
        }
        
        return $options;
    }
    
    /**
     * Answers the list component instance associated with the extended object.
     *
     * @return BaseListComponent
     */
    public function getListObject()
    {
        return $this->owner->getComponent('ListObject');
    }
    
    /**
     * Answers the list component instance associated with the extended object or alternatively the parent.
     *
     * @return BaseListComponent
     */
    public function getListObjectInherited()
    {
        return $this->owner->ListInherit ? $this->owner->getParent()->getListObject() : $this->owner->getListObject();
    }
    
    /**
     * Create a new instance of the list component.
     *
     * @return BaseListComponent
     */
    public function createListObject()
    {
        // Create List Object:
        
        $object = Injector::inst()->create(
            $this->owner->getListComponentClass(),
            $this->owner->getListComponentDefaults()
        );
        
        // Define List Object:
        
        if ($folder = ComponentFolder::find()) {
            $object->ParentID = $folder->ID;
            $object->write();
        }
        
        // Answer List Object:
        
        return $object;
    }
    
    /**
     * Answers either an existing instance or a new instance of the list object.
     *
     * @return BaseListComponent
     */
    public function findOrMakeListObject()
    {
        // Obtain List Object:
        
        $object = $this->owner->getListObject();
        
        // Create List Object (if required):
        
        if (!$object->isInDB()) {
            $object = $this->owner->createListObject();
        }
        
        // Answer List Object:
        
        return $object;
    }
    
    /**
     * Updates the list component instance within the database.
     *
     * @return void
     */
    public function updateListObject()
    {
        // Bail Early (if not draft):
        
        if (Versioned::get_stage() !== Versioned::DRAFT) {
           return; 
        }
        
        // Obtain List Object:
        
        $object = $this->owner->findOrMakeListObject();
        
        // Verify List Object Exists:
        
        if ($object->isInDB()) {
            
            // Obtain List Object Class:
            
            $class = $this->owner->getListComponentClass();
            
            // Mutate List Object (if required):
            
            if ($object->ClassName != $class) {
                $object = $object->newClassInstance($class);
            }
            
            // Define List Object:
            
            $object->Title     = $this->owner->Title;
            $object->HideTitle = 1;
            
            if ($request = Controller::curr()->getRequest()) {
                
                if ($config = $request->postVar(self::FIELD_WRAPPER)) {
                    
                    foreach ($config as $name => $value) {
                        $object->$name = $value;
                    }
                    
                }
                
            }
            
            // Record List Object:
            
            $object->write();
            
            // Associate with Owner:
            
            $this->owner->ListObjectID = $object->ID;
            
        }
    }
    
    /**
     * Answers the style fields for the list component.
     *
     * @return FieldList
     */
    protected function getListStyleFields()
    {
        $fields = FieldList::create();
        
        if ($list = $this->owner->getListObject()) {
            
            $fields = $list->getListStyleFields();
            
            foreach ($fields->dataFields() as $field) {
                
                $name = $field->getName();
                
                if (isset($list->$name)) {
                    $field->setValue($list->$name, $list);
                }
                
            }
            
            $this->hideFields($fields);
            
        }
        
        return $this->nest($fields);
    }
    
    /**
     * Answers the option fields for the list component.
     *
     * @return FieldList
     */
    protected function getListOptionFields()
    {
        $fields = FieldList::create();
        
        if ($list = $this->owner->getListObject()) {
            
            $fields = $list->getListOptionFields();
            
            foreach ($fields->dataFields() as $field) {
                
                $name = $field->getName();
                
                if (isset($list->$name)) {
                    $field->setValue($list->$name, $list);
                }
                
            }
            
            $this->hideFields($fields);
            
        }
        
        return $this->nest($fields);
    }
    
    /**
     * Hides the given list of fields if the extended object inherits the list component.
     *
     * @param FieldList $fields
     *
     * @return FieldList
     */
    public function hideFields(FieldList $fields)
    {
        if ($this->owner->ListInherit) {
            
            foreach ($fields as $field) {
                $field->addExtraClass('hidden');
            }
            
        }
        
        return $fields;
    }
    
    /**
     * Answers an array of options for a toggle dropdown field.
     *
     * @return array
     */
    protected function getToggleOptions()
    {
        return [
            0 => _t(__CLASS__ . '.TOGGLENO', 'No'),
            1 => _t(__CLASS__ . '.TOGGLEYES', 'Yes')
        ];
    }
    
    /**
     * Nests the names of the given fields within the list config wrapper.
     *
     * @param FieldList $fields
     *
     * @return FieldList
     */
    protected function nest(FieldList $fields)
    {
        // Iterate Data Fields:
        
        foreach ($fields->dataFields() as $field) {
            $field->setName($this->nestName($field->getName()));
        }
        
        // Answer Fields:
        
        return $fields;
    }
    
    /**
     * Nests the given field name within the list config wrapper.
     *
     * @param string $name
     *
     * @return string
     */
    protected function nestName($name)
    {
        // Obtain Bracket Position:
        
        $bpos = strpos($name, '[');
        
        // Has Bracket?
        
        if ($bpos !== false) {
            
            // Answer Bracketed Name:
            
            return sprintf('%s[%s]%s', self::FIELD_WRAPPER, substr($name, 0, $bpos), substr($name, $bpos));
            
        } else {
            
            // Answer Regular Name:
            
            return sprintf('%s[%s]', self::FIELD_WRAPPER, $name);
            
        }
    }
}
