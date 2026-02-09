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
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\UserInterface\Layout\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class BannerRenderer
{
    protected BreadcrumbTrail $breadcrumbTrail;

    private BreadcrumbTrailRenderer $breadcrumbTrailRenderer;

    private MenuRenderer $menuRenderer;

    private SessionInterface $session;

    private Translator $translator;

    private UrlGenerator $urlGenerator;

    public function __construct(
        SessionInterface $session, Translator $translator, UrlGenerator $urlGenerator, MenuRenderer $menuRenderer,
        BreadcrumbTrail $breadcrumbTrail, BreadcrumbTrailRenderer $breadcrumbTrailRenderer
    )
    {
        $this->session = $session;
        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;
        $this->menuRenderer = $menuRenderer;
        $this->breadcrumbTrail = $breadcrumbTrail;
        $this->breadcrumbTrailRenderer = $breadcrumbTrailRenderer;
    }

    public function render(?User $user = null): string
    {
        $session = $this->getSession();
        $translator = $this->getTranslator();

        $html = [];

        if (!is_null($session->get('_as_admin'))) {
            $link = $this->getUrlGenerator()->fromParameters([
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Application::PARAM_ACTION => Manager::ACTION_LOGIN_AS
            ]);

            $html[] = '<div class="warning-banner bg-warning text-warning">';
            $html[] = $translator->trans('LoggedInAsUser', [], 'Chamilo\Core\User');
            $html[] = ' ';
            $html[] = $user instanceof User ? $user->getFullName() : '';
            $html[] = ' ';
            $html[] = '<a href="' . $link . '">' . $translator->trans('Back', [], StringUtilities::LIBRARIES) . '</a>';
            $html[] = '</div>';
        }

        $html[] = $this->getMenuRenderer()->render($user);

        $breadcrumbtrail = $this->getBreadcrumbTrail();

        if ($breadcrumbtrail->count() > 0) {
            $html[] = $this->getBreadcrumbTrailRenderer()->render($breadcrumbtrail);
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
