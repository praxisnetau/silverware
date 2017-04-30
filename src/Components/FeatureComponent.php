<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Components
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\Components;

use SilverStripe\Assets\Image;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\CompositeField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\TreeDropdownField;
use SilverWare\Extensions\Model\ImageResizeExtension;
use SilverWare\Extensions\Style\AlignmentStyle;
use Page;

/**
 * An extension of the base component class for a feature component.
 *
 * @package SilverWare\Components
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class FeatureComponent extends BaseComponent
{
    /**
     * Human-readable singular name.
     *
     * @var string
     * @config
     */
    private static $singular_name = 'Feature Component';
    
    /**
     * Human-readable plural name.
     *
     * @var string
     * @config
     */
    private static $plural_name = 'Feature Components';
    
    /**
     * Description of this object.
     *
     * @var string
     * @config
     */
    private static $description = 'A component used to feature a particular page';
    
    /**
     * Icon file for this object.
     *
     * @var string
     * @config
     */
    private static $icon = 'silverware/admin/client/dist/images/icons/FeatureComponent.png';
    
    /**
     * Maps field names to field types for this object.
     *
     * @var array
     * @config
     */
    private static $db = [
        'Summary' => 'HTMLText',
        'HeadingLevel' => 'Varchar(2)',
        'ButtonLabel' => 'Varchar(128)',
        'LinkHeading' => 'Boolean',
        'ShowImage' => 'Boolean'
    ];
    
    /**
     * Defines the has-one associations for this object.
     *
     * @var array
     * @config
     */
    private static $has_one = [
        'Image' => Image::class,
        'FeaturedPage' => Page::class
    ];
    
    /**
     * Defines the ownership of associations for this object.
     *
     * @var array
     * @config
     */
    private static $owns = [
        'Image'
    ];
    
    /**
     * Defines the default values for the fields of this object.
     *
     * @var array
     * @config
     */
    private static $defaults = [
        'LinkHeading' => 1,
        'ShowImage' => 1
    ];
    
    /**
     * Maps field and method names to the class names of casting objects.
     *
     * @var array
     * @config
     */
    private static $casting = [
        'SummaryText' => 'HTMLFragment'
    ];
    
    /**
     * Defines an ancestor class to hide from the admin interface.
     *
     * @var string
     * @config
     */
    private static $hide_ancestor = BaseComponent::class;
    
    /**
     * Defines the allowed children for this object.
     *
     * @var array|string
     * @config
     */
    private static $allowed_children = 'none';
    
    /**
     * Defines the extension classes to apply to this object.
     *
     * @var array
     * @config
     */
    private static $extensions = [
        AlignmentStyle::class,
        ImageResizeExtension::class
    ];
    
    /**
     * Defines the asset folder for uploading images.
     *
     * @var string
     * @config
     */
    private static $asset_folder = 'Features';
    
    /**
     * Defines the default heading level to use.
     *
     * @var array
     * @config
     */
    private static $heading_level_default = 'h4';
    
    /**
     * Answers a list of field objects for the CMS interface.
     *
     * @return FieldList
     */
    public function getCMSFields()
    {
        // Obtain Field Objects (from parent):
        
        $fields = parent::getCMSFields();
        
        // Create Main Fields:
        
        $fields->addFieldsToTab(
            'Root.Main',
            [
                TreeDropdownField::create(
                    'FeaturedPageID',
                    $this->fieldLabel('FeaturedPageID'),
                    Page::class
                ),
                UploadField::create(
                    'Image',
                    $this->fieldLabel('Image')
                )->setAllowedFileCategories('image')->setFolderName($this->getAssetFolder()),
                HTMLEditorField::create(
                    'Summary',
                    $this->fieldLabel('Summary')
                )
            ]
        );
        
        // Define Placeholder:
        
        $placeholder = _t(__CLASS__ . '.DROPDOWNDEFAULT', '(default)');
        
        // Create Style Fields:
        
        $fields->addFieldToTab(
            'Root.Style',
            CompositeField::create([
                DropdownField::create(
                    'HeadingLevel',
                    $this->fieldLabel('HeadingLevel'),
                    $this->getTitleLevelOptions()
                )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder),
            ])->setName('FeatureComponentStyle')->setTitle($this->i18n_singular_name())
        );
        
        // Create Options Fields:
        
        $fields->addFieldToTab(
            'Root.Options',
            CompositeField::create([
                TextField::create(
                    'ButtonLabel',
                    $this->fieldLabel('ButtonLabel')
                ),
                CheckboxField::create(
                    'LinkHeading',
                    $this->fieldLabel('LinkHeading')
                ),
                CheckboxField::create(
                    'ShowImage',
                    $this->fieldLabel('ShowImage')
                )
            ])->setName('FeatureComponentOptions')->setTitle($this->i18n_singular_name())
        );
        
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
        
        $labels['ImageID'] = _t(__CLASS__ . '.IMAGE', 'Image');
        $labels['Summary'] = _t(__CLASS__ . '.SUMMARY', 'Summary');
        $labels['ShowImage'] = _t(__CLASS__ . '.SHOWIMAGE', 'Show image');
        $labels['LinkHeading'] = _t(__CLASS__ . '.LINKHEADING', 'Link heading');
        $labels['ButtonLabel'] = _t(__CLASS__ . '.BUTTONLABEL', 'Button label');
        $labels['FeaturedPageID'] = _t(__CLASS__ . '.FEATUREDPAGE', 'Featured page');
        
        // Define Relation Labels:
        
        if ($includerelations) {
            $labels['Image'] = _t(__CLASS__ . '.has_one_Image', 'Image');
            $labels['EntityPage'] = _t(__CLASS__ . '.has_one_FeaturedPage', 'Featured Page');
        }
        
        // Answer Field Labels:
        
        return $labels;
    }
    
    /**
     * Populates the default values for the fields of the receiver.
     *
     * @return void
     */
    public function populateDefaults()
    {
        // Populate Defaults (from parent):
        
        parent::populateDefaults();
        
        // Populate Defaults:
        
        $this->ButtonLabel = _t(__CLASS__ . '.DEFAULTBUTTONLABEL', 'Read More');
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
     * Answers an array of wrapper class names for the HTML template.
     *
     * @return array
     */
    public function getWrapperClassNames()
    {
        $classes = ['feature'];
        
        $classes[] = $this->style('feature');
        
        $this->extend('updateWrapperClassNames', $classes);
        
        return $classes;
    }
    
    /**
     * Answers an array of image class names for the HTML template.
     *
     * @return array
     */
    public function getImageClassNames()
    {
        $classes = $this->styles('image.fluid', 'feature.image-top');
        
        $this->extend('updateImageClassNames', $classes);
        
        return $classes;
    }
    
    /**
     * Answers an array of block class names for the HTML template.
     *
     * @return array
     */
    public function getBlockClassNames()
    {
        $classes = ['block'];
        
        $classes[] = $this->style('feature.block');
        
        $this->extend('updateBlockClassNames', $classes);
        
        return $classes;
    }
    
    /**
     * Answers an array of header class names for the HTML template.
     *
     * @return array
     */
    public function getHeadingClassNames()
    {
        $classes = $this->styles('feature.heading');
        
        $this->extend('updateHeadingClassNames', $classes);
        
        return $classes;
    }
    
    /**
     * Answers the heading tag for the receiver.
     *
     * @return string
     */
    public function getHeadingTag()
    {
        if ($tag = $this->getField('HeadingLevel')) {
            return $tag;
        }
        
        return $this->config()->heading_level_default;
    }
    
    /**
     * Answers true if the receiver has an image.
     *
     * @return boolean
     */
    public function hasImage()
    {
        return ($this->Image()->exists() || $this->FeaturedPage()->hasMetaImage());
    }
    
    /**
     * Answers true if the image is to be shown in the template.
     *
     * @return boolean
     */
    public function getImageShown()
    {
        return ($this->hasImage() && $this->ShowImage);
    }
    
    /**
     * Answers true if the header is to be shown in the template.
     *
     * @return boolean
     */
    public function getHeaderShown()
    {
        return true;
    }
    
    /**
     * Answers true if the summary is to be shown in the template.
     *
     * @return boolean
     */
    public function getSummaryShown()
    {
        return true;
    }
    
    /**
     * Answers true if the footer is to be shown in the template.
     *
     * @return boolean
     */
    public function getFooterShown()
    {
        return true;
    }
    
    /**
     * Answers the summary text either from the receiver or the featured page.
     *
     * @return DBHTMLText|string
     */
    public function getSummaryText()
    {
        return ($this->Summary) ? $this->Summary : $this->FeaturedPage()->getMetaSummary();
    }
    
    /**
     * Answers a resized image using the defined dimensions and resize method.
     *
     * @return Image
     */
    public function getImageResized()
    {
        if ($this->Image()->exists()) {
            $image = $this->Image();
        } elseif ($this->FeaturedPage()->hasMetaImage()) {
            $image = $this->FeaturedPage()->getMetaImage();
        }
        
        return $this->performImageResize($image);
    }
    
    /**
     * Answers true if the object is disabled within the template.
     *
     * @return boolean
     */
    public function isDisabled()
    {
        if (!$this->FeaturedPageID) {
            return true;
        }
        
        return parent::isDisabled();
    }
}
