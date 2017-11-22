<?php
namespace Chamilo\Libraries\Mail\Mailer;

use Chamilo\Configuration\Configuration;
use Chamilo\Libraries\Translation\Translation;

/**
 * Factory to instantiate the mailer
 *
 * @package Chamilo\Libraries\Mail\Mailer
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class MailerFactory
{

    /**
     * The configuration
     * 
     * @var \Chamilo\Configuration\Configuration
     */
    protected $configuration;

    /**
     * Constructor
     * 
     * @param \Chamilo\Configuration\Configuration $configuration
     */
    public function __construct(Configuration $configuration = null)
    {
        if (is_null($configuration) || ! $configuration instanceof Configuration)
        {
            $configuration = Configuration::getInstance();
        }
        
        $this->configuration = $configuration;
    }

    /**
     * Returns a list of available picture providers
     * 
     * @return string[]
     */
    public function getAvailableMailers()
    {
        $mailers = array();
        
        $mailerPackages = $this->configuration->get_registrations_by_type(__NAMESPACE__);
        
        foreach ($mailerPackages as $package)
        {
            /** @var MailerInterface|string $mailerClass */
            $mailerClass = $package['context'] . '\Mailer';
            
            if (class_exists($mailerClass))
            {
                $mailers[$mailerClass] = Translation::getInstance()->getTranslation('TypeName', array(), $package['context']);
            }
        }
        
        return $mailers;
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
        
        return new $mailerClass($this->configuration);
    }
}
