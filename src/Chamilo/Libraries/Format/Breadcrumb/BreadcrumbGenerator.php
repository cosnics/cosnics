<?php
namespace Chamilo\Libraries\Format\Breadcrumb;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Configuration\Service\FileConfigurationLocator;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\WebPathBuilder;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Symfony\Component\Translation\Translator;

/**
 * Standard breadcrumb generator.
 * Generates a breadcrumb based on the package and component name. Includes the
 * possibility to add additional breadcrumbs between the package breadcrumb and the component breadcrumb
 *
 * @package Chamilo\Libraries\Format\Structure
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class BreadcrumbGenerator implements BreadcrumbGeneratorInterface
{

    protected BreadcrumbTrail $breadcrumbTrail;

    protected ClassnameUtilities $classnameUtilities;

    protected ConfigurationConsulter $configurationConsulter;

    protected FileConfigurationLocator $fileConfigurationLocator;

    protected ChamiloRequest $request;

    protected Translator $translator;

    protected UrlGenerator $urlGenerator;

    protected WebPathBuilder $webPathBuilder;

    public function __construct(
        ClassnameUtilities $classnameUtilities, UrlGenerator $urlGenerator, Translator $translator,
        FileConfigurationLocator $fileConfigurationLocator, ConfigurationConsulter $configurationConsulter,
        WebPathBuilder $webPathBuilder, BreadcrumbTrail $breadcrumbTrail, ChamiloRequest $request
    )
    {
        $this->classnameUtilities = $classnameUtilities;
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;
        $this->fileConfigurationLocator = $fileConfigurationLocator;
        $this->configurationConsulter = $configurationConsulter;
        $this->webPathBuilder = $webPathBuilder;
        $this->breadcrumbTrail = $breadcrumbTrail;
        $this->request = $request;
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
        // TODO: Can this be fixed more elegantly?
        if ($this->getFileConfigurationLocator()->isAvailable())
        {
            $siteName = $this->getConfigurationConsulter()->getSetting(['Chamilo\Core\Admin', 'site_name']);
        }
        else
        {
            $siteName = 'Chamilo';
        }

        $this->getBreadcrumbTrail()->add(
            new Breadcrumb($this->getWebPathBuilder()->getBasePath(), $siteName, new FontAwesomeGlyph('home'))
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

    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->configurationConsulter;
    }

    public function getFileConfigurationLocator(): FileConfigurationLocator
    {
        return $this->fileConfigurationLocator;
    }

    public function getRequest(): ChamiloRequest
    {
        return $this->request;
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