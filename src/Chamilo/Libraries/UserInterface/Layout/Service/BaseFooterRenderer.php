<?php
namespace Chamilo\Libraries\UserInterface\Layout\Service;

/**
 * @package Chamilo\Libraries\UserInterface\Layout\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class BaseFooterRenderer
{
    public function render(): string
    {
        $html = [];

        $html[] = $this->renderHeader();
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    public function renderFooter(): string
    {
        $html = [];

        $html[] = '</body>';
        $html[] = '</html>';

        return implode(PHP_EOL, $html);
    }

    public function renderHeader(): string
    {
        $html = [];

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}
