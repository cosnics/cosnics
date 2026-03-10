<?php
namespace Chamilo\Application\Calendar\Implementation\Home;

use Chamilo\Application\Calendar\Manager;
use Chamilo\Application\Calendar\Service\CalendarDataProvider;
use Chamilo\Application\Calendar\Storage\Repository\VisibilityRepository;
use Chamilo\Core\Home\Service\HomeService;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Core\Home\UserInterface\HomeRenderer\BlockRenderer;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Calendar\Architecture\Enum\HtmlCalendarRendererTypeEnum;
use Chamilo\Libraries\Calendar\Service\View\HtmlCalendarRenderer;
use Chamilo\Libraries\Calendar\Service\View\MiniMonthCalendarRenderer;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Calendar\Implementation\Home
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class MonthBlockRenderer extends BlockRenderer
{
    public const CONTEXT = Manager::CONTEXT;

    protected CalendarDataProvider $calendarDataProvider;

    protected VisibilityRepository $calendarRendererProviderRepository;

    protected MiniMonthCalendarRenderer $miniMonthCalendarRenderer;

    protected ChamiloRequest $request;

    public function __construct(
        HomeService $homeService, UrlGenerator $urlGenerator, Translator $translator,
        MiniMonthCalendarRenderer $miniMonthCalendarRenderer, ChamiloRequest $request,
        VisibilityRepository $calendarRendererProviderRepository, CalendarDataProvider $calendarDataProvider
    )
    {
        parent::__construct($homeService, $urlGenerator, $translator);

        $this->miniMonthCalendarRenderer = $miniMonthCalendarRenderer;
        $this->request = $request;
        $this->calendarRendererProviderRepository = $calendarRendererProviderRepository;
        $this->calendarDataProvider = $calendarDataProvider;
    }

    /**
     * @throws \Exception
     */
    public function displayContent(Element $block, ?User $user = null): string
    {
        $displayParameters = [
            ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
            HtmlCalendarRenderer::PARAM_TYPE => HtmlCalendarRendererTypeEnum::DAY->value
        ];

        $miniMonthCalendarRenderer = $this->getMiniMonthCalendarRenderer();
        $events = [];

        if ($user instanceof User) {
            $events = $this->getCalendarDataProvider()->getEvents(
                $user, $miniMonthCalendarRenderer->getEventsStartTime($this->getDisplayTime()),
                $miniMonthCalendarRenderer->getEventsEndTime($this->getDisplayTime())
            );
        }

        return $this->getMiniMonthCalendarRenderer()->renderCalendar(
            $events, $displayParameters, $this->getDisplayTime()
        );
    }

    protected function getCalendarDataProvider(): CalendarDataProvider
    {
        return $this->calendarDataProvider;
    }

    public function getCalendarRendererProviderRepository(): VisibilityRepository
    {
        return $this->calendarRendererProviderRepository;
    }

    protected function getDisplayTime(): int
    {
        return (int) $this->getRequest()->query->get('time', time());
    }

    public function getMiniMonthCalendarRenderer(): MiniMonthCalendarRenderer
    {
        return $this->miniMonthCalendarRenderer;
    }

    public function getRequest(): ChamiloRequest
    {
        return $this->request;
    }

    public function getTitle(Element $block): string
    {
        return $this->getTranslator()->trans(date('F', $this->getDisplayTime()) . 'Long', [], StringUtilities::LIBRARIES
            ) . ' ' . date('Y', $this->getDisplayTime());
    }

    public function renderContentFooter(): string
    {
        return '</div>';
    }

    public function renderContentHeader(Element $block): string
    {
        return '<div class="portal-block-content' . ($block->isVisible() ? '' : ' hidden') . '">';
    }
}
