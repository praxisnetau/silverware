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

use SilverStripe\Assets\Image;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;
use SilverWare\Forms\DimensionsField;
use SilverWare\Forms\FieldSection;
use SilverWare\Tools\ImageTools;

/**
 * A data extension class which adds image resizing functionality to the extended object.
 *
 * @package SilverWare\Extensions\Model
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class ImageResizeExtension extends DataExtension
{
    /**
     * Maps field names to field types for the extended object.
     *
     * @var array
     * @config
     */
    private static $db = [
        'ImageResize' => 'Dimensions',
        'ImageResizeMethod' => 'Varchar(32)'
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
        // Create Style Fields:
        
        $fields->addFieldToTab(
            'Root.Style',
            FieldSection::create(
                'ImageResizeStyle',
                $this->owner->fieldLabel('ImageResizeStyle'),
                [
                    DimensionsField::create(
                        'ImageResize',
                        $this->owner->fieldLabel('ImageResize')
                    ),
                    DropdownField::create(
                        'ImageResizeMethod',
                        $this->owner->fieldLabel('ImageResizeMethod'),
                        $this->tools()->getResizeMethods()
                    )->setEmptyString(' ')->setAttribute('data-placeholder', _t(__CLASS__ . '.NONE', 'None'))
                ]
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
        $labels['ImageResize'] = _t(__CLASS__ . '.DIMENSIONS', 'Dimensions');
        $labels['ImageResizeStyle'] = _t(__CLASS__ . '.IMAGERESIZE', 'Image resize');
        $labels['ImageResizeMethod'] = _t(__CLASS__ . '.RESIZEMETHOD', 'Resize method');
    }
    
    /**
     * Answers a resized image using the defined dimensions and resize method.
     *
     * @param Image $image Image object to be resized.
     *
     * @return Image
     */
    public function performImageResize(Image $image)
    {
        return $this->tools()->resize(
            $image,
            $this->owner->ImageResizeWidth,
            $this->owner->ImageResizeHeight,
            $this->owner->ImageResizeMethod
        );
    }
    
    /**
     * Answers the image tools singleton.
     *
     * @return ImageTools
     */
    protected function tools()
    {
        return ImageTools::singleton();
    }
}
