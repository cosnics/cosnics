<?php
namespace Chamilo\Libraries\Format\Twig;

use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Twig\Extension\DateExtension;
use Chamilo\Libraries\Format\Twig\Extension\ResourceManagementExtension;
use Chamilo\Libraries\Format\Twig\Extension\UrlGenerationExtension;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\ChainLoader;

/**
 * Builds the Twig_Environment
 *
 * @package Chamilo\Libraries\Format\Twig
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TwigEnvironmentFactory
{

    protected function addTwigExtensions(TranslatorInterface $translator, UrlGenerator $generator, Environment $twig)
    {
        $twig->addExtension(new TranslationExtension($translator));
        $twig->addExtension(new ResourceManagementExtension(ResourceManager::getInstance()));
        $twig->addExtension(new UrlGenerationExtension($generator));
        $twig->addExtension(new DateExtension());

        $twig->addExtension(new DebugExtension());
    }

    public function createEnvironment(?TranslatorInterface $translator = null, ?UrlGenerator $generator = null
    ): Environment
    {
        $loader = new ChainLoader([new TwigLoaderChamiloFilesystem()]);

        $options = [
            'debug' => true,
            'auto_reload' => true,
            'cache' => Path::getInstance()->getCachePath() . 'templates/'
        ];

        $twig = new Environment($loader, $options);

        $this->addTwigExtensions($translator, $generator, $twig);

        return $twig;
    }
}