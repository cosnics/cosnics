<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Architecture\Enum\ActionEnum;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
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
    /**
     * @return \Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\LinkTab[]
     */
    public function getAvailableTabs(): array
    {
        $action = $this->getCurrentAction();
        $translator = $this->getTranslator();
        $tabs = [];

        $tabs[] = new LinkTab(
            ActionEnum::ACCOUNT->value,
            htmlentities($translator->trans(ActionEnum::ACCOUNT->value . 'Title', [], Manager::CONTEXT)),
            new FontAwesomeGlyph('user', ['fa-lg'], null, 'fas'), $this->getUrlGenerator()->fromParameters(
            [self::PARAM_CONTEXT => Manager::CONTEXT, self::PARAM_ACTION => ActionEnum::ACCOUNT->value]
        ), ActionEnum::ACCOUNT->value == $action
        );

        if ($this->getContainer()->getParameter('cosnics.application.user.rights.changeUserPicture')) {
            $tabs[] = new LinkTab(
                ActionEnum::UPDATE_USER_PICTURE->value,
                htmlentities($translator->trans(ActionEnum::UPDATE_USER_PICTURE->value . 'Title', [], Manager::CONTEXT)
                ), new FontAwesomeGlyph('image', ['fa-lg'], null, 'fas'), $this->getUrlGenerator()->fromParameters(
                [self::PARAM_CONTEXT => Manager::CONTEXT, self::PARAM_ACTION => ActionEnum::UPDATE_USER_PICTURE->value]
            ), ActionEnum::UPDATE_USER_PICTURE->value == $action
            );
        }

        $tabs[] = new LinkTab(
            ActionEnum::CONFIGURE->value,
            htmlentities($translator->trans(ActionEnum::CONFIGURE->value . 'Title', [], Manager::CONTEXT)),
            new FontAwesomeGlyph('cog', ['fa-lg'], null, 'fas'), $this->getUrlGenerator()->fromParameters(
            [self::PARAM_CONTEXT => Manager::CONTEXT, self::PARAM_ACTION => ActionEnum::CONFIGURE->value]
        ), ActionEnum::CONFIGURE->value == $action
        );

        return $tabs;
    }

    abstract public function getContent(User $user): string;

    public function getTabsRenderer(): TabsRenderer
    {
        return $this->getService(TabsRenderer::class);
    }

    public function renderPage(?User $user = null): string
    {
        $html = [];

        $html[] = $this->renderHeader($user);

        $availableTabs = $this->getAvailableTabs();

        if (count($availableTabs) > 1) {
            $tabs = new TabsCollection();

            foreach ($availableTabs as $availableTab) {
                $tabs->add($availableTab);
            }

            $html[] = $this->getTabsRenderer()->renderNavigation('profile', $tabs, $this->getCurrentAction());
        }

        $html[] = $this->getContent($user);
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }
}
