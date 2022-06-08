<?php
namespace Chamilo\Core\User\Service;

use Chamilo\Core\User\Storage\DataClass\Session;
use Chamilo\Core\User\Storage\Repository\SessionRepository;
use SessionHandlerInterface;

/**
 *
 * @package Chamilo\Libraries\Platform\Session
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class SessionHandler implements SessionHandlerInterface
{

    private int $lifetime = 43200;

    private string $name;

    private string $savePath;

    private SessionRepository $sessionRepository;

    public function __construct(SessionRepository $sessionRepository)
    {
        $this->sessionRepository = $sessionRepository;

        // see warning for php < 5.4: http://www.php.net/manual/en/function.session-set-save-handler.php
        register_shutdown_function('session_write_close');
    }

    public function close(): bool
    {
        return true;
    }

    public function destroy($id): bool
    {
        return $this->getSessionRepository()->deleteSessionForIdentifierNameAndSavePath(
            $id, $this->getName(), $this->getSavePath()
        );
    }

    public function gc($max_lifetime)
    {
        $border = time() - $this->getLifetime();

        return $this->getSessionRepository()->deleteSessionsOlderThanTimestamp($border);
    }

    public function getLifetime(): int
    {
        return $this->lifetime;
    }

    public function setLifetime(int $lifetime)
    {
        $this->lifetime = $lifetime;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getSavePath(): string
    {
        return $this->savePath;
    }

    public function setSavePath(string $savePath)
    {
        $this->savePath = $savePath;
    }

    public function getSessionRepository(): SessionRepository
    {
        return $this->sessionRepository;
    }

    public function setSessionRepository(SessionRepository $sessionRepository)
    {
        $this->sessionRepository = $sessionRepository;
    }

    public function open($path, $name): bool
    {
        $this->savePath = $path;
        $this->name = $name;

        return true;
    }

    public function read($id)
    {
        $session = $this->getSessionRepository()->getSessionForIdentifierNameAndSavePath(
            $id, $this->getName(), $this->getSavePath()
        );

        if ($session instanceof Session)
        {
            if ($session->is_valid())
            {
                return base64_decode($session->get_data());
            }
            else
            {
                $this->destroy($id);
            }
        }

        return '';
    }

    public function write($id, $data): bool
    {
        $data = base64_encode($data);

        $session = $this->getSessionRepository()->getSessionForIdentifierNameAndSavePath(
            $id, $this->getName(), $this->getSavePath()
        );

        if ($session instanceof Session)
        {
            $session->set_data($data);
            $session->set_modified(time());

            return $this->getSessionRepository()->updateSession($session);
        }
        else
        {
            $session = new Session();
            $session->set_modified(time());
            $session->set_lifetime($this->getLifetime());
            $session->set_data($data);
            $session->set_name($this->getName());
            $session->set_save_path($this->getSavePath());
            $session->set_session_id($id);

            return $this->getSessionRepository()->createSession($session);
        }
    }
}
