<?php

namespace Chamilo\Libraries\DependencyInjection\CompilerPass;

use Chamilo\Libraries\DependencyInjection\CompilerPass\TaggedServicesCompilerPass;
use Chamilo\Libraries\Mail\Mailer\MailerInterface;
use Chamilo\Libraries\Mail\Mailer\MailerRegistrar;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Compiler pass to collect request validator extensions
 *
 * @package Chamilo\Core\Repository\DependencyInjection\CompilerPass
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class MailerCompilerPass extends TaggedServicesCompilerPass
{

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $this->addTaggedServicesToService(
            $container, MailerRegistrar::class, MailerInterface::class,
            'addRegisteredMailer'
        );
    }
}
