<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Manager;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\LinkTab;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\TabsCollection;
use Chamilo\Libraries\UserInterface\Tab\Service\TabsRenderer;

/**
 * @package Chamilo\Core\User\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class ProfileComponent extends Manager
{
    protected function getAction(): string
    {
        return $this->getRequest()->query->get(self::PARAM_ACTION);
    }

    /**
     * @return \Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\LinkTab[]
     */
    public function getAvailableTabs(): array
    {
        $action = $this->getAction();
        $translator = $this->getTranslator();
        $tabs = [];

        $tabs[] = new LinkTab(
            self::ACTION_ACCOUNT,
            htmlentities($translator->trans(self::ACTION_ACCOUNT . 'Title', [], Manager::CONTEXT)),
            new FontAwesomeGlyph('user', ['fa-lg'], null, 'fas'), $this->getUrlGenerator()->fromParameters(
            [self::PARAM_CONTEXT => Manager::CONTEXT, self::PARAM_ACTION => self::ACTION_ACCOUNT]
        ), self::ACTION_ACCOUNT == $action
        );

        if ($this->getContainer()->getParameter('cosnics.application.user.rights.changeUserPicture')) {
            $tabs[] = new LinkTab(
                self::ACTION_UPDATE_USER_PICTURE,
                htmlentities($translator->trans(self::ACTION_UPDATE_USER_PICTURE . 'Title', [], Manager::CONTEXT)),
                new FontAwesomeGlyph('image', ['fa-lg'], null, 'fas'), $this->getUrlGenerator()->fromParameters(
                [self::PARAM_CONTEXT => Manager::CONTEXT, self::PARAM_ACTION => self::ACTION_UPDATE_USER_PICTURE]
            ), self::ACTION_UPDATE_USER_PICTURE == $action
            );
        }

        $tabs[] = new LinkTab(
            self::ACTION_CONFIGURE,
            htmlentities($translator->trans(self::ACTION_CONFIGURE . 'Title', [], Manager::CONTEXT)),
            new FontAwesomeGlyph('cog', ['fa-lg'], null, 'fas'), $this->getUrlGenerator()->fromParameters(
            [self::PARAM_CONTEXT => Manager::CONTEXT, self::PARAM_ACTION => self::ACTION_CONFIGURE]
        ), self::ACTION_CONFIGURE == $action
        );

        return $tabs;
    }

    abstract public function getContent(): string;

    public function getTabsRenderer(): TabsRenderer
    {
        return $this->getService(TabsRenderer::class);
    }

    public function renderPage(): string
    {
        $html = [];

        $html[] = $this->renderHeader();

        $availableTabs = $this->getAvailableTabs();

        if (count($availableTabs) > 1) {
            $tabs = new TabsCollection();

            foreach ($availableTabs as $availableTab) {
                $tabs->add($availableTab);
            }

            $html[] = $this->getTabsRenderer()->renderNavigation('profile', $tabs, $this->getAction());
        }

        $html[] = $this->getContent();
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }
}
