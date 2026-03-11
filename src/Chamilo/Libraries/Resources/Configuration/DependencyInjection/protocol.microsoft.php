<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Interface\UserExceptionRendererInterface;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Factory\GraphServiceClientFactory;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Service\CalendarService;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Service\GroupService;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Service\NoSuchCalendarExceptionRenderer;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Service\NoSuchGroupExceptionRenderer;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Service\NoSuchUserExceptionRenderer;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Service\TeamService;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Service\UserService;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\CalendarRepository;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GroupRepository;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\TeamRepository;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\UserRepository;
use Microsoft\Graph\GraphServiceClient;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(GraphServiceClientFactory::class);

    $services->set(GraphServiceClient::class)->factory(
        [service(GraphServiceClientFactory::class), 'buildGraphServiceClient']
    )->args([
        '$tenantId' => '%cosnics.libraries.protocol.microsoft.graph.tenantId%',
        '$clientId' => '%cosnics.libraries.protocol.microsoft.graph.clientId%',
        '$clientSecret' => '%cosnics.libraries.protocol.microsoft.graph.clientSecret%',
    ]);

    $services->set(CalendarRepository::class);
    $services->set(UserRepository::class);
    $services->set(GroupRepository::class);
    $services->set(TeamRepository::class);

    $services->set(UserService::class);
    $services->set(CalendarService::class);
    $services->set(GroupService::class)->args(
        ['$groupBaseUri' => '%cosnics.libraries.protocol.microsoft.graph.baseUri.group%']
    );
    $services->set(TeamService::class);

    $services->set(NoSuchCalendarExceptionRenderer::class)->tag(UserExceptionRendererInterface::class);
    $services->set(NoSuchUserExceptionRenderer::class)->tag(UserExceptionRendererInterface::class);
    $services->set(NoSuchGroupExceptionRenderer::class)->tag(UserExceptionRendererInterface::class);
};
