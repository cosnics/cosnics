<?php
namespace Chamilo\Libraries\Calendar\Service\View;

use Chamilo\Libraries\Calendar\Architecture\Domain\Event;
use Chamilo\Libraries\Calendar\Architecture\Interface\CalendarRendererProviderInterface;
use Chamilo\Libraries\Calendar\Architecture\Interface\VisibilitySupport;
use Chamilo\Libraries\Calendar\Service\LegendRenderer;
use Chamilo\Libraries\Calendar\Service\TableBuilder\CalendarTableBuilder;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\ButtonToolBar;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\DropDownButton;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\SubButton;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Interface\ButtonDisplayInterface;
use Chamilo\Libraries\UserInterface\ActionBar\Service\ButtonToolBarRenderer;
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
    public const PARAM_TIME = 'time';
    public const PARAM_TYPE = 'type';

    public const TYPE_DAY = 'Day';
    public const TYPE_LIST = 'List';
    public const TYPE_MONTH = 'Month';
    public const TYPE_WEEK = 'Week';

    protected LegendRenderer $legendRenderer;

    protected Translator $translator;

    protected UrlGenerator $urlGenerator;

    public function __construct(LegendRenderer $legendRenderer, UrlGenerator $urlGenerator, Translator $translator)
    {
        $this->legendRenderer = $legendRenderer;
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;
    }

    abstract public function render(
        CalendarRendererProviderInterface $dataProvider, int $displayTime, array $viewActions = []
    ): string;

    public function determineNavigationUrl(CalendarRendererProviderInterface $dataProvider): string
    {
        $parameters = $dataProvider->getDisplayParameters();
        $parameters[self::PARAM_TIME] = CalendarTableBuilder::TIME_PLACEHOLDER;

        return $this->getUrlGenerator()->fromParameters($parameters);
    }

    /**
     * @return \Chamilo\Libraries\Calendar\Architecture\Domain\Event[]
     */
    public function getEvents(CalendarRendererProviderInterface $dataProvider, int $startTime, int $endTime): array
    {
        $events = $dataProvider->getEventsInPeriod($startTime, $endTime);

        usort(
            $events, function (Event $eventLeft, Event $eventRight) {
            if ($eventLeft->getStartDate() < $eventRight->getStartDate())
            {
                return - 1;
            }
            elseif ($eventLeft->getStartDate() > $eventRight->getStartDate())
            {
                return 1;
            }
            else
            {
                return 0;
            }
        }
        );

        return $events;
    }

    public function getLegendRenderer(): LegendRenderer
    {
        return $this->legendRenderer;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return $this->urlGenerator;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function isEventSourceVisible(CalendarRendererProviderInterface $dataProvider, Event $event): bool
    {
        return $this->isSourceVisible($dataProvider, $event->getSource());
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function isSourceVisible(
        CalendarRendererProviderInterface $dataProvider, string $source, ?int $userIdentifier = null
    ): bool
    {
        if ($dataProvider instanceof VisibilitySupport)
        {
            return $dataProvider->isSourceVisible($source, $userIdentifier);
        }

        return true;
    }

    public function renderTypeButton(CalendarRendererProviderInterface $dataProvider): DropDownButton
    {
        $rendererTypes = [
            HtmlCalendarRenderer::TYPE_MONTH,
            HtmlCalendarRenderer::TYPE_WEEK,
            HtmlCalendarRenderer::TYPE_DAY,
            HtmlCalendarRenderer::TYPE_LIST
        ];

        $displayParameters = $dataProvider->getDisplayParameters();
        $currentRendererType = $displayParameters[self::PARAM_TYPE];
        $translator = $this->getTranslator();

        $button = new DropDownButton(
            $translator->trans($currentRendererType . 'View', [], 'Chamilo\Libraries'),
            new FontAwesomeGlyph('calendar-alt'), ButtonDisplayInterface::DISPLAY_ICON_AND_LABEL, [],
            ['dropdown-menu-right']
        );

        foreach ($rendererTypes as $rendererType)
        {
            $displayParameters[self::PARAM_TYPE] = $rendererType;

            $button->addDropDownButton(
                new SubButton(
                    $translator->trans($rendererType . 'View', [], 'Chamilo\Libraries'), null,
                    $this->getUrlGenerator()->fromParameters($displayParameters), ButtonDisplayInterface::DISPLAY_LABEL,
                    null, [], null, $currentRendererType == $rendererType
                )
            );
        }

        return $button;
    }

    /**
     * @throws \QuickformException
     */
    public function renderViewActions(CalendarRendererProviderInterface $dataProvider, array $viewActions = []): string
    {
        $buttonToolBar = new ButtonToolBar();

        foreach ($viewActions as $viewAction)
        {
            $buttonToolBar->addButton($viewAction);
        }

        $buttonToolBar->addButton($this->renderTypeButton($dataProvider));

        $buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolBar);

        return $buttonToolbarRenderer->render();
    }
}
