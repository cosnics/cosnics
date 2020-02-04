<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Menu\Renderer\Item;

use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Core\Menu\Renderer\ItemRenderer;
use Chamilo\Core\Menu\Service\ItemCacheService;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Rights\Structure\Service\Interfaces\AuthorizationCheckerInterface;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\User\Integration\Chamilo\Core\Menu\Renderer\ItemRenderer
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class WidgetItemRenderer extends ItemRenderer
{

    /**
     * @var \Chamilo\Configuration\Service\ConfigurationConsulter
     */
    private $configurationConsulter;

    /**
     * @param \Chamilo\Core\Rights\Structure\Service\Interfaces\AuthorizationCheckerInterface $authorizationChecker
     * @param \Symfony\Component\Translation\Translator $translator
     * @param \Chamilo\Core\Menu\Service\ItemCacheService $itemCacheService
     * @param \Chamilo\Libraries\Format\Theme $themeUtilities
     * @param \Chamilo\Libraries\Platform\ChamiloRequest $request
     * @param \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter
     */
    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker, Translator $translator, ItemCacheService $itemCacheService,
        Theme $themeUtilities, ChamiloRequest $request, ConfigurationConsulter $configurationConsulter
    )
    {
        parent::__construct($authorizationChecker, $translator, $itemCacheService, $themeUtilities, $request);

        $this->configurationConsulter = $configurationConsulter;
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     *
     * @return string
     */
    public function renderTitle(Item $item)
    {
        return $this->getTranslator()->trans('MyAccount', [], 'Chamilo\Core\User');
    }

    /**
     * @param \Chamilo\Core\User\Integration\Chamilo\Core\Menu\Storage\DataClass\WidgetItem $item
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return string
     */
    public function render(Item $item, User $user)
    {
        $translator = $this->getTranslator();
        $title = $this->renderTitle($item);

        if (!$this->isItemVisibleForUser($user))
        {
            return '';
        }

        $html = array();

        $html[] = '<li class="nav-item dropdown">';
        $html[] =
            '<a class="nav-link text-center" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';

        if ($item->showIcon())
        {
            $profilePhotoUrl = new Redirect(
                array(
                    Application::PARAM_CONTEXT => \Chamilo\Core\User\Ajax\Manager::context(),
                    Application::PARAM_ACTION => \Chamilo\Core\User\Ajax\Manager::ACTION_USER_PICTURE,
                    Manager::PARAM_USER_USER_ID => $user->getId()
                )
            );

            $html[] =
                '<img style="height: 50px;" class="rounded-circle" src="' . $profilePhotoUrl->getUrl() . '" title="' .
                $title . '" alt="' . $title . '" />';

            if ($item->showTitle())
            {
                $html[] = '<br/>';
            }
        }

        if ($item->showTitle())
        {
            $html[] = $title;
        }

        $html[] = '</a>';

        $profilePhotoUrl = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\User\Ajax\Manager::context(),
                Application::PARAM_ACTION => \Chamilo\Core\User\Ajax\Manager::ACTION_USER_PICTURE,
                Manager::PARAM_USER_USER_ID => $user->getId()
            )
        );

        $html[] = '<ul class="dropdown-menu dropdown-menu-right">';

        $html[] = '<h6 class="dropdown-header">' . $user->get_fullname() . '</h6>';

        $html[] = '<a class="dropdown-item" href="' . $this->getAccountUrl() . '">' .
            $translator->trans('MyAccount', [], 'Chamilo\Core\User') . '</a>';

        $html[] = '<a class="dropdown-item" href="' . $this->getSettingsUrl() . '">' .
            $translator->trans('Settings', [], 'Chamilo\Core\User') . '</a>';

        $html[] = '<div class="dropdown-divider"></div>';

        $html[] = '<a class="dropdown-item" href="' . $this->getLogoutUrl() . '">' .
            $translator->trans('Logout', [], 'Chamilo\Core\User') . '</a>';

        $html[] = '</ul>';

        $html[] = '</li>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    public function getAccountUrl()
    {
        return $this->getUserUrl(Manager::ACTION_VIEW_ACCOUNT);
    }

    /**
     * @return \Chamilo\Configuration\Service\ConfigurationConsulter
     */
    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->configurationConsulter;
    }

    /**
     * @param \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter
     */
    public function setConfigurationConsulter(ConfigurationConsulter $configurationConsulter): void
    {
        $this->configurationConsulter = $configurationConsulter;
    }

    /**
     *
     * @return string
     */
    public function getLogoutUrl()
    {
        return $this->getUserUrl(Manager::ACTION_LOGOUT);
    }

    /**
     *
     * @return string
     */
    public function getPictureUrl()
    {
        return $this->getUserUrl(Manager::ACTION_CHANGE_PICTURE);
    }

    /**
     *
     * @return string
     */
    public function getSettingsUrl()
    {
        return $this->getUserUrl(Manager::ACTION_USER_SETTINGS);
    }

    /**
     *
     * @param string $action
     *
     * @return string
     */
    public function getUserUrl($action)
    {
        $redirect = new Redirect(
            array(Application::PARAM_CONTEXT => Manager::context(), Application::PARAM_ACTION => $action)
        );

        return $redirect->getUrl();
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return boolean
     */
    public function isItemVisibleForUser(User $user)
    {
        return $this->getAuthorizationChecker()->isAuthorized($user, 'Chamilo\Core\User', 'ManageAccount');
    }
}