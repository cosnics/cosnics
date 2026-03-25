<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Core\Menu\UserInterface\Form\ItemConfigurationFormType;
use Chamilo\Core\Menu\UserInterface\Form\ItemFormType;
use Chamilo\Core\Menu\UserInterface\Form\ItemTitleFormType;
use Chamilo\Core\Menu\UserInterface\Menu\ItemOptionsTreeDataProvider;
use Chamilo\Core\Menu\UserInterface\Menu\ItemTreeMenuDataProvider;
use Chamilo\Core\Menu\UserInterface\MenuRenderer\MenuRenderer;
use Chamilo\Core\Menu\UserInterface\Table\ItemTableRenderer;
use Chamilo\Libraries\Filesystem\Service\WebPathBuilder;
use Chamilo\Libraries\UserInterface\Tree\Service\JsTreeMenuDataProvider;
use Chamilo\Libraries\UserInterface\Tree\Service\OptionsTreeRenderer;
use Symfony\Component\Form\FormTypeInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(ItemTreeMenuDataProvider::class);
    $services->set(ItemOptionsTreeDataProvider::class);

    $services->set('Chamilo\Core\Menu\UserInterface\Menu\ItemJsTreeMenuDataProvider', JsTreeMenuDataProvider::class)
        ->args(['$treeMenuDataProvider' => service(ItemTreeMenuDataProvider::class)]);

    $services->set('Chamilo\Core\Menu\UserInterface\Menu\ItemOptionsTreeRenderer', OptionsTreeRenderer::class)->args(
        ['$optionsTreeDataProvider' => service(ItemOptionsTreeDataProvider::class)]
    );

    $services->set(MenuRenderer::class)->args([
        '$webPathBuilder' => service(WebPathBuilder::class),
        '$themeWebPathBuilder' => service('Chamilo\Libraries\UserInterface\Theme\Service\ThemeWebPathBuilder'),
        '$siteName' => '%cosnics.libraries.userInterface.layout.site.name%',
        '$brandPath' => [
            'context' => '%cosnics.libraries.userInterface.layout.brandPath.context%',
            'filename' => '%cosnics.libraries.userInterface.layout.brandPath.filename%',
            'extension' => '%cosnics.libraries.userInterface.layout.brandPath.extension%'
        ]
    ]);

    $services->set(ItemTableRenderer::class);
    $services->set(ItemFormType::class)->args(
        ['$optionsTreeRenderer' => service('Chamilo\Core\Menu\UserInterface\Menu\ItemOptionsTreeRenderer')]
    )->tag(FormTypeInterface::class);
    $services->set(ItemConfigurationFormType::class)->tag(FormTypeInterface::class);
    $services->set(ItemTitleFormType::class)->args(
        ['$defaultLanguage' => '%cosnics.libraries.userInterface.translation.language.default%']
    )->tag(FormTypeInterface::class);
};
