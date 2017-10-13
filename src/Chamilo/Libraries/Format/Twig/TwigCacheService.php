<?php
namespace Chamilo\Libraries\Format\Twig;

use Chamilo\Configuration\Configuration;
use Chamilo\Libraries\Cache\FileBasedCacheService;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\PackagesContentFinder\PackagesFilesFinder;
use Chamilo\Libraries\File\PathBuilder;
use Symfony\Component\Form\FormFactoryInterface;

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
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     *
     * @param \Twig_Environment $twig
     * @param \Symfony\Component\Form\FormFactoryInterface $formFactory
     */
    public function __construct(\Twig_Environment $twig, FormFactoryInterface $formFactory)
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
     *
     * @see \Chamilo\Libraries\Cache\FileBasedCacheService::warmUp()
     */
    public function warmUp()
    {
        $packagesFilesFinder = new PackagesFilesFinder(
            PathBuilder::getInstance(),
            Configuration::getInstance()->get_registration_contexts());

        $templatesPerPackage = $packagesFilesFinder->findFiles('Resources/Templates', '*.html.twig');

        $basePath = Path::getInstance()->getBasePath();
        $this->twig->getLoader()->addLoader(new \Twig_Loader_Filesystem(array($basePath)));

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