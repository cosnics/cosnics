<?php
namespace Chamilo\Libraries\Format\Structure;

use Chamilo\Core\Menu\Renderer\MenuRenderer;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Traits\DependencyInjectionContainerTrait;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package Chamilo\Libraries\Format\Structure
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Banner
{
    use DependencyInjectionContainerTrait;

    private ?Application $application;

    private string $containerMode;

    private int $viewMode;

    public function __construct(
        ?Application $application = null, int $viewMode = Page::VIEW_MODE_FULL, string $containerMode = 'container-fluid'
    )
    {
        $this->application = $application;
        $this->viewMode = $viewMode;
        $this->containerMode = $containerMode;

        $this->initializeContainer();
    }

    /**
     * @throws \Exception
     */
    public function render(): string
    {
        $sessionUtilities = $this->getSessionUtilities();
        $configurationConsulter = $this->getConfigurationConsulter();
        $translator = $this->getTranslator();

        $html = [];

        if ($this->getApplication() instanceof Application && $this->getApplication()->getUser() instanceof User)
        {
            $user = $this->getApplication()->getUser();
            $userFullName = $this->getApplication()->getUser()->get_fullname();
        }
        else
        {
            $user = null;
            $userFullName = '';
        }

        $showMaintenanceWarning =
            $configurationConsulter->getSetting(array('Chamilo\Core\Admin', 'maintenance_warning_show'));

        if ($showMaintenanceWarning)
        {
            $maintenanceWarning = $configurationConsulter->getSetting(
                array('Chamilo\Core\Admin', 'maintenance_warning_message')
            );

            if (!empty($maintenanceWarning))
            {
                $html[] = '<div class="warning-banner bg-warning text-warning">';
                $html[] = '<strong>' . $maintenanceWarning . '</strong>';
                $html[] = '</div>';
            }
        }

        if (!is_null($sessionUtilities->get('_as_admin')))
        {
            $redirect = new Redirect(
                array(
                    Application::PARAM_CONTEXT => Manager::context(),
                    Application::PARAM_ACTION => Manager::ACTION_ADMIN_USER
                )
            );
            $link = $redirect->getUrl();

            $html[] = '<div class="warning-banner bg-warning text-warning">';
            $html[] = $translator->trans('LoggedInAsUser', [], 'Chamilo\Core\User');
            $html[] = ' ';
            $html[] = $userFullName;
            $html[] = ' ';
            $html[] = '<a href="' . $link . '">' . $translator->trans('Back', [], StringUtilities::LIBRARIES) . '</a>';
            $html[] = '</div>';
        }

        $html[] = $this->getMenuRenderer()->render($this->getContainerMode(), $user);

        if ($this->getViewMode() == Page::VIEW_MODE_FULL)
        {
            $breadcrumbtrail = BreadcrumbTrail::getInstance();
            $breadcrumbtrail->setContainerMode($this->getContainerMode());

            if ($breadcrumbtrail->size() > 0)
            {
                $breadcrumbTrailRenderer = new BreadcrumbTrailRenderer(new StringUtilities());
                $html[] = $breadcrumbTrailRenderer->render($breadcrumbtrail);
            }
        }

        return implode(PHP_EOL, $html);
    }

    public function getApplication(): ?Application
    {
        return $this->application;
    }

    public function setApplication(?Application $application)
    {
        $this->application = $application;
    }

    public function getContainerMode(): string
    {
        return $this->containerMode;
    }

    public function setContainerMode(string $containerMode)
    {
        $this->containerMode = $containerMode;
    }

    public function getMenuRenderer(): MenuRenderer
    {
        return $this->getService(MenuRenderer::class);
    }

    /**
     *
     * @return integer
     */
    public function getViewMode(): int
    {
        return $this->viewMode;
    }

    public function setViewMode(int $viewMode)
    {
        $this->viewMode = $viewMode;
    }
}
