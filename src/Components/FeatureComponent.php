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
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Forms\TextField;
use SilverWare\Extensions\Model\ImageResizeExtension;
use SilverWare\Extensions\Model\LinkToExtension;
use SilverWare\Extensions\Style\AlignmentStyle;
use SilverWare\Extensions\Style\CornerStyle;
use SilverWare\FontIcons\Extensions\FontIconExtension;
use SilverWare\Forms\FieldSection;
use SilverWare\Forms\PageDropdownField;
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
    private static $icon = 'silverware/silverware: admin/client/dist/images/icons/FeatureComponent.png';
    
    /**
     * Maps field names to field types for this object.
     *
     * @var array
     * @config
     */
    private static $db = [
        'Summary' => 'HTMLText',
        'Heading' => 'Varchar(255)',
        'SubHeading' => 'Varchar(255)',
        'HeadingLevel' => 'Varchar(2)',
        'SubHeadingLevel' => 'Varchar(2)',
        'ButtonLabel' => 'Varchar(128)',
        'LinkHeading' => 'Boolean',
        'LinkFeature' => 'Boolean',
        'ShowIcon' => 'Boolean',
        'ShowImage' => 'Boolean',
        'ShowHeader' => 'Boolean',
        'ShowSummary' => 'Boolean',
        'ShowFooter' => 'Boolean'
    ];
    
    /**
     * Defines the has-one associations for this object.
     *
     * @var array
     * @config
     */
    private static $has_one = [
        'Image' => Image::class
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
        'LinkFeature' => 0,
        'LinkHeading' => 1,
        'ShowIcon' => 1,
        'ShowImage' => 1,
        'ShowHeader' => 1,
        'ShowSummary' => 1,
        'ShowFooter' => 1
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
        CornerStyle::class,
        AlignmentStyle::class,
        LinkToExtension::class,
        FontIconExtension::class,
        ImageResizeExtension::class
    ];
    
    /**
     * Defines the style extension classes to apply to this object.
     *
     * @var array
     * @config
     */
    private static $apply_styles = [
        AlignmentStyle::class
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
     * Defines the default sub-heading level to use.
     *
     * @var array
     * @config
     */
    private static $sub_heading_level_default = 'h5';
    
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
                FieldSection::create(
                    'HeadingSection',
                    $this->fieldLabel('HeadingSection'),
                    [
                        TextField::create(
                            'Heading',
                            $this->fieldLabel('Heading')
                        ),
                        TextField::create(
                            'SubHeading',
                            $this->fieldLabel('SubHeading')
                        )
                    ]
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
            FieldSection::create(
                'FeatureStyle',
                $this->fieldLabel('FeatureStyle'),
                [
                    DropdownField::create(
                        'HeadingLevel',
                        $this->fieldLabel('HeadingLevel'),
                        $this->getTitleLevelOptions()
                    )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder),
                ]
            )
        );
        
        // Create Options Fields:
        
        $fields->addFieldToTab(
            'Root.Options',
            FieldSection::create(
                'FeatureOptions',
                $this->fieldLabel('FeatureOptions'),
                [
                    CheckboxField::create(
                        'ShowIcon',
                        $this->fieldLabel('ShowIcon')
                    ),
                    CheckboxField::create(
                        'ShowImage',
                        $this->fieldLabel('ShowImage')
                    ),
                    CheckboxField::create(
                        'ShowHeader',
                        $this->fieldLabel('ShowHeader')
                    ),
                    CheckboxField::create(
                        'ShowSummary',
                        $this->fieldLabel('ShowSummary')
                    ),
                    CheckboxField::create(
                        'ShowFooter',
                        $this->fieldLabel('ShowFooter')
                    ),
                    CheckboxField::create(
                        'LinkHeading',
                        $this->fieldLabel('LinkHeading')
                    ),
                    CheckboxField::create(
                        'LinkFeature',
                        $this->fieldLabel('LinkFeature')
                    ),
                    TextField::create(
                        'ButtonLabel',
                        $this->fieldLabel('ButtonLabel')
                    )
                ]
            )
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
        $labels['ShowIcon'] = _t(__CLASS__ . '.SHOWICON', 'Show icon');
        $labels['ShowImage'] = _t(__CLASS__ . '.SHOWIMAGE', 'Show image');
        $labels['ShowHeader'] = _t(__CLASS__ . '.SHOWHEADER', 'Show header');
        $labels['ShowSummary'] = _t(__CLASS__ . '.SHOWSUMMARY', 'Show summary');
        $labels['ShowFooter'] = _t(__CLASS__ . '.SHOWFOOTER', 'Show footer');
        $labels['LinkHeading'] = _t(__CLASS__ . '.LINKHEADING', 'Link heading');
        $labels['LinkFeature'] = _t(__CLASS__ . '.LINKFEATURE', 'Link feature');
        $labels['ButtonLabel'] = _t(__CLASS__ . '.BUTTONLABEL', 'Button label');
        $labels['Heading'] = _t(__CLASS__ . '.HEADING', 'Heading');
        $labels['SubHeading'] = _t(__CLASS__ . '.SUBHEADING', 'Sub-heading');
        $labels['HeadingLevel'] = _t(__CLASS__ . '.HEADINGLEVEL', 'Heading level');
        $labels['SubHeadingLevel'] = _t(__CLASS__ . '.SUBHEADINGLEVEL', 'Sub-heading level');
        $labels['HeadingSection'] = _t(__CLASS__ . '.HEADINGS', 'Headings');
        $labels['FeatureStyle'] = $labels['FeatureOptions'] = _t(__CLASS__ . '.FEATURE', 'Feature');
        
        // Define Relation Labels:
        
        if ($includerelations) {
            $labels['Image'] = _t(__CLASS__ . '.has_one_Image', 'Image');
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
        
        $this->ButtonLabel = _t(__CLASS__ . '.DEFAULTBUTTONLABEL', 'More');
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
        
        if ($this->CornerStyleClass) {
            $classes[] = $this->CornerStyleClass;
        }
        
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
     * Answers an array of body class names for the HTML template.
     *
     * @return array
     */
    public function getBodyClassNames()
    {
        $classes = ['body'];
        
        $classes[] = $this->style('feature.body');
        
        $this->extend('updateBodyClassNames', $classes);
        
        return $classes;
    }
    
    /**
     * Answers an array of heading class names for the HTML template.
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
     * Answers an array of sub-heading class names for the HTML template.
     *
     * @return array
     */
    public function getSubHeadingClassNames()
    {
        $classes = $this->styles('feature.sub-heading');
        
        $this->extend('updateSubHeadingClassNames', $classes);
        
        return $classes;
    }
    
    /**
     * Answers an array of link class names for the template.
     *
     * @return array
     */
    public function getLinkClassNames()
    {
        $classes = ['feature'];
        
        $this->extend('updateLinkClassNames', $classes);
        
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
     * Answers the heading text for the receiver.
     *
     * @return string
     */
    public function getHeadingText()
    {
        if ($this->Heading) {
            return $this->Heading;
        }
        
        if ($this->hasLinkPage()) {
            return $this->LinkPage()->MetaTitle;
        }
    }
    
    /**
     * Answers the sub-heading tag for the receiver.
     *
     * @return string
     */
    public function getSubHeadingTag()
    {
        if ($tag = $this->getField('SubHeadingLevel')) {
            return $tag;
        }
        
        return $this->config()->sub_heading_level_default;
    }
    
    /**
     * Answers the sub-heading text for the receiver.
     *
     * @return string
     */
    public function getSubHeadingText()
    {
        return $this->SubHeading;
    }
    
    /**
     * Answers true if the receiver has an image.
     *
     * @return boolean
     */
    public function hasImage()
    {
        return ($this->Image()->exists() || $this->LinkPage()->hasMetaImage());
    }
    
    /**
     * Answers true if the icon is to be shown in the template.
     *
     * @return boolean
     */
    public function getIconShown()
    {
        return ($this->hasFontIcon() && $this->ShowIcon);
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
        return (boolean) $this->ShowHeader;
    }
    
    /**
     * Answers the link for the feature.
     *
     * @return string
     */
    public function getFeatureLink()
    {
        return $this->getLink();
    }
    
    /**
     * Answers the title for the link.
     *
     * @return string
     */
    public function getLinkTitle()
    {
        return $this->HeadingText ? $this->HeadingText : $this->Title;
    }
    
    /**
     * Answers true if the feature is to be linked.
     *
     * @return boolean
     */
    public function getFeatureLinked()
    {
        return ($this->LinkFeature && $this->hasLink());
    }
    
    /**
     * Answers true if the heading is to be linked.
     *
     * @return boolean
     */
    public function getHeadingLinked()
    {
        return ($this->LinkHeading && $this->hasLink() && !$this->LinkFeature);
    }
    
    /**
     * Answers true if the summary is to be shown in the template.
     *
     * @return boolean
     */
    public function getSummaryShown()
    {
        return (boolean) $this->ShowSummary;
    }
    
    /**
     * Answers true if the footer is to be shown in the template.
     *
     * @return boolean
     */
    public function getFooterShown()
    {
        return ($this->ShowFooter && $this->hasLink() && !$this->LinkFeature);
    }
    
    /**
     * Answers the summary text either from the receiver or the featured page.
     *
     * @return DBHTMLText|string
     */
    public function getSummaryText()
    {
        if ($this->Summary) {
            return $this->Summary;
        }
        
        if ($this->hasLinkPage()) {
            return $this->LinkPage()->MetaSummary;
        }
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
        } elseif ($this->LinkPage()->hasMetaImage()) {
            $image = $this->LinkPage()->getMetaImage();
        }
        
        return $this->performImageResize($image);
    }
}
