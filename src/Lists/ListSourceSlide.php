<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Lists
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\Lists;

use SilverStripe\Core\Injector\Injector;
use SilverStripe\ORM\ArrayList;
use SilverStripe\View\ViewableData;
use SilverWare\Extensions\Lists\ListSourceExtension;
use SilverWare\Extensions\Model\LinkToExtension;
use SilverWare\Model\Slide;

/**
 * An extension of the slide class which creates a series of slides from a list source.
 *
 * @package SilverWare\Lists
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class ListSourceSlide extends Slide
{
    /**
     * Human-readable singular name.
     *
     * @var string
     * @config
     */
    private static $singular_name = 'List Source Slide';
    
    /**
     * Human-readable plural name.
     *
     * @var string
     * @config
     */
    private static $plural_name = 'List Source Slides';
    
    /**
     * Description of this object.
     *
     * @var string
     * @config
     */
    private static $description = 'Shows list items as a series of slides';
    
    /**
     * Icon file for this object.
     *
     * @var string
     * @config
     */
    private static $icon = 'silverware/silverware: admin/client/dist/images/icons/ListSourceSlide.png';
    
    /**
     * Defines the table name to use for this object.
     *
     * @var string
     * @config
     */
    private static $table_name = 'SilverWare_ListSourceSlide';
    
    /**
     * Defines the extension classes to apply to this object.
     *
     * @var array
     * @config
     */
    private static $extensions = [
        ListSourceExtension::class
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
        
        // Remove Field Objects:
        
        $fields->removeFieldsFromTab('Root.Main', ['Image', 'Caption', 'LinkTo']);
        
        // Answer Field Objects:
        
        return $fields;
    }
    
    /**
     * Populates the default values for the fields of the receiver.
     *
     * @return void
     */
    public function populateDefaults()
    {
        // Populate Defaults (from parent):
        
        parent::populateDefaults();
        
        // Populate Defaults:
        
        $this->ImageItems = true;
    }
    
    /**
     * Answers a list of the enabled slides within the receiver.
     *
     * @return ArrayList
     */
    public function getEnabledSlides()
    {
        $slides = ArrayList::create();
        
        foreach ($this->getListItems() as $item) {
            
            if ($slide = $this->createSlide($item)) {
                $slides->push($slide);
            }
            
        }
        
        return $slides;
    }
    
    /**
     * Answers true if the slide is disabled within the template.
     *
     * @return boolean
     */
    public function isDisabled()
    {
        return !$this->hasListItems();
    }
    
    /**
     * Creates a slide for the given list item.
     *
     * @param ViewableData $item
     *
     * @return Slide
     */
    protected function createSlide(ViewableData $item)
    {
        // Create Slide Object:
        
        $slide = Slide::create([
            'Title' => $item->MetaTitle,
            'ImageID' => $item->MetaImageID,
            'Caption' => $item->MetaImageCaption,
            'ParentID' => $this->ParentID,
            'HideImage' => $this->HideImage,
            'HideTitle' => $this->HideTitle,
            'HideCaption' => $this->HideCaption,
            'TitleAfterCaption' => $this->TitleAfterCaption
        ]);
        
        // Define Slide Link:
        
        $slide->LinkTo = LinkToExtension::MODE_URL;
        $slide->LinkURL = $item->MetaAbsoluteLink;
        $slide->LinkDisabled = $this->LinkDisabled;
        $slide->OpenLinkInNewTab = $this->OpenLinkInNewTab;
        
        // Answer Slide Object:
        
        return $slide;
    }
}
