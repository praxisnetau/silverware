<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Extensions\Model
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\Extensions\Model;

use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\CompositeField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Forms\Tab;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\FieldType\DBField;
use SilverWare\Components\ListComponent;
use SilverWare\Forms\DimensionsField;
use SilverWare\Tools\ImageTools;
use SilverWare\Tools\ViewTools;

/**
 * A data extension class to add meta data functionality to the extended object.
 *
 * @package SilverWare\Extensions\Model
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class MetaDataExtension extends DataExtension
{
    /**
     * Maps field names to field types for the extended object.
     *
     * @var array
     * @config
     */
    private static $db = [
        'SummaryMeta' => 'HTMLText',
        'ImageMetaHidden' => 'Boolean',
        'ImageMetaResize' => 'Dimensions',
        'ImageMetaResizeMethod' => 'Varchar(32)',
        'ImageMetaCaption' => 'HTMLText',
        'ImageMetaCaptionHidden' => 'Boolean',
        'ImageMetaAlignment' => 'Varchar(32)'
    ];
    
    /**
     * Defines the has-one associations for the extended object.
     *
     * @var array
     * @config
     */
    private static $has_one = [
        'ImageMeta' => Image::class
    ];
    
    /**
     * Defines the ownership of associations for the extended object.
     *
     * @var array
     * @config
     */
    private static $owns = [
        'ImageMeta'
    ];
    
    /**
     * Maps field and method names to the class names of casting objects.
     *
     * @var array
     * @config
     */
    private static $casting = [
        'MetaSummary' => 'HTMLFragment',
        'MetaContent' => 'HTMLFragment',
        'MetaImageCaption' => 'HTMLFragment',
        'MetaImageLinkAttributesHTML' => 'HTMLFragment'
    ];
    
    /**
     * Defines the default values for the fields of the extended object.
     *
     * @var array
     * @config
     */
    private static $defaults = [
        'ImageMetaHidden' => 0,
        'ImageMetaCaptionHidden' => 0
    ];
    
    /**
     * Defines the list item details to show for the extended object.
     *
     * @var array
     * @config
     */
    private static $list_item_details = [
        'date' => [
            'icon' => 'calendar',
            'text' => '$MetaDateFormatted',
            'args' => '$DateFormat'
        ]
    ];
    
    /**
     * Updates the CMS fields of the extended object.
     *
     * @param FieldList $fields List of CMS fields from the extended object.
     *
     * @return void
     */
    public function updateCMSFields(FieldList $fields)
    {
        // Insert Meta Tab:
        
        $fields->insertAfter(
            Tab::create(
                'Meta',
                $this->owner->fieldLabel('Meta')
            ),
            'Main'
        );
        
        // Define Placeholder:
        
        $placeholder = _t(__CLASS__ . '.DROPDOWNDEFAULT', '(default)');
        
        // Create Meta Fields:
        
        $fields->addFieldsToTab(
            'Root.Meta',
            [
                HTMLEditorField::create(
                    'SummaryMeta',
                    $this->owner->fieldLabel('SummaryMeta')
                )->setRows(10),
                CompositeField::create([
                    UploadField::create(
                        'ImageMeta',
                        $this->owner->fieldLabel('ImageMeta')
                    )->setAllowedFileCategories('image'), //->setFolderName($this->getAssetFolder()),
                    HTMLEditorField::create(
                        'ImageMetaCaption',
                        $this->owner->fieldLabel('ImageMetaCaption')
                    )->setRows(10),
                    DropdownField::create(
                        'ImageMetaAlignment',
                        $this->owner->fieldLabel('ImageMetaAlignment'),
                        ImageTools::singleton()->getAlignmentOptions()
                    )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder),
                    DimensionsField::create(
                        'ImageMetaResize',
                        $this->owner->fieldLabel('ImageMetaResize')
                    ),
                    DropdownField::create(
                        'ImageMetaResizeMethod',
                        $this->owner->fieldLabel('ImageMetaResizeMethod'),
                        ImageTools::singleton()->getResizeMethods()
                    )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder),
                    CheckboxField::create(
                        'ImageMetaHidden',
                        $this->owner->fieldLabel('ImageMetaHidden')
                    ),
                    CheckboxField::create(
                        'ImageMetaCaptionHidden',
                        $this->owner->fieldLabel('ImageMetaCaptionHidden')
                    )
                ])->setName('ImageMetaComposite')->setTitle($this->owner->fieldLabel('ImageMetaComposite'))
            ]
        );
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
        $labels['Meta'] = _t(__CLASS__ . '.META', 'Meta');
        $labels['ImageMeta'] = _t(__CLASS__ . '.FILE', 'File');
        $labels['ImageMetaResize'] = _t(__CLASS__ . '.DIMENSIONS', 'Dimensions');
        $labels['ImageMetaCaption'] = _t(__CLASS__ . '.CAPTION', 'Caption');
        $labels['ImageMetaComposite'] = _t(__CLASS__ . '.IMAGE', 'Image');
        $labels['ImageMetaResizeMethod'] = _t(__CLASS__ . '.RESIZEMETHOD', 'Resize method');
        $labels['ImageMetaCaptionHidden'] = _t(__CLASS__ . '.HIDECAPTION', 'Hide caption');
        $labels['ImageMetaAlignment'] = _t(__CLASS__ . '.ALIGNMENT', 'Alignment');
        $labels['ImageMetaHidden'] = _t(__CLASS__ . '.HIDEIMAGE', 'Hide image');
        $labels['SummaryMeta'] = _t(__CLASS__ . '.SUMMARY', 'Summary');
    }
    
    /**
     * Answers the link for the extended object.
     *
     * @return string
     */
    public function getMetaLink()
    {
        if ($this->owner->hasMethod('Link')) {
            return $this->owner->Link();
        } elseif ($this->owner->Link) {
            return $this->owner->Link;
        }
    }
    
    /**
     * Answers true if the extended object has a link.
     *
     * @return boolean
     */
    public function hasMetaLink()
    {
        return (boolean) $this->getMetaLink();
    }
    
    /**
     * Answers the absolute link for the extended object.
     *
     * @return string
     */
    public function getMetaAbsoluteLink()
    {
        if ($this->owner->hasMethod('AbsoluteLink')) {
            return $this->owner->AbsoluteLink();
        } elseif ($this->owner->AbsoluteLink) {
            return $this->owner->AbsoluteLink;
        }
    }
    
    /**
     * Answers the title for the extended object.
     *
     * @return string
     */
    public function getMetaTitle()
    {
        return $this->owner->Title;
    }
    
    /**
     * Answers an arbitrary date for the extended object (defaults to date created).
     *
     * @return DBDatetime
     */
    public function getMetaDate()
    {
        return $this->getMetaCreated();
    }
    
    /**
     * Formats the date of the extended object using the given format.
     *
     * @param string $format
     *
     * @return string
     */
    public function getMetaDateFormatted($format)
    {
        return $this->getMetaDate()->Format($format);
    }
    
    /**
     * Answers true if the extended object has a date.
     *
     * @return boolean
     */
    public function hasMetaDate()
    {
        return (boolean) $this->owner->getMetaDate();
    }
    
    /**
     * Answers the created date/time for the extended object.
     *
     * @return DBDatetime
     */
    public function getMetaCreated()
    {
        return $this->owner->dbObject('Created');
    }
    
    /**
     * Answers the modified date/time for the extended object.
     *
     * @return DBDatetime
     */
    public function getMetaModified()
    {
        return $this->owner->dbObject('LastEdited');
    }
    
    /**
     * Answers the summary for the extended object.
     *
     * @return DBHTMLText
     */
    public function getMetaSummary()
    {
        if ($this->owner->SummaryMeta) {
            return $this->owner->dbObject('SummaryMeta');
        }
        
        if ($this->owner->hasField('Summary') && $this->owner->Summary) {
            return $this->owner->dbObject('Summary');
        }
        
        if ($content = $this->getMetaContent()) {
            return DBField::create_field('HTMLFragment', sprintf('<p>%s</p>', $content->Summary()));
        }
    }
    
    /**
     * Answers true if the extended object has a summary.
     *
     * @return boolean
     */
    public function hasMetaSummary()
    {
        if ($summary = $this->getMetaSummary()) {
            return (boolean) $summary->RAW();
        }
        
        return false;
    }
    
    /**
     * Answers a plain-text version of the meta summary limited to the specified number of sentences.
     *
     * @param integer $maxSentences
     *
     * @return string
     */
    public function getMetaSummaryLimited($maxSentences = 2)
    {
        if ($this->hasMetaSummary()) {
            return trim(preg_replace('/\s+/', ' ', $this->getMetaSummary()->LimitSentences($maxSentences)));
        }
    }
    
    /**
     * Answers the content for the extended object.
     *
     * @return DBHTMLText
     */
    public function getMetaContent()
    {
        if ($this->owner->hasField('Content') && $this->owner->Content) {
            return $this->owner->dbObject('Content');
        }
    }
    
    /**
     * Answers true if the extended object has content.
     *
     * @return boolean
     */
    public function hasMetaContent()
    {
        if ($content = $this->getMetaContent()) {
            return (boolean) $content->RAW();
        }
        
        return false;
    }
    
    /**
     * Answers an array of class names for the extended object.
     *
     * @return array
     */
    public function getMetaClassNames()
    {
        return [
            ($this->hasMetaImage() ? 'has-image' : 'no-image')
        ];
    }
    
    /**
     * Answers the meta image for the extended object.
     *
     * @return Image
     */
    public function getMetaImage()
    {
        if ($this->owner->hasOne('ImageMeta')) {
            return $this->owner->ImageMeta();
        }
    }
    
    /**
     * Answers true if the extended object has an image.
     *
     * @return boolean
     */
    public function hasMetaImage()
    {
        if ($image = $this->getMetaImage()) {
            return $image->exists();
        }
        
        return false;
    }
    
    /**
     * Answers the link for the meta image.
     *
     * @return string
     */
    public function getMetaImageLink()
    {
        if ($this->hasMetaImage()) {
            
            if ($list = $this->getListComponent()) {
                
                switch ($list->ImageLinksTo) {
                    
                    case ListComponent::IMAGE_LINK_ITEM:
                        return $this->owner->getMetaLink();
                    
                }
                
            }
            
            return $this->owner->getMetaImage()->getAbsoluteURL();
            
        }
    }
    
    /**
     * Answers the HTML tag attributes for the image link as an array.
     *
     * @return array
     */
    public function getMetaImageLinkAttributes()
    {
        return [
            'href' => $this->owner->MetaImageLink,
            'title' => $this->owner->MetaTitle
        ];
    }
    
    /**
     * Answers the HTML tag attributes for the image link as a string.
     *
     * @return string
     */
    public function getMetaImageLinkAttributesHTML()
    {
        return ViewTools::singleton()->getAttributesHTML($this->getMetaImageLinkAttributes());
    }
    
    /**
     * Answers the meta image caption for the extended object.
     *
     * @return Image
     */
    public function getMetaImageCaption()
    {
        return $this->owner->ImageMetaCaption;
    }
    
    /**
     * Answers true if the extended object has an image caption.
     *
     * @return boolean
     */
    public function hasMetaImageCaption()
    {
        return (boolean) $this->owner->getMetaImageCaption();
    }
    
    /**
     * Answers true if the meta image is to be shown in the template.
     *
     * @return boolean
     */
    public function getMetaImageShown()
    {
        return ($this->hasMetaImage() && !$this->owner->ImageMetaHidden);
    }
    
    /**
     * Answers true if the meta image caption is to be shown in the template.
     *
     * @return boolean
     */
    public function getMetaImageCaptionShown()
    {
        return ($this->hasMetaImageCaption() && !$this->owner->ImageMetaCaptionHidden);
    }
    
    /**
     * Answers the meta image resized using the dimensions and resize method.
     *
     * @param integer $width
     * @param integer $height
     * @param string $method
     *
     * @return Image
     */
    public function getMetaImageResized($width = null, $height = null, $method = null)
    {
        if ($this->hasMetaImage()) {
            
            return ImageTools::singleton()->resize(
                $this->getMetaImage(),
                $this->getMetaImageResizeWidth($width),
                $this->getMetaImageResizeHeight($height),
                $this->getMetaImageResizeMethod($method)
            );
            
        }
    }
    
    /**
     * Answers the resize width for the meta image.
     *
     * @param integer $width
     *
     * @return integer
     */
    public function getMetaImageResizeWidth($width = null)
    {
        if ($width) {
            return $width;
        }
        
        if ($width = $this->owner->ImageMetaResizeWidth) {
            return $width;
        }
        
        return $this->owner->getFieldFromParent('DefaultImageResizeWidth');
    }
    
    /**
     * Answers the resize height for the meta image.
     *
     * @param integer $height
     *
     * @return integer
     */
    public function getMetaImageResizeHeight($height = null)
    {
        if ($height) {
            return $height;
        }
        
        if ($height = $this->owner->ImageMetaResizeHeight) {
            return $height;
        }
        
        return $this->owner->getFieldFromParent('DefaultImageResizeHeight');
    }
    
    /**
     * Answers the resize method for the meta image.
     *
     * @param string $method
     *
     * @return string
     */
    public function getMetaImageResizeMethod($method = null)
    {
        if ($method) {
            return $method;
        }
        
        if ($method = $this->owner->ImageMetaResizeMethod) {
            return $method;
        }
        
        return $this->owner->getFieldFromParent('DefaultImageResizeMethod');
    }
    
    /**
     * Answers the alignment for the meta image.
     *
     * @param string $alignment
     *
     * @return string
     */
    public function getMetaImageAlignment($alignment = null)
    {
        if ($alignment) {
            return $alignment;
        }
        
        if ($alignment = $this->owner->ImageMetaAlignment) {
            return $alignment;
        }
        
        return $this->owner->getFieldFromParent('DefaultImageAlignment');
    }
    
    /**
     * Answers a string of meta image class names for the template.
     *
     * @return string
     */
    public function getMetaImageClass()
    {
        return ViewTools::singleton()->array2att($this->getMetaImageClassNames());
    }
    
    /**
     * Answers an array of meta image class names for the template.
     *
     * @return array
     */
    public function getMetaImageClassNames()
    {
        $classes = ['image'];
        
        if ($alignment = $this->getMetaImageAlignment()) {
            $classes[] = $alignment;
        }
        
        return $classes;
    }
    
    /**
     * Answers a string of meta image caption class names for the template.
     *
     * @return string
     */
    public function getMetaImageCaptionClass()
    {
        return ViewTools::singleton()->array2att($this->getMetaImageCaptionClassNames());
    }
    
    /**
     * Answers an array of meta image caption class names for the template.
     *
     * @return array
     */
    public function getMetaImageCaptionClassNames()
    {
        $classes = ['caption'];
        
        if ($alignment = $this->getMetaImageAlignment()) {
            $classes[] = $alignment;
        }
        
        return $classes;
    }
    
    /**
     * Answers a string of meta image wrapper class names for the template.
     *
     * @return string
     */
    public function getMetaImageWrapperClass()
    {
        return ViewTools::singleton()->array2att($this->getMetaImageWrapperClassNames());
    }
    
    /**
     * Answers an array of meta image wrapper class names for the template.
     *
     * @return array
     */
    public function getMetaImageWrapperClassNames()
    {
        $classes = [$this->owner->MetaImageCaptionShown ? 'captionImage' : 'image'];
        
        if ($alignment = $this->getMetaImageAlignment()) {
            $classes[] = $alignment;
        }
        
        return $classes;
    }
    
    /**
     * Answers a list component associated with the extended object (if it exists).
     *
     * @return ListComponent
     */
    protected function getListComponent()
    {
        return $this->owner->ListComponent;
    }
}
