<?php
namespace Chamilo\Libraries\Platform\Session;

/**
 *
 * @package Chamilo\Libraries\Platform\Session
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class SessionUtilities
{
    private ?string $securityKey;

    public function __construct(?string $securityKey = null)
    {
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

    public function getSecurityKey(): ?string
    {
        return $this->securityKey;
    }

    public function setSecurityKey(?string $securityKey)
    {
        $this->securityKey = $securityKey;
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

        $sessionKey = $this->getSecurityKey();

        if (is_null($sessionKey))
        {
            $sessionKey = 'dk_sid';
        }

        session_name($sessionKey);
        session_start();
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
