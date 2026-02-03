<?php
namespace Chamilo\Libraries\Calendar\Architecture\Trait;

use Chamilo\Core\User\Service\UserSettingService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Calendar\Service\View\HtmlCalendarRenderer;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Detection\MobileDetect;
use Exception;

/**
 * @package Chamilo\Libraries\Calendar\Architecture\Trait
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait CurrentCalendarRendererTrait
{
    protected int $currentCalendarTime;

    public function getCurrentCalendarRendererType(): string
    {
        $rendererType = $this->getRequest()->query->get(HtmlCalendarRenderer::PARAM_TYPE);

        if (!$rendererType) {
            $rendererType = $this->getUserSettingService()->getSettingForUser(
                $this->getUser(), 'Chamilo\Libraries', 'calendar_default_view'
            );

            if ($rendererType == HtmlCalendarRenderer::TYPE_MONTH) {
                $detect = new MobileDetect();
                try {
                    if ($detect->isMobile() && !$detect->isTablet()) {
                        $rendererType = HtmlCalendarRenderer::TYPE_LIST;
                    }
                }
                catch (Exception) {
                }
            }
        }

        return $rendererType;
    }

    public function getCurrentCalendartRendererTime(): int
    {
        if (!isset($this->currentCalendarTime)) {
            $this->currentCalendarTime = $this->getRequest()->query->get(HtmlCalendarRenderer::PARAM_TIME, time());
        }

        return $this->currentCalendarTime;
    }

    abstract public function getRequest(): ChamiloRequest;

    abstract public function getUser(): ?User;

    abstract public function getUserSettingService(): UserSettingService;
}