<?php
namespace Chamilo\Libraries\Format\Structure;

use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Format\Structure\Page;

/**
 * $Id: banner.class.php 179 2009-11-12 13:51:39Z vanpouckesven $
 *
 * @package common.html
 */

/**
 * Class to display the banner of a HTML-page
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
     * @param Application $application
     * @param integer $viewMode
     */
    public function __construct(Application $application = null, $viewMode)
    {
        $this->application = $application;
        $this->viewMode = $viewMode;
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
     * Creates the HTML output for the banner.
     */
    public function toHtml()
    {
        $output = array();

        if ($this->getApplication() instanceof Application && $this->getApplication()->get_user() instanceof User)
        {
            $user = \Chamilo\Core\User\Storage\DataManager :: retrieve_by_id(
                User :: class_name(),
                Session :: get_user_id());
        }
        else
        {
            $user = null;
        }

        if (! is_null(Session :: get('_as_admin')))
        {
            $redirect = new Redirect(
                array(
                    Application :: PARAM_CONTEXT => \Chamilo\Core\User\Manager :: context(),
                    Application :: PARAM_ACTION => \Chamilo\Core\User\Manager :: ACTION_ADMIN_USER));
            $link = $redirect->getUrl();

            $output[] = '<div id="emulator">' .
                 Translation :: get('LoggedInAsUser', null, \Chamilo\Core\User\Manager :: context()) . ' ' .
                 $this->getApplication()->get_user()->get_fullname() . ' <a href="' . $link . '">' .
                 Translation :: get('Back', null, Utilities :: COMMON_LIBRARIES) . '</a></div>';
        }

        $output[] = '<a name="top"></a>';
        $output[] = '<div id="header">  <!-- header section start -->';
        $output[] = '<div id="header1"> <!-- top of banner with institution name/hompage link -->';

        $output[] = '<div class="banner"><a href="' . Path :: getInstance()->getBasePath(true) .
             'index.php" target="_top"><span class="logo">' . PlatformSetting :: get('site_name', 'Chamilo\Core\Admin') .
             '</span></a></div>';

        if ($this->getApplication() instanceof Application && $this->getApplication()->get_user() instanceof User)
        {
            $registration = \Chamilo\Configuration\Storage\DataManager :: get_registration('Chamilo\Core\Menu');
            if ($registration instanceof Registration && $registration->is_active())
            {
                $output[] = '<div class="applications">';
                $output[] = \Chamilo\Core\Menu\Renderer\Menu\Renderer :: as_html(
                    \Chamilo\Core\Menu\Renderer\Menu\Renderer :: TYPE_BAR,
                    $this->getApplication()->get_user());
                $output[] = '<div class="clear">&nbsp;</div>';
                $output[] = '</div>';
            }
            $output[] = '<div class="clear">&nbsp;</div>';
        }

        $output[] = '</div> <!-- end of #header1 -->';

        if ($this->getViewMode() == Page :: VIEW_MODE_FULL)
        {
            $breadcrumbtrail = BreadcrumbTrail :: get_instance();

            if ($breadcrumbtrail->size() > 0)
            {
                $output[] = '<div id="trailbox">';
                $output[] = $breadcrumbtrail->render();
                $output[] = '<div class="clear">&nbsp;</div>';
                $output[] = '</div>';
            }
        }

        $output[] = '<div class="clear">&nbsp;</div>';
        $output[] = '</div> <!-- end of the whole #header section -->';

        return implode(PHP_EOL, $output);
    }
}
