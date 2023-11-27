<?php
namespace Chamilo\Libraries\Mail\Mailer;

use Chamilo\Configuration\Configuration;
use Chamilo\Libraries\Architecture\Traits\DependencyInjectionContainerTrait;
use Chamilo\Libraries\Translation\Translation;
use TusPhp\Config;

/**
 * Factory to instantiate the mailer
 *
 * @package Chamilo\Libraries\Mail\Mailer
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class MailerFactory
{
    use DependencyInjectionContainerTrait;

    protected MailerRegistrar $mailerRegistrar;
    private Configuration $configuration;

    /**
     * Constructor
     * 
     * @param \Chamilo\Configuration\Configuration $configuration
     */
    public function __construct(Configuration $configuration = null)
    {
        if(!$configuration instanceof Configuration)
            $configuration = Configuration::getInstance();

        $this->configuration = $configuration;

        $this->initializeContainer();
        $this->mailerRegistrar = $this->getService(MailerRegistrar::class);
    }

    /**
     * Returns a list of available picture providers
     * 
     * @return string[]
     */
    public function getAvailableMailers()
    {
        return $this->mailerRegistrar->getRegisteredMailersByClassname();
    }

    /**
     * Returns the active mailer
     * 
     * @return \Chamilo\Libraries\Mail\Mailer\MailerInterface
     *
     * @throws \Exception
     */
    public function getActiveMailer()
    {
        $mailerClass = $this->configuration->get_setting(array('Chamilo\Core\Admin', 'mailer'));
        if (! class_exists($mailerClass))
        {
            throw new \Exception(Translation::getInstance()->getTranslation('InvalidMailerClass'));
        }
        
        return $this->mailerRegistrar->getMailerByClass($mailerClass);
    }
}
