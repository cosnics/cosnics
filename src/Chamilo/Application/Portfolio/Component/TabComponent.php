<?php
namespace Chamilo\Application\Portfolio\Component;

use Chamilo\Application\Portfolio\Manager;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Tabs\Link\LinkTab;
use Chamilo\Libraries\Format\Tabs\Link\LinkTabsRenderer;

/**
 * Abstract component for the several portfolio browsers (all, favourite, ..)
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class TabComponent extends Manager
{

    /**
     *
     * @var \Chamilo\Libraries\Format\Tabs\Link\LinkTabsRenderer
     */
    private $tabsRenderer;

    /**
     * Runs this component
     *
     * @return string
     */
    public function run()
    {
        $this->tabsRenderer = new LinkTabsRenderer('workspace');

        $this->tabsRenderer->addTab(
            new LinkTab(
                self::ACTION_BROWSE,
                $this->getTranslator()->trans(self::ACTION_BROWSE . 'Component', [], Manager::context()),
                new FontAwesomeGlyph('search', array('fa-lg'), null, 'fas'),
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_BROWSE)),
                $this->get_action() == self::ACTION_BROWSE, false, LinkTab::POSITION_LEFT, LinkTab::DISPLAY_BOTH
            )
        );

        $this->tabsRenderer->addTab(
            new LinkTab(
                self::ACTION_BROWSE_FAVOURITES,
                $this->getTranslator()->trans(self::ACTION_BROWSE_FAVOURITES . 'Component', [], Manager::context()),
                new FontAwesomeGlyph('star', array('fa-lg'), null, 'fas'),
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_BROWSE_FAVOURITES)),
                $this->get_action() == self::ACTION_BROWSE_FAVOURITES, false, LinkTab::POSITION_LEFT,
                LinkTab::DISPLAY_BOTH
            )
        );

        return $this->build();
    }

    /**
     * Runs the subcomponent
     *
     * @return string
     */
    abstract public function build();

    /**
     * Get the TabsRenderer
     *
     * @return \Chamilo\Libraries\Format\Tabs\Link\LinkTabsRenderer
     */
    public function getTabsRenderer()
    {
        return $this->tabsRenderer;
    }

    /**
     *
     * @see \Chamilo\Libraries\Architecture\Application\Application::render_footer()
     */
    public function render_footer()
    {
        $html = [];

        $html[] = $this->getTabsRenderer()->renderFooter();
        $html[] = parent::render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @see \Chamilo\Libraries\Architecture\Application\Application::render_header()
     */
    public function render_header($pageTitle = '')
    {
        $html = [];

        $html[] = parent::render_header($pageTitle);
        $html[] = $this->getTabsRenderer()->renderHeader();

        return implode(PHP_EOL, $html);
    }
}