<?php
namespace Chamilo\Libraries\Platform\Session;

use Chamilo\Configuration\Service\FileConfigurationLocator;
use Chamilo\Core\User\Service\SessionHandler;

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
     * @var \Chamilo\Configuration\Service\FileConfigurationLocator
     */
    private $fileConfigurationLocator;

    /**
     *
     * @var \Chamilo\Core\User\Service\SessionHandler|NULL
     */
    private $sessionHandler;

    /**
     *
     * @var string
     */
    private $securityKey;

    /**
     * @var bool
     */
    protected $started;

    /**
     *
     * @param \Chamilo\Configuration\Service\FileConfigurationLocator $fileConfigurationLocator
     * @param \Chamilo\Core\User\Service\SessionHandler|NULL $sessionHandler
     * @param string $securityKey
     */
    public function __construct(FileConfigurationLocator $fileConfigurationLocator, 
        SessionHandler $sessionHandler = null, $securityKey = null)
    {
        $this->fileConfigurationLocator = $fileConfigurationLocator;
        $this->sessionHandler = $sessionHandler;
        $this->securityKey = $securityKey;
    }

    /**
     *
     * @return \Chamilo\Configuration\Service\FileConfigurationLocator
     */
    public function getFileConfigurationLocator()
    {
        return $this->fileConfigurationLocator;
    }

    /**
     *
     * @param \Chamilo\Configuration\Service\FileConfigurationLocator $fileConfigurationLocator
     */
    public function setFileConfigurationLocator(FileConfigurationLocator $fileConfigurationLocator)
    {
        $this->fileConfigurationLocator = $fileConfigurationLocator;
    }

    /**
     *
     * @return \Chamilo\Core\User\Service\SessionHandler|NULL
     */
    public function getSessionHandler()
    {
        return $this->sessionHandler;
    }

    /**
     *
     * @param \Chamilo\Core\User\Service\SessionHandler|NULL $sessionHandler
     */
    public function setSessionHandler(SessionHandler $sessionHandler = null)
    {
        $this->sessionHandler = $sessionHandler;
    }

    /**
     *
     * @return string
     */
    public function getSecurityKey()
    {
        return $this->securityKey;
    }

    /**
     *
     * @param string $securityKey
     */
    public function setSecurityKey($securityKey)
    {
        $this->securityKey = $securityKey;
    }

    public function start()
    {
        if($this->isStarted())
        {
            return;
        }

        /**
         * Disables PHP automatically provided cache headers
         */
        session_cache_limiter('');
        
        if ($this->getFileConfigurationLocator()->isAvailable())
        {
            try
            {
                if ($this->getSessionHandler() instanceof SessionHandler)
                {
                    session_set_save_handler(
                        array($this->getSessionHandler(), 'open'), 
                        array($this->getSessionHandler(), 'close'), 
                        array($this->getSessionHandler(), 'read'), 
                        array($this->getSessionHandler(), 'write'), 
                        array($this->getSessionHandler(), 'destroy'), 
                        array($this->getSessionHandler(), 'garbage'));
                }
                
                $sessionKey = $this->getSecurityKey();
                
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

        $this->started = true;
    }

    /**
     * @return bool
     */
    public function isStarted()
    {
        return $this->started;
    }

    /**
     * @return array
     */
    public function all()
    {
        return $_SESSION;
    }

    /**
     * @param string $variable
     * @param mixed $value
     */
    public function register($variable, $value)
    {
        $_SESSION[$variable] = $value;
    }

    /**
     * @param string $variable
     * @param mixed $value
     */
    public function registerIfNotSet($variable, $value)
    {
        $sessionValue = $this->retrieve($variable);
        
        if (is_null($sessionValue))
        {
            $this->register($variable, $value);
        }
    }

    /**
     * @param string $variable
     */
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
//        session_regenerate_id();
        session_unset();
        $_SESSION = array();
    }

    public function destroy()
    {
        session_unset();
        $_SESSION = array();
        session_destroy();

        $this->started = false;
    }

    /**
     * @param string $variable
     *
     * @return mixed
     */
    public static function retrieve($variable)
    {
        if (array_key_exists($variable, $_SESSION))
        {
            return $_SESSION[$variable];
        }

        return null;
    }

    /**
     * @param string $variable
     * @param mixed $default
     *
     * @return mixed
     */
    public function get($variable, $default = null)
    {
        if ($this->has($variable))
        {
            return $_SESSION[$variable];
        }
        else
        {
            return $default;
        }
    }

    /**
     * @param $variable
     *
     * @return bool
     */
    public function has($variable)
    {
        return array_key_exists($variable, $_SESSION);
    }

    /**
     * @return int
     *
     * @deprecated
     *
     * @see getUserId
     */
    public static function get_user_id()
    {
        return self::getUserId();
    }

    /**
     * @return int
     */
    public static function getUserId()
    {
        return self::retrieve('_uid');
    }
}
