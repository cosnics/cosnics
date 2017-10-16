<?php
namespace Chamilo\Libraries\Format\Structure;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\Page;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
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
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     * @param integer $viewMode
     */
    public function __construct(Application $application = null, $viewMode = Page :: VIEW_MODE_FULL, $containerMode = 'container-fluid')
    {
        $this->application = $application;
        $this->viewMode = $viewMode;
        $this->containerMode = $containerMode;
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
     * Creates the HTML output for the banner.
     */
    public function render()
    {
        $html = array();

        if ($this->getApplication() instanceof Application && $this->getApplication()->get_user() instanceof User)
        {
            $user = \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(User::class_name(), Session::get_user_id());
            $userFullName = $this->getApplication()->getUser()->get_fullname();
        }
        else
        {
            $user = null;
            $userFullName = '';
        }

        $showMaintenanceWarning = Configuration::getInstance()->get_setting(
            array('Chamilo\Core\Admin', 'maintenance_warning_show'));

        if ($showMaintenanceWarning)
        {
            $maintenanceWarning = Configuration::getInstance()->get_setting(
                array('Chamilo\Core\Admin', 'maintenance_warning_message'));

            if (! empty($maintenanceWarning))
            {
                $html[] = '<div class="warning-banner bg-warning text-warning text-maintenance">';
                $html[] = $maintenanceWarning;
                $html[] = '</div>';
            }
        }

        if (! is_null(Session::get('_as_admin')))
        {
            $redirect = new Redirect(
                array(
                    Application::PARAM_CONTEXT => \Chamilo\Core\User\Manager::context(),
                    Application::PARAM_ACTION => \Chamilo\Core\User\Manager::ACTION_ADMIN_USER));
            $link = $redirect->getUrl();

            $html[] = '<div class="warning-banner bg-warning text-warning">' .
                 Translation::get('LoggedInAsUser', null, \Chamilo\Core\User\Manager::context()) . ' ' . $userFullName .
                 ' <a href="' . $link . '">' . Translation::get('Back', null, Utilities::COMMON_LIBRARIES) . '</a></div>';
        }

        $html[] = '<a name="top"></a>';

        if ($this->getApplication() instanceof Application)
        {
            $request = $this->getApplication()->getRequest();
        }

        $menuRenderer = Configuration::getInstance()->get_setting(array('Chamilo\Core\Menu', 'menu_renderer'));

        $html[] = \Chamilo\Core\Menu\Renderer\Menu\Renderer::toHtml(
            $menuRenderer,
            $this->getContainerMode(),
            $request,
            $user);

        if ($this->getApplication() instanceof Application && $this->getApplication()->getUser() instanceof User)
        {
            if ($this->getViewMode() == Page::VIEW_MODE_FULL)
            {
                $breadcrumbtrail = BreadcrumbTrail::getInstance();
                $breadcrumbtrail->setContainerMode($this->getContainerMode());

                if ($breadcrumbtrail->size() > 0)
                {
                    $html[] = $breadcrumbtrail->render();
                }
            }
        }

        return implode(PHP_EOL, $html);
    }
}
