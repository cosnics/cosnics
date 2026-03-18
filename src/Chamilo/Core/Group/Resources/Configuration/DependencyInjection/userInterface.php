<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Core\Group\UserInterface\Form\GroupFormType;
use Chamilo\Core\Group\UserInterface\Form\GroupMoveFormType;
use Chamilo\Core\Group\UserInterface\Menu\GroupOptionsTreeDataProvider;
use Chamilo\Core\Group\UserInterface\Menu\GroupTreeMenuDataProvider;
use Chamilo\Core\Group\UserInterface\Table\GroupTableRenderer;
use Chamilo\Core\Group\UserInterface\Table\NonSubscribedUserTableRenderer;
use Chamilo\Core\Group\UserInterface\Table\SubscribedUserTableRenderer;
use Chamilo\Libraries\UserInterface\Tree\Service\JsTreeMenuDataProvider;
use Chamilo\Libraries\UserInterface\Tree\Service\OptionsTreeRenderer;
use Symfony\Component\Form\FormTypeInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(GroupTreeMenuDataProvider::class);
    $services->set(GroupOptionsTreeDataProvider::class);

    $services->set('Chamilo\Core\Group\UserInterface\Menu\GroupJsTreeMenuDataProvider', JsTreeMenuDataProvider::class)
        ->args(['$treeMenuDataProvider' => service(GroupTreeMenuDataProvider::class)]);

    $services->set('Chamilo\Core\Group\UserInterface\Menu\GroupOptionsTreeRenderer', OptionsTreeRenderer::class)->args(
        ['$optionsTreeDataProvider' => service(GroupOptionsTreeDataProvider::class)]
    );

    $services->set(GroupTableRenderer::class);
    $services->set(SubscribedUserTableRenderer::class);
    $services->set(NonSubscribedUserTableRenderer::class);

    $services->set(GroupMoveFormType::class)->args(
        ['$optionsTreeRenderer' => service('Chamilo\Core\Group\UserInterface\Menu\GroupOptionsTreeRenderer')]
    )->tag(FormTypeInterface::class);
    $services->set(GroupFormType::class)->args(
        ['$optionsTreeRenderer' => service('Chamilo\Core\Group\UserInterface\Menu\GroupOptionsTreeRenderer')]
    )->tag(FormTypeInterface::class);
};
