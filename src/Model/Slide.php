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

use SilverStripe\Assets\Image;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\ArrayList;
use SilverWare\Extensions\Model\LinkToExtension;
use SilverWare\Forms\FieldSection;
use SilverWare\Forms\PageDropdownField;
use SilverWare\Tools\ImageTools;
use SilverWare\Tools\ViewTools;
use Page;

/**
 * An extension of the component class for a slide.
 *
 * @package SilverWare\Model
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class Slide extends Component
{
    /**
     * Human-readable singular name.
     *
     * @var string
     * @config
     */
    private static $singular_name = 'Slide';
    
    /**
     * Human-readable plural name.
     *
     * @var string
     * @config
     */
    private static $plural_name = 'Slides';
    
    /**
     * Description of this object.
     *
     * @var string
     * @config
     */
    private static $description = 'A component which represents a slide';
    
    /**
     * Icon file for this object.
     *
     * @var string
     * @config
     */
    private static $icon = 'silverware/admin/client/dist/images/icons/Slide.png';
    
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
        'Caption' => 'HTMLText',
        'HideImage' => 'Boolean',
        'HideTitle' => 'Boolean',
        'HideCaption' => 'Boolean',
        'LinkDisabled' => 'Boolean'
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
     * Maps field and method names to the class names of casting objects.
     *
     * @var array
     * @config
     */
    private static $casting = [
        'getSlideAttributesHTML' => 'HTMLFragment'
    ];
    
    /**
     * Defines the default values for the fields of this object.
     *
     * @var array
     * @config
     */
    private static $defaults = [
        'HideImage' => 0,
        'HideTitle' => 1,
        'HideCaption' => 0,
        'LinkDisabled' => 0
    ];
    
    /**
     * Defines the extension classes to apply to this object.
     *
     * @var array
     * @config
     */
    private static $extensions = [
        LinkToExtension::class
    ];
    
    /**
     * Defines the asset folder for uploading images.
     *
     * @var string
     * @config
     */
    private static $asset_folder = 'Slides';
    
    /**
     * Defines the default heading level to use.
     *
     * @var array
     * @config
     */
    private static $heading_level_default = 'h4';
    
    /**
     * Tag name to use when rendering this object.
     *
     * @var string
     * @config
     */
    private static $tag = 'div';
    
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
                UploadField::create(
                    'Image',
                    $this->fieldLabel('Image')
                )->setAllowedFileCategories('image')->setFolderName($this->getAssetFolder()),
                HTMLEditorField::create(
                    'Caption',
                    $this->fieldLabel('Caption')
                )->setRows(10)
            ],
            'LinkTo'
        );
        
        // Create Options Fields:
        
        $fields->fieldByName('Root.Options.LinkOptions')->merge([
            CheckboxField::create(
                'HideTitle',
                $this->fieldLabel('HideTitle')
            ),
            CheckboxField::create(
                'HideImage',
                $this->fieldLabel('HideImage')
            ),
            CheckboxField::create(
                'HideCaption',
                $this->fieldLabel('HideCaption')
            ),
            CheckboxField::create(
                'LinkDisabled',
                $this->fieldLabel('LinkDisabled')
            )
        ]);
        
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
        $labels['Caption'] = _t(__CLASS__ . '.CAPTION', 'Caption');
        $labels['StripThumbnail'] = _t(__CLASS__ . '.IMAGE', 'Image');
        $labels['CaptionLimited'] = _t(__CLASS__ . '.CAPTION', 'Caption');
        
        // Define Checkbox Field Labels:
        
        $labels['HideImage'] = _t(__CLASS__ . '.HIDEIMAGE', 'Hide image');
        $labels['HideTitle'] = _t(__CLASS__ . '.HIDETITLE', 'Hide title');
        $labels['HideCaption'] = _t(__CLASS__ . '.HIDECAPTION', 'Hide caption');
        $labels['LinkDisabled'] = _t(__CLASS__ . '.LINKDISABLED', 'Link disabled');
        
        // Define Relation Labels:
        
        if ($includerelations) {
            $labels['Image'] = _t(__CLASS__ . '.has_one_Image', 'Image');
        }
        
        // Answer Field Labels:
        
        return $labels;
    }
    
    /**
     * Answers the tag for the receiver.
     *
     * @return string
     */
    public function getTag()
    {
        if ($tag = $this->getParent()->SlideTag) {
            return $tag;
        }
        
        return parent::getTag();
    }
    
    /**
     * Answers the heading tag for the receiver.
     *
     * @return string
     */
    public function getHeadingTag()
    {
        if ($tag = $this->getParent()->HeadingTag) {
            return $tag;
        }
        
        return $this->config()->heading_level_default;
    }
    
    /**
     * Answers an array of HTML tag attributes for the slide.
     *
     * @param boolean $isFirst Slide is first in the list.
     * @param boolean $isMiddle Slide is in the middle of the list.
     * @param boolean $isLast Slide is last in the list.
     *
     * @return array
     */
    public function getSlideAttributes($isFirst = false, $isMiddle = false, $isLast = false)
    {
        $attributes = ['class' => $this->getSlideClass($isFirst, $isMiddle, $isLast)];
        
        if ($this->getParent()->hasMethod('getSlideAttributes')) {
            
            $attributes = array_merge(
                $attributes,
                $this->getParent()->getSlideAttributes(
                    $this,
                    $isFirst,
                    $isMiddle,
                    $isLast
                )
            );
            
        }
        
        $this->extend('updateSlideAttributes', $attributes);
        
        return $attributes;
    }
    
    /**
     * Answers the HTML tag attributes for the slide as a string.
     *
     * @param boolean $isFirst Slide is first in the list.
     * @param boolean $isMiddle Slide is in the middle of the list.
     * @param boolean $isLast Slide is last in the list.
     *
     * @return string
     */
    public function getSlideAttributesHTML($isFirst = false, $isMiddle = false, $isLast = false)
    {
        return $this->getAttributesHTML($this->getSlideAttributes($isFirst, $isMiddle, $isLast));
    }
    
    /**
     * Answers a string of slide class names for the HTML template.
     *
     * @param boolean $isFirst Slide is first in the list.
     * @param boolean $isMiddle Slide is in the middle of the list.
     * @param boolean $isLast Slide is last in the list.
     *
     * @return string
     */
    public function getSlideClass($isFirst = false, $isMiddle = false, $isLast = false)
    {
        return ViewTools::singleton()->array2att($this->getSlideClassNames($isFirst, $isMiddle, $isLast));
    }
    
    /**
     * Answers an array of slide class names for the HTML template.
     *
     * @param boolean $isFirst Slide is first in the list.
     * @param boolean $isMiddle Slide is in the middle of the list.
     * @param boolean $isLast Slide is last in the list.
     *
     * @return array
     */
    public function getSlideClassNames($isFirst = false, $isMiddle = false, $isLast = false)
    {
        $classes = ViewTools::singleton()->getAncestorClassNames($this, self::class);
        
        $classes[] = $this->ImageShown ? 'has-image' : 'no-image';
        
        if ($this->getParent()->hasMethod('getSlideClassNames')) {
            
            $classes = array_merge(
                $classes,
                $this->getParent()->getSlideClassNames(
                    $this,
                    $isFirst,
                    $isMiddle,
                    $isLast
                )
            );
            
        }
        
        $this->extend('updateSlideClassNames', $classes, $isFirst, $isMiddle, $isLast);
        
        return $classes;
    }
    
    /**
     * Answers an array of image class names for the HTML template.
     *
     * @return array
     */
    public function getImageClassNames()
    {
        $classes = ['slide-image'];
        
        if ($this->getParent()->hasMethod('getImageClassNames')) {
            $classes = array_merge($classes, $this->getParent()->getImageClassNames($this));
        }
        
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
        $classes = ['slide-caption'];
        
        if ($this->getParent()->hasMethod('getCaptionClassNames')) {
            $classes = array_merge($classes, $this->getParent()->getCaptionClassNames($this));
        }
        
        $this->extend('updateCaptionClassNames', $classes);
        
        return $classes;
    }
    
    /**
     * Answers the asset folder used by the receiver.
     *
     * @return string
     */
    public function getAssetFolder()
    {
        if ($folder = $this->getParent()->AssetFolder) {
            return $folder;
        }
        
        return $this->config()->asset_folder;
    }
    
    /**
     * Answers a thumbnail of the image for a grid field.
     *
     * @return AssetContainer|DBHTMLText
     */
    public function getStripThumbnail()
    {
        return $this->Image()->StripThumbnail();
    }
    
    /**
     * Answers the caption limited to the specified number of words.
     *
     * @param integer $words
     *
     * @return string
     */
    public function getCaptionLimited($words = 15)
    {
        return $this->dbObject('Caption')->LimitWordCount($words);
    }
    
    /**
     * Answers true if the receiver has an image.
     *
     * @return boolean
     */
    public function hasImage()
    {
        return ($this->Image()->exists() || $this->hasPageImage());
    }
    
    /**
     * Answers true if the receiver has an image from the linked page.
     *
     * @return boolean
     */
    public function hasPageImage()
    {
        return $this->LinkPage()->hasMetaImage();
    }
    
    /**
     * Answers true if the image is to be shown in the template.
     *
     * @return boolean
     */
    public function getImageShown()
    {
        return ($this->hasImage() && !$this->HideImage);
    }
    
    /**
     * Answers the image for the slide.
     *
     * @return Image
     */
    public function getSlideImage()
    {
        if ($this->hasImage()) {
            
            if ($this->Image()->exists()) {
                return $this->Image();
            } elseif ($this->LinkPage()->hasMetaImage()) {
                return $this->LinkPage()->getMetaImage();
            }
            
        }
    }
    
    /**
     * Answers a resized image using the dimensions and resize method from the parent object.
     *
     * @param integer $width
     * @param integer $height
     * @param string $method
     *
     * @return Image
     */
    public function getImageResized($width = null, $height = null, $method = null)
    {
        if ($this->hasImage()) {
            
            $image = $this->getSlideImage();
            
            if ($width || $height || $method) {
                return ImageTools::singleton()->resize($image, $width, $height, $method);
            }
            
            if ($this->getParent()->hasMethod('performImageResize')) {
                return $this->getParent()->performImageResize($image);
            }
            
            return $image;
            
        }
    }
    
    /**
     * Answers true if the slide title or caption is to be shown.
     *
     * @return boolean
     */
    public function getTitleOrCaptionShown()
    {
        return ($this->getTitleShown() || $this->getCaptionShown());
    }
    
    /**
     * Answers true if the slide title is to be shown.
     *
     * @return boolean
     */
    public function getTitleShown()
    {
        return ($this->Title && !$this->HideTitle);
    }
    
    /**
     * Answers true if the slide caption is to be shown.
     *
     * @return boolean
     */
    public function getCaptionShown()
    {
        return ($this->Caption && !$this->HideCaption);
    }
    
    /**
     * Answers true if the slide link is to be shown.
     *
     * @return boolean
     */
    public function getLinkShown()
    {
        return ($this->hasLink() && !$this->LinkDisabled);
    }
    
    /**
     * Answers a list of the enabled slides within the receiver.
     *
     * @return ArrayList
     */
    public function getEnabledSlides()
    {
        $slides = ArrayList::create();
        
        if ($this->isEnabled()) {
            $slides->push($this);
        }
        
        return $slides;
    }
    
    /**
     * Answers the template used to render the receiver.
     *
     * @return string|array|SSViewer
     */
    public function getTemplate()
    {
        if ($template = $this->getParent()->SlideTemplate) {
            return $template;
        }
        
        return parent::getTemplate();
    }
    
    /**
     * Renders the object as a slide for the HTML template.
     *
     * @param boolean $isFirst Slide is first in the list.
     * @param boolean $isMiddle Slide is in the middle of the list.
     * @param boolean $isLast Slide is last in the list.
     *
     * @return DBHTMLText
     */
    public function renderSlide($isFirst = false, $isMiddle = false, $isLast = false)
    {
        return $this->customise([
            'isFirst' => $isFirst,
            'isMiddle' => $isMiddle,
            'isLast' => $isLast
        ])->renderWith($this->getTemplate());
    }
}
