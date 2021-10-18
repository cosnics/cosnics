<?php

namespace Chamilo\Core\Repository\ContentObject\Presence\Display\Service;

use Chamilo\Core\Repository\ContentObject\Presence\Display\Storage\Repository\UserRepository;
use Chamilo\Core\Repository\ContentObject\Presence\Storage\DataClass\PresenceResultEntry;
use Chamilo\Core\Repository\ContentObject\Presence\Storage\DataClass\PresenceResultPeriod;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ContextIdentifier;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\FilterParameters\FieldMapper;
use Chamilo\Libraries\Storage\FilterParameters\FilterParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package Chamilo\Core\Repository\ContentObject\Presence\Display\Service
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class UserService
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var FieldMapper
     */
    protected $fieldMapper;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     *
     * @param int[] $userIds
     * @param ContextIdentifier $contextIdentifier
     * @param FilterParameters|null $filterParameters
     * @param array $options
     *
     * @return array
     */
    public function getUsersFromIds(array $userIds, ContextIdentifier $contextIdentifier, FilterParameters $filterParameters = null, array $options = array()): array
    {
        if (is_null($filterParameters))
        {
            $filterParameters = new FilterParameters();
        }

        $condition = null;
        if (isset($options['periodId']))
        {
            $periodCondition = new EqualityCondition(
                new PropertyConditionVariable(PresenceResultPeriod::class_name(), DataClass::PROPERTY_ID),
                new StaticConditionVariable($options['periodId'])
            );
            $subCondition = $this->createFilterCondition($options);
            $condition = isset($subCondition) ? new AndCondition([$periodCondition, $subCondition]) : $periodCondition;
        }

        $selectedUsers = $this->userRepository->getUsersFromIds($userIds, $contextIdentifier, $filterParameters, $condition);

        $users = [];

        foreach ($selectedUsers as $selectedUser)
        {
            $userId = $selectedUser['id'];
            if (!array_key_exists($userId, $users))
            {
                $users[$userId] = [
                    'id' => $userId,
                    'firstname' => $selectedUser['firstname'],
                    'lastname' => $selectedUser['lastname'],
                    'official_code' => $selectedUser['official_code']
                ];
            }
            $user = $users[$userId];

            $periodId = $selectedUser['period_id'];
            if (!is_null($periodId))
            {
                $user['period#' . $periodId . '-status'] = (int) $selectedUser['status'];
                $user['period#' . $periodId . '-checked_in_date'] = $selectedUser['checked_in_date'];
                $user['period#' . $periodId . '-checked_out_date'] = $selectedUser['checked_out_date'];
            }

            $users[$userId] = $user;
        }

        return array_values($users);
    }

    /**
     * @param int $userId
     *
     * @return string
     */
    public function getProfilePhotoUrl(int $userId): string
    {
        $profilePhotoUrl = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\User\Ajax\Manager::context(),
                Application::PARAM_ACTION => \Chamilo\Core\User\Ajax\Manager::ACTION_USER_PICTURE,
                \Chamilo\Core\User\Manager::PARAM_USER_USER_ID => $userId
            )
        );
        return $profilePhotoUrl->getUrl();
    }

    /**
     * @return FieldMapper
     */
    public function getFieldMapper(): FieldMapper
    {
        if (! isset($this->fieldMapper))
        {
            $class_name = User::class_name();
            $this->fieldMapper = new FieldMapper();
            $this->fieldMapper->addFieldMapping('firstname', $class_name, User::PROPERTY_FIRSTNAME);
            $this->fieldMapper->addFieldMapping('lastname', $class_name, User::PROPERTY_LASTNAME);
            $this->fieldMapper->addFieldMapping('official_code', $class_name, User::PROPERTY_OFFICIAL_CODE);
        }
        return $this->fieldMapper;
    }

    /**
     * @param array $options
     * @return Condition|null
     */
    protected function createFilterCondition(array $options): ?Condition
    {
        $withoutStatus = $options['withoutStatus'] == true;
        $hasStatusFilters = !empty($options['statusFilters']);

        if ($withoutStatus)
        {
            $withoutStatusCondition = new EqualityCondition(
                new PropertyConditionVariable(PresenceResultEntry::class_name(), PresenceResultEntry::PROPERTY_CHOICE_ID),
                NULL
            );
            if (!$hasStatusFilters)
            {
                return $withoutStatusCondition;
            }
        }

        if ($hasStatusFilters)
        {
            $filtersCondition = new InCondition(
                new PropertyConditionVariable(PresenceResultEntry::class_name(), PresenceResultEntry::PROPERTY_CHOICE_ID), $options['statusFilters']);
            if (!$withoutStatus)
            {
                return $filtersCondition;
            }
        }

        if ($withoutStatus && $hasStatusFilters)
        {
            return new OrCondition([$filtersCondition, $withoutStatusCondition]);
        }
        return null;
    }
}