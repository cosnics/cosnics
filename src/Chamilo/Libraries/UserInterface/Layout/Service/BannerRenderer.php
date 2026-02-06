<?php
namespace Chamilo\Libraries\UserInterface\Layout\Service;

use Chamilo\Core\Menu\UserInterface\MenuRenderer\MenuRenderer;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\Application;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\Breadcrumb\Architecture\Domain\BreadcrumbTrail;
use Chamilo\Libraries\UserInterface\Breadcrumb\Service\BreadcrumbTrailRenderer;
use Chamilo\Libraries\UserInterface\Layout\Architecture\Domain\PageConfiguration;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Format\Structure
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class BannerRenderer
{
    protected BreadcrumbTrail $breadcrumbTrail;

    private BreadcrumbTrailRenderer $breadcrumbTrailRenderer;

    private MenuRenderer $menuRenderer;

    private PageConfiguration $pageConfiguration;

    private SessionInterface $session;

    private Translator $translator;

    private UrlGenerator $urlGenerator;

    public function __construct(
        PageConfiguration $pageConfiguration, SessionInterface $session, Translator $translator,
        UrlGenerator $urlGenerator, MenuRenderer $menuRenderer, BreadcrumbTrail $breadcrumbTrail,
        BreadcrumbTrailRenderer $breadcrumbTrailRenderer
    )
    {
        $this->pageConfiguration = $pageConfiguration;
        $this->session = $session;
        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;
        $this->menuRenderer = $menuRenderer;
        $this->breadcrumbTrail = $breadcrumbTrail;
        $this->breadcrumbTrailRenderer = $breadcrumbTrailRenderer;
    }

    /**
     * @throws \Exception
     */
    public function render(?User $user = null): string
    {
        $pageConfiguration = $this->getPageConfiguration();
        $session = $this->getSession();
        $translator = $this->getTranslator();

        $html = [];

        if ($user instanceof User) {
            $userFullName = $user->getFullName();
        }
        else {
            $userFullName = '';
        }

        if (!is_null($session->get('_as_admin'))) {
            $link = $this->getUrlGenerator()->fromParameters([
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Application::PARAM_ACTION => Manager::ACTION_LOGIN_AS
            ]);

            $html[] = '<div class="warning-banner bg-warning text-warning">';
            $html[] = $translator->trans('LoggedInAsUser', [], 'Chamilo\Core\User');
            $html[] = ' ';
            $html[] = $userFullName;
            $html[] = ' ';
            $html[] = '<a href="' . $link . '">' . $translator->trans('Back', [], StringUtilities::LIBRARIES) . '</a>';
            $html[] = '</div>';
        }

        $html[] = $this->getMenuRenderer()->render($pageConfiguration->getContainerMode(), $user);

        if ($pageConfiguration->getViewMode() == PageConfiguration::VIEW_MODE_FULL) {
            $breadcrumbtrail = $this->getBreadcrumbTrail();
            $breadcrumbtrail->setContainerMode($pageConfiguration->getContainerMode());

            if ($breadcrumbtrail->size() > 0) {
                $html[] = $this->getBreadcrumbTrailRenderer()->render($breadcrumbtrail);
            }
        }

        return implode(PHP_EOL, $html);
    }

    public function getBreadcrumbTrail(): BreadcrumbTrail
    {
        return $this->breadcrumbTrail;
    }

    public function getBreadcrumbTrailRenderer(): BreadcrumbTrailRenderer
    {
        return $this->breadcrumbTrailRenderer;
    }

    public function getMenuRenderer(): MenuRenderer
    {
        return $this->menuRenderer;
    }

    public function getPageConfiguration(): PageConfiguration
    {
        return $this->pageConfiguration;
    }

    public function getSession(): SessionInterface
    {
        return $this->session;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return $this->urlGenerator;
    }
}
