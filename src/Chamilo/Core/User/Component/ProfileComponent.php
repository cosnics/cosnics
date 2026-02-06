<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Manager;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\LinkTab;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\TabsCollection;
use Chamilo\Libraries\UserInterface\Tab\Service\LinkTabsRenderer;

/**
 * @package Chamilo\Core\User\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class ProfileComponent extends Manager
{
    /**
     * @return \Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\LinkTab[]
     */
    public function getAvailableTabs(): array
    {
        $action = $this->getRequest()->query->get(self::PARAM_ACTION);
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
                self::ACTION_CHANGE_PICTURE,
                htmlentities($translator->trans(self::ACTION_CHANGE_PICTURE . 'Title', [], Manager::CONTEXT)),
                new FontAwesomeGlyph('image', ['fa-lg'], null, 'fas'), $this->getUrlGenerator()->fromParameters(
                [self::PARAM_CONTEXT => Manager::CONTEXT, self::PARAM_ACTION => self::ACTION_CHANGE_PICTURE]
            ), self::ACTION_CHANGE_PICTURE == $action
            );
        }

        $tabs[] = new LinkTab(
            self::ACTION_SETTINGS,
            htmlentities($translator->trans(self::ACTION_SETTINGS . 'Title', [], Manager::CONTEXT)),
            new FontAwesomeGlyph('cog', ['fa-lg'], null, 'fas'), $this->getUrlGenerator()->fromParameters(
            [self::PARAM_CONTEXT => Manager::CONTEXT, self::PARAM_ACTION => self::ACTION_SETTINGS]
        ), self::ACTION_SETTINGS == $action
        );

        return $tabs;
    }

    abstract public function getContent(): string;

    public function getLinkTabsRenderer(): LinkTabsRenderer
    {
        return $this->getService(LinkTabsRenderer::class);
    }

    public function renderHeader(string $pageTitle = ''): string
    {
        $availableTabs = $this->getAvailableTabs();

        $html = [];

        $html[] = parent::renderHeader($pageTitle);

        if (count($availableTabs) > 1) {
            $tabs = new TabsCollection();

            foreach ($availableTabs as $availableTab) {
                $tabs->add($availableTab);
            }

            $html[] = $this->getLinkTabsRenderer()->render($tabs, $this->getContent());
        }
        else {
            $html[] = $this->getContent();
        }

        return implode(PHP_EOL, $html);
    }

    public function renderPage(): string
    {
        $html = [];

        $html[] = $this->renderHeader();
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }
}
