<?php
namespace Chamilo\Libraries\Format\Structure;

use Chamilo\Libraries\Architecture\Application\Application;

/**
 *
 * @package Chamilo\Libraries\Format\Structure
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class BaseFooter implements FooterInterface
{

    private Application $application;

    private string $containerMode;

    private int $viewMode;

    public function __construct(int $viewMode = Page::VIEW_MODE_FULL, string $containerMode = 'container-fluid')
    {
        $this->viewMode = $viewMode;
        $this->containerMode = $containerMode;
    }

    abstract public function render(): string;

    public function getApplication(): Application
    {
        return $this->application;
    }

    public function setApplication(Application $application)
    {
        $this->application = $application;
    }

    protected function getContainerFooter(): string
    {
        $html = [];

        $html[] = '&nbsp;&copy;&nbsp;' . date('Y');

        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '</div> <!-- end of .container-fluid" -->';
        $html[] = '</footer>';

        return implode(PHP_EOL, $html);
    }

    protected function getContainerHeader(): string
    {
        $html = [];

        $html[] = '<footer class="chamilo-footer">';
        $html[] = '<div class="' . $this->getContainerMode() . '">';
        $html[] = '<div class="row footer">';
        $html[] = '<div class="col-xs-12">';

        return implode(PHP_EOL, $html);
    }

    public function getContainerMode(): string
    {
        return $this->containerMode;
    }

    public function setContainerMode(string $containerMode)
    {
        $this->containerMode = $containerMode;
    }

    /**
     *
     * @return string
     */
    protected function getFooter(): string
    {
        $html = [];

        $html[] = '</body>';
        $html[] = '</html>';

        return implode(PHP_EOL, $html);
    }

    protected function getHeader(): string
    {
        $html = [];

        $html[] = '</div> <!-- end of .container-fluid" -->';

        return implode(PHP_EOL, $html);
    }

    public function getViewMode(): int
    {
        return $this->viewMode;
    }

    public function setViewMode(int $viewMode)
    {
        $this->viewMode = $viewMode;
    }

    /**
     * @deprecated Use BaseFooter::render() now
     */
    public function toHtml(): string
    {
        return $this->render();
    }
}
