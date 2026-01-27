<?php
namespace Chamilo\Libraries\Mail\Mailer;

use Chamilo\Core\Admin\Service\Consulter\ConfigurationConsulter;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Mail\Mailer
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class MailerFactory
{

    protected ConfigurationConsulter $configurationConsulter;

    protected Translator $translator;

    public function __construct(ConfigurationConsulter $configurationConsulter, Translator $translator)
    {
        $this->configurationConsulter = $configurationConsulter;
        $this->translator = $translator;
    }

    /**
     * @throws \Exception
     */
    public function getActiveMailer(): MailerInterface
    {
        $mailerClass = $this->getConfigurationConsulter()->getSetting(['Chamilo\Core\Admin', 'mailer']);

        if (!class_exists($mailerClass) || !is_subclass_of($mailerClass, MailerInterface::class))
        {
            throw new Exception($this->getTranslator()->trans('InvalidMailerClass', [], StringUtilities::LIBRARIES));
        }

        /**
         * @var \Chamilo\Libraries\Mail\Mailer\MailerInterface
         */
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get($mailerClass);
    }

    /**
     * @return string[]
     */
    public function getAvailableMailers(): array
    {
        $mailers = [];

        $mailers['Chamilo\Libraries\Mail\Mailer\PhpMailer\Mailer'] = 'PhpMailer';
        $mailers['Chamilo\Libraries\Mail\Mailer\Platform\Mailer'] = 'Platform Mailer';

        return $mailers;
    }

    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->configurationConsulter;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }
}
