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

use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\CompositeField;
use SilverStripe\Forms\DropdownField;
use SilverWare\Extensions\Lists\ListSourceExtension;
use SilverWare\Extensions\Model\ImageResizeExtension;

/**
 * An extension of the base component class for a base list component.
 *
 * @package SilverWare\Components
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class BaseListComponent extends BaseComponent
{
    /**
     * Define image link constants.
     */
    const IMAGE_LINK_ITEM = 'item';
    const IMAGE_LINK_FILE = 'file';
    
    /**
     * Maps field names to field types for this object.
     *
     * @var array
     * @config
     */
    private static $db = [
        'ImageLinksTo' => 'Varchar(8)',
        'LinkImages' => 'Boolean'
    ];
    
    /**
     * Defines the default values for the fields of this object.
     *
     * @var array
     * @config
     */
    private static $defaults = [
        'ImageLinksTo' => 'item',
        'LinkImages' => 1
    ];
    
    /**
     * Defines the extension classes to apply to this object.
     *
     * @var array
     * @config
     */
    private static $extensions = [
        ListSourceExtension::class,
        ImageResizeExtension::class
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
        
        // Create Options Fields:
        
        $fields->addFieldsToTab(
            'Root.Options',
            [
                CompositeField::create([
                    DropdownField::create(
                        'ImageLinksTo',
                        $this->fieldLabel('ImageLinksTo'),
                        $this->getImageLinksToOptions()
                    ),
                    CheckboxField::create(
                        'LinkImages',
                        $this->fieldLabel('LinkImages')
                    )
                ])->setName('ListImageOptions')->setTitle($this->fieldLabel('ListImageOptions'))
            ]
        );
        
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
        
        $labels['LinkImages'] = _t(__CLASS__ . '.LINKIMAGES', 'Link images');
        $labels['ImageLinksTo'] = _t(__CLASS__ . '.IMAGELINKSTO', 'Image links to');
        $labels['ListImageOptions'] = _t(__CLASS__ . '.LISTIMAGES', 'List images');
        
        // Answer Field Labels:
        
        return $labels;
    }
    
    /**
     * Answers a message string to be shown when no data is available.
     *
     * @return string
     */
    public function getNoDataMessage()
    {
        return _t(__CLASS__ . '.NODATAAVAILABLE', 'No data available.');
    }
    
    /**
     * Answers an array of options for the image links to field.
     *
     * @return array
     */
    public function getImageLinksToOptions()
    {
        return [
            self::IMAGE_LINK_ITEM => _t(__CLASS__ . '.ITEM', 'Item'),
            self::IMAGE_LINK_FILE => _t(__CLASS__ . '.FILE', 'File')
        ];
    }
}
