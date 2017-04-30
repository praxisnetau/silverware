<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\ORM\FieldType
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\ORM\FieldType;

use SilverStripe\ORM\ArrayLib;
use SilverStripe\ORM\FieldType\DBComposite;
use SilverWare\Forms\ViewportsField;

/**
 * A composite database field used to store different values for particular device viewports.
 *
 * @package SilverWare\ORM\FieldType
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class DBViewports extends DBComposite
{
    /**
     * Define constants.
     */
    const VIEWPORT_TINY   = 'Tiny';
    const VIEWPORT_SMALL  = 'Small';
    const VIEWPORT_MEDIUM = 'Medium';
    const VIEWPORT_LARGE  = 'Large';
    const VIEWPORT_HUGE   = 'Huge';
    
    /**
     * Maps composite field names to field types for this object.
     *
     * @var array
     * @config
     */
    private static $composite_db = [
        'Tiny' => 'Varchar(128)',
        'Small' => 'Varchar(128)',
        'Medium' => 'Varchar(128)',
        'Large' => 'Varchar(128)',
        'Huge' => 'Varchar(128)'
    ];
    
    /**
     * Answers a form field instance for automatic form scaffolding.
     *
     * @param string $title Title of the field instance.
     * @param array $params Array of extra parameters.
     *
     * @return ViewportsField
     */
    public function scaffoldFormField($title = null, $params = null)
    {
        return ViewportsField::create($this->name, $title);
    }
    
    /**
     * Answers true if every viewport field is empty.
     *
     * @return boolean
     */
    public function allEmpty()
    {
        return $this->allEqualTo(false);
    }
    
    /**
     * Answers true if every viewport field is equal to the given value.
     *
     * @param mixed $value Value to test.
     *
     * @return boolean
     */
    public function allEqualTo($value)
    {
        foreach ($this->getViewports() as $viewport) {
            
            if ($this->getField($viewport) != $value) {
                return false;
            }
            
        }
        
        return true;
    }
    
    /**
     * Answers an array of the viewport field names.
     *
     * @return array
     */
    public function getViewports()
    {
        return array_keys($this->compositeDatabaseFields());
    }
    
    /**
     * Answers the label for the specified viewport.
     *
     * @param string $viewport
     *
     * @return string
     */
    public function getViewportLabel($viewport)
    {
        switch ($viewport) {
            case self::VIEWPORT_TINY:
                return _t(__CLASS__ . '.TINY', 'Tiny');
            case self::VIEWPORT_SMALL:
                return _t(__CLASS__ . '.SMALL', 'Small');
            case self::VIEWPORT_MEDIUM:
                return _t(__CLASS__ . '.MEDIUM', 'Medium');
            case self::VIEWPORT_LARGE:
                return _t(__CLASS__ . '.LARGE', 'Large');
            case self::VIEWPORT_HUGE:
                return _t(__CLASS__ . '.HUGE', 'Huge');
        }
    }
    
    /**
     * Answers an array of viewport options for a form field.
     *
     * @return array
     */
    public function getViewportOptions()
    {
        $options = [];
        
        foreach ($this->getViewports() as $viewport) {
            $options[strtolower($viewport)] = $this->getViewportLabel($viewport);
        }
        
        return $options;
    }
}
