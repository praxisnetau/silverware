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

use Embed\Embed;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Forms\SelectionGroup;
use SilverStripe\Forms\SelectionGroup_Item;
use SilverStripe\Forms\TextField;
use SilverStripe\View\ArrayData;
use SilverWare\FontIcons\Forms\FontIconField;
use SilverWare\Forms\FieldSection;
use SilverWare\Forms\Validators\MediaURLValidator;

/**
 * An extension of the base component class for a media component.
 *
 * @package SilverWare\Components
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class MediaComponent extends BaseComponent
{
    /**
     * Define caption constants.
     */
    const CAPTION_NONE = 'none';
    const CAPTION_DESC = 'desc';
    const CAPTION_TEXT = 'text';
    
    /**
     * Define aspect ratio constants.
     */
    const ASPECT_RATIO_WIDE = 'sixteen-nine';
    const ASPECT_RATIO_TV   = 'four-three';
    
    /**
     * Define image title constants.
     */
    const IMAGE_TITLE_NONE = 'none';
    const IMAGE_TITLE_MEDIA = 'media';
    const IMAGE_TITLE_COMPONENT = 'component';
    
    /**
     * Define image caption constants.
     */
    const IMAGE_CAPTION_NONE = 'none';
    const IMAGE_CAPTION_MEDIA = 'media';
    const IMAGE_CAPTION_COMPONENT = 'component';
    
    /**
     * Human-readable singular name.
     *
     * @var string
     * @config
     */
    private static $singular_name = 'Media Component';
    
    /**
     * Human-readable plural name.
     *
     * @var string
     * @config
     */
    private static $plural_name = 'Media Components';
    
    /**
     * Description of this object.
     *
     * @var string
     * @config
     */
    private static $description = 'A component which embeds media from a URL';
    
    /**
     * Icon file for this object.
     *
     * @var string
     * @config
     */
    private static $icon = 'silverware/silverware: admin/client/dist/images/icons/MediaComponent.png';
    
    /**
     * Defines the table name to use for this object.
     *
     * @var string
     * @config
     */
    private static $table_name = 'SilverWare_MediaComponent';
    
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
        'Type' => 'Varchar(16)',
        'Code' => 'HTMLText',
        'Width' => 'Int',
        'Height' => 'Int',
        'Description' => 'Text',
        'Caption' => 'HTMLText',
        'CaptionMode' => 'Varchar(8)',
        'MediaURL' => 'Varchar(2048)',
        'ImageURL' => 'Varchar(2048)',
        'MediaTitle' => 'Varchar(255)',
        'AspectRatio' => 'Varchar(16)',
        'ImageTitle' => 'Varchar(16)',
        'ImageCaption' => 'Varchar(16)',
        'ImageWidth' => 'Int',
        'ImageHeight' => 'Int',
        'TextLinkIcon' => 'FontIcon',
        'ShowTextLink' => 'Boolean',
        'LinkImage' => 'Boolean',
        'ShowIcon' => 'Boolean'
    ];
    
    /**
     * Defines the default values for the fields of this object.
     *
     * @var array
     * @config
     */
    private static $defaults = [
        'ImageTitle' => 'component',
        'TextLinkIcon' => 'external-link',
        'ImageCaption' => 'none',
        'CaptionMode' => 'none',
        'ShowTextLink' => 1,
        'LinkImage' => 1,
        'ShowIcon' => 1
    ];
    
    /**
     * Maps field and method names to the class names of casting objects.
     *
     * @var array
     * @config
     */
    private static $casting = [
        'Embed' => 'HTMLFragment',
        'CaptionHTML' => 'HTMLFragment',
        'ImageLinkAttributesHTML' => 'HTMLFragment'
    ];
    
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
                    'MediaURLSection',
                    $this->fieldLabel('MediaURL'),
                    [
                        TextField::create(
                            'MediaURL',
                            ''
                        )->setRightTitle(
                            _t(
                                __CLASS__ . '.MEDIAURLRIGHTTITLE',
                                'Paste the complete URL of the media you wish to embed (e.g. https://youtu.be/dQw4w9WgXcQ)'
                            )
                        )
                    ]
                ),
                SelectionGroup::create(
                    'CaptionMode',
                    [
                        SelectionGroup_Item::create(
                            self::CAPTION_NONE,
                            null,
                            _t(__CLASS__ . '.CAPTIONNONE', 'None')
                        ),
                        SelectionGroup_Item::create(
                            self::CAPTION_DESC,
                            null,
                            _t(__CLASS__ . '.CAPTIONDESC', 'Description')
                        ),
                        SelectionGroup_Item::create(
                            self::CAPTION_TEXT,
                            HTMLEditorField::create(
                                'Caption',
                                ''
                            )->setRows(10),
                            _t(__CLASS__ . '.CAPTIONTEXT', 'Text')
                        )
                    ]
                )->setTitle($this->fieldLabel('Caption'))
            ]
        );
        
        // Add Force Update Field (if Media URL defined):
        
        if ($this->MediaURL) {
            
            $fields->insertAfter(
                CheckboxField::create(
                    'ForceUpdate',
                    $this->fieldLabel('ForceUpdate')
                ),
                'MediaURL'
            );
            
        }
        
        // Create Style Fields:
        
        $fields->addFieldToTab(
            'Root.Style',
            FieldSection::create(
                'MediaStyle',
                $this->fieldLabel('MediaStyle'),
                [
                    FontIconField::create(
                        'TextLinkIcon',
                        $this->fieldLabel('TextLinkIcon')
                    )
                ]
            )
        );
        
        // Create Options Fields:
        
        $fields->addFieldToTab(
            'Root.Options',
            FieldSection::create(
                'MediaOptions',
                $this->fieldLabel('MediaOptions'),
                [
                    DropdownField::create(
                        'ImageTitle',
                        $this->fieldLabel('ImageTitle'),
                        $this->getImageTitleOptions()
                    ),
                    DropdownField::create(
                        'ImageCaption',
                        $this->fieldLabel('ImageCaption'),
                        $this->getImageCaptionOptions()
                    ),
                    CheckboxField::create(
                        'LinkImage',
                        $this->fieldLabel('LinkImage')
                    ),
                    CheckboxField::create(
                        'ShowTextLink',
                        $this->fieldLabel('ShowTextLink')
                    ),
                    CheckboxField::create(
                        'ShowIcon',
                        $this->fieldLabel('ShowIcon')
                    )
                ]
            )
        );
        
        // Answer Field Objects:
        
        return $fields;
    }
    
    /**
     * Answers a validator for the CMS interface.
     *
     * @return RequiredFields
     */
    public function getCMSValidator()
    {
        return MediaURLValidator::create();
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
        $labels['MediaURL'] = _t(__CLASS__ . '.MEDIAURL', 'Media URL');
        $labels['LinkImage'] = _t(__CLASS__ . '.LINKIMAGES', 'Link image');
        $labels['ImageTitle'] = _t(__CLASS__ . '.IMAGETITLE', 'Image title');
        $labels['ImageCaption'] = _t(__CLASS__ . '.IMAGECAPTION', 'Image caption');
        $labels['TextLinkIcon'] = _t(__CLASS__ . '.TEXTLINKICON', 'Text link icon');
        $labels['ShowTextLink'] = _t(__CLASS__ . '.SHOWTEXTLINK', 'Show text link');
        $labels['ForceUpdate'] = _t(__CLASS__ . '.FORCEUPDATEUPONSAVE', 'Force update upon save');
        $labels['ShowIcon'] = _t(__CLASS__ . '.SHOWICONWITHTEXTLINK', 'Show icon with text link');
        $labels['MediaStyle'] = $labels['MediaOptions'] = _t(__CLASS__ . '.MEDIA', 'Media');
        
        // Answer Field Labels:
        
        return $labels;
    }
    
    /**
     * Event method called before the receiver is written to the database.
     *
     * @return void
     */
    public function onBeforeWrite()
    {
        // Call Parent Event:
        
        parent::onBeforeWrite();
        
        // Detect Media URL Change:
        
        if ($this->isChanged('MediaURL', 2) || $this->ForceUpdate) {
            
            // Obtain Adapter Object:
            
            if ($adapter = Embed::create($this->MediaURL)) {
                
                // Define Type and Code:
                
                $this->Type   = $adapter->getType();
                $this->Code   = $adapter->getCode();
                
                // Define Dimensions:
                
                $this->Width  = $adapter->getWidth();
                $this->Height = $adapter->getHeight();
                
                // Define Title and Description:
                
                $this->MediaTitle  = $adapter->getTitle();
                $this->Description = $adapter->getDescription();
                
                // Define Aspect Ratio:
                
                $this->AspectRatio = $adapter->getAspectRatio();
                
                // Define Image URL and Dimensions:
                
                $this->ImageURL    = $adapter->getImage();
                $this->ImageWidth  = $adapter->getImageWidth();
                $this->ImageHeight = $adapter->getImageHeight();
                
            }
            
        }
    }
    
    /**
     * Answers an array of media class names for the HTML template.
     *
     * @return array
     */
    public function getMediaClassNames()
    {
        $classes = [$this->MediaType];
        
        if ($this->MediaType == 'video') {
            $classes[] = $this->VideoAspect;
        }
        
        if ($this->CaptionShown) {
            $classes[] = $this->style('figure.image');
        }
        
        $this->extend('updateMediaClassNames', $classes);
        
        return $classes;
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
        $classes = $this->styles('image.fluid');
        
        $this->extend('updateImageClassNames', $classes);
        
        return $classes;
    }
    
    /**
     * Answers the video aspect class for the template.
     *
     * @return string
     */
    public function getVideoAspect()
    {
        return ceil($this->AspectRatio) == 75 ? self::ASPECT_RATIO_TV : self::ASPECT_RATIO_WIDE;
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
     * Answers true if the caption is to be shown.
     *
     * @return boolean
     */
    public function getCaptionShown()
    {
        return ($this->CaptionHTML && $this->CaptionMode != self::CAPTION_NONE);
    }
    
    /**
     * Answers true if the text link is to be shown.
     *
     * @return boolean
     */
    public function getTextLinkShown()
    {
        return (boolean) $this->ShowTextLink;
    }
    
    /**
     * Answers true if the icon is to be shown.
     *
     * @return boolean
     */
    public function getIconShown()
    {
        return ($this->ShowIcon && $this->TextLinkIcon);
    }
    
    /**
     * Answers the HTML for the caption.
     *
     * @return string
     */
    public function getCaptionHTML()
    {
        if ($this->CaptionMode == self::CAPTION_TEXT && $this->Caption) {
            return $this->Caption;
        }
        
        if ($this->CaptionMode == self::CAPTION_DESC && $this->Description) {
            return sprintf('<p>%s</p>', $this->Description);
        }
    }
    
    /**
     * Answers an array of attributes for the image link element.
     *
     * @return array
     */
    public function getImageLinkAttributes()
    {
        // Detect Media Type:
        
        if ($this->MediaType == 'photo') {
            
            // Answer Photo Attributes:
            
            return [
                'href' => $this->ImageURL,
                'data-title' => $this->ImageTitleText,
                'data-footer' => $this->ImageCaptionText,
                'data-toggle' => 'lightbox',
                'class' => 'image'
            ];
            
        }
        
        // Answer Standard Attributes:
        
        return [
            'href' => $this->MediaURL,
            'class' => 'image'
        ];
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
     * Answers the alt text for the image.
     *
     * @return string
     */
    public function getImageAltText()
    {
        return $this->Title;
    }
    
    /**
     * Answers the title text for image popups.
     *
     * @return string
     */
    public function getImageTitleText()
    {
        switch ($this->ImageTitle) {
            case self::IMAGE_TITLE_MEDIA:
                return $this->MediaTitle;
            case self::IMAGE_TITLE_COMPONENT:
                return $this->Title;
        }
    }
    
    /**
     * Answers the caption text for image popups.
     *
     * @return string
     */
    public function getImageCaptionText()
    {
        switch ($this->ImageCaption) {
            case self::IMAGE_CAPTION_MEDIA:
                return $this->Description;
            case self::IMAGE_CAPTION_COMPONENT:
                return $this->dbObject('Caption')->Summary();
        }
    }
    
    /**
     * Answers the type of media to embed.
     *
     * @return string
     */
    public function getMediaType()
    {
        return strtolower($this->Type);
    }
    
    /**
     * Answers the HTML to embed the media within the template.
     *
     * @return string
     */
    public function getEmbed()
    {
        // Determine Media Type:
        
        switch ($this->MediaType) {
            
            case 'rich':
            case 'video':
                return $this->renderWith(sprintf('%s\Video', self::class));
            
            case 'photo':
                return $this->renderWith(sprintf('%s\Photo', self::class));
            
            case 'link':
                return $this->renderWith(sprintf('%s\Link', self::class));
            
        }
        
        // Answer Null (invalid media type):
        
        return null;
    }
    
    /**
     * Answers true if the receiver has an image.
     *
     * @return boolean
     */
    public function hasImage()
    {
        return (boolean) $this->ImageURL;
    }
    
    /**
     * Answers an array of options for the image title field.
     *
     * @return array
     */
    public function getImageTitleOptions()
    {
        return [
            self::IMAGE_TITLE_NONE => _t(__CLASS__ . '.NONE', 'None'),
            self::IMAGE_TITLE_MEDIA => _t(__CLASS__ . '.MEDIATITLE', 'Media Title'),
            self::IMAGE_TITLE_COMPONENT => _t(__CLASS__ . '.COMPONENTTITLE', 'Component Title')
        ];
    }
    
    /**
     * Answers an array of options for the image caption field.
     *
     * @return array
     */
    public function getImageCaptionOptions()
    {
        return [
            self::IMAGE_CAPTION_NONE => _t(__CLASS__ . '.NONE', 'None'),
            self::IMAGE_CAPTION_MEDIA => _t(__CLASS__ . '.MEDIADESCRIPTION', 'Media Description'),
            self::IMAGE_CAPTION_COMPONENT => _t(__CLASS__ . '.COMPONENTCAPTION', 'Component Caption')
        ];
    }
    
    /**
     * Answers true if the object is disabled within the template.
     *
     * @return boolean
     */
    public function isDisabled()
    {
        if (!$this->MediaURL) {
            return true;
        }
        
        return parent::isDisabled();
    }
}
