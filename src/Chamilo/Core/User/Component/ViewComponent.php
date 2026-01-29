<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Architecture\Domain\UserDetailsRendererCollection;
use Chamilo\Core\User\Architecture\Interface\UserDetailsRendererInterface;
use Chamilo\Core\User\Implementation\User\UserDetailsRenderer;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\Button;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\ButtonGroup;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\ButtonToolBar;
use Chamilo\Libraries\UserInterface\ActionBar\Service\ButtonToolBarRenderer;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Chamilo\Libraries\UserInterface\Layout\Architecture\Domain\ToolbarItem;
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

    protected ButtonToolBarRenderer $buttonToolbarRenderer;

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \QuickformException
     */
    public function run(): Response
    {
        $this->checkAuthorization(Manager::CONTEXT, 'ManageUsers');

        if (!$this->getUser()->isPlatformAdministrator())
        {
            throw new NotAllowedException();
        }

        $userIdentifier = $this->getRequest()->query->get(self::PARAM_USER_ID);
        $user = $this->getUserService()->findUserByIdentifier($userIdentifier);

        if ($user instanceof User)
        {
            $html = [];

            $html[] = $this->renderHeader();
            $html[] = $this->getButtonToolbarRenderer($user)->render();
            $html[] = $this->getTabsRenderer()->render('userDetails', $this->getTabsCollection($user));

            $html[] = $this->renderFooter();

            return new Response(implode(PHP_EOL, $html));
        }
        else
        {
            $translator = $this->getTranslator();

            return new Response(
                $this->display_error_page(
                    htmlentities(
                        $translator->trans(
                            'NoObjectSelected', ['OBJECT' => $translator->trans('User', [], Manager::CONTEXT)],
                            StringUtilities::LIBRARIES
                        )
                    )
                )
            );
        }
    }

    public function getButtonToolbarRenderer($user): ButtonToolBarRenderer
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $translator = $this->getTranslator();

            $buttonToolbar = new ButtonToolBar();
            $commonActions = new ButtonGroup();
            $toolActions = new ButtonGroup();

            $editUrl = $this->getUserUrlGenerator()->getUpdateUrl($user);

            $commonActions->addGroupButton(
                new Button(
                    $translator->trans('Edit', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('pencil-alt'),
                    $editUrl, ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );

            $deleteUrl = $this->getUserUrlGenerator()->getDeleteUrl($user);

            $commonActions->addGroupButton(
                new Button(
                    $translator->trans('Delete', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('times'),
                    $deleteUrl, ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );

            $changeUserUrl = $this->getUserUrlGenerator()->getChangeUserUrl($user);

            $toolActions->addGroupButton(
                new Button(
                    $translator->trans('LoginAsUser', [], Manager::CONTEXT), new FontAwesomeGlyph('mask'),
                    $changeUserUrl, ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );

            $buttonToolbar->addButton($commonActions);
            $buttonToolbar->addButton($toolActions);

            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    protected function getTabsCollection(User $user): TabsCollection
    {
        $tabsCollection = new TabsCollection();

        $userDetailsRendererCollection = $this->getUserDetailsRendererCollection();
        $userDetailsRenderer = $userDetailsRendererCollection->getUserDetailsRenderer(UserDetailsRenderer::class);

        $tabsCollection->add(
            $this->initializeContentTab(UserDetailsRenderer::class, $userDetailsRenderer, $user)
        );

        foreach (
            $this->getUserDetailsRendererCollection()->getUserDetailsRenderers() as $userDetailsRendererClassName =>
            $userDetailsRenderer
        )
        {
            if ($userDetailsRendererClassName !== UserDetailsRenderer::class &&
                $userDetailsRenderer->hasContentForUser($user, $this->getUser()))
            {
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

    public function getUserDetailsRendererCollection(): UserDetailsRendererCollection
    {
        return $this->getService(UserDetailsRendererCollection::class);
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
