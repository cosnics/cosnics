<?php
namespace Chamilo\Core\Install\Format\Structure;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\PathBuilder;
use Chamilo\Libraries\Format\Structure\Page;
use Chamilo\Libraries\Format\Theme\ThemePathBuilder;
use Chamilo\Libraries\Translation\Translation;

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
     * Banner constructor.
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application|null $application
     * @param int $viewMode
     * @param string $containerMode
     */
    public function __construct(
        Application $application = null, $viewMode = Page::VIEW_MODE_FULL, $containerMode = 'container-fluid'
    )
    {
        $this->application = $application;
        $this->viewMode = $viewMode;
        $this->containerMode = $containerMode;
    }

    /**
     * Creates the HTML output for the banner.
     */
    public function render()
    {
        $html = array();

        $html[] = '<nav class="navbar navbar-static-top navbar-cosnics navbar-inverse">';
        $html[] = '<div class="' . $this->getContainerMode() . '">';
        $html[] = '<div class="navbar-header">';

        $brandSource = $this->getThemePathBuilder()->getImagePath('Chamilo\Libraries', 'LogoHeader');
        $pathBuilder = new PathBuilder(ClassnameUtilities::getInstance());

        $html[] = '<a class="navbar-brand" href="' . $pathBuilder->getBasePath(true) . '">';
        $html[] = '<img alt="' . Translation::get('ChamiloInstallationTitle') . '" src="' . $brandSource . '">';
        $html[] = '</a>';

        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '</nav>';

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
     * @return \Chamilo\Libraries\Format\Theme\ThemePathBuilder
     */
    public function getThemePathBuilder()
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(ThemePathBuilder::class);
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
