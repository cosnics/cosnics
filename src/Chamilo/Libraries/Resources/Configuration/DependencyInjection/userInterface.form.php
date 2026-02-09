<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Libraries\UserInterface\Form\Factory\FormValidatorHtmlEditorOptionsFactory;
use Chamilo\Libraries\UserInterface\Form\Service\FormValidatorHtmlEditorRenderer;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(FormValidatorHtmlEditorRenderer::class);
    $services->set(FormValidatorHtmlEditorOptionsFactory::class);
};
