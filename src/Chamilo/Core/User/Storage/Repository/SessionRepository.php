<?php
namespace Chamilo\Core\User\Storage\Repository;

use Chamilo\Core\User\Storage\DataClass\Session;
use Chamilo\Libraries\Storage\Cache\DataClassRepositoryCache;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Exception;

/**
 *
 * @package Chamilo\Core\User\Storage\Repository
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class SessionRepository
{

    private DataClassRepository $dataClassRepository;

    private DataClassRepositoryCache $dataClassRepositoryCache;

    public function __construct(
        DataClassRepositoryCache $dataClassRepositoryCache, DataClassRepository $dataClassRepository
    )
    {
        $this->dataClassRepositoryCache = $dataClassRepositoryCache;
        $this->dataClassRepository = $dataClassRepository;
    }

    public function createSession(Session $session): bool
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

    public function deleteSessionForIdentifierNameAndSavePath(string $sessionIdentifier, string $name, string $savePath
    ): bool
    {
        return $this->getDataClassRepository()->deletes(
            Session::class, $this->getSessionCondition($sessionIdentifier, $name, $savePath)
        );
    }

    public function deleteSessionsOlderThanTimestamp(int $timetamp): bool
    {
        $condition = new ComparisonCondition(
            new PropertyConditionVariable(Session::class, Session::PROPERTY_MODIFIED), ComparisonCondition::LESS_THAN,
            new StaticConditionVariable($timetamp)
        );

        return $this->getDataClassRepository()->deletes(Session::class, $condition);
    }

    public function getDataClassRepository(): DataClassRepository
    {
        return $this->dataClassRepository;
    }

    public function getDataClassRepositoryCache(): DataClassRepositoryCache
    {
        return $this->dataClassRepositoryCache;
    }

    protected function getSessionCondition(string $sessionIdentifier, string $name, string $savePath): AndCondition
    {
        $conditions = [];
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Session::class, Session::PROPERTY_SESSION_ID),
            new StaticConditionVariable($sessionIdentifier)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Session::class, Session::PROPERTY_NAME), new StaticConditionVariable($name)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Session::class, Session::PROPERTY_SAVE_PATH),
            new StaticConditionVariable($savePath)
        );

        return new AndCondition($conditions);
    }

    public function getSessionForIdentifierNameAndSavePath(string $sessionIdentifier, string $name, string $savePath
    ): ?Session
    {
        $this->getDataClassRepositoryCache()->truncate(Session::class);

        return $this->getDataClassRepository()->retrieve(
            Session::class,
            new DataClassRetrieveParameters($this->getSessionCondition($sessionIdentifier, $name, $savePath))
        );
    }

    public function updateSession(Session $session): bool
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
}