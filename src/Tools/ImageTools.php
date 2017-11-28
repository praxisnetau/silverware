<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Tools
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\Tools;

use SilverStripe\Assets\Image;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Injector\Injectable;

/**
 * A singleton providing utility functions for use with images.
 *
 * @package SilverWare\Tools
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class ImageTools
{
    use Configurable;
    use Injectable;
    
    /**
     * Define resize constants.
     */
    const RESIZE_CROP_WIDTH = 'crop-width';
    const RESIZE_CROP_HEIGHT = 'crop-height';
    const RESIZE_FILL = 'fill';
    const RESIZE_FILL_MAX = 'fill-max';
    const RESIZE_FILL_PRIORITY = 'fill-priority';
    const RESIZE_FIT = 'fit';
    const RESIZE_FIT_MAX = 'fit-max';
    const RESIZE_PAD = 'pad';
    const RESIZE_SCALE_WIDTH = 'scale-width';
    const RESIZE_SCALE_HEIGHT = 'scale-height';
    const RESIZE_SCALE_MAX_WIDTH = 'scale-max-width';
    const RESIZE_SCALE_MAX_HEIGHT = 'scale-max-height';
    
    /**
     * Define alignment constants.
     */
    const ALIGN_LEFT = 'left';
    const ALIGN_LEFT_ALONE = 'leftAlone';
    const ALIGN_CENTER = 'center';
    const ALIGN_RIGHT = 'right';
    
    /**
     * Map of the available image resize methods.
     *
     * @var array
     * @config
     */
    private static $resize_methods = [];
    
    /**
     * Map of the available image alignment classes.
     *
     * @var array
     * @config
     */
    private static $alignment_classes = [];
    
    /**
     * Answers an array of the available image resize methods.
     *
     * @return array
     */
    public function getResizeMethods()
    {
        return $this->config()->resize_methods;
    }
    
    /**
     * Answers an array of the available image alignment options.
     *
     * @return array
     */
    public function getAlignmentOptions()
    {
        return [
            self::ALIGN_LEFT_ALONE => _t(__CLASS__ . '.ALIGNLEFTALONE', 'Left'),
            self::ALIGN_CENTER => _t(__CLASS__ . '.ALIGNCENTER', 'Center'),
            self::ALIGN_LEFT => _t(__CLASS__ . '.ALIGNLEFT', 'Left wrap'),
            self::ALIGN_RIGHT => _t(__CLASS__ . '.ALIGNRIGHT', 'Right wrap')
        ];
    }
    
    /**
     * Resizes the given image using the specified dimensions and resize method.
     *
     * @param Image $image
     * @param integer $width
     * @param integer $height
     * @param string $method
     *
     * @return Image
     */
    public function resize(Image $image, $width, $height, $method)
    {
        // Get Image Dimensions:
        
        $imageWidth  = $image->getWidth();
        $imageHeight = $image->getHeight();
        
        // Calculate Width and Height (if required):
        
        if ($width && !$height && $imageWidth) {
            $height = round(($width / $imageWidth) * $imageHeight);
        } elseif (!$width && $height && $imageHeight) {
            $width = round(($height / $imageHeight) * $imageWidth);
        }
        
        // Perform Image Resizing:
        
        if ($width && $height) {
            
            switch (strtolower($method)) {
                
                case self::RESIZE_CROP_WIDTH:
                    return $image->CropWidth($width);
                    
                case self::RESIZE_CROP_HEIGHT:
                    return $image->CropHeight($height);
                    
                case self::RESIZE_FILL:
                    return $image->Fill($width, $height);
                    
                case self::RESIZE_FILL_MAX:
                    return $image->FillMax($width, $height);
                    
                case self::RESIZE_FILL_PRIORITY:
                    return $image->FillPriority($width, $height);
                    
                case self::RESIZE_FIT:
                    return $image->Fit($width, $height);
                    
                case self::RESIZE_FIT_MAX:
                    return $image->FitMax($width, $height);
                    
                case self::RESIZE_PAD:
                    return $image->Pad($width, $height);
                    
                case self::RESIZE_SCALE_WIDTH:
                    return $image->ScaleWidth($width);
                    
                case self::RESIZE_SCALE_HEIGHT:
                    return $image->ScaleHeight($height);
                    
                case self::RESIZE_SCALE_MAX_WIDTH:
                    return $image->ScaleMaxWidth($width);
                    
                case self::RESIZE_SCALE_MAX_HEIGHT:
                    return $image->ScaleMaxHeight($height);
                    
            }
            
        }
        
        // Answer Original Image:
        
        return $image;
    }
}
