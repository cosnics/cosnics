<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Core\Menu\Component\BrowseComponent;
use Chamilo\Core\Menu\Component\CreateComponent;
use Chamilo\Core\Menu\Component\DeleteComponent;
use Chamilo\Core\Menu\Component\ItemTreeDataComponent;
use Chamilo\Core\Menu\Component\MoveComponent;
use Chamilo\Core\Menu\Component\UpdateComponent;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(BrowseComponent::class)->tag(ApplicationInterface::class);
    $services->set(CreateComponent::class)->args(['$twigFormEnvironment' => service('Twig\Environment\Form')])->tag(
        ApplicationInterface::class
    );
    $services->set(DeleteComponent::class)->tag(ApplicationInterface::class);
    $services->set(UpdateComponent::class)->args(['$twigFormEnvironment' => service('Twig\Environment\Form')])->tag(
        ApplicationInterface::class
    );
    $services->set(ItemTreeDataComponent::class)->args(
        ['$jsTreeMenuDataProvider' => service('Chamilo\Core\Menu\UserInterface\Menu\ItemJsTreeMenuDataProvider')]
    )->tag(ApplicationInterface::class);
    $services->set(MoveComponent::class)->tag(ApplicationInterface::class);
};
