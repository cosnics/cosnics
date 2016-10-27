<?php
namespace Chamilo\Libraries\Platform\Session;

use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Configuration\Service\FileConfigurationLoader;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Factory\ConnectionFactory;

/**
 *
 * @package Chamilo\Libraries\Platform\Session
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class SessionUtilities
{

    /**
     *
     * @var \Chamilo\Configuration\Service\FileConfigurationLoader
     */
    private $fileConfigurationLoader;

    /**
     *
     * @var \Chamilo\Libraries\Storage\DataManager\Doctrine\Factory\ConnectionFactory
     */
    private $connectionFactory;

    /**
     *
     * @var \Chamilo\Configuration\Service\ConfigurationConsulter
     */
    private $configurationConsulter;

    /**
     *
     * @param \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter
     */
    public function __construct(FileConfigurationLoader $fileConfigurationLoader, ConnectionFactory $connectionFactory,
        ConfigurationConsulter $configurationConsulter)
    {
        $this->fileConfigurationLoader = $fileConfigurationLoader;
        $this->connectionFactory = $connectionFactory;
        $this->configurationConsulter = $configurationConsulter;
    }

    /**
     *
     * @return \Chamilo\Configuration\Service\FileConfigurationLoader
     */
    public function getFileConfigurationLoader()
    {
        return $this->fileConfigurationLoader;
    }

    /**
     *
     * @param \Chamilo\Configuration\Service\FileConfigurationLoader $fileConfigurationLoader
     */
    public function setFileConfigurationLoader(FileConfigurationLoader $fileConfigurationLoader)
    {
        $this->fileConfigurationLoader = $fileConfigurationLoader;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\DataManager\Doctrine\Factory\ConnectionFactory
     */
    public function getConnectionFactory()
    {
        return $this->connectionFactory;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Doctrine\Factory\ConnectionFactory $connectionFactory
     */
    public function setConnectionFactory(ConnectionFactory $connectionFactory)
    {
        $this->connectionFactory = $connectionFactory;
    }

    /**
     *
     * @return \Chamilo\Configuration\Service\ConfigurationConsulter
     */
    public function getConfigurationConsulter()
    {
        return $this->configurationConsulter;
    }

    /**
     *
     * @param \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter
     */
    public function setConfigurationConsulter(ConfigurationConsulter $configurationConsulter)
    {
        $this->configurationConsulter = $configurationConsulter;
    }

    public function start()
    {
        /**
         * Disables PHP automatically provided cache headers
         */
        session_cache_limiter('');

        if ($this->getFileConfigurationLoader()->isAvailable())
        {
            try
            {
                $this->getConnectionFactory()->getConnection();
                $configurationConsulter = $this->getConfigurationConsulter();

                if ($configurationConsulter->getSetting(array('Chamilo\Configuration', 'session', 'session_handler')) ==
                     'chamilo')
                {
                    $sessionHandler = new SessionHandler();
                    session_set_save_handler(
                        array($sessionHandler, 'open'),
                        array($sessionHandler, 'close'),
                        array($sessionHandler, 'read'),
                        array($sessionHandler, 'write'),
                        array($sessionHandler, 'destroy'),
                        array($sessionHandler, 'garbage'));
                }

                $sessionKey = $configurationConsulter->getSetting(
                    array('Chamilo\Configuration', 'general', 'security_key'));

                if (is_null($sessionKey))
                {
                    $sessionKey = 'dk_sid';
                }

                session_name($sessionKey);
                session_start();
            }
            catch (\Exception $exception)
            {
                session_start();
            }
        }
        else
        {
            session_start();
        }
    }

    public function register($variable, $value)
    {
        $_SESSION[$variable] = $value;
    }

    public function registerIfNotSet($variable, $value)
    {
        $sessionValue = $this->retrieve($variable);

        if (is_null($sessionValue))
        {
            $this->register($variable, $value);
        }
    }

    public function unregister($variable)
    {
        if (array_key_exists($variable, $_SESSION))
        {
            $_SESSION[$variable] = null;
            unset($GLOBALS[$variable]);
        }
    }

    public function clear()
    {
        session_regenerate_id();
        session_unset();
        $_SESSION = array();
    }

    public function destroy()
    {
        session_unset();
        $_SESSION = array();
        session_destroy();
    }

    public static function retrieve($variable)
    {
        if (array_key_exists($variable, $_SESSION))
        {
            return $_SESSION[$variable];
        }
    }

    public function get($variable, $default = null)
    {
        if (array_key_exists($variable, $_SESSION))
        {
            return $_SESSION[$variable];
        }
        else
        {
            return $default;
        }
    }

    public function get_user_id()
    {
        return $this->retrieve('_uid');
    }
}
