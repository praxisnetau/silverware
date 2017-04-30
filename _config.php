<?php

/**
 * SilverWare configuration file.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

// Define Module Constants:

if (!defined('SILVERWARE_DIR')) {
    define('SILVERWARE_DIR', basename(__DIR__));
}

if (!defined('SILVERWARE_PATH')) {
    define('SILVERWARE_PATH', realpath(__DIR__));
}
