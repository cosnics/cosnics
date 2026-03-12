<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Core\Group\Component\BrowseComponent;
use Chamilo\Core\Group\Component\BrowseNonSubscribedUsersComponent;
use Chamilo\Core\Group\Component\CreateComponent;
use Chamilo\Core\Group\Component\DeleteComponent;
use Chamilo\Core\Group\Component\GroupFeedComponent;
use Chamilo\Core\Group\Component\GroupTreeDataComponent;
use Chamilo\Core\Group\Component\GroupXmlFeedComponent;
use Chamilo\Core\Group\Component\MoveComponent;
use Chamilo\Core\Group\Component\SubscribeComponent;
use Chamilo\Core\Group\Component\TruncateComponent;
use Chamilo\Core\Group\Component\UnsubscribeComponent;
use Chamilo\Core\Group\Component\UpdateComponent;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(BrowseComponent::class)->tag(ApplicationInterface::class);
    $services->set(BrowseNonSubscribedUsersComponent::class)->tag(ApplicationInterface::class);
    $services->set(CreateComponent::class)->tag(ApplicationInterface::class);
    $services->set(DeleteComponent::class)->tag(ApplicationInterface::class);
    $services->set(GroupFeedComponent::class)->tag(ApplicationInterface::class);
    $services->set(GroupTreeDataComponent::class)->args(
        ['$jsTreeMenuDataProvider' => service('Chamilo\Core\Group\UserInterface\Menu\GroupJsTreeMenuDataProvider')]
    )->tag(ApplicationInterface::class);
    $services->set(GroupXmlFeedComponent::class)->tag(ApplicationInterface::class);
    $services->set(MoveComponent::class)->tag(ApplicationInterface::class);
    $services->set(SubscribeComponent::class)->tag(ApplicationInterface::class);
    $services->set(TruncateComponent::class)->tag(ApplicationInterface::class);
    $services->set(UnsubscribeComponent::class)->tag(ApplicationInterface::class);
    $services->set(UpdateComponent::class)->tag(ApplicationInterface::class);
};
