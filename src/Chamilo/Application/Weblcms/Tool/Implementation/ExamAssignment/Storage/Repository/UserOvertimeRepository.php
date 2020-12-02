<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Storage\Repository;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Storage\DataClass\UserOvertime;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Storage\Repository
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class UserOvertimeRepository
{
    /**
     * @var DataClassRepository
     */
    protected $dataClassRepository;

    /**
     * TreeNodeDataRepository constructor.
     *
     * @param DataClassRepository $dataClassRepository
     */
    public function __construct(DataClassRepository $dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    /**
     * @param int $publicationId
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function getUserOvertimeDataByPublication(int $publicationId)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(UserOvertime::class, UserOvertime::PROPERTY_PUBLICATION_ID),
            new StaticConditionVariable($publicationId)
        );

        $joins = new Joins();
        $joins->add(
            new Join(
                User::class,
                new EqualityCondition(
                    new PropertyConditionVariable(UserOvertime::class, UserOvertime::PROPERTY_USER_ID),
                    new PropertyConditionVariable(User::class, User::PROPERTY_ID)
                )
            )
        );

        $properties = new DataClassProperties();
        $properties->add(new PropertyConditionVariable(UserOvertime::class, UserOvertime::PROPERTY_ID));
        $properties->add(new PropertyConditionVariable(UserOvertime::class, UserOvertime::PROPERTY_PUBLICATION_ID));
        $properties->add(new PropertyConditionVariable(UserOvertime::class, UserOvertime::PROPERTY_USER_ID));
        $properties->add(new PropertyConditionVariable(User::class, User::PROPERTY_FIRSTNAME));
        $properties->add(new PropertyConditionVariable(User::class, User::PROPERTY_LASTNAME));
        $properties->add(new PropertyConditionVariable(User::class, User::PROPERTY_EMAIL));
        $properties->add(new PropertyConditionVariable(UserOvertime::class, UserOvertime::PROPERTY_EXTRA_TIME));

        $parameters = new RecordRetrievesParameters($properties, $condition, null, null, [], $joins);

        return $this->dataClassRepository->records(UserOvertime::class, $parameters);
    }

    /**
     * @param int $publicationId
     * @param int $userId
     *
     * @return mixed
     */
    public function getUserOvertimeDataForPublicationAndUser(int $publicationId, int $userId)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(UserOvertime::class, UserOvertime::PROPERTY_PUBLICATION_ID),
            new StaticConditionVariable($publicationId)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(UserOvertime::class, UserOvertime::PROPERTY_USER_ID),
            new StaticConditionVariable($userId)
        );
        $condition = new AndCondition($conditions);

        return $this->dataClassRepository->retrieve(
            UserOvertime::class_name(), new DataClassRetrieveParameters($condition)
        );
    }

    /**
     * @param UserOvertime $userOvertime
     *
     * @return bool
     */
    public function addUserOvertimeData(UserOvertime $userOvertime)
    {
        return $this->dataClassRepository->create($userOvertime);
    }

    /**
     * @param UserOvertime $userOvertime
     *
     * @return bool
     */
    public function updateUserOvertimeData(UserOvertime $userOvertime)
    {
        return $this->dataClassRepository->update($userOvertime);
    }

    /**
     * @param UserOvertime $overtime
     *
     * @return bool
     */
    public function deleteUserOvertimeData(UserOvertime $overtime)
    {
        return $this->dataClassRepository->delete($overtime);
    }

    /**
     * @param int $userOvertimeId
     *
     * @return UserOvertime|\Chamilo\Libraries\Storage\DataClass\CompositeDataClass|\Chamilo\Libraries\Storage\DataClass\DataClass|false
     */
    public function findUserOvertimeById(int $userOvertimeId)
    {
        return $this->dataClassRepository->retrieveById(UserOvertime::class, $userOvertimeId);
    }
}
