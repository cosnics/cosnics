<?php
namespace Chamilo\Libraries\Mail\Mailer;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Configuration\Service\Consulter\RegistrationConsulter;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\SystemPathBuilder;
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

    protected RegistrationConsulter $registrationConsulter;

    protected SystemPathBuilder $systemPathBuilder;

    protected Translator $translator;

    public function __construct(
        ConfigurationConsulter $configurationConsulter, RegistrationConsulter $registrationConsulter,
        SystemPathBuilder $systemPathBuilder, Translator $translator
    )
    {
        $this->configurationConsulter = $configurationConsulter;
        $this->registrationConsulter = $registrationConsulter;
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
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function getAvailableMailers(): array
    {
        $mailers = [];

        $mailerPackages = $this->getRegistrationConsulter()->getRegistrationsByType(__NAMESPACE__);

        foreach ($mailerPackages as $package)
        {
            $mailerClass = $package['context'] . '\Mailer';

            if (class_exists($mailerClass))
            {
                $mailers[$mailerClass] = $this->getTranslator()->trans('TypeName', [], $package['context']);
            }
        }

        return $mailers;
    }

    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->configurationConsulter;
    }

    public function getRegistrationConsulter(): RegistrationConsulter
    {
        return $this->registrationConsulter;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }
}
