<?php
namespace Chamilo\Libraries\UserInterface\Breadcrumb\Service;

use Chamilo\Libraries\Architecture\Domain\Application;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Filesystem\Service\WebPathBuilder;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Service\Utilities\ClassnameUtilities;
use Chamilo\Libraries\UserInterface\Breadcrumb\Architecture\Domain\Breadcrumb;
use Chamilo\Libraries\UserInterface\Breadcrumb\Architecture\Domain\BreadcrumbTrail;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Symfony\Component\Translation\Translator;

/**
 * Standard breadcrumb generator.
 * Generates a breadcrumb based on the package and component name. Includes the
 * possibility to add additional breadcrumbs between the package breadcrumb and the component breadcrumb
 *
 * @package Chamilo\Libraries\UserInterface\Breadcrumb\Service
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class BreadcrumbGenerator
{
    protected BreadcrumbTrail $breadcrumbTrail;

    protected ClassnameUtilities $classnameUtilities;

    protected ChamiloRequest $request;

    protected string $siteName;

    protected Translator $translator;

    protected UrlGenerator $urlGenerator;

    protected WebPathBuilder $webPathBuilder;

    public function __construct(
        ClassnameUtilities $classnameUtilities, UrlGenerator $urlGenerator, Translator $translator,
        WebPathBuilder $webPathBuilder, BreadcrumbTrail $breadcrumbTrail, ChamiloRequest $request,
        string $siteName = 'Cosnics'
    )
    {
        $this->classnameUtilities = $classnameUtilities;
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;
        $this->webPathBuilder = $webPathBuilder;
        $this->breadcrumbTrail = $breadcrumbTrail;
        $this->request = $request;
        $this->siteName = $siteName;
    }

    public function addDefaultBreadcrumbs(string $context, ?string $action, string $defaultAction): void
    {
        $breadcrumbs = [];

        $breadcrumbs[] = $this->getRootBreadcrumb();
        $breadcrumbs[] = $this->getPackageBreadcrumb($context);

        if ($action && $action !== $defaultAction) {
            $breadcrumbs[] = $this->getComponentBreadcrumb($context, $action);
        }

        $this->getBreadcrumbTrail()->prependMultiple($breadcrumbs);
    }

    public function getBreadcrumbTrail(): BreadcrumbTrail
    {
        return $this->breadcrumbTrail;
    }

    public function setBreadcrumbTrail(BreadcrumbTrail $breadcrumbTrail): void
    {
        $this->breadcrumbTrail = $breadcrumbTrail;
    }

    public function getClassnameUtilities(): ClassnameUtilities
    {
        return $this->classnameUtilities;
    }

    public function getComponentBreadcrumb(string $context, string $action): Breadcrumb
    {
        $componentUrl = $this->getUrlGenerator()->fromParameters(
            [
                Application::PARAM_CONTEXT => $context,
                Application::PARAM_ACTION => $action
            ]
        );

        return new Breadcrumb(
            $componentUrl, $this->getTranslator()->trans($action . 'Component', [], $context)
        );
    }

    protected function getPackageBreadcrumb(string $context): Breadcrumb
    {
        $packageUrl = $this->getUrlGenerator()->fromParameters(
            [
                Application::PARAM_CONTEXT => $context
            ]
        );

        return new Breadcrumb(
            $packageUrl, $this->getTranslator()->trans('TypeName', [], $context)
        );
    }

    public function getRequest(): ChamiloRequest
    {
        return $this->request;
    }

    protected function getRootBreadcrumb(): Breadcrumb
    {
        return new Breadcrumb(
            $this->getWebPathBuilder()->getBasePath(), $this->getSiteName(), new FontAwesomeGlyph('home')
        );
    }

    public function getSiteName(): string
    {
        return $this->siteName;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return $this->urlGenerator;
    }

    public function getWebPathBuilder(): WebPathBuilder
    {
        return $this->webPathBuilder;
    }
}