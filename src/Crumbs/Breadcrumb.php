<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Crumbs
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\Crumbs;

use SilverStripe\Control\Controller;
use SilverStripe\View\ViewableData;

/**
 * An extension of the viewable data class for a breadcrumb.
 *
 * @package SilverWare\Crumbs
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class Breadcrumb extends ViewableData implements Crumbable
{
    /**
     * The link for the breadcrumb.
     *
     * @var string
     */
    protected $link;
    
    /**
     * The title for the breadcrumb.
     *
     * @var string
     */
    protected $title;
    
    /**
     * Constructs the object upon instantiation.
     *
     * @param string $link
     * @param string $title
     */
    public function __construct($link = null, $title = null)
    {
        // Construct Parent:
        
        parent::__construct();
        
        // Construct Object:
        
        $this->setLink($link);
        $this->setTitle($title);
    }
    
    /**
     * Defines the link of the receiver.
     *
     * @param string $link
     *
     * @return $this
     */
    public function setLink($link)
    {
        $this->link = (string) $link;
        
        return $this;
    }
    
    /**
     * Defines the title of the receiver.
     *
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = (string) $title;
        
        return $this;
    }
    
    /**
     * Defines the link and title of the receiver.
     *
     * @param string $link
     * @param string $title
     *
     * @return $this
     */
    public function setItem($link, $title)
    {
        $this->setLink($link);
        $this->setTitle($title);
        
        return $this;
    }
    
    /**
     * Answers the menu title for the receiver.
     *
     * @return string
     */
    public function getMenuTitle()
    {
        return $this->title;
    }
    
    /**
     * Answers the link for the receiver.
     *
     * @param string $action
     *
     * @return string
     */
    public function getLink($action = null)
    {
        return Controller::join_links($this->link, $action);
    }
}
