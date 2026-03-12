<?php
namespace Chamilo\Libraries\Calendar\Service\View;

use Chamilo\Libraries\Architecture\Enum\DisplayTypeEnum;
use Chamilo\Libraries\Calendar\Architecture\Domain\CalendarTableConfiguration;
use Chamilo\Libraries\Calendar\Architecture\Domain\Event;
use Chamilo\Libraries\Calendar\Architecture\Enum\HtmlCalendarRendererTypeEnum;
use Chamilo\Libraries\Calendar\Service\LegendRenderer;
use Chamilo\Libraries\Calendar\Service\TableBuilder\CalendarTableBuilder;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\ButtonToolBar;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\DropDownButtonCollection;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\SubButton;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Service\ButtonToolBarRenderer;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Calendar\Service\View
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class HtmlCalendarRenderer extends CalendarRenderer
{
    public const string PARAM_TIME = 'time';
    public const string PARAM_TYPE = 'type';

    protected ButtonToolBarRenderer $buttonToolBarRenderer;

    protected LegendRenderer $legendRenderer;

    protected Translator $translator;

    protected UrlGenerator $urlGenerator;

    public function __construct(
        LegendRenderer $legendRenderer, UrlGenerator $urlGenerator, Translator $translator,
        ButtonToolBarRenderer $buttonToolBarRenderer
    )
    {
        $this->legendRenderer = $legendRenderer;
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;
        $this->buttonToolBarRenderer = $buttonToolBarRenderer;
    }

    /**
     * @param \Chamilo\Libraries\Calendar\Architecture\Domain\Event[] $events
     * @param \Chamilo\Libraries\Calendar\Architecture\Domain\Visibility[] $invisibleSources
     */
    abstract public function render(
        array $events, CalendarTableConfiguration $calendarTableConfiguration, array $displayParameters, int $displayTime, array $viewActions = [],
        array $invisibleSources = [], ?string $invisibilityContext = null
    ): string;

    public function determineNavigationUrl(array $parameters): string
    {
        $parameters[self::PARAM_TIME] = CalendarTableBuilder::TIME_PLACEHOLDER;

        return $this->getUrlGenerator()->fromParameters($parameters);
    }

    public function getButtonToolBarRenderer(): ButtonToolBarRenderer
    {
        return $this->buttonToolBarRenderer;
    }

    abstract public function getEventsEndTime(CalendarTableConfiguration $calendarTableConfiguration, int $displayTime): int;

    abstract public function getEventsStartTime(CalendarTableConfiguration $calendarTableConfiguration, int $displayTime): int;

    public function getLegendRenderer(): LegendRenderer
    {
        return $this->legendRenderer;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    public function getType(): string
    {
        return HtmlCalendarRendererTypeEnum::getTypeValue(static::class);
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return $this->urlGenerator;
    }

    public function isEventSourceVisible(Event $event, array $invisibleSources = []): bool
    {
        return $this->isSourceVisible($event->getSource(), $invisibleSources);
    }

    public function isSourceVisible(string $source, array $invisibleSources = []): bool
    {
        return !array_key_exists($source, $invisibleSources);
    }

    /**
     * @param \Chamilo\Libraries\Calendar\Architecture\Domain\Event[] $events
     *
     * @return \Chamilo\Libraries\Calendar\Architecture\Domain\Event[]
     */
    public function orderEvents(array $events): array
    {
        usort(
            $events, function (Event $eventLeft, Event $eventRight) {
            if ($eventLeft->getStartDate() < $eventRight->getStartDate()) {
                return - 1;
            }
            elseif ($eventLeft->getStartDate() > $eventRight->getStartDate()) {
                return 1;
            }
            else {
                return 0;
            }
        }
        );

        return $events;
    }

    public function renderTypeButton(array $displayParameters): DropDownButtonCollection
    {
        $rendererTypes = [
            HtmlCalendarRendererTypeEnum::MONTH->value,
            HtmlCalendarRendererTypeEnum::WEEK->value,
            HtmlCalendarRendererTypeEnum::DAY->value,
            HtmlCalendarRendererTypeEnum::LIST->value
        ];

        $currentRendererType = $displayParameters[self::PARAM_TYPE];
        $translator = $this->getTranslator();

        $button = new DropDownButtonCollection(
            $translator->trans($currentRendererType . 'View', [], 'Chamilo\Libraries'),
            new FontAwesomeGlyph('calendar-alt'), DisplayTypeEnum::ICON_AND_LABEL, [], ['dropdown-menu-right']
        );

        foreach ($rendererTypes as $rendererType) {
            $displayParameters[self::PARAM_TYPE] = $rendererType;

            $button->addButton(
                new SubButton(
                    $translator->trans($rendererType . 'View', [], 'Chamilo\Libraries'), null,
                    $this->getUrlGenerator()->fromParameters($displayParameters), DisplayTypeEnum::LABEL, null, [],
                    null, $currentRendererType == $rendererType
                )
            );
        }

        return $button;
    }

    /**
     * @throws \QuickformException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function renderViewActions(array $displayParameters, array $viewActions = []): string
    {
        $buttonToolBar = new ButtonToolBar();

        foreach ($viewActions as $viewAction) {
            $buttonToolBar->addButton($viewAction);
        }

        $buttonToolBar->addButton($this->renderTypeButton($displayParameters));

        return $this->getButtonToolBarRenderer()->render($buttonToolBar);
    }
}
