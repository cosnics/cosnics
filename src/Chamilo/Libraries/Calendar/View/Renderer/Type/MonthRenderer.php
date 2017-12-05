<?php
namespace Chamilo\Libraries\Calendar\View\Renderer\Type;

use Chamilo\Libraries\Calendar\View\Renderer\ViewHtmlTableRenderer;

/**
 *
 * @package Chamilo\Libraries\Calendar\View\Renderer\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class MonthRenderer extends ViewHtmlTableRenderer
{

    /**
     *
     * @see \Chamilo\Libraries\Calendar\View\Renderer\ViewHtmlTableRenderer::getTitle()
     */
    protected function getTitle($currentRendererTime)
    {
        return $this->getDatetimeUtilities()->getDate($currentRendererTime)->format('F Y');
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\View\Renderer\ViewHtmlTableRenderer::getPreviousDisplayTime()
     */
    public function getPreviousDisplayTime($currentRendererTime)
    {
        return strtotime('first day of previous month', $currentRendererTime);
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\View\Renderer\ViewHtmlTableRenderer::getNextDisplayTime()
     */
    public function getNextDisplayTime($currentRendererTime)
    {
        return strtotime('first day of next month', $currentRendererTime);
    }
}