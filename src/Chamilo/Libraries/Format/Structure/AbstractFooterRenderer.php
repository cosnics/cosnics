<?php
namespace Chamilo\Libraries\Format\Structure;

/**
 *
 * @package Chamilo\Libraries\Format\Structure
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class AbstractFooterRenderer implements FooterRendererInterface
{
    private PageConfiguration $pageConfiguration;

    public function __construct(PageConfiguration $pageConfiguration)
    {
        $this->pageConfiguration = $pageConfiguration;
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
        $html[] = '<div class="' . $this->getPageConfiguration()->getContainerMode() . '">';
        $html[] = '<div class="row footer">';
        $html[] = '<div class="col-xs-12">';

        return implode(PHP_EOL, $html);
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

    public function getPageConfiguration(): PageConfiguration
    {
        return $this->pageConfiguration;
    }

}
