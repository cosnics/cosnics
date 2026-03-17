<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\ButtonsType;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\CategoryType;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\HtmlEditorType;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\HtmlType;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\MessageType;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\VisualContentType;
use Chamilo\Libraries\UserInterface\Form\Factory\FormFactoryBuilder;
use Chamilo\Libraries\UserInterface\Form\Factory\HtmlEditorOptionsFactory;
use Chamilo\Libraries\UserInterface\Form\Factory\TwigFormRendererFactory;
use Chamilo\Libraries\UserInterface\Form\Service\FormValidatorHtmlEditorRenderer;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormTypeInterface;
use Twig\Environment;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(FormValidatorHtmlEditorRenderer::class);
    $services->set(HtmlEditorOptionsFactory::class);

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

    $services->set(ButtonsType::class)->tag(FormTypeInterface::class);
    $services->set(HtmlType::class)->tag(FormTypeInterface::class);
    $services->set(VisualContentType::class)->tag(FormTypeInterface::class);
    $services->set(MessageType::class)->tag(FormTypeInterface::class);
    $services->set(CategoryType::class)->tag(FormTypeInterface::class);
    $services->set(HtmlEditorType::class)->tag(FormTypeInterface::class);
};
