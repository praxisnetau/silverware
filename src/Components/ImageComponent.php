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

use SilverStripe\Assets\File;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverWare\Extensions\Model\ImageResizeExtension;
use SilverWare\Extensions\Model\LinkToExtension;
use SilverWare\Forms\FieldSection;

/**
 * An extension of the base component class for an image component.
 *
 * @package SilverWare\Components
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class ImageComponent extends BaseComponent
{
    /**
     * Human-readable singular name.
     *
     * @var string
     * @config
     */
    private static $singular_name = 'Image Component';
    
    /**
     * Human-readable plural name.
     *
     * @var string
     * @config
     */
    private static $plural_name = 'Image Components';
    
    /**
     * Description of this object.
     *
     * @var string
     * @config
     */
    private static $description = 'A component to show an image';
    
    /**
     * Icon file for this object.
     *
     * @var string
     * @config
     */
    private static $icon = 'silverware/silverware: admin/client/dist/images/icons/ImageComponent.png';
    
    /**
     * Defines the table name to use for this object.
     *
     * @var string
     * @config
     */
    private static $table_name = 'SilverWare_ImageComponent';
    
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
     * Maps field names to field types for this object.
     *
     * @var array
     * @config
     */
    private static $db = [
        'Caption' => 'HTMLText',
        'LinkImage' => 'Boolean',
        'HideCaption' => 'Boolean',
        'InlineVector' => 'Boolean'
    ];
    
    /**
     * Defines the has-one associations for this object.
     *
     * @var array
     * @config
     */
    private static $has_one = [
        'Image' => File::class
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
        'LinkImage' => 1,
        'HideCaption' => 0,
        'InlineVector' => 0
    ];
    
    /**
     * Maps field and method names to the class names of casting objects.
     *
     * @var array
     * @config
     */
    private static $casting = [
        'ImageAttributesHTML' => 'HTMLFragment',
        'ImageLinkAttributesHTML' => 'HTMLFragment'
    ];
    
    /**
     * Defines the extension classes to apply to this object.
     *
     * @var array
     * @config
     */
    private static $extensions = [
        ImageResizeExtension::class,
        LinkToExtension::class
    ];
    
    /**
     * Defines the asset folder for uploading images.
     *
     * @var string
     * @config
     */
    private static $asset_folder = 'Images';
    
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
                $image = UploadField::create(
                    'Image',
                    $this->fieldLabel('Image')
                ),
                HTMLEditorField::create(
                    'Caption',
                    $this->fieldLabel('Caption')
                )->setRows(10)
            ]
        );
        
        // Define Image Field:
        
        $image->setAllowedExtensions(['gif', 'jpg', 'jpeg', 'png', 'svg']);
        $image->setFolderName($this->getAssetFolder());
        
        // Create Options Fields:
        
        $fields->addFieldToTab(
            'Root.Options',
            FieldSection::create(
                'ImageOptions',
                $this->fieldLabel('ImageOptions'),
                [
                    CheckboxField::create(
                        'LinkImage',
                        $this->fieldLabel('LinkImage')
                    ),
                    CheckboxField::create(
                        'HideCaption',
                        $this->fieldLabel('HideCaption')
                    ),
                    CheckboxField::create(
                        'InlineVector',
                        $this->fieldLabel('InlineVector')
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
        
        $labels['Caption'] = _t(__CLASS__ . '.CAPTION', 'Caption');
        $labels['ImageID'] = _t(__CLASS__ . '.IMAGE', 'Image');
        $labels['HideCaption'] = _t(__CLASS__ . '.HIDECAPTION', 'Hide caption');
        $labels['LinkImage'] = _t(__CLASS__ . '.LINKIMAGE', 'Link image');
        $labels['ImageOptions'] = _t(__CLASS__ . '.IMAGE', 'Image');
        $labels['InlineVector'] = _t(__CLASS__ . '.INLINEVECTORIMAGE', 'Inline vector image');
        
        // Define Relation Labels:
        
        if ($includerelations) {
            $labels['Image'] = _t(__CLASS__ . '.has_one_IMAGE', 'Image');
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
     * Answers an array of figure class names for the HTML template.
     *
     * @return array
     */
    public function getFigureClassNames()
    {
        $classes = $this->styles('figure');
        
        $this->extend('updateFigureClassNames', $classes);
        
        return $classes;
    }
    
    /**
     * Answers an array of image class names for the HTML template.
     *
     * @return array
     */
    public function getImageClassNames()
    {
        $classes = $this->styles('figure.image', 'image.fluid');
        
        $this->extend('updateImageClassNames', $classes);
        
        return $classes;
    }
    
    /**
     * Answers an array of caption class names for the HTML template.
     *
     * @return array
     */
    public function getCaptionClassNames()
    {
        $classes =  $this->styles('figure.caption');
        
        $this->extend('updateCaptionClassNames', $classes);
        
        return $classes;
    }
    
    /**
     * Answers the URL for the image.
     *
     * @return string
     */
    public function getImageURL()
    {
        return $this->hasVectorImage() ? $this->Image()->URL : $this->getImageResized()->URL;
    }
    
    /**
     * Answers an array of attributes for the image element.
     *
     * @return array
     */
    public function getImageAttributes()
    {
        $attributes = [
            'src' => $this->ImageURL,
            'class' => $this->ImageClass,
            'title' => $this->Title,
            'alt' => $this->Title
        ];
        
        if ($this->hasVectorImage()) {
            
            if ($width = $this->ImageResizeWidth) {
                $attributes['width'] = $width;
            }
            
            if ($height = $this->ImageResizeHeight) {
                $attributes['height'] = $height;
            }
            
        }
        
        $this->extend('updateImageAttributes', $attributes);
        
        return $attributes;
    }
    
    /**
     * Answers a string of attributes for the image element.
     *
     * @return string
     */
    public function getImageAttributesHTML()
    {
        return $this->getAttributesHTML($this->getImageAttributes());
    }
    
    /**
     * Answers the URL for the image link.
     *
     * @return string
     */
    public function getImageLinkURL()
    {
        return ($link = $this->getLink()) ? $link : $this->Image()->URL;
    }
    
    /**
     * Answers an array of attributes for the image link element.
     *
     * @return array
     */
    public function getImageLinkAttributes()
    {
        $attributes = [
            'href' => $this->ImageLinkURL,
            'data-title' => $this->Title,
            'data-footer' => $this->dbObject('Caption')->Summary(),
            'data-toggle' => 'lightbox',
            'class' => 'image'
        ];
        
        $this->extend('updateImageLinkAttributes', $attributes);
        
        return $attributes;
    }
    
    /**
     * Answers a string of attributes for the image link element.
     *
     * @return string
     */
    public function getImageLinkAttributesHTML()
    {
        return $this->getAttributesHTML($this->getImageLinkAttributes());
    }
    
    /**
     * Answers true if the image caption is to be shown.
     *
     * @return boolean
     */
    public function getCaptionShown()
    {
        return ($this->Caption && !$this->HideCaption);
    }
    
    /**
     * Answers a resized image using the defined dimensions and resize method.
     *
     * @return Image
     */
    public function getImageResized()
    {
        return $this->performImageResize($this->Image());
    }
    
    /**
     * Answers true if the image is an inline vector.
     *
     * @return boolean
     */
    public function hasInlineVector()
    {
        return ($this->hasVectorImage() && $this->InlineVector);
    }
    
    /**
     * Answers true if the image is a vector image.
     *
     * @return boolean
     */
    public function hasVectorImage()
    {
        return ($this->Image()->getExtension() == 'svg');
    }
    
    /**
     * Answers true if the object is disabled within the template.
     *
     * @return boolean
     */
    public function isDisabled()
    {
        if (!$this->Image()->exists()) {
            return true;
        }
        
        return parent::isDisabled();
    }
}
