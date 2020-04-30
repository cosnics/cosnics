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

/**
 *
 * @package Chamilo\Core\User\Storage\Repository
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class SessionRepository
{

    /**
     *
     * @var \Chamilo\Libraries\Storage\Cache\DataClassRepositoryCache
     */
    private $dataClassRepositoryCache;

    /**
     *
     * @var \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository
     */
    private $dataClassRepository;

    /**
     *
     * @param \Chamilo\Libraries\Storage\Cache\DataClassRepositoryCache $dataClassRepositoryCache
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
     */
    public function __construct(DataClassRepositoryCache $dataClassRepositoryCache, 
        DataClassRepository $dataClassRepository)
    {
        $this->dataClassRepositoryCache = $dataClassRepositoryCache;
        $this->dataClassRepository = $dataClassRepository;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\Cache\DataClassRepositoryCache
     */
    public function getDataClassRepositoryCache()
    {
        return $this->dataClassRepositoryCache;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Cache\DataClassRepositoryCache $dataClassRepositoryCache
     */
    public function setDataClassRepositoryCache(DataClassRepositoryCache $dataClassRepositoryCache)
    {
        $this->dataClassRepositoryCache = $dataClassRepositoryCache;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository
     */
    public function getDataClassRepository()
    {
        return $this->dataClassRepository;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
     */
    public function setDataClassRepository(DataClassRepository $dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    /**
     *
     * @param string $sessionIdentifier
     * @param string $name
     * @param string $savePath
     * @return \Chamilo\Core\User\Storage\DataClass\Session
     */
    public function getSessionForIdentifierNameAndSavePath($sessionIdentifier, $name, $savePath)
    {
        $this->getDataClassRepositoryCache()->truncate(Session::class);
        
        return $this->getDataClassRepository()->retrieve(
            Session::class,
            new DataClassRetrieveParameters($this->getSessionCondition($sessionIdentifier, $name, $savePath)));
    }

    /**
     *
     * @param string $sessionIdentifier
     * @param string $name
     * @param string $savePath
     * @return \Chamilo\Libraries\Storage\Query\Condition\AndCondition
     */
    protected function getSessionCondition($sessionIdentifier, $name, $savePath)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Session::class, Session::PROPERTY_SESSION_ID),
            new StaticConditionVariable($sessionIdentifier));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Session::class, Session::PROPERTY_NAME),
            new StaticConditionVariable($name));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Session::class, Session::PROPERTY_SAVE_PATH),
            new StaticConditionVariable($savePath));
        
        return new AndCondition($conditions);
    }

    /**
     *
     * @param string $sessionIdentifier
     * @param string $name
     * @param string $savePath
     * @return boolean
     */
    public function deleteSessionForIdentifierNameAndSavePath($sessionIdentifier, $name, $savePath)
    {
        return $this->getDataClassRepository()->deletes(
            Session::class,
            $this->getSessionCondition($sessionIdentifier, $name, $savePath));
    }

    /**
     *
     * @param integer $timetamp
     * @return boolean
     */
    public function deleteSessionsOlderThanTimestamp($timetamp)
    {
        $condition = new ComparisonCondition(
            new PropertyConditionVariable(Session::class, Session::PROPERTY_MODIFIED),
            ComparisonCondition::LESS_THAN, 
            new StaticConditionVariable($timetamp));
        
        return $this->getDataClassRepository()->deletes(Session::class, $condition);
    }
}