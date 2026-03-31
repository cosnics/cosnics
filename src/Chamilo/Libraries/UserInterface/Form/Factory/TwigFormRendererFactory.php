<?php
namespace Chamilo\Libraries\UserInterface\Form\Factory;

use Chamilo\Libraries\Filesystem\Service\SystemPathBuilder;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\Theme\Service\ThemePathBuilder;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Translation\Translator;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\RuntimeLoader\FactoryRuntimeLoader;

/**
 * @package Chamilo\Libraries\UserInterface\Form\Factory
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TwigFormRendererFactory
{
    public function __construct(
        protected SystemPathBuilder $systemPathBuilder, protected ThemePathBuilder $themeSystemPathBuilder,
        protected Translator $translator
    )
    {
    }

    /**
     * @throws \Twig\Error\LoaderError
     */
    public function getFormRenderer(): Environment
    {
        $loader = new FilesystemLoader([$this->themeSystemPathBuilder->getTemplatePath(StringUtilities::LIBRARIES)]);
        $loader->addPath($this->systemPathBuilder->getVendorPath() . 'symfony\twig-bridge\Resources\views\Form');

        $twig = new Environment($loader);
        $twig->addExtension(new TranslationExtension($this->translator));

        $formEngine = new TwigRendererEngine(
            ['form.bootstrap.html.twig'], $twig
        );

        $twig->addExtension(new FormExtension());

        $twig->addRuntimeLoader(new FactoryRuntimeLoader([
            FormRenderer::class => fn() => new FormRenderer($formEngine),
        ]));

        return $twig;
    }
}