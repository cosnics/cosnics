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
     * @throws \Chamilo\Libraries\Architecture\Exception\ClassNotExistException
     */
    public function run(): Response
    {
        $this->checkAuthorization(Manager::CONTEXT, 'ManageUsers');

        if (!$this->getUser()->isPlatformAdministrator()) {
            throw new NotAllowedException();
        }

        $userIdentifier = $this->getRequest()->query->get(self::PARAM_USER_ID);
        $user = $this->getUserService()->findUserByIdentifier($userIdentifier);

        if ($user instanceof User) {
            $this->getBreadcrumbTrail()->add(new Breadcrumb('', $user->getFullName()));

            $html = [];

            $html[] = $this->getDefaultHeaderRenderer()->render($user);
            $html[] = $this->getButtonToolBarRenderer()->render($this->getButtonToolBar($user));
            $html[] = $this->getTabsRenderer()->renderNavigationAndContent(
                'userDetails', $this->getTabsCollection($user), md5(UserDetailsRenderer::class)
            );
            $html[] = $this->renderFooter();

            return new Response(implode(PHP_EOL, $html));
        }
        else {
            $translator = $this->getTranslator();

            return new Response(
                $this->displayErrorPage(
                    htmlentities(
                        $translator->trans(
                            'NoObjectSelected', ['%Object%' => $translator->trans('User', [], Manager::CONTEXT)],
                            StringUtilities::LIBRARIES
                        )
                    )
                )
            );
        }
    }

    public function getButtonToolBar($user): ButtonToolBar
    {
        $translator = $this->getTranslator();

        $buttonToolBar = new ButtonToolBar();
        $commonActions = new ButtonGroup();
        $toolActions = new ButtonGroup();

        $editUrl = $this->getUserUrlGenerator()->getUpdateUrl($user);

        $commonActions->addButton(
            new Button(
                $translator->trans('Edit', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('pencil-alt'),
                $editUrl, DisplayTypeEnum::ICON_AND_LABEL
            )
        );

        $deleteUrl = $this->getUserUrlGenerator()->getDeleteUrl($user);

        $commonActions->addButton(
            new Button(
                $translator->trans('Delete', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('times'), $deleteUrl,
                DisplayTypeEnum::ICON_AND_LABEL
            )
        );

        $changeUserUrl = $this->getUserUrlGenerator()->getChangeUserUrl($user);

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

    protected function getTabsCollection(User $user): TabsCollection
    {
        $tabsCollection = new TabsCollection();

        //        $userDetailsRendererCollection = $this->getUserDetailsRendererCollection();
        //        $userDetailsRenderer = $userDetailsRendererCollection->getUserDetailsRenderer(UserDetailsRenderer::class);
        //
        //        $tabsCollection->add(
        //            $this->initializeContentTab(UserDetailsRenderer::class, $userDetailsRenderer, $user)
        //        );

        foreach (
            $this->getUserDetailsRendererCollection()->getUserDetailsRenderers() as $userDetailsRendererClassName =>
            $userDetailsRenderer
        ) {
            if ($userDetailsRenderer->hasContentForUser($user, $this->getUser())) {
                $tabsCollection->add(
                    $this->initializeContentTab($userDetailsRendererClassName, $userDetailsRenderer, $user)
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

    /**
     * @param int|string $userDetailsRendererClassName
     * @param \Chamilo\Core\User\Architecture\Interface\UserDetailsRendererInterface $userDetailsRenderer
     * @param \Chamilo\Core\User\Storage\DataClass\User|null $user
     *
     * @return \Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\ContentTab
     */
    protected function initializeContentTab(
        int|string $userDetailsRendererClassName, UserDetailsRendererInterface $userDetailsRenderer, ?User $user
    ): ContentTab
    {
        return new ContentTab(
            md5($userDetailsRendererClassName), $userDetailsRenderer->renderTitle($user, $this->getUser()),
            $userDetailsRenderer->renderUserDetails($user, $this->getUser()), $userDetailsRenderer->getGlyph()
        );
    }
}
