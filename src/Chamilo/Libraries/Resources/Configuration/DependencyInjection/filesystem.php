<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Libraries\Filesystem\Factory\HtmlPurifierFactory;
use Chamilo\Libraries\Filesystem\Service\Compression\ArchiveCreator;
use Chamilo\Libraries\Filesystem\Service\Compression\ZipArchiveFilecompression;
use Chamilo\Libraries\Filesystem\Service\ConfigurablePathBuilder;
use Chamilo\Libraries\Filesystem\Service\FilesystemTools;
use Chamilo\Libraries\Filesystem\Service\ImageConverter;
use Chamilo\Libraries\Filesystem\Service\SystemPathBuilder;
use Chamilo\Libraries\Filesystem\Service\WebPathBuilder;
use HTMLPurifier;
use Symfony\Component\Filesystem\Filesystem;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(HtmlPurifierFactory::class);
    $services->set(HTMLPurifier::class, HTMLPurifier::class)->factory(
        [service(HtmlPurifierFactory::class), 'buildHtmlPurifier']
    );

    $services->set(Filesystem::class);
    $services->set(FilesystemTools::class);

    $services->set(SystemPathBuilder::class);
    $services->set(WebPathBuilder::class);
    $services->set(ConfigurablePathBuilder::class)->args(['%cosnics.libraries.filesystem.path%']);

    $services->set(ArchiveCreator::class);
    $services->set(ZipArchiveFilecompression::class);

    $services->set(ImageConverter::class);
};