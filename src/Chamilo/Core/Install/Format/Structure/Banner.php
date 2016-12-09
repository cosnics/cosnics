<?php
namespace Chamilo\Core\Install\Format\Structure;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\PathBuilder;
use Chamilo\Libraries\Format\Structure\Page;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

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
     * @param Application $application
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

        $html[] = '<a name="top"></a>';
        $html[] = '<nav class="navbar navbar-chamilo navbar-default navbar-no-items">';
        $html[] = '<div class="' . $this->getContainerMode() . '">';
        $html[] = '<div class="navbar-header">';

        $html[] = '<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#menu-navbar-collapse" aria-expanded="false">';
        $html[] = '<span class="sr-only">Toggle navigation</span>';
        $html[] = '<span class="icon-bar"></span>';
        $html[] = '<span class="icon-bar"></span>';
        $html[] = '<span class="icon-bar"></span>';
        $html[] = '</button>';

        $brandSource = Theme::getInstance()->getImagePath('Chamilo\Configuration', 'LogoHeader');
        $pathBuilder = new PathBuilder(ClassnameUtilities::getInstance());

        $html[] = '<a class="navbar-brand" href="' . $pathBuilder->getBasePath(true) . '">' . '<img alt="' .
             Translation::get('ChamiloInstallationTitle') . '" src="' . $brandSource . '"></a>';

        $html[] = '</div>';
        $html[] = '<div class="collapse navbar-collapse" id="menu-navbar-collapse">';
        $html[] = '<ul class="nav navbar-nav navbar-right">';

        $html[] = '</ul>';
        $html[] = '</nav>';

        return implode(PHP_EOL, $html);
    }
}
