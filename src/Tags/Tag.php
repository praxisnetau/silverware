<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Tags
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\Tags;

use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Control\Controller;
use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\TabSet;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\SS_List;
use SilverWare\Extensions\Model\URLSegmentExtension;
use SilverWare\Security\CMSMainPermissions;

/**
 * An extension of the data object class for a tag.
 *
 * @package SilverWare\Tags
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class Tag extends DataObject
{
    use CMSMainPermissions;
    
    /**
     * Human-readable singular name.
     *
     * @var string
     * @config
     */
    private static $singular_name = 'Tag';
    
    /**
     * Human-readable plural name.
     *
     * @var string
     * @config
     */
    private static $plural_name = 'Tags';
    
    /**
     * Defines the default sort field and order for this object.
     *
     * @var string
     * @config
     */
    private static $default_sort = 'Title';
    
    /**
     * Defines the table name to use for this object.
     *
     * @var string
     * @config
     */
    private static $table_name = 'SilverWare_Tag';
    
    /**
     * Maps field names to field types for this object.
     *
     * @var array
     * @config
     */
    private static $db = [
        'Title' => 'Varchar(255)'
    ];
    
    /**
     * Defines the summary fields of this object.
     *
     * @var array
     * @config
     */
    private static $summary_fields = [
        'Title'
    ];
    
    /**
     * Defines the extension classes to apply to this object.
     *
     * @var array
     * @config
     */
    private static $extensions = [
        URLSegmentExtension::class
    ];
    
    /**
     * Defines the name of the default source to obtain from the current controller.
     *
     * @var string
     * @config
     */
    private static $default_source;
    
    /**
     * The tag source object used for linking.
     *
     * @var SiteTree
     */
    protected $source;
    
    /**
     * Answers a list of tags for the given tag source and optional list of tagged items.
     *
     * @param TagSource $source The tag source object.
     * @param SS_List $items Optional list of tagged items for filtering tags.
     *
     * @return ArrayList
     */
    public static function forSource(TagSource $source, SS_List $items = null)
    {
        // Create Data List:
        
        $tags = DataList::create(static::class);
        
        // Filter Tagged Items:
        
        if ($items instanceof DataList) {
            
            if (!$items->exists()) {
                return ArrayList::create();
            }
            
            if ($belongs = Config::inst()->get(static::class, 'belongs_many_many')) {
                
                // Filter Tags Associated with Items:
                
                if ($component = array_search($items->dataClass(), $belongs)) {
                    $tags = $tags->filter(sprintf('%s.ID', $component), $items->column('ID'));
                }
                
            }
            
        }
        
        // Convert List to Array:
        
        $tags = $tags->toArray();
        
        // Associate Tags with Source:
        
        foreach ($tags as $tag) {
            $tag->setSource($source);
        }
        
        // Answer Array List:
        
        return ArrayList::create($tags);
    }
    
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
        
        // Answer Field Labels:
        
        return $labels;
    }
    
    /**
     * Defines the value of the source attribute.
     *
     * @param SiteTree $source
     *
     * @return $this
     */
    public function setSource(SiteTree $source)
    {
        $this->source = $source;
        
        return $this;
    }
    
    /**
     * Answers the value of the source attribute.
     *
     * @return SiteTree
     */
    public function getSource()
    {
        return $this->source;
    }
    
    /**
     * Answers a list of the records tagged by the receiver.
     *
     * @return ManyManyList
     */
    public function getTagged()
    {
        foreach ($this->manyMany() as $name => $class) {
            
            if (self::getSchema()->manyManyComponent(static::class, $name)['parentClass'] === static::class) {
                return $this->getManyManyComponents($name);
            }
            
        }
    }
    
    /**
     * Answers the weight of the tag as an integer.
     *
     * @return integer
     */
    public function getWeight()
    {
        if ($tagged = $this->getTagged()) {
            return $tagged->count();
        }
        
        return 0;
    }
    
    /**
     * Answers the default source object for the tag.
     *
     * @return SiteTree
     */
    public function getDefaultSource()
    {
        if (($name = $this->config()->default_source) && $source = Controller::curr()->{$name}) {
            return $source;
        }
    }
    
    /**
     * Answers the link for the tag.
     *
     * @return string
     */
    public function getLink()
    {
        if ($source = $this->getSource()) {
            return $source->Link($this->TagAction);
        }
        
        if ($source = $this->getDefaultSource()) {
            return $source->Link($this->TagAction);
        }
        
        return $this->TagAction;
    }
    
    /**
     * Answers the action for the tag.
     *
     * @return string
     */
    public function getTagAction()
    {
        return sprintf('tag/%s', $this->URLSegment);
    }
}
