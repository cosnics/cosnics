<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Libraries\Protocol\Security\Factory\HashingAlgorithmFactory;
use Chamilo\Libraries\Protocol\Security\Service\Hashing\Haval256HashingAlgorithm;
use Chamilo\Libraries\Protocol\Security\Service\Hashing\Md5HashingAlgorithm;
use Chamilo\Libraries\Protocol\Security\Service\Hashing\Sha1HashingAlgorithm;
use Chamilo\Libraries\Protocol\Security\Service\Hashing\Sha512HashingAlgorithm;
use Chamilo\Libraries\Protocol\Security\Service\Hashing\WhirlpoolHashingAlgorithm;
use Chamilo\Libraries\Protocol\Security\Service\HashingAlgorithm;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(HashingAlgorithm::class)->factory(
        [service(HashingAlgorithmFactory::class), 'getActiveHashingAlgorithm']
    );

    $services->set(Haval256HashingAlgorithm::class)->tag(HashingAlgorithm::class);
    $services->set(Md5HashingAlgorithm::class)->tag(HashingAlgorithm::class);
    $services->set(Sha1HashingAlgorithm::class)->tag(HashingAlgorithm::class);
    $services->set(Sha512HashingAlgorithm::class)->tag(HashingAlgorithm::class);
    $services->set(WhirlpoolHashingAlgorithm::class)->tag(HashingAlgorithm::class);

    $services->set(HashingAlgorithmFactory::class)->args(
        ['$configuredHashingAlgorithm' => '%chamilo.configuration.general.hashing_algorithm%']
    );
};
