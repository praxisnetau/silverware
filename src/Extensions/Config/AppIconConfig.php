<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Extensions\Config
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\Extensions\Config;

use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\FieldList;
use SilverWare\Extensions\ConfigExtension;

/**
 * A config extension which adds app icon settings to site configuration.
 *
 * @package SilverWare\Extensions\Config
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class AppIconConfig extends ConfigExtension
{
    /**
     * Defines the has-one associations for this object.
     *
     * @var array
     * @config
     */
    private static $has_one = [
        'AppIconSmall' => Image::class,
        'AppIconLarge' => Image::class,
        'AppIconTouch' => Image::class
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
        // Update Field Objects (from parent):
        
        parent::updateCMSFields($fields);
        
        // Create Icons Tab:
        
        $fields->findOrMakeTab('Root.SilverWare.Icons', $this->owner->fieldLabel('Icons'));
        
        // Create Icon Upload Fields:
        
        $fields->addFieldsToTab(
            'Root.SilverWare.Icons',
            [
                $small = UploadField::create(
                    'AppIconSmall',
                    $this->owner->fieldLabel('AppIconSmall')
                )->setAllowedFileCategories('image')->setFolderName($this->getAssetFolder()),
                $large = UploadField::create(
                    'AppIconLarge',
                    $this->owner->fieldLabel('AppIconLarge')
                )->setAllowedFileCategories('image')->setFolderName($this->getAssetFolder()),
                $touch = UploadField::create(
                    'AppIconTouch',
                    $this->owner->fieldLabel('AppIconTouch')
                )->setAllowedFileCategories('image')->setFolderName($this->getAssetFolder())
            ]
        );
        
        // Define Icon Upload Fields:
        
        $small->setRightTitle(
            _t(
                __CLASS__ . '.APPICONSMALLRIGHTTITLE',
                'Used for the bookmark icon (16 x 16 pixels recommended).'
            )
        );
        
        $large->setRightTitle(
            _t(
                __CLASS__ . '.APPICONLARGERIGHTTITLE',
                'Used for social media sharing (at least 500 x 500 pixels recommended).'
            )
        );
        
        $touch->getValidator()->setAllowedExtensions(['png']);
        $touch->setRightTitle(
            _t(
                __CLASS__ . '.APPICONTOUCHRIGHTTITLE',
                'Used for touch icons (at least 500 x 500 pixels recommended, PNG format required).'
            )
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
        // Update Field Labels (from parent):
        
        parent::updateFieldLabels($labels);
        
        // Update Field Labels:
        
        $labels['Icons'] = _t(__CLASS__ . '.ICONS', 'Icons');
        $labels['AppIconSmall'] = _t(__CLASS__ . '.SMALLICON', 'Small icon');
        $labels['AppIconLarge'] = _t(__CLASS__ . '.LARGEICON', 'Large icon');
        $labels['AppIconTouch'] = _t(__CLASS__ . '.TOUCHICON', 'Touch icon');
    }
    
    /**
     * Event method called before the extended object is written to the database.
     *
     * @return void
     */
    public function onBeforeWrite()
    {
        // Publish Icon Images:
        
        if ($this->owner->AppIconSmall()->exists()) {
            $this->owner->AppIconSmall()->publishRecursive();
        }
        
        if ($this->owner->AppIconLarge()->exists()) {
            $this->owner->AppIconLarge()->publishRecursive();
        }
        
        if ($this->owner->AppIconTouch()->exists()) {
            $this->owner->AppIconTouch()->publishRecursive();
        }
    }
    
    /**
     * Answers the favicon image.
     *
     * @return Image
     */
    public function getFavIcon()
    {
        if ($this->owner->AppIconSmall()->exists()) {
            return $this->owner->AppIconSmall()->ResizedImage(16, 16);
        }
    }
    
    /**
     * Answers the favicon URL.
     *
     * @return string
     */
    public function getFavIconURL()
    {
        if ($icon = $this->getFavIcon()) {
            return $icon->URL;
        }
        
        return '/favicon.ico';
    }
    
    /**
     * Answers the favicon mime type.
     *
     * @return string
     */
    public function getFavIconType()
    {
        if ($icon = $this->getFavIcon()) {
            return $icon->getMimeType();
        }
        
        return 'image/x-icon';
    }
    
    /**
     * Answers the app touch icon mime type.
     *
     * @return string
     */
    public function getAppIconTouchType()
    {
        if ($icon = $this->owner->AppIconTouch()) {
            return $icon->getMimeType();
        }
    }
    
    /**
     * Answers a resized version of the app touch icon.
     *
     * @param integer $width
     * @param integer $height
     *
     * @return Image
     */
    public function getAppIconTouchResized($width, $height)
    {
        if ($this->owner->AppIconTouch()->exists()) {
            return $this->owner->AppIconTouch()->ResizedImage($width, $height);
        }
    }
}
