<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Libraries\Protocol\Console\Service\ChamiloConnectionProvider;
use Doctrine\DBAL\Tools\Console\Command\RunSqlCommand;
use Symfony\Component\Console\Command\Command;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(ChamiloConnectionProvider::class);

    $services->set('Chamilo\Libraries\Protocol\Console\Architecture\Domain\RunSqlCommand', RunSqlCommand::class)->args(
        ['$connectionProvider' => service(ChamiloConnectionProvider::class)]
    )->tag(Command::class);
};
