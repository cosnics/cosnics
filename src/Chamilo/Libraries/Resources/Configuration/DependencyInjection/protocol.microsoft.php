<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Libraries\Protocol\Microsoft\Graph\Factory\GraphServiceClientFactory;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Service\CalendarService;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Service\UserService;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\CalendarRepository;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\UserRepository;
use Microsoft\Graph\GraphServiceClient;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(GraphServiceClientFactory::class);

    $services->set(GraphServiceClient::class)->factory(
        [service(GraphServiceClientFactory::class), 'buildGraphServiceClient']
    );

    $services->set(CalendarRepository::class);
    $services->set(UserRepository::class);

    $services->set(UserService::class);
    $services->set(CalendarService::class);
};
