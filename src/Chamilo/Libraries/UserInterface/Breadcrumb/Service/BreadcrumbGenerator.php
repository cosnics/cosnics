<?php
namespace Chamilo\Libraries\UserInterface\Breadcrumb\Service;

use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
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

    public function addDefaultApplicationBreadcrumbs(string $context, ?string $action, string $defaultAction): void
    {
        $breadcrumbs = [];

        $breadcrumbs[] = $this->getContextBreadcrumb($context);

        if ($action && $action !== $defaultAction) {
            $breadcrumbs[] = $this->getApplicationBreadcrumb($context, $action);
        }

        $this->getBreadcrumbTrail()->prependMultiple($breadcrumbs);
        $this->addRootBreadcrumb();
    }

    public function addRootAndTitleBreadcrumbs(string $title): void
    {
        $this->getBreadcrumbTrail()->prepend(
            new Breadcrumb($title)
        );

        $this->addRootBreadcrumb();
    }

    public function addRootBreadcrumb(): void
    {
        $this->getBreadcrumbTrail()->prepend(
            new Breadcrumb(
                $this->getSiteName(), $this->getWebPathBuilder()->getBasePath(), new FontAwesomeGlyph('home')
            )
        );
    }

    protected function getApplicationBreadcrumb(string $context, string $action): Breadcrumb
    {
        $componentUrl = $this->getUrlGenerator()->fromParameters(
            [
                ApplicationInterface::PARAM_CONTEXT => $context,
                ApplicationInterface::PARAM_ACTION => $action
            ]
        );

        return new Breadcrumb(
            $this->getTranslator()->trans($action . 'Component', [], $context), $componentUrl
        );
    }

    protected function getBreadcrumbTrail(): BreadcrumbTrail
    {
        return $this->breadcrumbTrail;
    }

    protected function setBreadcrumbTrail(BreadcrumbTrail $breadcrumbTrail): void
    {
        $this->breadcrumbTrail = $breadcrumbTrail;
    }

    protected function getClassnameUtilities(): ClassnameUtilities
    {
        return $this->classnameUtilities;
    }

    protected function getContextBreadcrumb(string $context): Breadcrumb
    {
        $packageUrl = $this->getUrlGenerator()->fromParameters(
            [
                ApplicationInterface::PARAM_CONTEXT => $context
            ]
        );

        return new Breadcrumb(
            $this->getTranslator()->trans('TypeName', [], $context), $packageUrl
        );
    }

    protected function getRequest(): ChamiloRequest
    {
        return $this->request;
    }

    protected function getSiteName(): string
    {
        return $this->siteName;
    }

    protected function getTranslator(): Translator
    {
        return $this->translator;
    }

    protected function getUrlGenerator(): UrlGenerator
    {
        return $this->urlGenerator;
    }

    protected function getWebPathBuilder(): WebPathBuilder
    {
        return $this->webPathBuilder;
    }
}