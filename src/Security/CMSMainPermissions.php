<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Security
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\Security;

use SilverStripe\Security\Permission;

/**
 * Enables CRUD operations on the object if the member can access CMSMain.
 *
 * @package SilverWare\Security
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
trait CMSMainPermissions
{
    /**
     * Answers true if the member can create a new instance of the receiver.
     *
     * @param Member $member Optional member object.
     * @param array $context Context-specific data.
     *
     * @return boolean
     */
    public function canCreate($member = null, $context = [])
    {
        return Permission::check('CMS_ACCESS_CMSMain', 'any', $member);
    }
    
    /**
     * Answers true if the member can delete the receiver.
     *
     * @param Member $member
     *
     * @return boolean
     */
    public function canDelete($member = null)
    {
        return Permission::check('CMS_ACCESS_CMSMain', 'any', $member);
    }
    
    /**
     * Answers true if the member can edit the receiver.
     *
     * @param Member $member
     *
     * @return boolean
     */
    public function canEdit($member = null)
    {
        return Permission::check('CMS_ACCESS_CMSMain', 'any', $member);
    }
    
    /**
     * Answers true if the member can view the receiver.
     *
     * @param Member $member
     *
     * @return boolean
     */
    public function canView($member = null)
    {
        return Permission::check('CMS_ACCESS_CMSMain', 'any', $member);
    }
}
