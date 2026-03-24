<?php
namespace Chamilo\Application\Calendar\Component;

use Chamilo\Application\Calendar\Architecture\Domain\CalendarExtensionActionProviderRegistry;
use Chamilo\Application\Calendar\Architecture\Domain\CalendarExtensionDataProviderRegistry;
use Chamilo\Application\Calendar\Manager;
use Chamilo\Application\Calendar\Service\CalendarDataProvider;
use Chamilo\Application\Calendar\Storage\Repository\VisibilityRepository;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Service\UserSettingsService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Calendar\Factory\HtmlCalendarRendererFactory;
use Chamilo\Libraries\Calendar\Service\CalendarTableConfigurationBuilder;
use Chamilo\Libraries\Filesystem\Service\WebPathBuilder;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\UserInterface\Layout\Architecture\Domain\PageHeaders;
use Chamilo\Libraries\UserInterface\Layout\Service\ApplicationHeaderRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\BaseFooterRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\BaseHeaderRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\DefaultFooterRenderer;
use Chamilo\Libraries\UserInterface\Theme\Service\ThemePathBuilder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Calendar\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class PrintComponent extends BrowseComponent
{
    protected BaseFooterRenderer $baseFooterRenderer;

    protected BaseHeaderRenderer $baseHeaderRenderer;

    public function __construct(
        ChamiloRequest $request, ApplicationHeaderRenderer $applicationHeaderRenderer,
        DefaultFooterRenderer $defaultFooterRenderer, Translator $translator,
        VisibilityRepository $visibilityRepository, ThemePathBuilder $themeWebPathBuilder, PageHeaders $pageHeaders,
        WebPathBuilder $webPathBuilder, UserService $userService, UrlGenerator $urlGenerator,
        BaseHeaderRenderer $baseHeaderRenderer, BaseFooterRenderer $baseFooterRenderer,
        CalendarDataProvider $calendarDataProvider,
        CalendarExtensionActionProviderRegistry $calendarExtensionActionProviderRegistry,
        CalendarExtensionDataProviderRegistry $calendarExtensionDataProviderRegistry,
        HtmlCalendarRendererFactory $htmlCalendarRendererFactory,
        CalendarTableConfigurationBuilder $calendarTableConfigurationBuilder, UserSettingsService $userSettingsService,
        string $defaultView
    )
    {
        parent::__construct(
            $request, $applicationHeaderRenderer, $defaultFooterRenderer, $translator, $visibilityRepository,
            $themeWebPathBuilder, $pageHeaders, $webPathBuilder, $userService, $urlGenerator, $calendarDataProvider,
            $calendarExtensionActionProviderRegistry, $calendarExtensionDataProviderRegistry,
            $htmlCalendarRendererFactory, $calendarTableConfigurationBuilder, $userSettingsService, $defaultView
        );

        $this->baseHeaderRenderer = $baseHeaderRenderer;
        $this->baseFooterRenderer = $baseFooterRenderer;
    }

    public function run(?User $currentUser = null): Response
    {
        $this->checkAuthorization(Manager::CONTEXT);

        $this->getPageHeaders()->addCss(
            $this->getWebPathBuilder()->getCssPath(Manager::CONTEXT) . 'print.' .
            $this->getThemeWebPathBuilder()->getTheme() . '.min.css', 'print'
        );

        $html = [];

        $html[] = $this->getHeaderRenderer()->render();
        $html[] = $this->renderCalendar($currentUser);
        $html[] = '<script>';
        $html[] = 'window.print();';
        $html[] = '</script>';
        $html[] = $this->getFooterRenderer()->render();

        return new Response(implode(PHP_EOL, $html));
    }

    protected function getFooterRenderer(): BaseFooterRenderer
    {
        return $this->baseFooterRenderer;
    }

    protected function getHeaderRenderer(): BaseHeaderRenderer
    {
        return $this->baseHeaderRenderer;
    }
}
