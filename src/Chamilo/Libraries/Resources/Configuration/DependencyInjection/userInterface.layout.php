<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Libraries\UserInterface\Layout\Architecture\Domain\PageHeaders;
use Chamilo\Libraries\UserInterface\Layout\Service\BannerRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\BaseFooterRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\BaseHeaderRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\DefaultFooterRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\DefaultHeaderRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\PanelRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\ProgressBarRenderer;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(BannerRenderer::class);
    $services->set(PageHeaders::class);
    $services->set(BaseFooterRenderer::class);
    $services->set(BaseHeaderRenderer::class)->args(
        [
            '$themeWebPathBuilder' => service('Chamilo\Libraries\UserInterface\Theme\Service\ThemeWebPathBuilder'),
            '$siteName' => '%cosnics.libraries.userInterface.layout.site.name%',
            '$institutionName' => '%cosnics.libraries.userInterface.layout.institution.name%'
        ]
    );

    $services->set(DefaultHeaderRenderer::class);
    $services->set(DefaultFooterRenderer::class)->args(
        [
            '$administratorData' => '%cosnics.libraries.userInterface.layout.administrator%',
            '$institutionData' => '%cosnics.libraries.userInterface.layout.institution%'
        ]
    );

    $services->set(PanelRenderer::class);
    $services->set(ProgressBarRenderer::class);
};
