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
    public function __construct(
        protected ClassnameUtilities $classnameUtilities, protected UrlGenerator $urlGenerator,
        protected Translator $translator, protected WebPathBuilder $webPathBuilder,
        protected BreadcrumbTrail $breadcrumbTrail, protected ChamiloRequest $request,
        protected string $siteName = 'Cosnics'
    )
    {
    }

    public function addDefaultApplicationBreadcrumbs(string $context, ?string $action, string $defaultAction): void
    {
        $breadcrumbs = [];

        $breadcrumbs[] = $this->getContextBreadcrumb($context);

        if ($action && $action !== $defaultAction) {
            $breadcrumbs[] = $this->getApplicationBreadcrumb($context, $action);
        }

        $this->breadcrumbTrail->prependMultiple($breadcrumbs);
        $this->addRootBreadcrumb();
    }

    public function addRootAndTitleBreadcrumbs(string $title): void
    {
        $this->breadcrumbTrail->prepend(
            new Breadcrumb($title)
        );

        $this->addRootBreadcrumb();
    }

    public function addRootBreadcrumb(): void
    {
        $this->breadcrumbTrail->prepend(
            new Breadcrumb(
                $this->siteName, $this->webPathBuilder->getBasePath(), new FontAwesomeGlyph('home')
            )
        );
    }

    protected function getApplicationBreadcrumb(string $context, string $action): Breadcrumb
    {
        $componentUrl = $this->urlGenerator->fromParameters(
            [
                ApplicationInterface::PARAM_CONTEXT => $context,
                ApplicationInterface::PARAM_ACTION => $action
            ]
        );

        return new Breadcrumb(
            $this->translator->trans($action . 'Component', [], $context), $componentUrl
        );
    }

    protected function getContextBreadcrumb(string $context): Breadcrumb
    {
        $packageUrl = $this->urlGenerator->fromParameters(
            [
                ApplicationInterface::PARAM_CONTEXT => $context
            ]
        );

        return new Breadcrumb(
            $this->translator->trans('TypeName', [], $context), $packageUrl
        );
    }
}