<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\ButtonsFormType;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\CategoryFormType;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\ElementFinderFormType;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\HtmlEditorFormType;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\HtmlFormType;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\MessageFormType;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\PictureFormType;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\VisualContentFormType;
use Chamilo\Libraries\UserInterface\Form\Factory\FormFactoryBuilder;
use Chamilo\Libraries\UserInterface\Form\Factory\TwigFormRendererFactory;
use Chamilo\Libraries\UserInterface\Form\Service\FormButtonTypeBuilder;
use Chamilo\Libraries\UserInterface\Form\Service\FormTypeBuilder;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormTypeInterface;
use Twig\Environment;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

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

    $services->set(FormTypeBuilder::class);
    $services->set(FormButtonTypeBuilder::class);

    $services->set(ButtonsFormType::class)->tag(FormTypeInterface::class);
    $services->set(HtmlFormType::class)->tag(FormTypeInterface::class);
    $services->set(VisualContentFormType::class)->tag(FormTypeInterface::class);
    $services->set(MessageFormType::class)->tag(FormTypeInterface::class);
    $services->set(CategoryFormType::class)->tag(FormTypeInterface::class);
    $services->set(HtmlEditorFormType::class)->tag(FormTypeInterface::class);
    $services->set(ElementFinderFormType::class)->tag(FormTypeInterface::class);
    $services->set(PictureFormType::class)->tag(FormTypeInterface::class);
};
