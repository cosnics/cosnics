<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Calendar\Component;

use Chamilo\Application\Weblcms\Service\CalendarRendererProvider;
use Chamilo\Application\Weblcms\Tool\Implementation\Calendar\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Calendar\Architecture\Factory\HtmlCalendarRendererFactory;
use Chamilo\Libraries\Calendar\Service\View\HtmlCalendarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Translation\Translation;
use Mobile_Detect;

class BrowserComponent extends Manager
{
    public const FILTER_THIS_MONTH = 'month';

    public const FILTER_THIS_WEEK = 'week';

    public const FILTER_TODAY = 'today';

    public const PARAM_FILTER = 'filter';

    /**
     * @var \Chamilo\Application\Weblcms\Service\CalendarRendererProvider
     */
    private $calendarDataProvider;

    /**
     * @var int
     */
    private $currentTime;

    /**
     * @var \Chamilo\Application\Weblcms\Tool\Action\Component\BrowserComponent
     */
    private $defaultComponent;

    public function run()
    {
        $defaultComponent = $this->getDefaultComponent();
        $defaultComponent->checkAuthorization('');

        $html = [];

        $html[] = $this->render_header();
        $html[] = $defaultComponent->renderToolHeader();

        $html[] = '<div class="row">';
        $html[] = $this->renderCalendar();
        $html[] = '</div>';

        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * @param BreadcrumbTrail $breadcrumbtrail
     */
    public function addAdditionalBreadcrumbs(BreadcrumbTrail $breadcrumbtrail): void
    {
    }

    public function getAdditionalParameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = self::PARAM_BROWSE_PUBLICATION_TYPE;

        return parent::getAdditionalParameters($additionalParameters);
    }

    public function getCalendarDataProvider()
    {
        if (!isset($this->calendarDataProvider))
        {
            $displayParameters = $this->getDefaultComponent()->get_parameters();
            $displayParameters[HtmlCalendarRenderer::PARAM_TYPE] = $this->getCurrentRendererType();
            $displayParameters[HtmlCalendarRenderer::PARAM_TIME] = $this->getCurrentRendererTime();

            $this->calendarDataProvider = new CalendarRendererProvider(
                $this->getDefaultComponent(), $this->get_user(), $displayParameters
            );
        }

        return $this->calendarDataProvider;
    }

    protected function getCalendarRendererFactory(): HtmlCalendarRendererFactory
    {
        return $this->getService(HtmlCalendarRendererFactory::class);
    }

    /**
     * @return int
     */
    public function getCurrentRendererTime()
    {
        if (!isset($this->currentTime))
        {
            $this->currentTime = $this->getRequest()->query->get(HtmlCalendarRenderer::PARAM_TIME, time());
        }

        return $this->currentTime;
    }

    /**
     * @return string
     */
    public function getCurrentRendererType()
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

    /**
     * @return \Chamilo\Application\Weblcms\Tool\Action\Component\BrowserComponent
     */
    public function getDefaultComponent()
    {
        if (!isset($this->defaultComponent))
        {
            $this->defaultComponent = $this->getApplicationFactory()->getApplication(
                \Chamilo\Application\Weblcms\Tool\Action\Manager::CONTEXT,
                new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this)
            );
        }

        return $this->defaultComponent;
    }

    public function get_tool_actions()
    {
        $toolActions = [];

        $toolActions[] = new Button(
            Translation::get('ICalExternal'), new FontAwesomeGlyph('globe'),
            $this->get_url([\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_ICAL]),
            Button::DISPLAY_ICON_AND_LABEL
        );

        return $toolActions;
    }

    public function renderCalendar()
    {
        $renderer = $this->getCalendarRendererFactory()->getRenderer($this->getCurrentRendererType());

        return $renderer->render($this->getCalendarDataProvider(), $this->getCurrentRendererTime());
    }
}
