<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Architecture\Interfaces\UserDetailsRendererInterface;
use Chamilo\Core\User\Domain\UserDetails\UserDetailsRendererCollection;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Service\UserDetails\UserDetailsRenderer;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Tabs\ContentTab;
use Chamilo\Libraries\Format\Tabs\TabsCollection;
use Chamilo\Libraries\Format\Tabs\TabsRenderer;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\User\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserDetailComponent extends Manager
{

    protected ButtonToolBarRenderer $buttonToolbarRenderer;

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \QuickformException
     */
    public function run()
    {
        $this->checkAuthorization(Manager::CONTEXT, 'ManageUsers');

        if (!$this->getUser()->isPlatformAdmin())
        {
            throw new NotAllowedException();
        }

        $userIdentifier = $this->getRequest()->query->get(self::PARAM_USER_USER_ID);
        $user = $this->getUserService()->findUserByIdentifier($userIdentifier);

        if ($user instanceof User)
        {
            $html = [];

            $html[] = $this->renderHeader();
            $html[] = $this->getButtonToolbarRenderer($user)->render();
            $html[] = $this->getTabsRenderer()->render('userDetails', $this->getTabsCollection($user));

            $html[] = $this->renderFooter();

            return implode(PHP_EOL, $html);
        }
        else
        {
            $translator = $this->getTranslator();

            return $this->display_error_page(
                htmlentities(
                    $translator->trans(
                        'NoObjectSelected', ['OBJECT' => $translator->trans('User', [], Manager::CONTEXT)],
                        StringUtilities::LIBRARIES
                    )
                )
            );
        }
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail): void
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url([self::PARAM_ACTION => self::ACTION_BROWSE_USERS]),
                $this->getTranslator()->trans('AdminUserBrowserComponent', [], Manager::CONTEXT)
            )
        );
    }

    public function getButtonToolbarRenderer($user): ButtonToolBarRenderer
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $translator = $this->getTranslator();

            $buttonToolbar = new ButtonToolBar();
            $commonActions = new ButtonGroup();
            $toolActions = new ButtonGroup();

            $commonActions->addButton(
                new Button(
                    $translator->trans('Edit', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('pencil-alt'),
                    $this->get_user_editing_url($user), ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );

            $commonActions->addButton(
                new Button(
                    $translator->trans('Delete', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('times'),
                    $this->get_user_delete_url($user), ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );

            $commonActions->addButton(
                new Button(
                    $translator->trans('ViewQuota', [], Manager::CONTEXT), new FontAwesomeGlyph('folder'),
                    $this->get_url(
                        [self::PARAM_ACTION => self::ACTION_VIEW_QUOTA, 'user_id' => $user->get_id()]
                    ), ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );

            $toolActions->addButton(
                new Button(
                    $translator->trans('LoginAsUser', [], Manager::CONTEXT), new FontAwesomeGlyph('mask'),
                    $this->get_change_user_url($user), ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );

            $buttonToolbar->addButtonGroup($commonActions);
            $buttonToolbar->addButtonGroup($toolActions);

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
     * @param \Chamilo\Core\User\Architecture\Interfaces\UserDetailsRendererInterface $userDetailsRenderer
     * @param \Chamilo\Core\User\Storage\DataClass\User|null $user
     *
     * @return \Chamilo\Libraries\Format\Tabs\ContentTab
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
