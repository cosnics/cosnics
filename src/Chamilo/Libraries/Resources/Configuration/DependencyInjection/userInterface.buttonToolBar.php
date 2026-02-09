<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\ButtonRendererCollection;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonRendererInterface;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Service\ButtonGroupRenderer;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Service\ButtonRenderer;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Service\ButtonToolBarRenderer;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Service\DropDownButtonRenderer;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Service\MiniButtonToolBarRenderer;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Service\SplitDropdownButtonRenderer;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Service\SubButtonDividerRenderer;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Service\SubButtonHeaderRenderer;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Service\SubButtonRenderer;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(ButtonRendererCollection::class);
    $services->set(ButtonToolBarRenderer::class);
    $services->set(MiniButtonToolBarRenderer::class);
    $services->set(ButtonGroupRenderer::class)->tag(ButtonRendererInterface::class);
    $services->set(ButtonRenderer::class)->tag(ButtonRendererInterface::class);
    $services->set(DropDownButtonRenderer::class)->tag(ButtonRendererInterface::class);
    $services->set(SplitDropdownButtonRenderer::class)->tag(ButtonRendererInterface::class);
    $services->set(SubButtonDividerRenderer::class)->tag(ButtonRendererInterface::class);
    $services->set(SubButtonHeaderRenderer::class)->tag(ButtonRendererInterface::class);
    $services->set(SubButtonRenderer::class)->tag(ButtonRendererInterface::class);
};
