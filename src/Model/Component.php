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

use SilverStripe\CMS\Controllers\ModelAsController;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Core\ClassInfo;
use SilverStripe\Core\Convert;
use SilverStripe\Core\Flushable;
use SilverStripe\ORM\ArrayList;
use SilverStripe\Security\Permission;
use SilverStripe\Security\PermissionProvider;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\View\SSViewer;
use SilverWare\Extensions\RenderableExtension;
use SilverWare\Tools\ClassTools;
use SilverWare\Tools\ViewTools;
use SilverWare\View\GridAware;
use SilverWare\View\Renderable;
use SilverWare\View\ViewClasses;
use Page;

/**
 * An extension of the site tree class for a SilverWare component.
 *
 * @package SilverWare\Model
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class Component extends SiteTree implements Flushable, PermissionProvider
{
    use GridAware;
    use Renderable;
    use ViewClasses;
    
    /**
     * Human-readable singular name.
     *
     * @var string
     * @config
     */
    private static $singular_name = 'Component';
    
    /**
     * Human-readable plural name.
     *
     * @var string
     * @config
     */
    private static $plural_name = 'Components';
    
    /**
     * Description of this object.
     *
     * @var string
     * @config
     */
    private static $description = 'A component within a SilverWare template or layout';
    
    /**
     * Icon file for this object.
     *
     * @var string
     * @config
     */
    private static $icon = 'silverware/silverware: admin/client/dist/images/icons/Component.png';
    
    /**
     * Determines whether this object can exist at the root level.
     *
     * @var boolean
     * @config
     */
    private static $can_be_root = false;
    
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
     * Defines the extension classes to apply to this object.
     *
     * @var array
     * @config
     */
    private static $extensions = [
        RenderableExtension::class
    ];
    
    /**
     * Tag name to use when rendering this object.
     *
     * @var string
     * @config
     */
    private static $tag = 'div';
    
    /**
     * Defines the default classes to use when rendering this object.
     *
     * @var array
     * @config
     */
    private static $default_classes = [];
    
    /**
     * Local cache of all immediate children.
     *
     * @var DataList
     */
    protected $cacheAllChildren;
    
    /**
     * Local cache of all components (children and descendants).
     *
     * @var ArrayList
     */
    protected $cacheAllComponents;
    
    /**
     * Local cache of enabled immediate children.
     *
     * @var DataList
     */
    protected $cacheEnabledChildren;
    
    /**
     * Local cache of enabled components (children and descendants).
     *
     * @var ArrayList
     */
    protected $cacheEnabledComponents;
    
    /**
     * Holds the parent instance of the receiver.
     *
     * @var SiteTree
     */
    protected $parentInstance;
    
    /**
     * Clears the component render cache upon flush.
     *
     * @return void
     */
    public static function flush()
    {
        self::flushRenderCache();
    }
    
    /**
     * Constructs the object upon instantiation.
     *
     * @param array|null $record
     * @param boolean $isSingleton
     * @param DataModel $model
     * @param array $queryParams
     */
    public function __construct($record = null, $isSingleton = false, $model = null, $queryParams = [])
    {
        // Construct Parent:
        
        parent::__construct($record, $isSingleton, $model, $queryParams);
        
        // Construct Object:
        
        if ($defaultClasses = $this->config()->get('default_classes')) {
            $this->extraClasses = $defaultClasses;
        };
        
        // Initialise:
        
        $this->doInit();
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
        
        // Add Class Ancestry:
        
        foreach ($this->getAncestorClassNames(false) as $class) {
            $fields->fieldByName('Root')->addExtraClass($class);
        }
        
        // Remove Field Objects:
        
        $fields->removeFieldsFromTab('Root.Main', ['Content', 'Metadata']);
        
        // Modify Field Objects:
        
        $fields->fieldByName('Root.Main')->setTitle(_t(__CLASS__ . '.TABMAIN', 'Main'));
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
        
        $labels['Title'] = _t(__CLASS__ . '.TITLE', 'Title');
        
        // Answer Field Labels:
        
        return $labels;
    }
    
    /**
     * Defines the parent instance of the receiver.
     *
     * @param SiteTree $parent
     *
     * @return $this
     */
    public function setParentInstance(SiteTree $parent)
    {
        $this->parentInstance = $parent;
        
        $this->setStyleIDFrom($parent);
        
        return $this;
    }
    
    /**
     * Answers the parent of the receiver.
     *
     * @return SiteTree
     */
    public function getParent()
    {
        if ($this->parentInstance) {
            return $this->parentInstance;
        }
        
        return parent::getParent();
    }
    
    /**
     * Overrides the method inherited from DataObject in order to answer parent instances (if applicable).
     *
     * @param string $componentName
     *
     * @return DataObject
     */
    public function getComponent($componentName)
    {
        return ($componentName == 'Parent') ? $this->getParent() : parent::getComponent($componentName);
    }
    
    /**
     * Answers a string of CSS classes to apply to the receiver in the CMS tree.
     *
     * @return string
     */
    public function CMSTreeClasses()
    {
        $classes = parent::CMSTreeClasses();
        
        $classes = ClassTools::singleton()->getStyleClasses($classes, static::class);
        
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
        // Obtain Parent Context:
        
        if (isset($context['Parent'])) {
            
            // Obtain Parent Class:
            
            $class = get_class($context['Parent']);
            
            // Obtain Allowed Parents:
            
            if ($parents = $this->config()->allowed_parents) {
                
                // Initialise Allowed Classes Array:
                
                $classes = [];
                
                // Obtain Subclasses for Parent Class:
                
                foreach ($parents as $parent) {
                    $classes = array_merge($classes, array_values(ClassInfo::subclassesFor($parent)));
                }
                
                // Disallow Parent (if not found):
                
                if (!in_array($class, $classes)) {
                    return false;
                }
                
            } else {
                
                // Disallow Page as Parent (except if explicitly allowed):
                
                if ($context['Parent'] instanceof Page) {
                    
                    $allowed_children = $context['Parent']->allowedChildren();
                    
                    if (in_array(SiteTree::class, $allowed_children) || !in_array(static::class, $allowed_children)) {
                        return false;
                    }
                    
                }
                
            }
            
        }
        
        // Check Permissions for Member:
        
        return Permission::checkMember($member, ['ADMIN', 'SILVERWARE_COMPONENT_CREATE']);
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
        return Permission::checkMember($member, ['ADMIN', 'SILVERWARE_COMPONENT_DELETE']);
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
        return Permission::checkMember($member, ['ADMIN', 'SILVERWARE_COMPONENT_EDIT']);
    }
    
    /**
     * Provides the permissions for the security interface.
     *
     * @return array
     */
    public function providePermissions()
    {
        $category = _t(__CLASS__ . '.PERMISSION_CATEGORY', 'SilverWare components');
        
        return [
            'SILVERWARE_COMPONENT_CREATE' => [
                'category' => $category,
                'name' => _t(__CLASS__ . '.PERMISSION_CREATE_NAME', 'Create components'),
                'help' => _t(__CLASS__ . '.PERMISSION_CREATE_HELP', 'Ability to create SilverWare components.'),
                'sort' => 100
            ],
            'SILVERWARE_COMPONENT_EDIT' => [
                'category' => $category,
                'name' => _t(__CLASS__ . '.PERMISSION_EDIT_NAME', 'Edit components'),
                'help' => _t(__CLASS__ . '.PERMISSION_EDIT_HELP', 'Ability to edit SilverWare components.'),
                'sort' => 200
            ],
            'SILVERWARE_COMPONENT_DELETE' => [
                'category' => $category,
                'name' => _t(__CLASS__ . '.PERMISSION_DELETE_NAME', 'Delete components'),
                'help' => _t(__CLASS__ . '.PERMISSION_DELETE_HELP', 'Ability to delete SilverWare components.'),
                'sort' => 300
            ]
        ];
    }
    
    /**
     * Initialises the component (with extension hooks).
     *
     * @return void
     */
    public function doInit()
    {
        // Trigger Before Init Hook:
        
        $this->extend('onBeforeInit');
        
        // Perform Initialisation:
        
        $this->init();
        
        // Trigger After Init Hook:
        
        $this->extend('onAfterInit');
    }
    
    /**
     * Answers the type of component as a string.
     *
     * @return string
     */
    public function getComponentType()
    {
        return $this->i18n_singular_name();
    }
    
    /**
     * Answers the default style ID for the HTML template.
     *
     * @return string
     */
    public function getDefaultStyleID()
    {
        $ids = [$this->getTitleID()];
        
        $parent = $this->getParent();
        
        while ($parent instanceof Component) {
            $ids[]  = $parent->getTitleID();
            $parent = $parent->getParent();
        }
        
        return implode('_', array_reverse($ids));
    }
    
    /**
     * Converts the title of the receiver into a string suitable for an HTML ID.
     *
     * @return string
     */
    public function getTitleID()
    {
        return str_replace(' ', '', ucwords(preg_replace('/\s+/', ' ', $this->Title)));
    }
    
    /**
     * Answers a list of all children within the receiver.
     *
     * @return DataList
     */
    public function getAllChildren()
    {
        // Answer Cached Children (if available):
        
        if ($this->cacheAllChildren) {
            return $this->cacheAllChildren;
        }
        
        // Obtain Child Objects:
        
        $this->cacheAllChildren = $this->stageChildren(true);
        
        // Answer Child Objects:
        
        return $this->cacheAllChildren;
    }
    
    /**
     * Answers a list of all children within the receiver of the given class.
     *
     * @param string $class
     *
     * @return DataList
     */
    public function getAllChildrenByClass($class)
    {
        return $this->getAllChildren()->filter('ClassName', ClassInfo::subclassesFor($class));
    }
    
    /**
     * Answers a list of all components within the receiver.
     *
     * @return ArrayList
     */
    public function getAllComponents()
    {
        // Answer Cached Components (if available):
        
        if ($this->cacheAllComponents) {
            return $this->cacheAllComponents;
        }
        
        // Obtain Component Objects:
        
        $components = ArrayList::create();
        
        foreach ($this->getAllChildren() as $child) {
            $components->push($child);
            $components->merge($child->getAllComponents());
        }
        
        $this->cacheAllComponents = $components;
        
        // Answer Component Objects:
        
        return $this->cacheAllComponents;
    }
    
    /**
     * Answers a list of the enabled children within the receiver.
     *
     * @return DataList
     */
    public function getEnabledChildren()
    {
        // Answer Cached Children (if available):
        
        if ($this->cacheEnabledChildren) {
            return $this->cacheEnabledChildren;
        }
        
        // Obtain Child Objects:
        
        $this->cacheEnabledChildren = $this->getAllChildren()->filterByCallback(function ($child) {
            return ($child instanceof Component) ? $child->isEnabled() : false;
        });
        
        // Answer Child Objects:
        
        return $this->cacheEnabledChildren;
    }
    
    /**
     * Answers a list of all the enabled components within the receiver.
     *
     * @return ArrayList
     */
    public function getEnabledComponents()
    {
        // Answer Cached Components (if available):
        
        if ($this->cacheEnabledComponents) {
            return $this->cacheEnabledComponents;
        }
        
        // Obtain Component Objects:
        
        $components = ArrayList::create();
        
        foreach ($this->getEnabledChildren() as $child) {
            $components->push($child);
            $components->merge($child->getEnabledComponents());
        }
        
        $this->cacheEnabledComponents = $components;
        
        // Answer Component Objects:
        
        return $this->cacheEnabledComponents;
    }
    
    /**
     * Answers true if the receiver has a child of the given class.
     *
     * @param string $class Class name of child.
     *
     * @return boolean
     */
    public function hasChild($class)
    {
        return $this->getAllChildren()->filter('ClassName', $class)->exists();
    }
    
    /**
     * Answers true if the receiver has an enabled child of the given class.
     *
     * @param string $class Class name of child.
     *
     * @return boolean
     */
    public function hasEnabledChild($class)
    {
        return $this->getEnabledChildren()->filter('ClassName', $class)->exists();
    }
    
    /**
     * Answers the first child found within the receiver of the given class.
     *
     * @param string $class Class name of child.
     *
     * @return SiteTree
     */
    public function getChild($class)
    {
        return $this->getAllChildren()->find('ClassName', $class);
    }
    
    /**
     * Answers true if the receiver has a descendant of the given class.
     *
     * @param string $class Class name of descendant.
     *
     * @return boolean
     */
    public function hasDescendant($class)
    {
        // Iterate All Descendants:
        
        foreach ($this->getAllComponents() as $node) {
            
            // Answer True (if found):
            
            if ($node instanceof $class) {
                return true;
            }
            
        }
        
        // Answer False (if not found):
        
        return false;
    }
    
    /**
     * Answers true if the receiver has an enabled descendant of the given class.
     *
     * @param string $class Class name of descendant.
     *
     * @return boolean
     */
    public function hasEnabledDescendant($class)
    {
        // Iterate Enabled Descendants:
        
        foreach ($this->getEnabledComponents() as $node) {
            
            // Answer True (if found):
            
            if ($node instanceof $class) {
                return true;
            }
            
        }
        
        // Answer False (if not found):
        
        return false;
    }
    
    /**
     * Answers an array containing the class names of the ancestral components of the object.
     *
     * @return array
     */
    public function getComponentAncestry()
    {
        return ClassTools::singleton()->getObjectAncestry($this, self::class);
    }
    
    /**
     * Answers the current site configuration object.
     *
     * @return SiteConfig
     */
    public function getSiteConfig()
    {
        return SiteConfig::current_site_config();
    }
    
    /**
     * Answers the controller for the receiver.
     *
     * @return Controller
     */
    public function getController()
    {
        return ModelAsController::controller_for($this);
    }
    
    /**
     * Answers the current page.
     *
     * @param string $class Optional class name restriction.
     *
     * @return SiteTree
     */
    public function getCurrentPage($class = null)
    {
        $page = Director::get_current_page();
        
        if (!$class || ($class && $page instanceof $class)) {
            return $page;
        }
    }
    
    /**
     * Answers the class name of the current page.
     *
     * @return string
     */
    public function getCurrentPageClass()
    {
        if ($page = $this->getCurrentPage()) {
            return get_class($page);
        }
    }
    
    /**
     * Answers the class name of the current page as a value suitable for an HTML ID.
     *
     * @return string
     */
    public function getCurrentPageClassID()
    {
        return Convert::raw2htmlid($this->getCurrentPageClass());
    }
    
    /**
     * Answers a string containing the current page ancestry for the HTML template.
     *
     * @return string
     */
    public function getCurrentPageAncestry()
    {
        $ancestry = ViewTools::singleton()->convertClass(
            ClassTools::singleton()->getObjectAncestry($this->getCurrentPageClass(), Page::class, true)
        );
        
        return ViewTools::singleton()->array2att($ancestry);
    }
    
    /**
     * Answers the current controller.
     *
     * @param string $class Optional class name restriction.
     *
     * @return Controller
     */
    public function getCurrentController($class = null)
    {
        $controller = Controller::curr();
        
        if (!$class || ($class && $controller instanceof $class)) {
            return $controller;
        }
    }
    
    /**
     * Answers the HTML tag for the receiver.
     *
     * @return string
     */
    public function getTag()
    {
        return $this->config()->tag;
    }
    
    /**
     * Answers the opening tag for the receiver.
     *
     * @return string
     */
    public function getOpeningTag()
    {
        return sprintf("<%s %s>\n", $this->getTag(), $this->getAttributesHTML());
    }
    
    /**
     * Answers the closing tag for the receiver.
     *
     * @return string
     */
    public function getClosingTag()
    {
        return sprintf("</%s>\n", $this->getTag());
    }
    
    /**
     * Answers the given string of content wrapped in the opening and closing tags of the receiver.
     *
     * @param string $content
     *
     * @return string
     */
    public function renderTag($content = null)
    {
        return $this->getOpeningTag() . $content . $this->getClosingTag();
    }
    
    /**
     * Answers the template used to render the receiver.
     *
     * @return string|array|SSViewer
     */
    public function getTemplate()
    {
        foreach ($this->getComponentAncestry() as $template) {
            
            if (SSViewer::hasTemplate($template)) {
                return $template;
            }
            
        }
    }
    
    /**
     * Renders the component for the HTML template.
     *
     * @param string $layout Page layout passed from template.
     * @param string $title Page title passed from template.
     *
     * @return DBHTMLText|string
     */
    public function renderSelf($layout = null, $title = null)
    {
        return $this->getController()->customise([
            'Self' => $this,
            'Title' => $title,
            'Layout' => $layout
        ])->renderWith($this->getTemplate());
    }
    
    /**
     * Renders the enabled children within the component for the HTML template.
     *
     * @param string $layout Page layout passed from template.
     * @param string $title Page title passed from template.
     *
     * @return DBHTMLText|string
     */
    public function renderChildren($layout = null, $title = null)
    {
        $html = '';
        
        foreach ($this->getEnabledChildren() as $child) {
            $html .= $child->render($layout, $title);
        }
        
        return $html;
    }
    
    /**
     * Renders the component for preview within the CMS.
     *
     * @return DBHTMLText|string
     */
    public function renderPreview()
    {
        return $this->renderSelf();
    }
    
    /**
     * Initialises the component.
     *
     * @return void
     */
    protected function init()
    {
    }
}
