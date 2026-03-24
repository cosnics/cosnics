<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Libraries\UserInterface\Translation\Factory\TranslatorFactory;
use Symfony\Component\Translation\Translator;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(TranslatorFactory::class);

    $services->set(Translator::class)->factory(
        [service(TranslatorFactory::class), 'createTranslator']
    )->args(
        [
            '$locale' => '%cosnics.libraries.userInterface.translation.language.default%',
            '$fallbackLanguages' => '%cosnics.libraries.userInterface.translation.language.fallback%'
        ]
    );
};