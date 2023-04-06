<?php
namespace Chamilo\Core\User\Service;

use Chamilo\Core\User\Storage\DataClass\Session;
use Chamilo\Core\User\Storage\Repository\SessionRepository;

/**
 *
 * @package Chamilo\Libraries\Platform\Session
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class SessionHandler implements \SessionHandlerInterface
{

    /**
     *
     * @var \Chamilo\Core\User\Storage\Repository\SessionRepository
     */
    private $sessionRepository;

    /**
     *
     * @var string
     */
    private $savePath;

    /**
     *
     * @var string
     */
    private $name;

    /**
     *
     * @var integer
     */
    private $lifetime = 43200;

    public function __construct(SessionRepository $sessionRepository)
    {
        $this->sessionRepository = $sessionRepository;
        
        // see warning for php < 5.4: http://www.php.net/manual/en/function.session-set-save-handler.php
        register_shutdown_function('session_write_close');
    }

    /**
     *
     * @return \Chamilo\Core\User\Storage\Repository\SessionRepository
     */
    public function getSessionRepository()
    {
        return $this->sessionRepository;
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\Repository\SessionRepository $sessionRepository
     */
    public function setSessionRepository($sessionRepository)
    {
        $this->sessionRepository = $sessionRepository;
    }

    /**
     *
     * @return string
     */
    public function getSavePath()
    {
        return $this->savePath;
    }

    /**
     *
     * @param string $savePath
     */
    public function setSavePath($savePath)
    {
        $this->savePath = $savePath;
    }

    /**
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     *
     * @return integer
     */
    public function getLifetime()
    {
        return $this->lifetime;
    }

    /**
     *
     * @param integer $lifetime
     */
    public function setLifetime($lifetime)
    {
        $this->lifetime = $lifetime;
    }

    public function open($savePath, $name): bool
    {
        $this->savePath = $savePath;
        $this->name = $name;
        
        return true;
    }

    public function close(): bool
    {
        return true;
    }

    /**
     *
     * @see SessionHandlerInterface::read()
     */
    public function read($sessionIdentifier): false|string
    {
        $session = $this->getSessionRepository()->getSessionForIdentifierNameAndSavePath(
            $sessionIdentifier, 
            $this->getName(), 
            $this->getSavePath());
        
        if ($session instanceof Session)
        {
            if ($session->is_valid())
            {
                return base64_decode($session->get_data());
            }
            else
            {
                $this->destroy($sessionIdentifier);
            }
        }
        
        return '';
    }

    /**
     *
     * @see SessionHandlerInterface::write()
     */
    public function write($sessionIdentifier, $data): bool
    {
        $data = base64_encode($data);
        
        $session = $this->getSessionRepository()->getSessionForIdentifierNameAndSavePath(
            $sessionIdentifier, 
            $this->getName(), 
            $this->getSavePath());
        
        if ($session instanceof Session)
        {
            $session->set_data($data);
            $session->set_modified(time());
            
            return $session->update();
        }
        else
        {
            $session = new Session();
            $session->set_modified(time());
            $session->set_lifetime($this->getLifetime());
            $session->set_data($data);
            $session->set_name($this->getName());
            $session->set_save_path($this->getSavePath());
            $session->set_session_id($sessionIdentifier);
            
            return $session->create();
        }
    }

    /**
     *
     * @see SessionHandlerInterface::destroy()
     */
    public function destroy($sessionIdentifier): bool
    {
        return $this->getSessionRepository()->deleteSessionForIdentifierNameAndSavePath(
            $sessionIdentifier, 
            $this->getName(), 
            $this->getSavePath());
    }

    /**
     *
     * @param integer $maxLifetime
     * @return boolean
     */
    public function garbage($maxLifetime)
    {
        $border = time() - $this->getLifetime();
        
        return $this->getSessionRepository()->deleteSessionsOlderThanTimestamp($border);
    }

    /**
     *
     * @see SessionHandlerInterface::gc()
     */
    public function gc($maxlifetime): false|int
    {
        $this->garbage($maxlifetime);
    }
}
