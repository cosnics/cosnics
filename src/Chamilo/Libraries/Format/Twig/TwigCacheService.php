<?php
namespace Chamilo\Libraries\Format\Twig;

use Chamilo\Configuration\Configuration;
use Chamilo\Libraries\Cache\FileBasedCacheService;
use Chamilo\Libraries\File\PackagesContentFinder\PackagesFilesFinder;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\PathBuilder;
use Symfony\Component\Form\FormFactoryInterface;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * Manages the cache for the twig templates
 *
 * @package Chamilo\Libraries\Format\Twig
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TwigCacheService extends FileBasedCacheService
{

    protected Environment $twig;

    public function __construct(Environment $twig, FormFactoryInterface $formFactory)
    {
        $this->twig = $twig;
    }

    public function clearAndWarmUp()
    {
        return $this->clear()->warmUp();
    }

    public function getCachePath()
    {
        return Path::getInstance()->getCachePath(__NAMESPACE__);
    }

    public function warmUp()
    {
        $packagesFilesFinder = new PackagesFilesFinder(
            PathBuilder::getInstance(), Configuration::getInstance()->get_registration_contexts()
        );

        $templatesPerPackage = $packagesFilesFinder->findFiles('Resources/Templates', '*.html.twig');

        $basePath = Path::getInstance()->getBasePath();
        $this->twig->getLoader()->addLoader(new FilesystemLoader(array($basePath)));

        foreach ($templatesPerPackage as $package => $templates)
        {
            foreach ($templates as $template)
            {
                $template = str_replace($basePath, '', $template);
                $this->twig->loadTemplate($template);
            }
        }

        return $this;
    }
}