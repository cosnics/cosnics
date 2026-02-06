<?php
namespace Chamilo\Libraries\UserInterface\Breadcrumb\Service;

use Chamilo\Core\Admin\Service\FileConfigurationLocator;
use Chamilo\Libraries\Architecture\Domain\Application;
use Chamilo\Libraries\Filesystem\Service\WebPathBuilder;
use Chamilo\Libraries\Platform\ChamiloRequest;
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

    protected FileConfigurationLocator $fileConfigurationLocator;

    protected ChamiloRequest $request;

    protected string $siteName;

    protected Translator $translator;

    protected UrlGenerator $urlGenerator;

    protected WebPathBuilder $webPathBuilder;

    public function __construct(
        ClassnameUtilities $classnameUtilities, UrlGenerator $urlGenerator, Translator $translator,
        FileConfigurationLocator $fileConfigurationLocator, WebPathBuilder $webPathBuilder,
        BreadcrumbTrail $breadcrumbTrail, ChamiloRequest $request, string $siteName = 'Cosnics'
    )
    {
        $this->classnameUtilities = $classnameUtilities;
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;
        $this->fileConfigurationLocator = $fileConfigurationLocator;
        $this->webPathBuilder = $webPathBuilder;
        $this->breadcrumbTrail = $breadcrumbTrail;
        $this->request = $request;
        $this->siteName = $siteName;
    }

    public function addComponentBreadcrumb(Application $application): void
    {
        $request = $this->getRequest();
        $context = $request->query->get(Application::PARAM_CONTEXT);

        $componentUrl = $this->getUrlGenerator()->fromParameters(
            [
                Application::PARAM_CONTEXT => $context,
                Application::PARAM_ACTION => $request->query->get(Application::PARAM_ACTION)
            ]
        );

        $variable = $this->getClassnameUtilities()->getClassnameFromNamespace($application::class);

        $this->getBreadcrumbTrail()->add(
            new Breadcrumb(
                $componentUrl, $this->getTranslator()->trans($variable, [], $context)
            )
        );
    }

    public function addDefaultBreadcrumbs(): void
    {
        $this->generateRootBreadcrumb();
        $this->generatePackageBreadcrumb();
    }

    protected function generatePackageBreadcrumb(): void
    {
        $request = $this->getRequest();

        $packageUrl = $this->getUrlGenerator()->fromParameters(
            [
                Application::PARAM_CONTEXT => $request->query->get(Application::PARAM_CONTEXT)
            ]
        );

        $this->getBreadcrumbTrail()->add(
            new Breadcrumb(
                $packageUrl,
                $this->getTranslator()->trans('TypeName', [], $request->query->get(Application::PARAM_CONTEXT))
            )
        );
    }

    protected function generateRootBreadcrumb(): void
    {
        $this->getBreadcrumbTrail()->add(
            new Breadcrumb(
                $this->getWebPathBuilder()->getBasePath(), $this->getSiteName(), new FontAwesomeGlyph('home')
            )
        );
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

    public function getFileConfigurationLocator(): FileConfigurationLocator
    {
        return $this->fileConfigurationLocator;
    }

    public function getRequest(): ChamiloRequest
    {
        return $this->request;
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