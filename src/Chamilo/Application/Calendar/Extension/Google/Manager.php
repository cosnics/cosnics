<?php
namespace Chamilo\Application\Calendar\Extension\Google;

use Chamilo\Application\Calendar\Extension\Google\Architecture\Enum\ActionEnum;
use Chamilo\Application\Calendar\Extension\Google\Service\CalendarService;
use Chamilo\Libraries\Architecture\Domain\Application;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\UserInterface\Layout\Service\ApplicationHeaderRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\DefaultFooterRenderer;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Calendar\Extension\Google
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class Manager extends Application
{
    public const string CONTEXT = __NAMESPACE__;

    protected CalendarService $calendarService;

    public function __construct(
        ChamiloRequest $request, ApplicationHeaderRenderer $applicationHeaderRenderer,
        DefaultFooterRenderer $defaultFooterRenderer, Translator $translator, CalendarService $calendarService,
        UrlGenerator $urlGenerator
    )
    {
        parent::__construct($request, $applicationHeaderRenderer, $defaultFooterRenderer, $translator, $urlGenerator);

        $this->calendarService = $calendarService;
    }

    public function getApplicationAction(): string
    {
        return ActionEnum::getActionValue(static::class);
    }

    public function getApplicationContext(): string
    {
        return self::CONTEXT;
    }

    public function getCalendarService(): CalendarService
    {
        return $this->calendarService;
    }

    public function getDefaultApplicationAction(): string
    {
        return ActionEnum::LOGIN->value;
    }
}
