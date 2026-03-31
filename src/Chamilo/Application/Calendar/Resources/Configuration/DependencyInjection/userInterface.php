<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Application\Calendar\UserInterface\Form\AvailabilityFormType;
use Symfony\Component\Form\FormTypeInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(AvailabilityFormType::class)->tag(FormTypeInterface::class);
};
