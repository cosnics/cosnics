<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Libraries\Protocol\Mail\Architecture\Interface\MailerInterface;
use Chamilo\Libraries\Protocol\Mail\Factory\MailerFactory;
use Chamilo\Libraries\Protocol\Mail\Service\PhpMailer\Mailer as PhpMailer;
use Chamilo\Libraries\Protocol\Mail\Service\Platform\Mailer as PlatformMailer;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set('Chamilo\Libraries\Protocol\Mail\ActiveMailer')->factory(
        [service(MailerFactory::class), 'getActiveMailer']
    );

    $services->set(MailerFactory::class)->args(
        ['$configuredMailerClass' => '%cosnics.libraries.protocol.mail.mailerClass%']
    );
    $services->set(PhpMailer::class)->args(
        [
            '$administratorName' => '%cosnics.libraries.userInterface.layout.administrator.name%',
            '$administratorEmail' => '%cosnics.libraries.userInterface.layout.administrator.email%',
            '$noRepyEmail' => '%cosnics.libraries.protocol.mail.noReplyEmail%',
        ]
    )->tag(MailerInterface::class);
    $services->set(PlatformMailer::class)->args([
        '$administratorName' => '%cosnics.libraries.userInterface.layout.administrator.name%',
        '$administratorEmail' => '%cosnics.libraries.userInterface.layout.administrator.email%',
        '$noRepyEmail' => '%cosnics.libraries.protocol.mail.noReplyEmail%',
    ])->tag(MailerInterface::class);
};
