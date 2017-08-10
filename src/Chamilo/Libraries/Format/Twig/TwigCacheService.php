<?php

namespace Chamilo\Libraries\Format\Twig;

use Chamilo\Configuration\Configuration;
use Chamilo\Libraries\Cache\FileBasedCacheService;
use Chamilo\Libraries\File\Path;
use Hogent\Libraries\File\PackagesContentFinder\PackagesFilesFinder;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * Manages the cache for the twig templates
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TwigCacheService extends FileBasedCacheService
{
    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @param \Twig_Environment $twig
     * @param \Symfony\Component\Form\FormFactoryInterface $formFactory
     */
    public function __construct(\Twig_Environment $twig, FormFactoryInterface $formFactory)
    {
        $this->twig = $twig;
    }

    /**
     * Returns the path to the cache directory or file
     *
     * @return string
     */
    function getCachePath()
    {
        return Path::getInstance()->getCachePath(__NAMESPACE__);
    }

    /**
     * Clears the cache and warms it up again.
     */
    public function clearAndWarmUp()
    {
        return $this->clear()->warmUp();
    }

    /**
     * Warms up the cache.
     */
    public function warmUp()
    {
        $packagesFilesFinder =
            new PackagesFilesFinder(Path::getInstance(), Configuration::getInstance()->get_registration_contexts());
        
        $templatesPerPackage = $packagesFilesFinder->findFiles('Resources/Templates', '*.html.twig');

        $basePath = Path::getInstance()->getBasePath();
        $this->twig->getLoader()->addLoader(new \Twig_Loader_Filesystem(array($basePath)));

        foreach($templatesPerPackage as $package => $templates)
        {
            foreach($templates as $template)
            {
                $template = str_replace($basePath, '', $template);
                $this->twig->loadTemplate($template);
            }
        }

        return $this;
    }
}