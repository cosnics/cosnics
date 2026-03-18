<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Libraries\Protocol\Security\Factory\CsrfTokenManagerFactory;
use Chamilo\Libraries\Protocol\Security\Factory\HashingAlgorithmFactory;
use Chamilo\Libraries\Protocol\Security\Factory\PasswordGeneratorFactory;
use Chamilo\Libraries\Protocol\Security\Service\Hashing\Haval256HashingAlgorithm;
use Chamilo\Libraries\Protocol\Security\Service\Hashing\Md5HashingAlgorithm;
use Chamilo\Libraries\Protocol\Security\Service\Hashing\Sha1HashingAlgorithm;
use Chamilo\Libraries\Protocol\Security\Service\Hashing\Sha512HashingAlgorithm;
use Chamilo\Libraries\Protocol\Security\Service\Hashing\WhirlpoolHashingAlgorithm;
use Chamilo\Libraries\Protocol\Security\Service\HashingAlgorithm;
use Chamilo\Libraries\Protocol\Security\Service\SecurityUtilities;
use Hackzilla\PasswordGenerator\Generator\PasswordGeneratorInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(SecurityUtilities::class);

    $services->set(HashingAlgorithm::class)->factory(
        [service(HashingAlgorithmFactory::class), 'getActiveHashingAlgorithm']
    );

    $services->set(Haval256HashingAlgorithm::class)->tag(HashingAlgorithm::class);
    $services->set(Md5HashingAlgorithm::class)->tag(HashingAlgorithm::class);
    $services->set(Sha1HashingAlgorithm::class)->tag(HashingAlgorithm::class);
    $services->set(Sha512HashingAlgorithm::class)->tag(HashingAlgorithm::class);
    $services->set(WhirlpoolHashingAlgorithm::class)->tag(HashingAlgorithm::class);

    $services->set(HashingAlgorithmFactory::class)->args(
        ['$configuredHashingAlgorithm' => '%cosnics.libraries.protocol.security.hashingAlgorithmClass%']
    );

    $services->alias(PasswordGeneratorInterface::class, 'Chamilo\Libraries\Protocol\Security\PasswordGenerator');

    $services->set('Chamilo\Libraries\Protocol\Security\PasswordGenerator')->factory(
        [service(PasswordGeneratorFactory::class), 'createPasswordGenerator']
    );

    $services->set(PasswordGeneratorFactory::class);

    $services->set(CsrfTokenManagerFactory::class);
    $services->set(CsrfTokenManager::class)->factory(
        [service(CsrfTokenManagerFactory::class), 'buildCsrfTokenManager']
    );
    $services->alias(CsrfTokenManagerInterface::class, CsrfTokenManager::class);
};
