<?php
namespace Chamilo\Core\Notification\Integration\Chamilo\Core\Menu\Renderer\Item;

use Chamilo\Core\Menu\Renderer\Item\PriorityItemRenderer;
use Chamilo\Core\Menu\Service\CachedItemService;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Notification\Manager;
use Chamilo\Core\Rights\Structure\Service\Interfaces\AuthorizationCheckerInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Symfony\Component\Translation\Translator;
use Twig_Environment;

/**
 * @package Chamilo\Core\Notification\Integration\Chamilo\Core\Menu\Renderer\Item\Bar\Item
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class NotificationWidgetItemRenderer extends PriorityItemRenderer
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker, Translator $translator,
        CachedItemService $itemCacheService, ChamiloRequest $request, Twig_Environment $twig
    )
    {
        parent::__construct($authorizationChecker, $translator, $itemCacheService, $request);

        $this->twig = $twig;
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function render(Item $item, User $user)
    {
        if (!$this->isItemVisibleForUser($user))
        {
            return '';
        }

        $viewerUrl = new Redirect(
            [
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Application::PARAM_ACTION => Manager::ACTION_VIEW
            ]
        );

        $filterManagerUrl = new Redirect(
            [
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Application::PARAM_ACTION => Manager::ACTION_MANAGE_FILTERS
            ]
        );

        return $this->getTwig()->render(
            'Chamilo\Core\Notification\Integration\Chamilo\Core\Menu:NotificationWidgetItem.html.twig', [
                'VIEWER_URL' => $viewerUrl->getUrl(),
                'FILTER_MANAGER_URL' => $filterManagerUrl->getUrl()
            ]
        );
    }

    /**
     * @param bool $isSelected
     * @param array $existingClasses
     *
     * @return array
     */
    protected function getClasses($isSelected = false, $existingClasses = [])
    {
        $existingClasses[] = 'chamilo-menu-item-priority';
        $existingClasses[] = 'dropdown';

        return parent::getClasses($isSelected, $existingClasses);
    }

    /**
     * @return \Twig_Environment
     */
    public function getTwig(): Twig_Environment
    {
        return $this->twig;
    }

    /**
     * Returns whether or not the given user can view this menu item
     *
     * @param User $user
     *
     * @return bool
     */
    public function isItemVisibleForUser(User $user)
    {
        $authorizationChecker = $this->getAuthorizationChecker();

        return Application::is_active('Chamilo\Core\Notification') &&
            $authorizationChecker->isAuthorized($user, 'Chamilo\Core\Notification');
    }

    /**
     * @param \Twig_Environment $twig
     */
    public function setTwig(Twig_Environment $twig): void
    {
        $this->twig = $twig;
    }
}