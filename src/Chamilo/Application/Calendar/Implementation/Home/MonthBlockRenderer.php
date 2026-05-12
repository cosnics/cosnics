<?php
namespace Chamilo\Application\Calendar\Implementation\Home;

use Chamilo\Application\Calendar\Manager;
use Chamilo\Application\Calendar\Service\CalendarDataProvider;
use Chamilo\Application\Calendar\Storage\Repository\VisibilityRepository;
use Chamilo\Core\Home\Service\HomeService;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Core\Home\UserInterface\HomeRenderer\BlockRenderer;
use Chamilo\Core\User\Storage\Entity\User;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Calendar\Architecture\Enum\HtmlCalendarRendererTypeEnum;
use Chamilo\Libraries\Calendar\Service\CalendarTableConfigurationBuilder;
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
readonly class MonthBlockRenderer extends BlockRenderer
{
    public const string CONTEXT = Manager::CONTEXT;

    public function __construct(
        HomeService $homeService, UrlGenerator $urlGenerator, Translator $translator,
        protected MiniMonthCalendarRenderer $miniMonthCalendarRenderer, protected ChamiloRequest $request,
        protected VisibilityRepository $calendarRendererProviderRepository,
        protected CalendarDataProvider $calendarDataProvider,
        protected CalendarTableConfigurationBuilder $calendarTableConfigurationBuilder
    )
    {
        parent::__construct($homeService, $urlGenerator, $translator);
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

        $events = [];

        $calendarTableConfiguration = $this->calendarTableConfigurationBuilder->buildConfiguration($user);

        if ($user instanceof User) {
            $events = $this->calendarDataProvider->getEvents(
                $user, $this->miniMonthCalendarRenderer->getEventsStartTime(
                $calendarTableConfiguration, $this->getDisplayTime()
            ), $this->miniMonthCalendarRenderer->getEventsEndTime($calendarTableConfiguration, $this->getDisplayTime())
            );
        }

        return $this->miniMonthCalendarRenderer->renderCalendar(
            $calendarTableConfiguration, $events, $displayParameters, $this->getDisplayTime()
        );
    }

    protected function getDisplayTime(): int
    {
        return (int) $this->request->query->get('time', time());
    }

    public function getTitle(Element $block): string
    {
        return $this->translator->trans(date('F', $this->getDisplayTime()) . 'Long', [], StringUtilities::LIBRARIES) .
            ' ' . date('Y', $this->getDisplayTime());
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
