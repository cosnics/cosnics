<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Service;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Storage\DataClass\UserOvertime;
use Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Storage\Repository\UserOvertimeRepository;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Service
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class UserOvertimeService
{
    /**
     * @var \Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Storage\Repository\UserOvertimeRepository
     */
    protected $userOvertimeRepository;

    /**
     * UserOvertimeService constructor.
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Storage\Repository\UserOvertimeRepository $userOvertimeRepository
     */
    public function __construct(UserOvertimeRepository $userOvertimeRepository)
    {
        $this->userOvertimeRepository = $userOvertimeRepository;
    }

    /**
     * @param ContentObjectPublication $contentObjectPublication
     * @return array
     */
    public function getUserOvertimeDataByPublication(ContentObjectPublication $contentObjectPublication)
    {
        // We're receiving a record set through left joining, so we get associative arrays instead of UserOvertime objects.
        $retrievedItems = $this->userOvertimeRepository->getUserOvertimeDataByPublication($contentObjectPublication->getId());

        // Performing integer casting here on the relevant properties.
        $items = [];
        foreach ($retrievedItems as $item)
        {
            $item[UserOvertime::PROPERTY_ID] = (int) $item[UserOvertime::PROPERTY_ID];
            $item[UserOvertime::PROPERTY_PUBLICATION_ID] = (int) $item[UserOvertime::PROPERTY_PUBLICATION_ID];
            $item[UserOvertime::PROPERTY_USER_ID] = (int) $item[UserOvertime::PROPERTY_USER_ID];
            $item[UserOvertime::PROPERTY_EXTRA_TIME] = (int) $item[UserOvertime::PROPERTY_EXTRA_TIME];
            $items[] = $item;
        }
        return $items;
    }

    /**
     * @param ContentObjectPublication $contentObjectPublication
     * @param int $userId
     * @param int $extraTime
     * @return UserOvertime
     */
    public function addUserOvertimeData(ContentObjectPublication $contentObjectPublication, int $userId, int $extraTime)
    {
        $userOvertime = new UserOvertime();
        $userOvertime->setPublicationId($contentObjectPublication->get_id());
        $userOvertime->setUserId($userId);
        $userOvertime->setExtraTime($extraTime);
        $this->userOvertimeRepository->addUserOvertimeData($userOvertime);
        return $userOvertime;
    }

    /**
     * @param int $userOvertimeId
     * @param int $extraTime
     * @return UserOvertime
     */
    public function updateUserOvertimeData(int $userOvertimeId, int $extraTime)
    {
        $userOvertime = DataManager::retrieve_by_id(UserOvertime::class_name(), $userOvertimeId);
        $userOvertime->setExtraTime($extraTime);
        $this->userOvertimeRepository->updateUserOvertimeData($userOvertime);
        return $userOvertime;
    }

    /**
     * @param int $userOvertimeId
     * @return bool
     */
    public function deleteUserOvertimeData(int $userOvertimeId)
    {
        $userOvertime = DataManager::retrieve_by_id(UserOvertime::class_name(), $userOvertimeId);
        return $this->userOvertimeRepository->deleteUserOvertimeData($userOvertime);
    }
}