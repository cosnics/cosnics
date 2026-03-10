<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Architecture\Domain\UserDetailsRendererRegistry;
use Chamilo\Core\User\Architecture\Interface\UserDetailsRendererInterface;
use Chamilo\Core\User\Implementation\User\UserDetailsRenderer;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Enum\DisplayTypeEnum;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\Breadcrumb\Architecture\Domain\Breadcrumb;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\Button;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\ButtonGroup;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\ButtonToolBar;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Service\ButtonToolBarRenderer;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\ContentTab;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\TabsCollection;
use Chamilo\Libraries\UserInterface\Tab\Service\TabsRenderer;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Core\User\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ViewComponent extends Manager
{
    protected ButtonToolBarRenderer $buttonToolBarRenderer;

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \QuickformException
     * @throws \Chamilo\Libraries\Protocol\Error\Architecture\Exception\NoSuchClassException
     */
    public function run(?User $currentUser = null): Response
    {
        $this->checkAuthorization(Manager::CONTEXT, $currentUser, 'ManageUsers');

        if (!$currentUser->isPlatformAdministrator()) {
            throw new NotAllowedException();
        }

        $userIdentifier = $this->getRequest()->query->get(self::PARAM_USER_ID);
        $userToRender = $this->getUserService()->findUserByIdentifier($userIdentifier);

        if ($userToRender instanceof User) {
            $this->getBreadcrumbTrail()->add(new Breadcrumb($userToRender->getFullName()));

            $html = [];

            $html[] = $this->renderHeader($currentUser);
            $html[] = $this->getButtonToolBarRenderer()->render($this->getButtonToolBar($userToRender));
            $html[] = $this->getTabsRenderer()->renderNavigationAndContent(
                'userDetails', $this->getTabsCollection($userToRender, $currentUser), md5(UserDetailsRenderer::class)
            );
            $html[] = $this->renderFooter();

            return new Response(implode(PHP_EOL, $html));
        }
        else {
            $translator = $this->getTranslator();

            return new Response(
                $this->getErrorPageRenderer()->render(
                    $this, htmlentities(
                    $translator->trans(
                        'NoObjectSelected', ['%Object%' => $translator->trans('User', [], Manager::CONTEXT)],
                        StringUtilities::LIBRARIES
                    )
                ), $currentUser
                )
            );
        }
    }

    public function getButtonToolBar(User $userToRender): ButtonToolBar
    {
        $translator = $this->getTranslator();

        $buttonToolBar = new ButtonToolBar();
        $commonActions = new ButtonGroup();
        $toolActions = new ButtonGroup();

        $editUrl = $this->getUserUrlGenerator()->getUpdateUrl($userToRender);

        $commonActions->addButton(
            new Button(
                $translator->trans('Edit', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('pencil-alt'),
                $editUrl, DisplayTypeEnum::ICON_AND_LABEL
            )
        );

        $deleteUrl = $this->getUserUrlGenerator()->getDeleteUrl($userToRender);

        $commonActions->addButton(
            new Button(
                $translator->trans('Delete', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('times'), $deleteUrl,
                DisplayTypeEnum::ICON_AND_LABEL
            )
        );

        $changeUserUrl = $this->getUserUrlGenerator()->getChangeUserUrl($userToRender);

        $toolActions->addButton(
            new Button(
                $translator->trans('LoginAsUser', [], Manager::CONTEXT), new FontAwesomeGlyph('mask'), $changeUserUrl,
                DisplayTypeEnum::ICON_AND_LABEL
            )
        );

        $buttonToolBar->addButton($commonActions);
        $buttonToolBar->addButton($toolActions);

        return $buttonToolBar;
    }

    protected function getTabsCollection(User $userToView, User $currentUser): TabsCollection
    {
        $tabsCollection = new TabsCollection();

        foreach (
            $this->getUserDetailsRendererCollection()->getUserDetailsRenderers() as $userDetailsRendererClassName =>
            $userDetailsRenderer
        ) {
            if ($userDetailsRenderer->hasContentForUser($userToView, $currentUser)) {
                $tabsCollection->add(
                    $this->initializeContentTab(
                        $userDetailsRendererClassName, $userDetailsRenderer, $userToView, $currentUser
                    )
                );
            }
        }

        return $tabsCollection;
    }

    public function getTabsRenderer(): TabsRenderer
    {
        return $this->getService(TabsRenderer::class);
    }

    public function getUserDetailsRendererCollection(): UserDetailsRendererRegistry
    {
        return $this->getService(UserDetailsRendererRegistry::class);
    }

    protected function initializeContentTab(
        string $userDetailsRendererClassName, UserDetailsRendererInterface $userDetailsRenderer, User $userToRender,
        User $currentUser
    ): ContentTab
    {
        return new ContentTab(
            md5($userDetailsRendererClassName), $userDetailsRenderer->renderTitle($userToRender, $currentUser),
            $userDetailsRenderer->renderUserDetails($userToRender, $currentUser), $userDetailsRenderer->getGlyph()
        );
    }
}
