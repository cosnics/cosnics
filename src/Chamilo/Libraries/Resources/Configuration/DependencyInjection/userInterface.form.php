<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Libraries\UserInterface\Form\Factory\FormFactoryBuilder;
use Chamilo\Libraries\UserInterface\Form\Factory\FormValidatorHtmlEditorOptionsFactory;
use Chamilo\Libraries\UserInterface\Form\Factory\TwigFormRendererFactory;
use Chamilo\Libraries\UserInterface\Form\Service\FormValidatorHtmlEditorRenderer;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormFactoryInterface;
use Twig\Environment;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(FormValidatorHtmlEditorRenderer::class);
    $services->set(FormValidatorHtmlEditorOptionsFactory::class);

    $services->set(FormFactoryBuilder::class);

    $services->set(FormFactory::class)->factory(
        [service(FormFactoryBuilder::class), 'createFormFactory']
    );
    $services->alias(FormFactoryInterface::class, FormFactory::class);

    $services->set(TwigFormRendererFactory::class)->args(
        [
            '$themeSystemPathBuilder' => service(
                'Chamilo\Libraries\UserInterface\Theme\Service\ThemeSystemPathBuilder'
            )
        ]
    );

    $services->set('Twig\Environment\Form', Environment::class)->factory(
        [service(TwigFormRendererFactory::class), 'getFormRenderer']
    );
};
