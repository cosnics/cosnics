<?php
namespace Chamilo\Libraries\Calendar\View\Renderer\Type;

use Chamilo\Libraries\Calendar\View\Renderer\ViewHtmlTableRenderer;

/**
 *
 * @package Chamilo\Libraries\Calendar\View\Renderer\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class WeekRenderer extends ViewHtmlTableRenderer
{

    /**
     *
     * @see \Chamilo\Libraries\Calendar\View\Renderer\ViewHtmlTableRenderer::getTitle()
     */
    protected function getTitle($currentRendererTime)
    {
        $titleParts = [];
        $date = $this->getDatetimeUtilities()->getDate($this->getFormatRenderer()->getStartTime());

        $titleParts[] = $date->format('l d F Y');
        $titleParts[] = '-';
        $titleParts[] = $date->addDays(6)->format('l d F Y');

        return implode(' ', $titleParts);
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\View\Renderer\ViewHtmlTableRenderer::getPreviousDisplayTime()
     */
    protected function getPreviousDisplayTime($currentRendererTime)
    {
        return strtotime('-1 Week', $currentRendererTime);
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\View\Renderer\ViewHtmlTableRenderer::getNextDisplayTime()
     */
    protected function getNextDisplayTime($currentRendererTime)
    {
        return strtotime('+1 Week', $currentRendererTime);
    }
}