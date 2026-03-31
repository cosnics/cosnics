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
    public function __construct(
        ChamiloRequest $request, ApplicationHeaderRenderer $applicationHeaderRenderer,
        DefaultFooterRenderer $defaultFooterRenderer, Translator $translator, UrlGenerator $urlGenerator,
        VisibilityRepository $visibilityRepository, CalendarDataProvider $calendarDataProvider,
        CalendarExtensionActionProviderRegistry $calendarExtensionActionProviderRegistry,
        CalendarExtensionDataProviderRegistry $calendarExtensionDataProviderRegistry,
        CalendarDataProvider $calendarRendererProvider,
        CalendarTableConfigurationBuilder $calendarTableConfigurationBuilder, string $defaultView,
        HtmlCalendarRendererFactory $htmlCalendarRendererFactory, PageHeaders $pageHeaders, UserService $userService,
        UserSettingsService $userSettingsService, WebPathBuilder $webPathBuilder,
        protected BaseFooterRenderer $baseFooterRenderer, protected BaseHeaderRenderer $baseHeaderRenderer,
        string $theme, ?int $currentTime = null
    )
    {
        parent::__construct(
            $request, $applicationHeaderRenderer, $defaultFooterRenderer, $translator, $urlGenerator,
            $visibilityRepository, $calendarDataProvider, $calendarExtensionActionProviderRegistry,
            $calendarExtensionDataProviderRegistry, $calendarRendererProvider, $calendarTableConfigurationBuilder,
            $defaultView, $htmlCalendarRendererFactory, $pageHeaders, $userService, $userSettingsService,
            $webPathBuilder, $theme, $currentTime
        );
    }

    public function run(?User $currentUser = null): Response
    {
        $this->checkAuthorization(Manager::CONTEXT, $currentUser);

        $this->pageHeaders->addCss(
            $this->getWebPathBuilder()->getCssPath(Manager::CONTEXT) . 'print.' . $this->theme . '.min.css', 'print'
        );

        $html = [];

        $html[] = $this->baseHeaderRenderer->render();
        $html[] = $this->renderCalendar($currentUser);
        $html[] = '<script>';
        $html[] = 'window.print();';
        $html[] = '</script>';
        $html[] = $this->baseFooterRenderer->render();

        return new Response(implode(PHP_EOL, $html));
    }
}
