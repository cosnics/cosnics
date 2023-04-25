<?php
namespace Chamilo\Libraries\Format\Twig;

use Chamilo\Configuration\Service\Consulter\RegistrationConsulter;
use Chamilo\Libraries\Cache\FileBasedCacheService;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\File\PackagesContentFinder\PackagesFilesFinder;
use Chamilo\Libraries\File\PathBuilder;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * Manages the cache for the twig templates
 *
 * @package Chamilo\Libraries\Format\Twig
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TwigCacheService extends FileBasedCacheService
{

    protected PathBuilder $pathBuilder;

    protected RegistrationConsulter $registrationConsulter;

    protected Environment $twig;

    public function __construct(
        ConfigurablePathBuilder $configurablePathBuilder, Environment $twig, PathBuilder $pathBuilder,
        RegistrationConsulter $registrationConsulter
    )
    {
        parent::__construct($configurablePathBuilder);

        $this->twig = $twig;
        $this->pathBuilder = $pathBuilder;
        $this->registrationConsulter = $registrationConsulter;
    }

    public function getCachePath(): string
    {
        return $this->getConfigurablePathBuilder()->getCachePath(__NAMESPACE__);
    }

    public function getPathBuilder(): PathBuilder
    {
        return $this->pathBuilder;
    }

    public function getRegistrationConsulter(): RegistrationConsulter
    {
        return $this->registrationConsulter;
    }

    public function getTwig(): Environment
    {
        return $this->twig;
    }

    public function preLoadCacheData()
    {
        $packagesFilesFinder = new PackagesFilesFinder(
            $this->getPathBuilder(), $this->getRegistrationConsulter()->getRegistrationContexts()
        );

        $templatesPerPackage = $packagesFilesFinder->findFiles('Resources/Templates', '*.html.twig');

        $basePath = $this->getPathBuilder()->getBasePath();
        $this->getTwig()->getLoader()->addLoader(new FilesystemLoader([$basePath]));

        foreach ($templatesPerPackage as $package => $templates)
        {
            foreach ($templates as $template)
            {
                $template = str_replace($basePath, '', $template);
                $this->getTwig()->loadTemplate($template);
            }
        }
    }
}