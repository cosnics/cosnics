<?php
namespace Chamilo\Core\Repository\Workspace\Component;

use Chamilo\Core\Repository\Workspace\Manager;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTab;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class TabComponent extends Manager
{

    /**
     *
     * @var \Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer
     */
    private $tabsRenderer;

    public function run()
    {
        $this->tabsRenderer = new DynamicVisualTabsRenderer('workspace');

        $this->tabsRenderer->add_tab(
            new DynamicVisualTab(
                self::ACTION_BROWSE_PERSONAL,
                Translation::get(self::ACTION_BROWSE_PERSONAL . 'Component', null, 'Chamilo\Core\Repository\Workspace'),
                Theme::getInstance()->getImagePath(Manager::package(), 'Tab/' . self::ACTION_BROWSE_PERSONAL),
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_BROWSE_PERSONAL)),
                $this->get_action() == self::ACTION_BROWSE_PERSONAL,
                false,
                DynamicVisualTab::POSITION_LEFT,
                DynamicVisualTab::DISPLAY_BOTH));

        $this->tabsRenderer->add_tab(
            new DynamicVisualTab(
                self::ACTION_BROWSE_SHARED,
                Translation::get(self::ACTION_BROWSE_SHARED . 'Component'),
                Theme::getInstance()->getImagePath(Manager::package(), 'Tab/' . self::ACTION_BROWSE_SHARED),
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_BROWSE_SHARED)),
                $this->get_action() == self::ACTION_BROWSE_SHARED,
                false,
                DynamicVisualTab::POSITION_LEFT,
                DynamicVisualTab::DISPLAY_BOTH));

        $this->tabsRenderer->add_tab(
            new DynamicVisualTab(
                self::ACTION_FAVOURITE,
                Translation::get(self::ACTION_FAVOURITE . 'Component'),
                Theme::getInstance()->getImagePath(Manager::package(), 'Tab/' . self::ACTION_FAVOURITE),
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_FAVOURITE)),
                $this->get_action() == self::ACTION_FAVOURITE,
                false,
                DynamicVisualTab::POSITION_LEFT,
                DynamicVisualTab::DISPLAY_BOTH));

        if ($this->get_user()->is_platform_admin())
        {
            $this->tabsRenderer->add_tab(
                new DynamicVisualTab(
                    self::ACTION_BROWSE,
                    Translation::get(self::ACTION_BROWSE . 'Component'),
                    Theme::getInstance()->getImagePath(Manager::package(), 'Tab/' . self::ACTION_BROWSE),
                    $this->get_url(array(self::PARAM_ACTION => self::ACTION_BROWSE)),
                    $this->get_action() == self::ACTION_BROWSE,
                    false,
                    DynamicVisualTab::POSITION_LEFT,
                    DynamicVisualTab::DISPLAY_BOTH));
        }

        $this->tabsRenderer->add_tab(
            new DynamicVisualTab(
                self::ACTION_CREATE,
                Translation::get(self::ACTION_CREATE . 'Component'),
                Theme::getInstance()->getImagePath(Manager::package(), 'Tab/' . self::ACTION_CREATE),
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_CREATE)),
                $this->get_action() == self::ACTION_CREATE,
                false,
                DynamicVisualTab::POSITION_LEFT,
                DynamicVisualTab::DISPLAY_BOTH));

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
                    DynamicVisualTab::POSITION_RIGHT,
                    DynamicVisualTab::DISPLAY_BOTH));
        }

        if ($this->get_action() == self::ACTION_RIGHTS)
        {
            if ($this->getRequest()->get(\Chamilo\Core\Repository\Workspace\Rights\Manager::PARAM_ACTION) ==
                 \Chamilo\Core\Repository\Workspace\Rights\Manager::ACTION_CREATE)
            {
                $icon = Theme::getInstance()->getImagePath(Manager::package(), 'Tab/RightsCreate');
                $translation = Translation::get('CreateRightsComponent');
            }
            else
            {
                $icon = Theme::getInstance()->getImagePath(Manager::package(), 'Tab/Rights');
                $translation = Translation::get('RightsComponent');
            }

            $this->tabsRenderer->add_tab(
                new DynamicVisualTab(
                    self::ACTION_RIGHTS,
                    $translation,
                    $icon,
                    $this->get_url(array(self::ACTION_RIGHTS => self::ACTION_RIGHTS)),
                    $this->get_action() == self::ACTION_RIGHTS,
                    false,
                    DynamicVisualTab::POSITION_RIGHT,
                    DynamicVisualTab::DISPLAY_BOTH));
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