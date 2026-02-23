<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Core\Home\UserInterface\HomeRenderer\TabRenderer;
use Chamilo\Libraries\UserInterface\Tab\Service\ActionRenderer;
use Chamilo\Libraries\UserInterface\Tab\Service\ActionsTabRenderer;
use Chamilo\Libraries\UserInterface\Tab\Service\ContentTabRenderer;
use Chamilo\Libraries\UserInterface\Tab\Service\FormTabGenerator;
use Chamilo\Libraries\UserInterface\Tab\Service\FormTabsGenerator;
use Chamilo\Libraries\UserInterface\Tab\Service\GenericTabRenderer;
use Chamilo\Libraries\UserInterface\Tab\Service\LinkTabRenderer;
use Chamilo\Libraries\UserInterface\Tab\Service\LinkTabsRenderer;
use Chamilo\Libraries\UserInterface\Tab\Service\TabsRenderer;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(FormTabGenerator::class);
    $services->set(FormTabsGenerator::class);
    $services->set(LinkTabRenderer::class);
    $services->set(LinkTabsRenderer::class);
    $services->set(ActionRenderer::class);
    $services->set(ActionsTabRenderer::class);
    $services->set(ContentTabRenderer::class);
    $services->set(GenericTabRenderer::class);
    $services->set(TabRenderer::class);
    $services->set(TabsRenderer::class);
};
