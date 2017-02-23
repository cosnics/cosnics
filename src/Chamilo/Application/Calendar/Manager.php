<?php
namespace Chamilo\Application\Calendar;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Calendar\Renderer\Type\ViewRenderer;
use Chamilo\Libraries\Platform\Configuration\LocalSetting;

/**
 *
 * @package application\calendar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class Manager extends Application
{
    // Parameters
    const PARAM_TIME = 'time';
    const PARAM_VIEW = 'view';
    const PARAM_DOWNLOAD = 'download';

    // Actions
    const ACTION_BROWSE = 'Browser';
    const ACTION_AVAILABILITY = 'Availability';
    const ACTION_ICAL = 'ICal';
    const ACTION_PRINT = 'Printer';

    // Default action
    const DEFAULT_ACTION = self::ACTION_BROWSE;

    /**
     *
     * @var \Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer
     */
    private $tabs;

    /**
     *
     * @var integer
     */
    private $currentTime;

    /**
     *
     * @return string
     */
    public function getCurrentRendererType()
    {
        $rendererType = $this->getRequest()->query->get(ViewRenderer::PARAM_TYPE);

        if (!$rendererType)
        {
            $rendererType = LocalSetting::getInstance()->get('default_view', 'Chamilo\Libraries\Calendar');

            if ($rendererType == ViewRenderer::TYPE_MONTH)
            {
                $detect = new \Mobile_Detect();
                if ($detect->isMobile() && !$detect->isTablet())
                {
                    $rendererType = ViewRenderer::TYPE_LIST;
                }
            }
        }

        return $rendererType;
    }

    /**
     *
     * @return integer
     */
    public function getCurrentRendererTime()
    {
        if (!isset($this->currentTime))
        {
            $defaultRenderDate = new \DateTime();
            $defaultRenderDate->setTime(0, 0, 0);

            $this->currentTime = $this->getRequest()->query->get(
                ViewRenderer::PARAM_TIME, $defaultRenderDate->getTimestamp()
            );
        }

        return $this->currentTime;
    }

    /**
     *
     * @param integer $currentTime
     */
    public function setCurrentRendererTime($currentTime)
    {
        $this->currentTime = $currentTime;
    }
}
