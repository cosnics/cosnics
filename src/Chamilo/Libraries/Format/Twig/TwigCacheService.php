<?php
namespace Chamilo\Libraries\Format\Twig;

use Chamilo\Configuration\Configuration;
use Chamilo\Libraries\Cache\FileBasedCacheService;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\PackagesContentFinder\PackagesFilesFinder;
use Chamilo\Libraries\File\PathBuilder;
use Symfony\Component\Form\FormFactoryInterface;
use Twig\Loader\FilesystemLoader;

/**
 * Manages the cache for the twig templates
 *
 * @package Chamilo\Libraries\Format\Twig
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TwigCacheService extends FileBasedCacheService
{

    /**
     *
     * @var \Twig\Environment
     */
    protected $twig;

    /**
     *
     * @param \Twig\Environment $twig
     * @param \Symfony\Component\Form\FormFactoryInterface $formFactory
     */
    public function __construct(\Twig\Environment $twig, FormFactoryInterface $formFactory)
    {
        $this->twig = $twig;
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\FileBasedCacheService::getCachePath()
     */
    function getCachePath()
    {
        return Path::getInstance()->getCachePath(__NAMESPACE__);
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\FileBasedCacheService::clearAndWarmUp()
     */
    public function clearAndWarmUp()
    {
        return $this->clear()->warmUp();
    }

    /**
     * @return $this|TwigCacheService
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function warmUp()
    {
        $packagesFilesFinder = new PackagesFilesFinder(
            PathBuilder::getInstance(),
            Configuration::getInstance()->get_registration_contexts());

        $templatesPerPackage = $packagesFilesFinder->findFiles('Resources/Templates', '*.html.twig');

        $basePath = Path::getInstance()->getBasePath();
        $this->twig->getLoader()->addLoader(new FilesystemLoader(array($basePath)));

        foreach ($templatesPerPackage as $package => $templates)
        {
            foreach ($templates as $template)
            {
                $template = str_replace($basePath, '', $template);
                $this->twig->load($template);
            }
        }

        return $this;
    }
}
