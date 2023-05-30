<?php
namespace Chamilo\Core\Notification\Integration\Chamilo\Core\Menu\Renderer\Item;

use Chamilo\Configuration\Service\Consulter\RegistrationConsulter;
use Chamilo\Core\Menu\Renderer\Item\PriorityItemRenderer;
use Chamilo\Core\Menu\Service\CachedItemService;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Notification\Manager;
use Chamilo\Core\Rights\Structure\Service\Interfaces\AuthorizationCheckerInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Symfony\Component\Translation\Translator;
use Twig\Environment;

/**
 * @package Chamilo\Core\Notification\Integration\Chamilo\Core\Menu\Renderer\Item\Bar\Item
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class NotificationWidgetItemRenderer extends PriorityItemRenderer
{
    protected RegistrationConsulter $registrationConsulter;

    protected UrlGenerator $urlGenerator;

    private Environment $twig;

    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker, Translator $translator,
        CachedItemService $itemCacheService, ChamiloRequest $request, Environment $twig, UrlGenerator $urlGenerator,
        RegistrationConsulter $registrationConsulter
    )
    {
        parent::__construct($authorizationChecker, $translator, $itemCacheService, $request);

        $this->twig = $twig;
        $this->urlGenerator = $urlGenerator;
        $this->registrationConsulter = $registrationConsulter;
    }

    /**
     * @return string
     * @throws \Symfony\Component\Cache\Exception\CacheException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function render(Item $item, User $user)
    {
        if (!$this->isItemVisibleForUser($user))
        {
            return '';
        }

        $viewerUrl = $this->getUrlGenerator()->fromParameters(
            [
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Application::PARAM_ACTION => Manager::ACTION_VIEW
            ]
        );

        $filterManagerUrl = $this->getUrlGenerator()->fromParameters(
            [
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Application::PARAM_ACTION => Manager::ACTION_MANAGE_FILTERS
            ]
        );

        return $this->getTwig()->render(
            'Chamilo\Core\Notification\Integration\Chamilo\Core\Menu:NotificationWidgetItem.html.twig', [
                'VIEWER_URL' => $viewerUrl,
                'FILTER_MANAGER_URL' => $filterManagerUrl
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

    public function getRegistrationConsulter(): RegistrationConsulter
    {
        return $this->registrationConsulter;
    }

    public function getTwig(): Environment
    {
        return $this->twig;
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return $this->urlGenerator;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return bool
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function isItemVisibleForUser(User $user)
    {
        $authorizationChecker = $this->getAuthorizationChecker();

        return $this->getRegistrationConsulter()->isContextRegisteredAndActive('Chamilo\Core\Notification') &&
            $authorizationChecker->isAuthorized($user, 'Chamilo\Core\Notification');
    }

    public function setTwig(Environment $twig): void
    {
        $this->twig = $twig;
    }
}