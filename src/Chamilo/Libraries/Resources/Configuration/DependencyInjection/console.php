<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Libraries\Protocol\Console\Architecture\Domain\ClearCacheCommand;
use Chamilo\Libraries\Protocol\Console\Architecture\Domain\GenerateResourcesCommand;
use Chamilo\Libraries\Protocol\Console\Architecture\Domain\PreLoadCacheCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\HelperSet;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set('Chamilo\Libraries\Protocol\Console\Console', Application::class)->args([
        'Chamilo Console',
        '1.1',
    ])->call('setHelperSet', [service('Chamilo\Libraries\Protocol\Console\HelperSet')]);

    $services->set('Chamilo\Libraries\Protocol\Console\HelperSet', HelperSet::class)->factory(
        [service('Chamilo\Libraries\Protocol\Console\Console'), 'getHelperSet']
    );

    $services->set(PreLoadCacheCommand::class)->tag(Command::class);
    $services->set(ClearCacheCommand::class)->tag(Command::class);
    $services->set(GenerateResourcesCommand::class)->tag(Command::class);
};
