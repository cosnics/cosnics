<?php
namespace Chamilo\Core\Repository\Workspace\Component;

use Chamilo\Core\Repository\Workspace\Manager;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Tabs\Link\LinkTab;
use Chamilo\Libraries\Format\Tabs\Link\LinkTabsRenderer;
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
     * @var \Chamilo\Libraries\Format\Tabs\Link\LinkTabsRenderer
     */
    private $tabsRenderer;

    public function run()
    {
        $this->tabsRenderer = new LinkTabsRenderer('workspace');

        $this->tabsRenderer->addTab(
            new LinkTab(
                self::ACTION_BROWSE_PERSONAL,
                Translation::get(self::ACTION_BROWSE_PERSONAL . 'Component', null, 'Chamilo\Core\Repository\Workspace'),
                new FontAwesomeGlyph('user', array('fa-lg'), null, 'fas'),
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_BROWSE_PERSONAL)),
                $this->get_action() == self::ACTION_BROWSE_PERSONAL, false, LinkTab::POSITION_LEFT,
                LinkTab::DISPLAY_BOTH
            )
        );

        $this->tabsRenderer->addTab(
            new LinkTab(
                self::ACTION_BROWSE_SHARED, Translation::get(self::ACTION_BROWSE_SHARED . 'Component'),
                new FontAwesomeGlyph('users', array('fa-lg'), null, 'fas'),
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_BROWSE_SHARED)),
                $this->get_action() == self::ACTION_BROWSE_SHARED, false, LinkTab::POSITION_LEFT, LinkTab::DISPLAY_BOTH
            )
        );

        $this->tabsRenderer->addTab(
            new LinkTab(
                self::ACTION_FAVOURITE, Translation::get(self::ACTION_FAVOURITE . 'Component'),
                new FontAwesomeGlyph('star', array('fa-lg'), null, 'fas'),
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_FAVOURITE)),
                $this->get_action() == self::ACTION_FAVOURITE, false, LinkTab::POSITION_LEFT, LinkTab::DISPLAY_BOTH
            )
        );

        if ($this->getUser()->is_platform_admin())
        {
            $this->tabsRenderer->addTab(
                new LinkTab(
                    self::ACTION_BROWSE, Translation::get(self::ACTION_BROWSE . 'Component'),
                    new FontAwesomeGlyph('folder', array('fa-lg'), null, 'fas'),
                    $this->get_url(array(self::PARAM_ACTION => self::ACTION_BROWSE)),
                    $this->get_action() == self::ACTION_BROWSE, false, LinkTab::POSITION_LEFT, LinkTab::DISPLAY_BOTH
                )
            );
        }

        $this->tabsRenderer->addTab(
            new LinkTab(
                self::ACTION_CREATE, Translation::get(self::ACTION_CREATE . 'Component'),
                new FontAwesomeGlyph('plus', array('fa-lg'), null, 'fas'),
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_CREATE)),
                $this->get_action() == self::ACTION_CREATE, false, LinkTab::POSITION_LEFT, LinkTab::DISPLAY_BOTH
            )
        );

        if ($this->get_action() == self::ACTION_UPDATE)
        {
            $this->tabsRenderer->addTab(
                new LinkTab(
                    self::ACTION_UPDATE, Translation::get(self::ACTION_UPDATE . 'Component'),
                    new FontAwesomeGlyph('pencil-alt', array('fa-lg'), null, 'fas'),
                    $this->get_url(array(self::PARAM_ACTION => self::ACTION_UPDATE)),
                    $this->get_action() == self::ACTION_UPDATE, false, LinkTab::POSITION_RIGHT, LinkTab::DISPLAY_BOTH
                )
            );
        }

        if ($this->get_action() == self::ACTION_RIGHTS)
        {
            if ($this->getRequest()->get(\Chamilo\Core\Repository\Workspace\Rights\Manager::PARAM_ACTION) ==
                \Chamilo\Core\Repository\Workspace\Rights\Manager::ACTION_CREATE)
            {
                $icon = new FontAwesomeGlyph('key', array('fa-lg'), null, 'fas');
                $translation = Translation::get('CreateRightsComponent');
            }
            else
            {
                $icon = new FontAwesomeGlyph('lock', array('fa-lg'), null, 'fas');
                $translation = Translation::get('RightsComponent');
            }

            $this->tabsRenderer->addTab(
                new LinkTab(
                    self::ACTION_RIGHTS, $translation, $icon,
                    $this->get_url(array(self::ACTION_RIGHTS => self::ACTION_RIGHTS)),
                    $this->get_action() == self::ACTION_RIGHTS, false, LinkTab::POSITION_RIGHT, LinkTab::DISPLAY_BOTH
                )
            );
        }

        return $this->build();
    }

    abstract public function build();

    public function getLinkTabsRenderer(): LinkTabsRenderer
    {
        return $this->getService(LinkTabsRenderer::class);
    }

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