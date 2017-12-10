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

use SilverStripe\Assets\File;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\Tab;
use SilverStripe\ORM\ArrayLib;
use SilverStripe\ORM\ArrayList;
use SilverStripe\View\ArrayData;
use SilverWare\Extensions\Model\LinkToExtension;
use SilverWare\Extensions\Style\LinkColorStyle;
use SilverWare\FontIcons\Extensions\FontIconExtension;
use SilverWare\Forms\FieldSection;
use SilverWare\Forms\ViewportsField;
use Page;

/**
 * An extension of the component class for a link.
 *
 * @package SilverWare\Model
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class Link extends Component
{
    /**
     * Human-readable singular name.
     *
     * @var string
     * @config
     */
    private static $singular_name = 'Link';
    
    /**
     * Human-readable plural name.
     *
     * @var string
     * @config
     */
    private static $plural_name = 'Links';
    
    /**
     * Description of this object.
     *
     * @var string
     * @config
     */
    private static $description = 'A component which represents a link';
    
    /**
     * Icon file for this object.
     *
     * @var string
     * @config
     */
    private static $icon = 'silverware/silverware: admin/client/dist/images/icons/Link.png';
    
    /**
     * Defines the table name to use for this object.
     *
     * @var string
     * @config
     */
    private static $table_name = 'SilverWare_Link';
    
    /**
     * Defines an ancestor class to hide from the admin interface.
     *
     * @var string
     * @config
     */
    private static $hide_ancestor = Component::class;
    
    /**
     * Defines the allowed children for this object.
     *
     * @var array|string
     * @config
     */
    private static $allowed_children = 'none';
    
    /**
     * Maps field names to field types for this object.
     *
     * @var array
     * @config
     */
    private static $db = [
        'LinkImageWidth' => 'Viewports',
        'LinkImageHeight' => 'Viewports',
        'InlineVector' => 'Boolean'
    ];
    
    /**
     * Defines the has-one associations for this object.
     *
     * @var array
     * @config
     */
    private static $has_one = [
        'LinkImage' => File::class
    ];
    
    /**
     * Defines the default values for the fields of this object.
     *
     * @var array
     * @config
     */
    private static $defaults = [
        'InlineVector' => 0
    ];
    
    /**
     * Defines the ownership of associations for this object.
     *
     * @var array
     * @config
     */
    private static $owns = [
        'LinkImage'
    ];
    
    /**
     * Defines the extension classes to apply to this object.
     *
     * @var array
     * @config
     */
    private static $extensions = [
        FontIconExtension::class,
        LinkToExtension::class,
        LinkColorStyle::class
    ];
    
    /**
     * Defines the available icon sizes (in pixels).
     *
     * @var array
     * @config
     */
    private static $icon_sizes = [16, 24, 32, 48, 64, 96, 128];
    
    /**
     * Defines the default size of an icon (in pixels).
     *
     * @var integer
     * @config
     */
    private static $default_icon_size = 32;
    
    /**
     * Defines the asset folder for uploading images.
     *
     * @var string
     * @config
     */
    private static $asset_folder = 'Links';
    
    /**
     * Answers a list of field objects for the CMS interface.
     *
     * @return FieldList
     */
    public function getCMSFields()
    {
        // Obtain Field Objects (from parent):
        
        $fields = parent::getCMSFields();
        
        // Insert Image Tab:
        
        $fields->insertAfter(
            Tab::create(
                'Image',
                $this->fieldLabel('Image')
            ),
            'Main'
        );
        
        // Create Image Fields:
        
        $fields->addFieldsToTab(
            'Root.Image',
            [
                FieldSection::create(
                    'LinkImage',
                    $this->fieldLabel('LinkImage'),
                    [
                        $image = UploadField::create(
                            'LinkImage',
                            $this->fieldLabel('LinkImageFile')
                        ),
                        ViewportsField::create(
                            'LinkImageWidth',
                            $this->fieldLabel('LinkImageWidth')
                        )->setUseTextInput(true),
                        ViewportsField::create(
                            'LinkImageHeight',
                            $this->fieldLabel('LinkImageHeight')
                        )->setUseTextInput(true),
                        CheckboxField::create(
                            'InlineVector',
                            $this->fieldLabel('InlineVector')
                        )
                    ]
                )
            ]
        );
        
        // Define Image Field:
        
        $image->setAllowedExtensions(['gif', 'jpg', 'jpeg', 'png', 'svg']);
        $image->setFolderName($this->getAssetFolder());
        
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
        
        $labels['Image'] = _t(__CLASS__ . '.IMAGE', 'Image');
        $labels['InlineVector'] = _t(__CLASS__ . '.INLINEVECTORIMAGE', 'Inline vector image');
        $labels['LinkImageFile'] = _t(__CLASS__ . '.FILE', 'File');
        $labels['LinkImageWidth'] = _t(__CLASS__ . '.WIDTH', 'Width');
        $labels['LinkImageHeight'] = _t(__CLASS__ . '.HEIGHT', 'Height');
        
        // Define Relation Labels:
        
        if ($includerelations) {
            $labels['LinkImage'] = _t(__CLASS__ . '.has_one_LinkImage', 'Image');
        }
        
        // Answer Field Labels:
        
        return $labels;
    }
    
    /**
     * Answers the asset folder used by the receiver.
     *
     * @return string
     */
    public function getAssetFolder()
    {
        return $this->config()->asset_folder;
    }
    
    /**
     * Answers an array of HTML tag attributes for the object.
     *
     * @return array
     */
    public function getAttributes()
    {
        $attributes = array_merge(
            parent::getAttributes(),
            $this->getLinkAttributes()
        );
        
        return $attributes;
    }
    
    /**
     * Answers an array of class names for the HTML template.
     *
     * @return array
     */
    public function getClassNames()
    {
        $classes = array_merge(
            parent::getClassNames(),
            [
                $this->IconSizeClass,
                $this->CornerStyleClass
            ]
        );
        
        return $classes;
    }
    
    /**
     * Renders the font icon tag for the HTML template.
     *
     * @return string
     */
    public function getFontIconTag()
    {
        if ($this->hasFontIcon()) {
            return parent::getFontIconTag();
        }
        
        if (($parent = $this->getParent()) && $parent->FontIconTag) {
            return $parent->FontIconTag;
        }
    }
    
    /**
     * Answers the icon size class for the receiver.
     *
     * @return string
     */
    public function getIconSizeClass()
    {
        $size = $this->config()->default_icon_size;
        
        if (($parent = $this->getParent()) && $parent->IconSize) {
            $size = $parent->IconSize;
        }
        
        return sprintf('size-%d', $size);
    }
    
    /**
     * Answers the corner style class for the receiver.
     *
     * @return string
     */
    public function getCornerStyleClass()
    {
        if (($parent = $this->getParent()) && $parent->CornerStyleClass) {
            return $parent->CornerStyleClass;
        }
    }
    
    /**
     * Answers an array of link image class names for the receiver.
     *
     * @return string
     */
    public function getLinkImageClassNames()
    {
        $classes = $this->styles('link.image', 'image.fluid');
        
        $this->extend('updateLinkImageClassNames', $classes);
        
        return $classes;
    }
    
    /**
     * Answers true if a link images exists.
     *
     * @return boolean
     */
    public function hasLinkImage()
    {
        return $this->LinkImage()->exists();
    }
    
    /**
     * Answers true if the link image is an inline vector.
     *
     * @return boolean
     */
    public function hasInlineVector()
    {
        return ($this->hasLinkImage() && $this->LinkImage()->getExtension() == 'svg' && $this->InlineVector);
    }
    
    /**
     * Answers a list of link image dimensions for the custom CSS template.
     *
     * @return ArrayList
     */
    public function getLinkImageDimensions()
    {
        // Initialise:
        
        $data = [];
        
        // Obtain Dimensions:
        
        $widths  = $this->dbObject('LinkImageWidth');
        $heights = $this->dbObject('LinkImageHeight');
        
        // Iterate Width Viewports:
        
        foreach ($widths->getViewports() as $viewport) {
            
            if ($value = $widths->getField($viewport)) {
                $data[$viewport]['Width'] = $value;
                $data[$viewport]['Breakpoint'] = $widths->getBreakpoint($viewport);
            }
            
        }
        
        // Iterate Height Viewports:
        
        foreach ($heights->getViewports() as $viewport) {
            
            if ($value = $heights->getField($viewport)) {
                $data[$viewport]['Height'] = $value;
                $data[$viewport]['Breakpoint'] = $heights->getBreakpoint($viewport);
            }
            
        }
        
        // Create Items List:
        
        $items = ArrayList::create();
        
        // Create Data Items:
        
        foreach ($data as $item) {
            $items->push(ArrayData::create($item));
        }
        
        // Answer Items List:
        
        return $items;
    }
    
    /**
     * Defines the receiver from the given page.
     *
     * @param Page $page
     * @param string $nameField
     *
     * @return $this
     */
    public function fromPage(Page $page, $nameField = 'MenuTitle')
    {
        $this->Title = $page->{$nameField};
        $this->LinkTo = LinkToExtension::MODE_PAGE;
        $this->LinkPageID = $page->ID;
        
        return $this;
    }
    
    /**
     * Answers the CSS prefix used for the custom CSS template.
     *
     * @return string
     */
    public function getCustomCSSPrefix()
    {
        if ($parent = $this->getParent()) {
            return sprintf('%s %s', $parent->CSSID, $this->CSSID);
        }
        
        return $this->CSSID;
    }
    
    /**
     * Answers an array of options for an icon size field.
     *
     * @return array
     */
    public function getIconSizeOptions()
    {
        return ArrayLib::valuekey($this->config()->icon_sizes);
    }
}
