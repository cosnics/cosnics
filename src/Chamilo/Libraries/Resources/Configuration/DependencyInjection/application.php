<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Component\CalendarPopupComponent;
use Chamilo\Libraries\Component\DeleteTemporaryFileComponent;
use Chamilo\Libraries\Component\UploadTemporaryFileComponent;
use Chamilo\Libraries\Component\UtilitiesComponent;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(CalendarPopupComponent::class)->args(
        ['$defaultFirstDayOfWeek' => '%cosnics.libraries.calendar.firstDayOfWeek%']
    )->tag(ApplicationInterface::class);
    $services->set(DeleteTemporaryFileComponent::class)->tag(ApplicationInterface::class);
    $services->set(UploadTemporaryFileComponent::class)->tag(ApplicationInterface::class);
    $services->set(UtilitiesComponent::class)->args(
        ['$themeWebPathBuilder' => service('Chamilo\Libraries\UserInterface\Theme\Service\ThemeWebPathBuilder')]
    )->tag(ApplicationInterface::class);
};