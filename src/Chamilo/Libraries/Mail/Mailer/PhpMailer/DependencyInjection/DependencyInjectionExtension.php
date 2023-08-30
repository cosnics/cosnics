<?php
namespace Chamilo\Libraries\Mail\Mailer\PhpMailer\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;
use Chamilo\Libraries\DependencyInjection\Traits\ExtensionTrait;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * @package Chamilo\Libraries\Mail\Mailer\PhpMailer\DependencyInjection
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension implements ExtensionInterface
{
    use ExtensionTrait;

    public function getAlias()
    {
        return 'chamilo.libraries.mail.mailer.phpmailer';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Libraries\Mail\Mailer\PhpMailer' => ['package.xml']];
    }
}