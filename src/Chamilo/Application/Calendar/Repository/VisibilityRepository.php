<?php
namespace Chamilo\Application\Calendar\Repository;

use Chamilo\Application\Calendar\Storage\DataClass\Visibility;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package Chamilo\Application\Calendar\Repository
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class VisibilityRepository
{

    /**
     *
     * @var \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository
     */
    private $dataClassRepository;

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
     */
    public function __construct(DataClassRepository $dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository
     */
    protected function getDataClassRepository()
    {
        return $this->dataClassRepository;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
     */
    protected function setDataClassRepository($dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    /**
     *
     * @param string $source
     * @param integer $userIdentifier
     * @return \Chamilo\Application\Calendar\Storage\DataClass\Visibility
     */
    public function findVisibilityBySourceAndUserIdentifier($source, $userIdentifier)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Visibility::class_name(), Visibility::PROPERTY_USER_ID),
            new StaticConditionVariable($userIdentifier));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Visibility::class_name(), Visibility::PROPERTY_SOURCE),
            new StaticConditionVariable($source));
        $condition = new AndCondition($conditions);

        return $this->getDataClassRepository()->retrieve(
            Visibility::class_name(),
            new DataClassRetrieveParameters($condition));
    }
}