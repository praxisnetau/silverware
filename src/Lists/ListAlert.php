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

use SilverStripe\ORM\ArrayList;
use SilverStripe\View\ArrayData;

/**
 * Allows an object to add alert messages to a list component.
 *
 * @package SilverWare\Lists
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
trait ListAlert
{
    /**
     * Defines the alerts to be added to the list source.
     *
     * @var array
     */
    protected $listAlerts = [];
    
    /**
     * Defines the value of the listAlerts attribute.
     *
     * @param array $listAlerts
     *
     * @return $this
     */
    public function setListAlerts($listAlerts)
    {
        $this->listAlerts = (array) $listAlerts;
        
        return $this;
    }
    
    /**
     * Answers the value of the listAlerts attribute.
     *
     * @return array
     */
    public function getListAlerts()
    {
        return $this->listAlerts;
    }
    
    /**
     * Answers an array list of alerts for the template.
     *
     * @return ArrayList
     */
    public function getListAlertsData()
    {
        $alerts = ArrayList::create();
        
        foreach ($this->getListAlerts() as $alert) {
            $alerts->push(ArrayData::create($alert));
        }
        
        return $alerts;
    }
    
    /**
     * Adds an alert with the given details to the array of alerts.
     *
     * @param string $text
     * @param string $type
     * @param string $icon
     *
     * @return $this
     */
    public function addListAlert($text, $type = 'primary', $icon = 'info-circle')
    {
        $this->listAlerts[] = [
            'Text' => $text,
            'Type' => $type,
            'Icon' => $icon
        ];
        
        return $this;
    }
    
    /**
     * Answers true if the receiver has at least one list alert.
     *
     * @return boolean
     */
    public function hasListAlerts()
    {
        return !empty($this->listAlerts);
    }
}
