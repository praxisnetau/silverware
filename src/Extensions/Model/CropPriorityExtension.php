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

use SilverStripe\Assets\Image_Backend;
use SilverStripe\ORM\DataExtension;

/**
 * A data extension class which allows images to set a crop priority region. 
 *
 * @package SilverWare\Extensions\Model
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class CropPriorityExtension extends DataExtension
{
    /**
     * Maps field names to field types for the extended object.
     *
     * @var array
     * @config
     */
    private static $db = [
        'CropPriority' => 'Varchar(16)'
    ];
    
    /**
     * Defines the default values for the fields of the extended object.
     *
     * @var array
     * @config
     */
    private static $defaults = [
        'CropPriority' => 'center'
    ];
    
    /**
     * Resize and crop the image to fill specified dimensions, using the crop priority as the source region.
     *
     * @param integer $width Width to crop to.
     * @param integer $height Height to crop to.
     *
     * @return Image
     */
    public function FillPriority($width, $height)
    {
        // Obtain Crop Priority:
        
        $priority = $this->owner->CropPriority;
        
        // Check Crop Priority Value:
        
        if (!$priority || $priority == 'center') {
            
            // Perform Regular Fill:
            
            return $this->owner->Fill($width, $height);
            
        } else {
            
            // Obtain Variant Name:
            
            $variant = $this->owner->variantName(__FUNCTION__, $width, $height, $priority);
            
            // Perform Image Manipulation:
            
            return $this->owner->manipulateImage(
                $variant,
                function (Image_Backend $backend) use ($width, $height, $priority) {
                    return $this->croppedResizePriority($backend, $width, $height, $priority);
                }
            );
            
        }
    }
    
    /**
     * Resize and crop the image to fill specified dimensions, using the crop priority as the source region.
     *
     * @param Image_Backend $backend
     * @param integer $width Width to crop to.
     * @param integer $height Height to crop to.
     * @param string $priority Region of image for crop priority.
     *
     * @return Image_Backend
     */
    public function croppedResizePriority(Image_Backend $backend, $width, $height, $priority)
    {
        // Get Source Image:
        
        $image = $this->owner;
        
        // Round Crop Dimensions:
        
        $width  = round($width);
        $height = round($height);
        
        // Get Source Dimensions:
        
        $sourceWidth  = $image->getWidth();
        $sourceHeight = $image->getHeight();
        
        // Check Resize Required:
        
        if ($width == $sourceWidth && $height == $sourceHeight) {
            return $backend;
        }
        
        // Determine Aspect Ratios:
        
        $sourceAspect = $sourceWidth / $sourceHeight;
        $targetAspect = $width / $height;
        
        // Compare Aspect Ratios:
        
        if ($targetAspect < $sourceAspect) {
            
            // Resize by Height (target is narrower):
            
            $backend = $backend->resizeByHeight($height);
            
            // Determine Overwidth Value:
            
            $overWidth = round($backend->getWidth() - $width);
            
            // Perform Crop:
            
            if ($priority == 'left') {
                $backend = $backend->crop(0, 0, $width, $height);
            } elseif ($priority == 'right') {
                $backend = $backend->crop(0, $overWidth, $width, $height);
            } else {
                $backend = $backend->crop(0, floor($overWidth / 2), $width, $height);
            }
            
        } else {
            
            // Resize by Width (target is shorter):
            
            $backend = $backend->resizeByWidth($width);
            
            // Determine Overheight Value:
            
            $overHeight = round($backend->getHeight() - $height);
            
            // Perform Crop:
            
            if ($priority == 'top') {
                $backend = $backend->crop(0, 0, $width, $height);
            } elseif ($priority == 'bottom') {
                $backend = $backend->crop($overHeight, 0, $width, $height);
            } else {
                $backend = $backend->crop(floor($overHeight / 2), 0, $width, $height);
            }
            
        }
        
        // Answer Image Backend:
        
        return $backend;
    }
}
