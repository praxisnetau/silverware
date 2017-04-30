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

use SilverStripe\Forms\TextField;
use SilverStripe\ORM\Connect\MySQLDatabase;
use SilverStripe\ORM\DB;
use SilverStripe\ORM\FieldType\DBField;

/**
 * A database field used to store an absolute integer (which may also be null).
 *
 * @package SilverWare\ORM\FieldType
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class DBAbsoluteInt extends DBField
{
    /**
     * Adds the field to the underlying database.
     *
     * @return void
     */
    public function requireField()
    {
        // Obtain Charset and Collation:
        
        $charset   = MySQLDatabase::config()->charset;
        $collation = MySQLDatabase::config()->collation;
        
        // Define Field Specification:
        
        $spec = [
            'type' => 'varchar',
            'parts' => [
                'datatype' => 'varchar',
                'precision' => 11,
                'collate' => $collation,
                'character set' => $charset,
                'arrayValue' => $this->arrayValue
            ]
        ];
        
        // Require Database Field:
        
        DB::require_field($this->tableName, $this->name, $spec);
    }
    
    /**
     * Answers the value as a formatted number string.
     *
     * @return string
     */
    public function Formatted()
    {
        return number_format($this->value);
    }
    
    /**
     * Answers the value as a 'nice' number string.
     *
     * @return string
     */
    public function Nice()
    {
        return sprintf('%d', $this->value);
    }
    
    /**
     * Answers a form field instance for automatic form scaffolding.
     *
     * @param string $title Title of the field instance.
     * @param array $params Array of extra parameters.
     *
     * @return TextField
     */
    public function scaffoldFormField($title = null, $params = null)
    {
        return TextField::create($this->name, $title);
    }
    
    /**
     * Answers true if the field value is not considered to be a 'null' value.
     *
     * @return boolean
     */
    public function exists()
    {
        return is_numeric($this->value);
    }
    
    /**
     * Prepares the specified value to be stored within the database.
     *
     * @param string $value
     *
     * @return integer
     */
    public function prepValueForDB($value)
    {
        return (int) abs($value);
    }
}
