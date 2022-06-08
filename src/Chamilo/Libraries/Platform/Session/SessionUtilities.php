<?php
namespace Chamilo\Libraries\Platform\Session;

use Chamilo\Configuration\Service\FileConfigurationLocator;
use Chamilo\Core\User\Service\SessionHandler;
use Exception;

/**
 *
 * @package Chamilo\Libraries\Platform\Session
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class SessionUtilities
{

    private FileConfigurationLocator $fileConfigurationLocator;

    private ?string $securityKey;

    private ?SessionHandler $sessionHandler;

    public function __construct(
        FileConfigurationLocator $fileConfigurationLocator, SessionHandler $sessionHandler = null,
        ?string $securityKey = null
    )
    {
        $this->fileConfigurationLocator = $fileConfigurationLocator;
        $this->sessionHandler = $sessionHandler;
        $this->securityKey = $securityKey;
    }

    public function clear()
    {
        session_unset();
        $_SESSION = [];
    }

    public function destroy()
    {
        session_unset();
        $_SESSION = [];
        session_destroy();
    }

    public function get(string $variable, $default = null)
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

    public function getFileConfigurationLocator(): FileConfigurationLocator
    {
        return $this->fileConfigurationLocator;
    }

    public function setFileConfigurationLocator(FileConfigurationLocator $fileConfigurationLocator)
    {
        $this->fileConfigurationLocator = $fileConfigurationLocator;
    }

    public function getSecurityKey(): ?string
    {
        return $this->securityKey;
    }

    public function setSecurityKey(?string $securityKey)
    {
        $this->securityKey = $securityKey;
    }

    public function getSessionHandler(): ?SessionHandler
    {
        return $this->sessionHandler;
    }

    public function setSessionHandler(?SessionHandler $sessionHandler = null)
    {
        $this->sessionHandler = $sessionHandler;
    }

    public function getUserId(): ?int
    {
        return $this->retrieve('_uid');
    }

    /**
     * @deprecated Use SessionUtilities::getUserId() now
     */
    public function get_user_id(): ?int
    {
        return $this->getUserId();
    }

    public function register(string $variable, $value)
    {
        $_SESSION[$variable] = $value;
    }

    public function registerIfNotSet(string $variable, $value)
    {
        $sessionValue = $this->retrieve($variable);

        if (is_null($sessionValue))
        {
            $this->register($variable, $value);
        }
    }

    public function retrieve(string $variable)
    {
        if (array_key_exists($variable, $_SESSION))
        {
            return $_SESSION[$variable];
        }

        return null;
    }

    /**
     * @throws \Exception
     */
    public function start()
    {
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
                        array($this->getSessionHandler(), 'open'), array($this->getSessionHandler(), 'close'),
                        array($this->getSessionHandler(), 'read'), array($this->getSessionHandler(), 'write'),
                        array($this->getSessionHandler(), 'destroy'), array($this->getSessionHandler(), 'gc')
                    );
                }

                $sessionKey = $this->getSecurityKey();

                if (is_null($sessionKey))
                {
                    $sessionKey = 'dk_sid';
                }

                session_name($sessionKey);
                session_start();
            }
            catch (Exception $exception)
            {
                session_start();
            }
        }
        else
        {
            session_start();
        }
    }

    public function unregister(string $variable)
    {
        if (array_key_exists($variable, $_SESSION))
        {
            $_SESSION[$variable] = null;
            unset($GLOBALS[$variable]);
        }
    }
}
