<?php
namespace Chamilo\Libraries\Format\Structure;

/**
 *
 * @package Chamilo\Libraries\Format\Structure
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class BaseFooter implements FooterInterface
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
     * @param integer $viewMode
     */
    public function __construct($viewMode = Page :: VIEW_MODE_FULL, $containerMode = 'container-fluid')
    {
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

    protected function getContainerHeader()
    {
        $html = array();

        $html[] = '<footer class="chamilo-footer">';
        $html[] = '<div class="' . $this->getContainerMode() . '">';
        $html[] = '<div class="row footer">';
        $html[] = '<div class="col-xs-12">';

        return implode(PHP_EOL, $html);
    }

    protected function getContainerFooter()
    {
        $html = array();

        $html[] = '&nbsp;&copy;&nbsp;' . date('Y');

        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '</div> <!-- end of .container-fluid" -->';
        $html[] = '</footer>';

        return implode(PHP_EOL, $html);
    }

    protected function getHeader()
    {
        $html = array();

        $html[] = '</div> <!-- end of .container-fluid" -->';

        return implode(PHP_EOL, $html);
    }

    protected function getFooter()
    {
        $html = array();

        $html[] = '</body>';
        $html[] = '</html>';

        return implode(PHP_EOL, $html);
    }

    abstract public function toHtml();
}
