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

use SilverStripe\ORM\FieldType\DBComposite;
use SilverWare\Forms\DimensionsField;

/**
 * A composite database field used to store dimensions (typically for an image).
 *
 * @package SilverWare\ORM\FieldType
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class DBDimensions extends DBComposite
{
    /**
     * Define constants.
     */
    const DIM_WIDTH  = 'Width';
    const DIM_HEIGHT = 'Height';
    
    /**
     * Maps composite field names to field types for this object.
     *
     * @var array
     * @config
     */
    private static $composite_db = [
        'Width' => 'Varchar(8)',
        'Height' => 'Varchar(8)'
    ];
    
    /**
     * Answers a form field instance for automatic form scaffolding.
     *
     * @param string $title Title of the field instance.
     * @param array $params Array of extra parameters.
     *
     * @return DimensionsField
     */
    public function scaffoldFormField($title = null, $params = null)
    {
        return DimensionsField::create($this->name, $title);
    }
    
    /**
     * Answers an array of the dimension field names.
     *
     * @return array
     */
    public function getDimensions()
    {
        return array_keys($this->compositeDatabaseFields());
    }
    
    /**
     * Answers the label for the specified dimension.
     *
     * @param string $dim
     *
     * @return string
     */
    public function getDimensionLabel($dim)
    {
        switch ($dim) {
            case self::DIM_WIDTH:
                return _t(__CLASS__ . '.WIDTH', 'Width');
            case self::DIM_HEIGHT:
                return _t(__CLASS__ . '.HEIGHT', 'Height');
        }
    }
}
