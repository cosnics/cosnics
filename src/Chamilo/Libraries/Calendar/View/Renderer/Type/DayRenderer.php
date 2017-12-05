<?php
namespace Chamilo\Libraries\Calendar\View\Renderer\Type;

use Chamilo\Libraries\Calendar\View\Renderer\ViewHtmlTableRenderer;

/**
 *
 * @package Chamilo\Libraries\Calendar\View\Renderer\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DayRenderer extends ViewHtmlTableRenderer
{

    /**
     *
     * @see \Chamilo\Libraries\Calendar\View\Renderer\ViewHtmlTableRenderer::getTitle()
     */
    protected function getTitle($currentRendererTime)
    {
        return $this->getDatetimeUtilities()->getDate($currentRendererTime)->format('l d F Y');
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\View\Renderer\ViewHtmlTableRenderer::getPreviousDisplayTime()
     */
    protected function getPreviousDisplayTime($currentRendererTime)
    {
        return strtotime('-1 Day', $currentRendererTime);
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\View\Renderer\ViewHtmlTableRenderer::getNextDisplayTime()
     */
    protected function getNextDisplayTime($currentRendererTime)
    {
        return strtotime('+1 Day', $currentRendererTime);
    }
}