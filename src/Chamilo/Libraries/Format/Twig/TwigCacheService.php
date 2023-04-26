<?php
namespace Chamilo\Libraries\Format\Twig;

use Chamilo\Configuration\Service\Consulter\RegistrationConsulter;
use Chamilo\Libraries\Cache\FileBasedCacheService;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\File\PackagesContentFinder\PackagesFilesFinder;
use Chamilo\Libraries\File\SystemPathBuilder;
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

    protected SystemPathBuilder $pathBuilder;

    protected RegistrationConsulter $registrationConsulter;

    protected Environment $twig;

    public function __construct(
        ConfigurablePathBuilder $configurablePathBuilder, Environment $twig, SystemPathBuilder $pathBuilder,
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

    public function getPathBuilder(): SystemPathBuilder
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

    /**
     * @throws \Twig\Error\SyntaxError
     * @throws \Symfony\Component\Cache\Exception\CacheException
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\LoaderError
     * @throws \Exception
     */
    public function initializeCache()
    {
        $packagesFilesFinder = new PackagesFilesFinder(
            $this->getPathBuilder(), $this->getRegistrationConsulter()->getRegistrationContexts()
        );

        $templatesPerPackage = $packagesFilesFinder->findFiles('Resources/Templates', '*.html.twig');

        $basePath = $this->getPathBuilder()->getBasePath();

        $this->getTwig()->getLoader()->addLoader(new FilesystemLoader([$basePath]));

        foreach ($templatesPerPackage as $templates)
        {
            foreach ($templates as $template)
            {
                $template = str_replace($basePath, '', $template);
                $this->getTwig()->load($template);
            }
        }
    }
}