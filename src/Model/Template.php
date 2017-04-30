<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Model
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */

namespace SilverWare\Model;

use SilverStripe\View\ArrayData;
use SilverWare\Components\PageComponent;
use SilverWare\Folders\TemplateFolder;
use SilverWare\Model\Layout;
use SilverWare\Sections\LayoutSection;

/**
 * An extension of the section holder class for a template.
 *
 * @package SilverWare\Model
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware
 */
class Template extends SectionHolder
{
    /**
     * Human-readable singular name.
     *
     * @var string
     * @config
     */
    private static $singular_name = 'Template';
    
    /**
     * Human-readable plural name.
     *
     * @var string
     * @config
     */
    private static $plural_name = 'Templates';
    
    /**
     * Description of this object.
     *
     * @var string
     * @config
     */
    private static $description = 'An individual SilverWare template';
    
    /**
     * Icon file for this object.
     *
     * @var string
     * @config
     */
    private static $icon = 'silverware/admin/client/dist/images/icons/Template.png';
    
    /**
     * Defines an ancestor class to hide from the admin interface.
     *
     * @var string
     * @config
     */
    private static $hide_ancestor = SectionHolder::class;
    
    /**
     * Defines the allowed parents for this object.
     *
     * @var array
     * @config
     */
    private static $allowed_parents = [
        TemplateFolder::class
    ];
    
    /**
     * Renders the component for the HTML template.
     *
     * @param string $layout Page layout passed from template.
     * @param string $title Page title passed from template.
     *
     * @return DBHTMLText|string
     */
    public function renderSelf($layout = null, $title = null)
    {
        // Verify Existence of Layout Section + Layout:
        
        if (!$this->hasEnabledChild(LayoutSection::class)) {
            return $this->renderNotFound($layout, $title, LayoutSection::singleton()->i18n_singular_name());
        } elseif (!$this->getChild(LayoutSection::class)->hasPageLayout()) {
            return $this->renderNotFound($layout, $title, Layout::singleton()->i18n_singular_name());
        }
        
        // Verify Existence of Page Component:
        
        if (!$this->hasEnabledDescendant(PageComponent::class)) {
            return $this->renderNotFound($layout, $title, PageComponent::singleton()->i18n_singular_name());
        }
        
        // Render Template Sections:
        
        return $this->renderChildren($layout, $title);
    }
    
    /**
     * Renders a 'not found' error for the HTML template.
     *
     * @param string $layout Page layout passed from template.
     * @param string $title Page title passed from template.
     * @param string $type Type of item which was not found.
     *
     * @return DBHTMLText
     */
    public function renderNotFound($layout, $title, $type)
    {
        return ArrayData::create([
            'Type' => $type,
            'Title' => $title,
            'Layout' => $layout
        ])->renderWith('Error\Includes\NotFound');
    }
}
