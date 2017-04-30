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

use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\DB;
use SilverStripe\View\Parsers\URLSegmentFilter;
use SilverWare\Tools\ClassTools;

/**
 * A data extension class which adds a URL segment to the extended object.
 *
 * @package SilverWare\Extensions\Model
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class URLSegmentExtension extends DataExtension
{
    /**
     * Maps field names to field types for the extended object.
     *
     * @var array
     * @config
     */
    private static $db = [
        'URLSegment' => 'Varchar(255)'
    ];
    
    /**
     * Defines the database indexes for the extended object.
     *
     * @var array
     * @config
     */
    private static $indexes = [
        'URLSegment' => true
    ];
    
    /**
     * Event method called before the receiver is written to the database.
     */
    public function onBeforeWrite()
    {
        // Generate Valid URL Segment:
        
        $this->generateValidURLSegment($this->getURLSegmentSource());
    }
    
    /**
     * Creates any required default records (if they do not already exist).
     *
     * @return void
     */
    public function requireDefaultRecords()
    {
        $class = $this->owner->class;
        
        $records = DataList::create($class)->where('URLSegment IS NULL');
        
        if ($records->exists()) {
            
            foreach ($records as $record) {
                $record->write();
            }
            
            DB::alteration_message(sprintf('Updated %s records without URL segments', $class), 'changed');
        }
    }
    
    /**
     * Answers true if the extended object has a valid URL segment.
     *
     * @return boolean
     */
    public function validURLSegment()
    {
        $list = DataList::create($this->owner->class)->filter(['URLSegment' => $this->owner->URLSegment]);
        
        if ($id = $this->owner->ID) {
            $list = $list->exclude(['ID' => $id]);
        }
        
        return !$list->exists();
    }
    
    /**
     * Generates a URL segment for the extended object based on the given string.
     *
     * @param string $string
     *
     * @return string
     */
    public function generateURLSegment($string)
    {
        if (!$string) {
            $class = ClassTools::singleton()->getClassWithoutNamespace($this->owner->class);
            $string = sprintf('%s-%s', $class, $this->owner->ID);
        }
        
        $segment = URLSegmentFilter::create()->filter($string);
        
        $this->owner->extend('updateURLSegment', $segment, $string);
        
        return $segment;
    }
    
    /**
     * Generates a valid URL segment for the extended object based on the given string.
     *
     * @param string $string
     *
     * @return string
     */
    public function generateValidURLSegment($string)
    {
        // Generate URL Segment:
        
        $this->owner->URLSegment = $this->generateURLSegment($string);
        
        // Check for Duplicates:
        
        $count = 2;
        
        while (!$this->validURLSegment()) {
            
            $this->owner->URLSegment = sprintf(
                '%s-%d',
                preg_replace('/-[0-9]+$/', '', $this->owner->URLSegment),
                $count
            );
            
            $count++;
            
        }
    }
    
    /**
     * Answers the source string for generating the URL segment.
     *
     * @return string
     */
    public function getURLSegmentSource()
    {
        return $this->owner->Title;
    }
}
