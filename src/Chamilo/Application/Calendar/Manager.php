<?php
namespace Chamilo\Application\Calendar;

use Chamilo\Application\Calendar\Repository\CalendarRendererProviderRepository;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Calendar\Service\View\HtmlCalendarRenderer;
use DateTime;
use Detection\MobileDetect;
use Exception;

/**
 * @package application\calendar
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class Manager extends Application
{
    // Parameters
    public const ACTION_AVAILABILITY = 'Availability';
    public const ACTION_BROWSE = 'Browser';
    public const ACTION_ICAL = 'ICal';
    public const ACTION_PRINT = 'Printer';

    public const CONTEXT = __NAMESPACE__;

    public const DEFAULT_ACTION = self::ACTION_BROWSE;

    public const PARAM_DOWNLOAD = 'download';
    public const PARAM_TIME = 'time';
    public const PARAM_VIEW = 'view';

    private int $currentTime;

    public function getCalendarRendererProviderRepository(): CalendarRendererProviderRepository
    {
        return $this->getService(CalendarRendererProviderRepository::class);
    }

    public function getCurrentRendererTime(): int
    {
        if (!isset($this->currentTime))
        {
            $defaultRenderDate = new DateTime();
            $defaultRenderDate->setTime(0, 0);

            $this->currentTime = $this->getRequest()->query->get(
                HtmlCalendarRenderer::PARAM_TIME, $defaultRenderDate->getTimestamp()
            );
        }

        return $this->currentTime;
    }

    public function getCurrentRendererType(): string
    {
        $rendererType = $this->getRequest()->query->get(HtmlCalendarRenderer::PARAM_TYPE);

        if (!$rendererType)
        {
            $rendererType = $this->getUserSettingService()->getSettingForUser(
                $this->getUser(), 'Chamilo\Libraries\Calendar', 'default_view'
            );

            if ($rendererType == HtmlCalendarRenderer::TYPE_MONTH)
            {
                $detect = new MobileDetect();

                try
                {
                    if ($detect->isMobile() && !$detect->isTablet())
                    {
                        $rendererType = HtmlCalendarRenderer::TYPE_LIST;
                    }
                }
                catch (Exception)
                {

                }
            }
        }

        return $rendererType;
    }

    /**
     * @param int $currentTime
     */
    public function setCurrentRendererTime($currentTime)
    {
        $this->currentTime = $currentTime;
    }
}
