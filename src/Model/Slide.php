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
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\TabSet;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataObject;
use SilverWare\Forms\PageDropdownField;
use SilverWare\Security\CMSMainPermissions;
use Page;

/**
 * An extension of the data object class for a slide.
 *
 * @package SilverWare\Model
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class Slide extends DataObject
{
    use CMSMainPermissions;
    
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
     * Defines the default sort field and order for this object.
     *
     * @var string
     * @config
     */
    private static $default_sort = 'Sort';
    
    /**
     * Maps field names to field types for this object.
     *
     * @var array
     * @config
     */
    private static $db = [
        'Sort' => 'Int',
        'Title' => 'Varchar(255)',
        'Caption' => 'HTMLText',
        'LinkURL' => 'Varchar(2048)',
        'Disabled' => 'Boolean',
        'HideTitle' => 'Boolean',
        'HideCaption' => 'Boolean',
        'LinkDisabled' => 'Boolean',
        'OpenLinkInNewTab' => 'Boolean'
    ];
    
    /**
     * Defines the has-one associations for this object.
     *
     * @var array
     * @config
     */
    private static $has_one = [
        'Image' => Image::class,
        'LinkPage' => Page::class
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
        'Disabled' => 0,
        'HideTitle' => 1,
        'HideCaption' => 0,
        'LinkDisabled' => 0,
        'OpenLinkInNewTab' => 0
    ];
    
    /**
     * Defines the summary fields of this object.
     *
     * @var array
     * @config
     */
    private static $summary_fields = [
        'StripThumbnail',
        'Title',
        'CaptionLimited',
        'Disabled.Nice'
    ];
    
    /**
     * Defines the asset folder for uploading images.
     *
     * @var string
     * @config
     */
    private static $asset_folder = 'Slides';
    
    /**
     * Answers a list of field objects for the CMS interface.
     *
     * @return FieldList
     */
    public function getCMSFields()
    {
        // Create Field Tab Set:
        
        $fields = FieldList::create(TabSet::create('Root'));
        
        // Create Main Fields:
        
        $fields->addFieldsToTab(
            'Root.Main',
            [
                TextField::create(
                    'Title',
                    $this->fieldLabel('Title')
                ),
                UploadField::create(
                    'Image',
                    $this->fieldLabel('Image')
                )->setAllowedFileCategories('image')->setFolderName($this->getAssetFolder()),
                HTMLEditorField::create(
                    'Caption',
                    $this->fieldLabel('Caption')
                )->setRows(10),
                PageDropdownField::create(
                    'LinkPageID',
                    $this->fieldLabel('LinkPageID')
                ),
                TextField::create(
                    'LinkURL',
                    $this->fieldLabel('LinkURL')
                )
            ]
        );
        
        // Create Options Tab:
        
        $fields->findOrMakeTab('Root.Options', $this->fieldLabel('Options'));
        
        // Create Options Fields:
        
        $fields->addFieldsToTab(
            'Root.Options',
            [
                CheckboxField::create(
                    'HideTitle',
                    $this->fieldLabel('HideTitle')
                ),
                CheckboxField::create(
                    'HideCaption',
                    $this->fieldLabel('HideCaption')
                ),
                CheckboxField::create(
                    'OpenLinkInNewTab',
                    $this->fieldLabel('OpenLinkInNewTab')
                ),
                CheckboxField::create(
                    'LinkDisabled',
                    $this->fieldLabel('LinkDisabled')
                ),
                CheckboxField::create(
                    'Disabled',
                    $this->fieldLabel('Disabled')
                )
            ]
        );
        
        // Extend Field Objects:
        
        $this->extend('updateCMSFields', $fields);
        
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
        return RequiredFields::create([
            'Title'
        ]);
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
        $labels['ImageID'] = _t(__CLASS__ . '.IMAGE', 'Image');
        $labels['Caption'] = _t(__CLASS__ . '.CAPTION', 'Caption');
        $labels['Options'] = _t(__CLASS__ . '.OPTIONS', 'Options');
        $labels['LinkURL'] = _t(__CLASS__ . '.LINKURL',  'Link URL');
        $labels['LinkPageID'] = _t(__CLASS__ . '.LINKPAGE', 'Link page');
        $labels['Disabled.Nice'] = _t(__CLASS__ . '.DISABLED', 'Disabled');
        $labels['StripThumbnail'] = _t(__CLASS__ . '.IMAGE', 'Image');
        $labels['CaptionLimited'] = _t(__CLASS__ . '.CAPTION', 'Caption');
        
        // Define Checkbox Field Labels:
        
        $labels['Disabled'] = _t(__CLASS__ . '.DISABLED', 'Disabled');
        $labels['HideTitle'] = _t(__CLASS__ . '.HIDETITLE', 'Hide title');
        $labels['HideCaption'] = _t(__CLASS__ . '.HIDECAPTION', 'Hide caption');
        $labels['LinkDisabled'] = _t(__CLASS__ . '.LINKDISABLED', 'Link disabled');
        $labels['OpenLinkInNewTab'] = _t(__CLASS__ . '.OPENLINKINNEWTAB', 'Open link in new tab');
        
        // Define Relation Labels:
        
        if ($includerelations) {
            $labels['Image'] = _t(__CLASS__ . '.has_one_Image', 'Image');
            $labels['LinkPage'] = _t(__CLASS__ . '.has_one_LinkPage', 'Link Page');
        }
        
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
        
        // Publish Associated Image:
        
        if ($this->Image()->exists()) {
            $this->Image()->publishRecursive();
        }
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
     * Answers the parent object for the slide.
     *
     * @return DataObject
     */
    public function getParent()
    {
        if ($this->hasField('ParentID')) {
            return $this->getComponent('Parent');
        }
    }
    
    /**
     * Answers true if the receiver has a parent object.
     *
     * @return boolean
     */
    public function hasParent()
    {
        return (boolean) $this->getParent();
    }
    
    /**
     * Answers a resized image using the dimensions and resize method from the parent object.
     *
     * @return Image
     */
    public function getImageResized()
    {
        if ($this->hasParent() && $this->getParent()->hasMethod('performImageResize')) {
            return $this->getParent()->performImageResize($this->Image());
        }
        
        return $this->Image();
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
     * Answers the link URL.
     *
     * @return string
     */
    public function getLink()
    {
        if ($this->LinkURL) {
            return $this->dbObject('LinkURL')->URL();
        }
        
        if ($this->LinkPageID) {
            return $this->LinkPage()->Link();
        }
    }
    
    /**
     * Answers true if the receiver has a link.
     *
     * @return boolean
     */
    public function hasLink()
    {
        return (boolean) $this->getLink();
    }
}
