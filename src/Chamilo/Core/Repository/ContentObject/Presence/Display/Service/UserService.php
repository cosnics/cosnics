<?php

namespace Chamilo\Core\Repository\ContentObject\Presence\Display\Service;

use Chamilo\Core\Repository\ContentObject\Presence\Display\Storage\Repository\UserRepository;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ContextIdentifier;
use Chamilo\Libraries\Storage\FilterParameters\FieldMapper;
use Chamilo\Libraries\Storage\FilterParameters\FilterParameters;

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
     *
     * @return array
     */
    public function getUsersFromIds(array $userIds, ContextIdentifier $contextIdentifier, FilterParameters $filterParameters = null): array
    {
        if (is_null($filterParameters))
        {
            $filterParameters = new FilterParameters();
        }

        $selectedUsers = $this->userRepository->getUsersFromIds($userIds, $contextIdentifier, $filterParameters);

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
     *
     * @param int[] $entityIds
     * @param FilterParameters $filterParameters
     *
     * @return integer
     */
    /*public function countEntitiesFromIds(array $entityIds, FilterParameters $filterParameters): int
    {
        return $this->userRepository->countUsersFromIds($entityIds, $filterParameters);
    }*/

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
}