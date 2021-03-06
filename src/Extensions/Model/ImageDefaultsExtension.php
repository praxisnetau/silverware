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

use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;
use SilverWare\Forms\DimensionsField;
use SilverWare\Forms\FieldSection;
use SilverWare\Tools\ImageTools;

/**
 * A data extension class which adds default image settings to the extended object.
 *
 * @package SilverWare\Extensions\Model
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class ImageDefaultsExtension extends DataExtension
{
    /**
     * Maps field names to field types for this object.
     *
     * @var array
     * @config
     */
    private static $db = [
        'ImageDefaultResize' => 'Dimensions',
        'ImageDefaultResizeMethod' => 'Varchar(32)',
        'ImageDefaultAlignment' => 'Varchar(32)',
        'ImageDefaultLinked' => 'Varchar(1)'
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
        // Create Style Tab:
        
        $fields->findOrMakeTab('Root.Style', $this->owner->fieldLabel('Style'));
        
        // Define Placeholder:
        
        $placeholder = _t(__CLASS__ . '.DROPDOWNDEFAULT', '(default)');
        
        // Create Style Fields:
        
        $fields->addFieldToTab(
            'Root.Style',
            FieldSection::create(
                'ImageDefaultsStyle',
                $this->owner->fieldLabel('ImageDefaults'),
                [
                    DropdownField::create(
                        'ImageDefaultAlignment',
                        $this->owner->fieldLabel('ImageDefaultAlignment'),
                        ImageTools::singleton()->getAlignmentOptions()
                    )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder),
                    DimensionsField::create(
                        'ImageDefaultResize',
                        $this->owner->fieldLabel('ImageDefaultResize')
                    ),
                    DropdownField::create(
                        'ImageDefaultResizeMethod',
                        $this->owner->fieldLabel('ImageDefaultResizeMethod'),
                        ImageTools::singleton()->getResizeMethods()
                    )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder),
                    DropdownField::create(
                        'ImageDefaultLinked',
                        $this->owner->fieldLabel('ImageDefaultLinked'),
                        $this->getToggleOptions()
                    )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder)
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
        $labels['Style'] = _t(__CLASS__ . '.STYLE', 'Style');
        $labels['ImageDefaults'] = _t(__CLASS__ . '.IMAGEDEFAULTS', 'Image Defaults');
        $labels['ImageDefaultResize'] = _t(__CLASS__ . '.DIMENSIONS', 'Dimensions');
        $labels['ImageDefaultResizeMethod'] = _t(__CLASS__ . '.RESIZEMETHOD', 'Resize method');
        $labels['ImageDefaultAlignment'] = _t(__CLASS__ . '.ALIGNMENT', 'Alignment');
        $labels['ImageDefaultLinked'] = _t(__CLASS__ . '.LINKIMAGE', 'Link image');
    }
    
    /**
     * Answers the default image resize width.
     *
     * @return integer
     */
    public function getDefaultImageResizeWidth()
    {
        if ($width = $this->owner->ImageDefaultResizeWidth) {
            return $width;
        }
        
        if ($this->owner->hasExtension(MetaDataExtension::class)) {
            return $this->owner->getFieldFromParent('DefaultImageResizeWidth');
        }
    }
    
    /**
     * Answers the default image resize height.
     *
     * @return integer
     */
    public function getDefaultImageResizeHeight()
    {
        if ($height = $this->owner->ImageDefaultResizeHeight) {
            return $height;
        }
        
        if ($this->owner->hasExtension(MetaDataExtension::class)) {
            return $this->owner->getFieldFromParent('DefaultImageResizeHeight');
        }
    }
    
    /**
     * Answers the default image resize method.
     *
     * @return string
     */
    public function getDefaultImageResizeMethod()
    {
        if ($method = $this->owner->ImageDefaultResizeMethod) {
            return $method;
        }
        
        if ($this->owner->hasExtension(MetaDataExtension::class)) {
            return $this->owner->getFieldFromParent('DefaultImageResizeMethod');
        }
    }
    
    /**
     * Answers the default image alignment.
     *
     * @return string
     */
    public function getDefaultImageAlignment()
    {
        if ($alignment = $this->owner->ImageDefaultAlignment) {
            return $alignment;
        }
        
        if ($this->owner->hasExtension(MetaDataExtension::class)) {
            return $this->owner->getFieldFromParent('DefaultImageAlignment');
        }
    }
    
    /**
     * Answers the default image linked setting.
     *
     * @return string
     */
    public function getDefaultImageLinked()
    {
        if ($method = $this->owner->ImageDefaultLinked) {
            return (boolean) $method;
        }
        
        if ($this->owner->hasExtension(MetaDataExtension::class)) {
            return $this->owner->getFieldFromParent('DefaultImageLinked');
        }
    }
    
    /**
     * Answers an array of options for a toggle dropdown field.
     *
     * @return array
     */
    protected function getToggleOptions()
    {
        return [
            0 => _t(__CLASS__ . '.TOGGLENO', 'No'),
            1 => _t(__CLASS__ . '.TOGGLEYES', 'Yes')
        ];
    }
}
