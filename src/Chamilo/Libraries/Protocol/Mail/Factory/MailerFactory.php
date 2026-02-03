<?php
namespace Chamilo\Libraries\Protocol\Mail\Factory;

use Chamilo\Core\Admin\Service\Consulter\ConfigurationConsulter;
use Chamilo\Libraries\Architecture\Exception\ClassNotExistException;
use Chamilo\Libraries\Protocol\Mail\Architecture\Interface\MailerInterface;
use Chamilo\Libraries\Protocol\Mail\Service\Platform;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Protocol\Mail\Factory
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class MailerFactory
{
    protected ConfigurationConsulter $configurationConsulter;

    protected string $configuredMailerClass;

    /**
     * @var \Chamilo\Libraries\Protocol\Mail\Architecture\Interface\MailerInterface[]
     */
    protected array $mailers = [];

    protected Translator $translator;

    public function __construct(
        ConfigurationConsulter $configurationConsulter, Translator $translator, string $configuredMailerClass
    )
    {
        $this->configurationConsulter = $configurationConsulter;
        $this->translator = $translator;
        $this->configuredMailerClass = $configuredMailerClass;
    }

    public function addMailer(MailerInterface $mailer): static
    {
        $this->mailers[get_class($mailer)] = $mailer;

        return $this;
    }

    public function getActiveMailer(): MailerInterface
    {
        try {
            return $this->getMailer($this->getConfiguredMailerClass());
        }
        catch (ClassNotExistException) {
            return $this->getDefaultMailer();
        }
    }

    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->configurationConsulter;
    }

    public function getConfiguredMailerClass(): string
    {
        return $this->configuredMailerClass;
    }

    public function getDefaultMailer(): MailerInterface
    {
        return $this->mailers[Platform\Mailer::class];
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exception\ClassNotExistException
     */
    public function getMailer(string $mailerClass): MailerInterface
    {
        if (!isset($this->mailers[$mailerClass])) {
            throw new ClassNotExistException($mailerClass);
        }

        return $this->mailers[$mailerClass];
    }

    /**
     * @return \Chamilo\Libraries\Protocol\Mail\Architecture\Interface\MailerInterface[]
     */
    public function getMailers(): array
    {
        return $this->mailers;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }
}
