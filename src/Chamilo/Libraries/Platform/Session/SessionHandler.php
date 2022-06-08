<?php
namespace Chamilo\Libraries\Platform\Session;

use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Storage\Cache\DataClassRepositoryCache;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Exception;
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

    public function __construct()
    {
        // see warning for php < 5.4: http://www.php.net/manual/en/function.session-set-save-handler.php
        register_shutdown_function('session_write_close');
    }

    public function close(): bool
    {
        return true;
    }

    protected function createSession(\Chamilo\Core\User\Storage\DataClass\Session $session): bool
    {
        try
        {
            return $this->getDataClassRepository()->create($session);
        }
        catch (Exception $exception)
        {
            return false;
        }
    }

    protected function deleteSessionByCondition(Condition $condition): bool
    {
        return $this->getDataClassRepository()->deletes(
            \Chamilo\Core\User\Storage\DataClass\Session::class, $condition
        );
    }

    public function destroy($id): bool
    {
        return $this->deleteSessionByCondition($this->getCondition($id));
    }

    public function gc($max_lifetime)
    {
        $border = time() - $this->lifetime;

        $condition = new ComparisonCondition(
            new PropertyConditionVariable(
                \Chamilo\Core\User\Storage\DataClass\Session::class,
                \Chamilo\Core\User\Storage\DataClass\Session::PROPERTY_MODIFIED
            ), ComparisonCondition::LESS_THAN, new StaticConditionVariable($border)
        );

        return $this->deleteSessionByCondition($condition);
    }

    public function getCondition(int $sessionId): AndCondition
    {
        $conditions = [];
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Core\User\Storage\DataClass\Session::class,
                \Chamilo\Core\User\Storage\DataClass\Session::PROPERTY_SESSION_ID
            ), new StaticConditionVariable($sessionId)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Core\User\Storage\DataClass\Session::class,
                \Chamilo\Core\User\Storage\DataClass\Session::PROPERTY_NAME
            ), new StaticConditionVariable($this->name)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Core\User\Storage\DataClass\Session::class,
                \Chamilo\Core\User\Storage\DataClass\Session::PROPERTY_SAVE_PATH
            ), new StaticConditionVariable($this->savePath)
        );

        return new AndCondition($conditions);
    }

    protected function getDataClassRepository(): DataClassRepository
    {
        return $this->getService(
            'Chamilo\Libraries\Storage\DataManager\Doctrine\DataClassRepository'
        );
    }

    protected function getDataClassRepositoryCache(): DataClassRepositoryCache
    {
        return $this->getService(
            DataClassRepositoryCache::class
        );
    }

    /**
     * @template getServiceName
     *
     * @param class-string<getServiceName> $serviceName
     *
     * @return getServiceName
     * @throws \Exception
     */
    protected function getService(string $serviceName): ?object
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            $serviceName
        );
    }

    protected function getSessionById(int $sessionId)
    {
        $this->getDataClassRepositoryCache()->truncate(\Chamilo\Core\User\Storage\DataClass\Session::class);

        return $this->getDataClassRepository()->retrieve(
            \Chamilo\Core\User\Storage\DataClass\Session::class,
            new DataClassRetrieveParameters($this->getCondition($sessionId))
        );
    }

    public function open($path, $name): bool
    {
        $this->savePath = $path;
        $this->name = $name;

        return true;
    }

    public function read($id)
    {
        $session = $this->getSessionById($id);

        if ($session instanceof \Chamilo\Core\User\Storage\DataClass\Session)
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

        return false;
    }

    protected function updateSession(\Chamilo\Core\User\Storage\DataClass\Session $session): bool
    {
        try
        {
            return $this->getDataClassRepository()->update($session);
        }
        catch (Exception $exception)
        {
            return false;
        }
    }

    public function write($id, $data): bool
    {
        $data = base64_encode($data);
        $session = $this->getSessionById($id);

        if ($session instanceof \Chamilo\Core\User\Storage\DataClass\Session)
        {
            $session->set_data($data);
            $session->set_modified(time());

            return $this->updateSession($session);
        }
        else
        {
            $session = new \Chamilo\Core\User\Storage\DataClass\Session();
            $session->set_modified(time());
            $session->set_lifetime($this->lifetime);
            $session->set_data($data);
            $session->set_name($this->name);
            $session->set_save_path($this->savePath);
            $session->set_session_id($id);

            return $this->createSession($session);
        }
    }
}
