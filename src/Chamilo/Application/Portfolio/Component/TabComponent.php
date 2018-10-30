<?php
namespace Chamilo\Application\Portfolio\Component;

use Chamilo\Application\Portfolio\Manager;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTab;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer;
use Chamilo\Libraries\Format\Theme;

/**
 * Abstract component for the several portfolio browsers (all, favourite, ..)
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class TabComponent extends Manager
{

    /**
     *
     * @var \Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer
     */
    private $tabsRenderer;

    /**
     * Runs this component
     *
     * @return string
     */
    public function run()
    {
        $this->tabsRenderer = new DynamicVisualTabsRenderer('workspace');

        $this->tabsRenderer->add_tab(
            new DynamicVisualTab(
                self::ACTION_BROWSE,
                $this->getTranslator()->trans(self::ACTION_BROWSE . 'Component', [], Manager::context()),
                Theme::getInstance()->getImagePath(Manager::package(), 'Tab/' . self::ACTION_BROWSE),
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_BROWSE)),
                $this->get_action() == self::ACTION_BROWSE,
                false,
                DynamicVisualTab::POSITION_LEFT,
                DynamicVisualTab::DISPLAY_BOTH));

        $this->tabsRenderer->add_tab(
            new DynamicVisualTab(
                self::ACTION_BROWSE_FAVOURITES,
                $this->getTranslator()->trans(self::ACTION_BROWSE_FAVOURITES . 'Component', [], Manager::context()),
                Theme::getInstance()->getImagePath(Manager::package(), 'Tab/' . self::ACTION_BROWSE_FAVOURITES),
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_BROWSE_FAVOURITES)),
                $this->get_action() == self::ACTION_BROWSE_FAVOURITES,
                false,
                DynamicVisualTab::POSITION_LEFT,
                DynamicVisualTab::DISPLAY_BOTH));

        return $this->build();
    }

    /**
     * Runs the subcomponent
     *
     * @return string
     */
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