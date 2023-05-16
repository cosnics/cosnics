<?php

namespace Chamilo\Libraries\Format\Twig;

use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Twig\Extension\DateExtension;
use Chamilo\Libraries\Format\Twig\Extension\ResourceManagementExtension;
use Chamilo\Libraries\Format\Twig\Extension\UrlGenerationExtension;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\DebugExtension;
use Twig\Loader\ChainLoader;
use Twig\Loader\FilesystemLoader;
use Twig\RuntimeLoader\FactoryRuntimeLoader;

/**
 * Builds the Twig_Environment
 *
 * @package Chamilo\Libraries\Format\Twig
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TwigEnvironmentFactory
{
    /**
     * Initializes the twig templating for forms
     *
     * @param TranslatorInterface|null $translator
     * @param UrlGenerator|null $generator
     * @param CsrfTokenManagerInterface $csrfTokenManager
     *
     * @return \Twig\Environment
     */
    public function createEnvironment(
        CsrfTokenManagerInterface $csrfTokenManager,
        TranslatorInterface $translator = null, UrlGenerator $generator = null
    )
    {
        $loader = new ChainLoader(array(new TwigLoaderChamiloFilesystem()));

        $options = array(
            'debug' => true,
            'auto_reload' => true,
            'cache' => Path::getInstance()->getCachePath() . 'templates/'
        );

        $twig = new \Twig\Environment($loader, $options);

        $this->addTwigExtensions($translator, $generator, $twig);

        $chamiloFormTemplatesPath = __DIR__ . '/../../Resources/Templates/Form';

        $this->createFormLoader($twig, $chamiloFormTemplatesPath);
        $this->addFormExtension($twig, $chamiloFormTemplatesPath, $csrfTokenManager);

        return $twig;
    }

    /**
     * Adds the necessary extensions to twig
     *
     * @param \Symfony\Component\Translation\TranslatorInterface $translator
     * @param \Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator $generator
     * @param \Twig\Environment $twig
     */
    protected function addTwigExtensions(TranslatorInterface $translator, UrlGenerator $generator, $twig)
    {
        $twig->addExtension(new TranslationExtension($translator));
        $twig->addExtension(new ResourceManagementExtension(ResourceManager::getInstance()));
        $twig->addExtension(new UrlGenerationExtension($generator));
        $twig->addExtension(new DateExtension());

        $twig->addExtension(new DebugExtension());
    }

    /**
     * Adds the twig loaders that are necessary for the form templates
     *
     * @param \Twig\Environment $twig
     * @param string $chamiloFormTemplatesPath
     *
     * @throws \InvalidArgumentException
     */
    protected function createFormLoader(\Twig\Environment $twig, $chamiloFormTemplatesPath)
    {
        $vendorTwigBridgeDir = Path::getInstance()->getBasePath() . '../vendor/symfony/twig-bridge/';

        $formLoader = new FilesystemLoader(
            array($chamiloFormTemplatesPath, $vendorTwigBridgeDir . '/Resources/views/Form')
        );

        $twigLoader = $twig->getLoader();
        if (!$twigLoader instanceof ChainLoader)
        {
            throw new \InvalidArgumentException('The given Twig_Environment must use a chain loader');
        }

        $twigLoader->addLoader($formLoader);
    }

    /**
     * Adds the twig extension for the forms
     *
     * @param \Twig\Environment $twig
     * @param string $chamiloFormTemplatesPath
     * @param CsrfTokenManagerInterface $csrfTokenManager
     */
    protected function addFormExtension(
        \Twig\Environment $twig, $chamiloFormTemplatesPath, CsrfTokenManagerInterface $csrfTokenManager
    )
    {
        $chamiloFiles = Filesystem::get_directory_content($chamiloFormTemplatesPath, Filesystem::LIST_FILES, false);
        $twigRenderingFiles = array_merge(array('form_div_layout.html.twig'), $chamiloFiles);

        $formEngine = new TwigRendererEngine($twigRenderingFiles, $twig);
        $formRenderer = new FormRenderer($formEngine, $csrfTokenManager);

        $twig->addRuntimeLoader(
            new FactoryRuntimeLoader(
                array(FormRenderer::class => function () use ($formRenderer) { return $formRenderer; })
            )
        );

        $twig->addExtension(new FormExtension());
    }
}
