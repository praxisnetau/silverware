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
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataExtension;
use SilverWare\Colorpicker\Forms\ColorField;
use SilverWare\Components\BaseListComponent;
use SilverWare\Components\ListComponent;
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
    const FIELD_WRAPPER = 'ListConfig';
    
    /**
     * Maps field names to field types for the extended object.
     *
     * @var array
     * @config
     */
    private static $db = [
        'ListClass' => 'Varchar(255)',
        'ListConfig' => 'Text'
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
        
        // Create List Component:
        
        $list = $this->owner->getListComponent();
        
        // Create Style Fields:
        
        $fields->addFieldToTab(
            'Root.Style',
            FieldSection::create(
                'ListViewStyle',
                $this->owner->fieldLabel('ListView'),
                [
                    DropdownField::create(
                        $this->nestName('HeadingLevel'),
                        $this->owner->fieldLabel('HeadingLevel'),
                        $list->getTitleLevelOptions()
                    )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder),
                    ViewportsField::create(
                        $this->nestName('TextAlignment'),
                        $this->owner->fieldLabel('TextAlignment'),
                        $list->getTextAlignmentOptions()
                    ),
                    ViewportsField::create(
                        $this->nestName('ImageAlignment'),
                        $this->owner->fieldLabel('ImageAlignment'),
                        $list->getImageAlignmentOptions()
                    ),
                    DimensionsField::create(
                        $this->nestName('ImageResize'),
                        $this->owner->fieldLabel('ImageResize')
                    ),
                    DropdownField::create(
                        $this->nestName('ImageResizeMethod'),
                        $this->owner->fieldLabel('ImageResizeMethod'),
                        ImageTools::singleton()->getResizeMethods()
                    )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder),
                    FontIconField::create(
                        $this->nestName('OverlayIcon'),
                        $this->owner->fieldLabel('OverlayIcon')
                    ),
                    ColorField::create(
                        $this->nestName('OverlayIconColor'),
                        $this->owner->fieldLabel('OverlayIconColor')
                    )
                ]
            )
        );
        
        // Add List Style Fields:
        
        $fields->addFieldsToTab('Root.Style', $this->nest($list->getListStyleFields()));
        
        // Create Options Fields:
        
        $fields->addFieldsToTab(
            'Root.Options',
            [
                $sourceOpts = FieldSection::create(
                    'ListSourceOptions',
                    $this->owner->fieldLabel('ListSource'),
                    [
                        TextField::create(
                            $this->nestName('NumberOfItems'),
                            $this->owner->fieldLabel('NumberOfItems')
                        ),
                        DropdownField::create(
                            $this->nestName('ReverseItems'),
                            $this->owner->fieldLabel('ReverseItems'),
                            $this->getToggleOptions()
                        )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder),
                        DropdownField::create(
                            $this->nestName('ImageItems'),
                            $this->owner->fieldLabel('ImageItems'),
                            $this->getToggleOptions()
                        )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder)
                    ]
                ),
                FieldSection::create(
                    'ListViewOptions',
                    $this->owner->fieldLabel('ListView'),
                    [
                        DropdownField::create(
                            'ListClass',
                            $this->owner->fieldLabel('ListClass'),
                            $this->owner->getListClassOptions()
                        )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder),
                        TextField::create(
                            $this->nestName('Title'),
                            $this->owner->fieldLabel('ListTitle')
                        ),
                        DropdownField::create(
                            $this->nestName('ShowImage'),
                            $this->owner->fieldLabel('ShowImage'),
                            $list->getShowOptions()
                        )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder),
                        DropdownField::create(
                            $this->nestName('ShowHeader'),
                            $this->owner->fieldLabel('ShowHeader'),
                            $list->getShowOptions()
                        )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder),
                        DropdownField::create(
                            $this->nestName('ShowDetails'),
                            $this->owner->fieldLabel('ShowDetails'),
                            $list->getShowOptions()
                        )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder),
                        DropdownField::create(
                            $this->nestName('ShowSummary'),
                            $this->owner->fieldLabel('ShowSummary'),
                            $list->getShowOptions()
                        )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder),
                        DropdownField::create(
                            $this->nestName('ShowContent'),
                            $this->owner->fieldLabel('ShowContent'),
                            $list->getShowOptions()
                        )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder),
                        DropdownField::create(
                            $this->nestName('ShowFooter'),
                            $this->owner->fieldLabel('ShowFooter'),
                            $list->getShowOptions()
                        )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder),
                        TextField::create(
                            $this->nestName('DateFormat'),
                            $this->owner->fieldLabel('DateFormat')
                        ),
                        TextField::create(
                            $this->nestName('ButtonLabel'),
                            $this->owner->fieldLabel('ButtonLabel')
                        ),
                        DropdownField::create(
                            $this->nestName('LinkTitles'),
                            $this->owner->fieldLabel('LinkTitles'),
                            $this->getToggleOptions()
                        )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder),
                        DropdownField::create(
                            $this->nestName('TitleHidden'),
                            $this->owner->fieldLabel('TitleHidden'),
                            $this->getToggleOptions()
                        )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder)
                    ]
                ),
                FieldSection::create(
                    'ListImageOptions',
                    $this->owner->fieldLabel('ListImages'),
                    [
                        DropdownField::create(
                            $this->nestName('LinkImages'),
                            $this->owner->fieldLabel('LinkImages'),
                            $this->getToggleOptions()
                        )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder),
                        DropdownField::create(
                            $this->nestName('ImageLinksTo'),
                            $this->owner->fieldLabel('ImageLinksTo'),
                            $list->getImageLinksToOptions()
                        )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder),
                        DropdownField::create(
                            $this->nestName('OverlayImages'),
                            $this->owner->fieldLabel('OverlayImages'),
                            $this->getToggleOptions()
                        )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder)
                    ]
                )
            ]
        );
        
        // Add List Option Fields:
        
        $fields->addFieldsToTab('Root.Options', $this->nest($list->getListOptionFields()));
        
        // Merge Pagination Fields:
        
        if ($list->canPaginate()) {
            
            $sourceOpts->merge([
                DropdownField::create(
                    $this->nestName('PaginateItems'),
                    $this->owner->fieldLabel('PaginateItems'),
                    $this->getToggleOptions()
                )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder),
                TextField::create(
                    $this->nestName('ItemsPerPage'),
                    $this->owner->fieldLabel('ItemsPerPage')
                )
            ]);
            
        }
        
        // Load List Config:
        
        $this->owner->loadListConfig($fields);
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
        $labels['DateFormat'] = _t(__CLASS__ . '.DATEFORMAT', 'Date format');
        $labels['LinkTitles'] = _t(__CLASS__ . '.LINKTITLES', 'Link titles');
        $labels['LinkImages'] = _t(__CLASS__ . '.LINKIMAGES', 'Link images');
        $labels['ButtonLabel'] = _t(__CLASS__ . '.BUTTONLABEL', 'Button label');
        $labels['HeadingLevel'] = _t(__CLASS__ . '.HEADINGLEVEL', 'Heading level');
        $labels['ItemsPerPage'] = _t(__CLASS__ . '.ITEMSPERPAGE', 'Items per page');
        $labels['PaginateItems'] = _t(__CLASS__ . '.PAGINATEITEMS', 'Paginate items');
        $labels['ImageLinksTo'] = _t(__CLASS__ . '.IMAGELINKSTO', 'Image links to');
        $labels['ImageResize'] = _t(__CLASS__ . '.IMAGEDIMENSIONS', 'Image dimensions');
        $labels['ImageResizeMethod'] = _t(__CLASS__ . '.IMAGERESIZEMETHOD', 'Image resize method');
        $labels['ImageAlignment'] = _t(__CLASS__ . '.IMAGEALIGNMENT', 'Image alignment');
        $labels['TextAlignment'] = _t(__CLASS__ . '.TEXTALIGNMENT', 'Text alignment');
        $labels['TitleHidden'] = _t(__CLASS__ . '.HIDELISTTITLE', 'Hide list title');
        $labels['OverlayImages'] = _t(__CLASS__ . '.OVERLAYIMAGES', 'Overlay images');
        $labels['OverlayIcon'] = _t(__CLASS__ . '.OVERLAYICON', 'Overlay icon');
        $labels['OverlayIconColor'] = _t(__CLASS__ . '.OVERLAYICONCOLOR', 'Overlay icon color');
        $labels['NumberOfItems'] = _t(__CLASS__ . '.NUMBEROFITEMS', 'Number of items');
        $labels['ReverseItems'] = _t(__CLASS__ . '.REVERSEITEMS', 'Reverse items');
        $labels['ImageItems'] = _t(__CLASS__ . '.IMAGEITEMS', 'Show only items with images');
        $labels['ListClass'] = _t(__CLASS__ . '.LISTCLASS', 'List class');
    }
    
    /**
     * Event method called before the extended object is written to the database.
     *
     * @return void
     */
    public function onBeforeWrite()
    {
        if ($this->owner->isInDB()) {
            
            if ($request = Controller::curr()->getRequest()) {
                
                if ($config = $request->postVar(self::FIELD_WRAPPER)) {
                    $this->owner->setListConfig($config);
                }
                
            }
            
        }
    }
    
    /**
     * Defines the value of the list config field.
     *
     * @param array $config
     *
     * @return $this
     */
    public function setListConfig($config)
    {
        return $this->owner->setField('ListConfig', Convert::array2json($config));
    }
    
    /**
     * Answers the value of the list config field.
     *
     * @return array|null
     */
    public function getListConfig()
    {
        return ($config = $this->owner->getField('ListConfig')) ? Convert::json2array($config) : null;
    }
    
    /**
     * Answers true if the extended object has list config defined.
     *
     * @return boolean
     */
    public function hasListConfig()
    {
        return is_array($this->owner->getListConfig());
    }
    
    /**
     * Answers an array of list configuration with values inherited from parent objects.
     *
     * @return array
     */
    public function getListConfigInherited()
    {
        // Obtain List Configuration:
        
        $config = $this->owner->hasListConfig() ? $this->owner->getListConfig() : [];
        
        // Obtain Inherited List Configuration:
        
        if ($this->owner->hasMethod('getParent')) {
            
            // Obtain Parent:
            
            $parent = $this->owner->getParent();
            
            // Iterate While Parent Valid:
            
            while ($parent) {
                
                // Detect Extension and Configuration:
                
                if ($parent->hasExtension(self::class) && $parent->hasListConfig()) {
                    
                    // Iterate Parent Configuration:
                    
                    foreach ($parent->getListConfig() as $name => $value) {
                        
                        if (!$this->isEmptyValue($value)) {
                            
                            if (isset($config[$name]) && $this->isEmptyValue($config[$name])) {
                                $config[$name] = $value;
                            }
                            
                        }
                        
                    }
                    
                }
                
                // Obtain Next Ancestor:
                
                $parent = $parent->hasMethod('getParent') ? $parent->getParent() : false;
                
            }
            
        }
        
        // Answer Config:
        
        return $config;
    }
    
    /**
     * Loads the list config into the given list of fields.
     *
     * @param FieldList $fields
     *
     * @return void
     */
    public function loadListConfig(FieldList $fields)
    {
        // Obtain List Configuration:
        
        $config = $this->owner->getListConfig();
        
        // Bail Early (if not array):
        
        if (!is_array($config)) {
            return;
        }
        
        // Iterate Configuration Values:
        
        foreach ($config as $name => $value) {
            
            if (is_array($value)) {
                
                // Handle Array Value:
                
                foreach ($value as $k => $v) {
                    
                    if ($field = $fields->dataFieldByName(self::FIELD_WRAPPER . "[$name][$k]")) {
                        $field->setValue($v);
                    }
                    
                }
                
            } else {
                
                // Handle Regular Value:
                
                if ($field = $fields->dataFieldByName(self::FIELD_WRAPPER . "[$name]")) {
                    $field->setValue($value);
                }
                
            }
            
        }
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
     * Answers the list component for the template.
     *
     * @return BaseListComponent
     */
    public function getListComponent()
    {
        // Create List Component:
        
        $list = Injector::inst()->create($this->owner->getListComponentClass());
        
        // Obtain List Configuration:
        
        $config = $this->owner->getListConfigInherited();
        
        // Configure List Component:
        
        if (is_array($config)) {
            
            foreach ($config as $name => $value) {
                
                if (!$this->isEmptyValue($value)) {
                    $list->setField($name, $value);
                }
                
            }
            
        }
        
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
        $classes = ClassTools::singleton()->getVisibleSubClasses(BaseListComponent::class);
        
        foreach ($classes as $key => $value) {
            $classes[$key] = Injector::inst()->get($key)->i18n_singular_name();
        }
        
        return $classes;
    }
    
    /**
     * Answers true if the given value is considered to be an 'empty' value.
     *
     * @param mixed $value
     *
     * @return boolean
     */
    protected function isEmptyValue($value)
    {
        if (is_array($value)) {
            
            foreach ($value as $item) {
                
                if ($this->isEmptyValue($item)) {
                    return true;
                }
                
            }
            
            return false;
            
        }
        
        return ($value === '');
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
