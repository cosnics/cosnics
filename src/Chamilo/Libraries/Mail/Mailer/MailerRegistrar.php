<?php

namespace Chamilo\Libraries\Mail\Mailer;

class MailerRegistrar
{
    /** @var MailerInterface[] */
    protected array $registeredMailers = [];

    public function getRegisteredMailersByClassname(): array
    {
        $availableMailers = [];

        foreach($this->registeredMailers as $registeredMailer)
        {
            $availableMailers[get_class($registeredMailer)] = $registeredMailer->getMailerName();
        }

        return $availableMailers;
    }

    public function getMailerByClass(string $className)
    {
        if(!array_key_exists($className, $this->registeredMailers))
        {
            throw new \Exception(sprintf('Could not find mailer with classname %s', $className));
        }

        return $this->registeredMailers[$className];
    }

    public function addRegisteredMailer(MailerInterface $mailer)
    {
        $this->registeredMailers[get_class($mailer)] = $mailer;
    }


}