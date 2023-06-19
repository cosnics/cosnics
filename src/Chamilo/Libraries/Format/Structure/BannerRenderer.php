<?php
namespace Chamilo\Libraries\Format\Structure;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\Menu\Service\Renderer\MenuRenderer;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Format\Structure
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class BannerRenderer
{
    private BreadcrumbTrailRenderer $breadcrumbTrailRenderer;

    private ConfigurationConsulter $configurationConsulter;

    private MenuRenderer $menuRenderer;

    private PageConfiguration $pageConfiguration;

    private SessionInterface $session;

    private Translator $translator;

    private UrlGenerator $urlGenerator;

    public function __construct(
        PageConfiguration $pageConfiguration, SessionInterface $session, Translator $translator,
        ConfigurationConsulter $configurationConsulter, UrlGenerator $urlGenerator, MenuRenderer $menuRenderer,
        BreadcrumbTrailRenderer $breadcrumbTrailRenderer
    )
    {
        $this->pageConfiguration = $pageConfiguration;
        $this->session = $session;
        $this->configurationConsulter = $configurationConsulter;
        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;
        $this->menuRenderer = $menuRenderer;
        $this->breadcrumbTrailRenderer = $breadcrumbTrailRenderer;
    }

    /**
     * @throws \Exception
     */
    public function render(): string
    {
        $pageConfiguration = $this->getPageConfiguration();
        $session = $this->getSession();
        $configurationConsulter = $this->getConfigurationConsulter();
        $translator = $this->getTranslator();

        $html = [];

        if ($pageConfiguration->getApplication() instanceof Application &&
            $pageConfiguration->getApplication()->getUser() instanceof User)
        {
            $user = $pageConfiguration->getApplication()->getUser();
            $userFullName = $user->get_fullname();
        }
        else
        {
            $user = null;
            $userFullName = '';
        }

        $showMaintenanceWarning =
            $configurationConsulter->getSetting(['Chamilo\Core\Admin', 'maintenance_warning_show']);

        if ($showMaintenanceWarning)
        {
            $maintenanceWarning = $configurationConsulter->getSetting(
                ['Chamilo\Core\Admin', 'maintenance_warning_message']
            );

            if (!empty($maintenanceWarning))
            {
                $html[] = '<div class="warning-banner bg-warning text-warning">';
                $html[] = '<strong>' . $maintenanceWarning . '</strong>';
                $html[] = '</div>';
            }
        }

        if (!is_null($session->get('_as_admin')))
        {
            $link = $this->getUrlGenerator()->fromParameters([
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Application::PARAM_ACTION => Manager::ACTION_ADMIN_USER
            ]);

            $html[] = '<div class="warning-banner bg-warning text-warning">';
            $html[] = $translator->trans('LoggedInAsUser', [], 'Chamilo\Core\User');
            $html[] = ' ';
            $html[] = $userFullName;
            $html[] = ' ';
            $html[] = '<a href="' . $link . '">' . $translator->trans('Back', [], StringUtilities::LIBRARIES) . '</a>';
            $html[] = '</div>';
        }

        $html[] = $this->getMenuRenderer()->render($pageConfiguration->getContainerMode(), $user);

        if ($pageConfiguration->getViewMode() == PageConfiguration::VIEW_MODE_FULL)
        {
            $breadcrumbtrail = BreadcrumbTrail::getInstance();
            $breadcrumbtrail->setContainerMode($pageConfiguration->getContainerMode());

            if ($breadcrumbtrail->size() > 0)
            {
                $html[] = $this->getBreadcrumbTrailRenderer()->render($breadcrumbtrail);
            }
        }

        return implode(PHP_EOL, $html);
    }

    public function getBreadcrumbTrailRenderer(): BreadcrumbTrailRenderer
    {
        return $this->breadcrumbTrailRenderer;
    }

    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->configurationConsulter;
    }

    public function getMenuRenderer(): MenuRenderer
    {
        return $this->menuRenderer;
    }

    public function getPageConfiguration(): PageConfiguration
    {
        return $this->pageConfiguration;
    }

    public function getSession(): SessionInterface
    {
        return $this->session;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return $this->urlGenerator;
    }
}
