<?php

namespace Chamilo\Libraries\Calendar\Architecture\Traits;

use Chamilo\Core\User\Service\UserSettingService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Calendar\Service\View\HtmlCalendarRenderer;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Mobile_Detect;

trait CurrentCalendarRendererTrait
{
    protected int $currentCalendarTime;

    public function getCurrentCalendarRendererType(): string
    {
        $rendererType = $this->getRequest()->query->get(HtmlCalendarRenderer::PARAM_TYPE);

        if (!$rendererType)
        {
            $rendererType = $this->getUserSettingService()->getSettingForUser(
                $this->getUser(), 'Chamilo\Libraries\Calendar', 'default_view'
            );

            if ($rendererType == HtmlCalendarRenderer::TYPE_MONTH)
            {
                $detect = new Mobile_Detect();
                if ($detect->isMobile() && !$detect->isTablet())
                {
                    $rendererType = HtmlCalendarRenderer::TYPE_LIST;
                }
            }
        }

        return $rendererType;
    }

    public function getCurrentCalendartRendererTime(): int
    {
        if (!isset($this->currentTime))
        {
            $this->currentTime = $this->getRequest()->query->get(HtmlCalendarRenderer::PARAM_TIME, time());
        }

        return $this->currentTime;
    }

    abstract public function getRequest(): ChamiloRequest;

    abstract public function getUser(): ?User;

    abstract public function getUserSettingService(): UserSettingService;
}