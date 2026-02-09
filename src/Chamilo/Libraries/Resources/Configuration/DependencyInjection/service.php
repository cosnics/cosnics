<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Libraries\Service\Diagnoser\Diagnoser;
use Chamilo\Libraries\Service\Diagnoser\DiagnoserCellRenderer;
use Chamilo\Libraries\Service\Resource\ResourceManager;
use Chamilo\Libraries\Service\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\Table\Service\SimpleTableRenderer;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(Diagnoser::class)->args(
        [
            '$installationDate' => '%cosnics.libraries.protocol.security.installDate%',
            '$diagnoserTableRenderer' => service('Chamilo\Libraries\Service\Diagnoser\DiagnoserTableRenderer')
        ]
    );

    $services->set(DiagnoserCellRenderer::class);

    $services->set('Chamilo\Libraries\Service\Diagnoser\DiagnoserTableRenderer', SimpleTableRenderer::class)->args(
        ['$cellRenderer' => service(DiagnoserCellRenderer::class)]
    );

    $services->set(StringUtilities::class);
    $services->set(DatetimeUtilities::class);
    $services->set(ResourceManager::class);
};
