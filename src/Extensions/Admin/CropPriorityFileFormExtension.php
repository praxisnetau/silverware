<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Extensions\Admin
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\Extensions\Admin;

use SilverStripe\Assets\Image;
use SilverStripe\Control\Controller;
use SilverStripe\Core\Extension;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;

/**
 * An extension class which adds crop priority fields to the file form for images.
 *
 * @package SilverWare\Extensions\Admin
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class CropPriorityFileFormExtension extends Extension
{
    /**
     * Updates the form fields of the extended object.
     *
     * @param FieldList $fields
     * @param Controller $controller
     * @param string $name
     * @param array $context
     *
     * @return void
     */
    public function updateFormFields(FieldList $fields, Controller $controller, $name, $context)
    {
        // Check Record Type:
        
        if ($context['Record'] instanceof Image) {
            
            // Update Field Objects:
            
            $fields->insertAfter(
                'ParentID',
                DropdownField::create(
                    'CropPriority',
                    _t(__CLASS__ . '.CROPPRIORITY', 'Crop priority'),
                    $this->getCropPriorityOptions()
                )
            );
            
        }
    }
    
    /**
     * Answers an array of options for the crop priority field.
     *
     * @return array
     */
    public function getCropPriorityOptions()
    {
        return [
            'center' => _t(__CLASS__ . '.CENTER', 'Center'),
            'top' => _t(__CLASS__ . '.TOP', 'Top'),
            'left' => _t(__CLASS__ . '.LEFT', 'Left'),
            'right' => _t(__CLASS__ . '.RIGHT', 'Right'),
            'bottom' => _t(__CLASS__ . '.BOTTOM', 'Bottom')
        ];
    }
}
