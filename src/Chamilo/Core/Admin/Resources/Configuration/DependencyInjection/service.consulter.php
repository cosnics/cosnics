<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Core\Admin\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\Admin\Service\Consulter\LanguageConsulter;
use Chamilo\Core\Admin\Service\DataLoader\FileConfigurationCacheDataPreLoader;
use Chamilo\Core\Admin\Service\DataLoader\StorageConfigurationCacheDataPreLoader;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(ConfigurationConsulter::class)->args([service(StorageConfigurationCacheDataPreLoader::class)]);

    $services->set('Chamilo\Core\Admin\Service\Consulter\FileConfigurationConsulter', ConfigurationConsulter::class)
        ->args(['$dataPreLoader' => service(FileConfigurationCacheDataPreLoader::class)]);

    $services->set(LanguageConsulter::class);
};