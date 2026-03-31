<?php
namespace Chamilo\Libraries\Protocol\Mail\Factory;

use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException;
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
    /**
     * @var \Chamilo\Libraries\Protocol\Mail\Architecture\Interface\MailerInterface[]
     */
    protected array $mailers = [];

    public function __construct(
        protected Translator $translator, protected string $configuredMailerClass
    )
    {
    }

    public function addMailer(MailerInterface $mailer): static
    {
        $this->mailers[get_class($mailer)] = $mailer;

        return $this;
    }

    public function getActiveMailer(): MailerInterface
    {
        try {
            return $this->getMailer($this->configuredMailerClass);
        }
        catch (NoSuchClassException) {
            return $this->getDefaultMailer();
        }
    }

    public function getDefaultMailer(): MailerInterface
    {
        return $this->mailers[Platform\Mailer::class];
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function getMailer(string $mailerClass): MailerInterface
    {
        if (!isset($this->mailers[$mailerClass])) {
            throw new NoSuchClassException(
                $mailerClass, MailerInterface::class
            );
        }

        return $this->mailers[$mailerClass];
    }
}
