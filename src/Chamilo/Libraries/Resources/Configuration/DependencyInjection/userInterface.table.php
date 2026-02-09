<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Libraries\UserInterface\Table\Factory\DataClassPropertyTableColumnFactory;
use Chamilo\Libraries\UserInterface\Table\Service\ArrayCollectionTableRenderer;
use Chamilo\Libraries\UserInterface\Table\Service\ListHtmlTableRenderer;
use Chamilo\Libraries\UserInterface\Table\Service\ListTableRenderer;
use Chamilo\Libraries\UserInterface\Table\Service\PageNavigationCalculator;
use Chamilo\Libraries\UserInterface\Table\Service\PageNavigationRenderer;
use Chamilo\Libraries\UserInterface\Table\Service\PropertiesTableRenderer;
use Chamilo\Libraries\UserInterface\Table\Service\RequestTableParameterValuesCompiler;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(ArrayCollectionTableRenderer::class);
    $services->set(PropertiesTableRenderer::class);
    $services->set(ListTableRenderer::class);
    $services->set(ListHtmlTableRenderer::class);

    $services->set(PageNavigationCalculator::class);
    $services->set(PageNavigationRenderer::class);

    $services->set(RequestTableParameterValuesCompiler::class);
    $services->set(DataClassPropertyTableColumnFactory::class);
};
