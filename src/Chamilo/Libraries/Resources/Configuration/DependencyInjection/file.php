<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Libraries\Filesystem\Factory\HtmlPurifierFactory;
use Chamilo\Libraries\Filesystem\Service\Compression\ArchiveCreator;
use Chamilo\Libraries\Filesystem\Service\Compression\ZipArchiveFilecompression;
use Chamilo\Libraries\Filesystem\Service\ConfigurablePathBuilder;
use Chamilo\Libraries\Filesystem\Service\FilesystemTools;
use Chamilo\Libraries\Filesystem\Service\SystemPathBuilder;
use Chamilo\Libraries\Filesystem\Service\WebPathBuilder;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(FilesystemTools::class);
    $services->set(SystemPathBuilder::class);
    $services->set(WebPathBuilder::class);
    $services->set(ArchiveCreator::class);
    $services->set(HtmlPurifierFactory::class);
    $services->set(ConfigurablePathBuilder::class)->args(['%chamilo.configuration.storage%']);
    $services->set(ZipArchiveFilecompression::class);
};
