<?php
namespace Chamilo\Application\Survey\Rights\Component;

use Chamilo\Application\Survey\Rights\Manager;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTab;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package Chamilo\Application\Survey\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class TabComponent extends Manager implements DelegateComponent
{

    /**
     *
     * @var \Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer
     */
    private $tabsRenderer;

    public function run()
    {
        $this->tabsRenderer = new DynamicVisualTabsRenderer('learning_path');
        
        $this->tabsRenderer->add_tab(
            new DynamicVisualTab(
                self::ACTION_CREATE, 
                Translation::get(self::ACTION_CREATE . 'Component'), 
                Theme::getInstance()->getImagePath(Manager::package(), 'Tab/' . self::ACTION_CREATE), 
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_CREATE)), 
                $this->get_action() == self::ACTION_CREATE, 
                false, 
                DynamicVisualTab::POSITION_LEFT, 
                DynamicVisualTab::DISPLAY_BOTH_SELECTED));
        
        $this->tabsRenderer->add_tab(
            new DynamicVisualTab(
                self::ACTION_BROWSE, 
                Translation::get(self::ACTION_BROWSE . 'Component'), 
                Theme::getInstance()->getImagePath(Manager::package(), 'Tab/' . self::ACTION_BROWSE), 
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_BROWSE)), 
                $this->get_action() == self::ACTION_BROWSE, 
                false, 
                DynamicVisualTab::POSITION_LEFT, 
                DynamicVisualTab::DISPLAY_BOTH_SELECTED));
        
        if ($this->get_action() == self::ACTION_UPDATE)
        {
            $this->tabsRenderer->add_tab(
                new DynamicVisualTab(
                    self::ACTION_UPDATE, 
                    Translation::get(self::ACTION_UPDATE . 'Component'), 
                    Theme::getInstance()->getImagePath(Manager::package(), 'Tab/' . self::ACTION_UPDATE), 
                    $this->get_url(array(self::PARAM_ACTION => self::ACTION_UPDATE)), 
                    $this->get_action() == self::ACTION_UPDATE, 
                    false, 
                    DynamicVisualTab::POSITION_LEFT, 
                    DynamicVisualTab::DISPLAY_BOTH_SELECTED));
        }
        
        return $this->build();
    }

    abstract function build();

    /**
     *
     * @see \Chamilo\Libraries\Architecture\Application\Application::render_header()
     */
    public function render_header()
    {
        $html = array();
        
        $html[] = parent::render_header();
        $html[] = $this->getTabsRenderer()->renderHeader();
        
        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @see \Chamilo\Libraries\Architecture\Application\Application::render_footer()
     */
    public function render_footer()
    {
        $html = array();
        
        $html[] = $this->getTabsRenderer()->renderFooter();
        $html[] = parent::render_footer();
        
        return implode(PHP_EOL, $html);
    }

    /**
     * Get the TabsRenderer
     * 
     * @return \Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer
     */
    public function getTabsRenderer()
    {
        return $this->tabsRenderer;
    }
}