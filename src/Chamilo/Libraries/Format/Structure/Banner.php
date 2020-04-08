<?php
namespace Chamilo\Libraries\Format\Structure;

use Chamilo\Core\Menu\Renderer\MenuRenderer;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Traits\DependencyInjectionContainerTrait;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Utilities\Utilities;

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

    /**
     *
     * @var \Chamilo\Libraries\Architecture\Application\Application
     */
    private $application;

    /**
     *
     * @var integer
     */
    private $viewMode;

    /**
     *
     * @var string
     */
    private $containerMode;

    /**
     * @param \Chamilo\Libraries\Architecture\Application\Application|null $application
     * @param integer $viewMode
     * @param string $containerMode
     */
    public function __construct(
        Application $application = null, $viewMode = Page::VIEW_MODE_FULL, $containerMode = 'container-fluid'
    )
    {
        $this->application = $application;
        $this->viewMode = $viewMode;
        $this->containerMode = $containerMode;

        $this->initializeContainer();
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function render()
    {
        $sessionUtilities = $this->getSessionUtilities();
        $configurationConsulter = $this->getConfigurationConsulter();
        $translator = $this->getTranslator();

        $html = array();

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
            $html[] = $translator->trans('LoggedInAsUser', array(), 'Chamilo\Core\User');
            $html[] = ' ';
            $html[] = $userFullName;
            $html[] = ' ';
            $html[] =
                '<a href="' . $link . '">' . $translator->trans('Back', array(), Utilities::COMMON_LIBRARIES) . '</a>';
            $html[] = '</div>';
        }

        $html[] = $this->getMenuRenderer()->render($this->getContainerMode(), $user);

        if ($this->getViewMode() == Page::VIEW_MODE_FULL)
        {
            $breadcrumbtrail = BreadcrumbTrail::getInstance();
            $breadcrumbtrail->setContainerMode($this->getContainerMode());

            if ($breadcrumbtrail->size() > 0)
            {
                $html[] = $breadcrumbtrail->render();
            }
        }

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\Application\Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     */
    public function setApplication($application)
    {
        $this->application = $application;
    }

    /**
     *
     * @return string
     */
    public function getContainerMode()
    {
        return $this->containerMode;
    }

    /**
     *
     * @param string $containerMode
     */
    public function setContainerMode($containerMode)
    {
        $this->containerMode = $containerMode;
    }

    /**
     * @return \Chamilo\Core\Menu\Renderer\MenuRenderer
     */
    public function getMenuRenderer()
    {
        return $this->getService(MenuRenderer::class);
    }

    /**
     *
     * @return integer
     */
    public function getViewMode()
    {
        return $this->viewMode;
    }

    /**
     *
     * @param integer $viewMode
     */
    public function setViewMode($viewMode)
    {
        $this->viewMode = $viewMode;
    }
}
