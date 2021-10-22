<?php

namespace Chamilo\Core\Repository\ContentObject\Presence\Display\Service;

use Chamilo\Core\Repository\ContentObject\Presence\Display\Storage\Repository\PresenceRepository;
use Chamilo\Core\Repository\ContentObject\Presence\Storage\DataClass\PresenceResultEntry;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Chamilo\Libraries\Storage\FilterParameters\FilterParameters;
use Chamilo\Libraries\Storage\FilterParameters\FilterParametersBuilder;
use Chamilo\Core\Repository\ContentObject\Presence\Storage\DataClass\Presence;
use Chamilo\Libraries\Architecture\ContextIdentifier;
use http\Exception\InvalidArgumentException;
use DateTime;

/**
 * @package Chamilo\Core\Repository\ContentObject\Presence\Display\Service
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class PresenceResultEntryService
{
    /**
     * @var PresenceRepository
     */
    protected $presenceRepository;

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @var PresenceService
     */
    protected $presenceService;

    /**
     * @var FilterParametersBuilder
     */
    protected $filterParametersBuilder;

    /**
     * @param PresenceRepository $presenceRepository
     * @param UserService $userService
     * @param PresenceService $presenceService
     * @param FilterParametersBuilder $filterParametersBuilder
     */
    public function __construct(PresenceRepository $presenceRepository, UserService $userService, PresenceService $presenceService, FilterParametersBuilder $filterParametersBuilder)
    {
        $this->presenceRepository = $presenceRepository;
        $this->userService = $userService;
        $this->presenceService = $presenceService;
        $this->filterParametersBuilder = $filterParametersBuilder;
    }

    /**
     * @param array $userIds
     * @param array $periods
     * @param ContextIdentifier $contextIdentifier
     * @param FilterParameters $filterParameters
     * @param array $options
     *
     * @return array
     */
    public function getUsers(array $userIds, array $periods, ContextIdentifier $contextIdentifier, FilterParameters $filterParameters, array $options = array()): array
    {
        $users = $this->userService->getUsersFromIds($userIds, $contextIdentifier, $filterParameters, $options);

        foreach ($users as $index => $user)
        {
            foreach ($periods as $period)
            {
                $users[$index] = $this->completeUserFields($users[$index], $period['id']);
            }
        }
        return $users;
    }

    /**
     * @param int $periodId
     * @param int $userId
     *
     * @return int
     */
    public function getPresenceResultEntryPresenceStatus(int $periodId, int $userId): int
    {
        $presenceResultEntry = $this->getPresenceResultEntry($periodId, $userId);
        if (! $presenceResultEntry instanceof PresenceResultEntry)
        {
            return Presence::STATUS_ABSENT;
        }
        return $presenceResultEntry->getPresenceStatusId();
    }

    /**
     * @param int $periodId
     * @param int $userId
     *
     * @return PresenceResultEntry|\Chamilo\Libraries\Storage\DataClass\CompositeDataClass|\Chamilo\Libraries\Storage\DataClass\DataClass|false
     */
    public function getPresenceResultEntry(int $periodId, int $userId)
    {
        return $this->presenceRepository->getPresenceResultEntry($periodId, $userId);
    }

    /**
     * @param Presence $presence
     * @param int $periodId
     * @param int $userId
     * @param int $statusId
     * @return PresenceResultEntry
     */
    public function createOrUpdatePresenceResultEntry(Presence $presence, int $periodId, int $userId, int $statusId): PresenceResultEntry
    {
        $fixedStatusId = $this->presenceService->findFixedStatusId($presence, $statusId);
        if ($fixedStatusId == -1)
        {
            throw new InvalidArgumentException();
        }

        $presenceResultEntry = $this->presenceRepository->getPresenceResultEntry($periodId, $userId);
        if (! $presenceResultEntry instanceof PresenceResultEntry)
        {
            $presenceResultEntry = $this->createPresenceResultEntry($periodId, $userId);
            $presenceResultEntry = $this->updatePresenceResultEntryFields($presenceResultEntry, $statusId, $fixedStatusId);
            $this->presenceRepository->createPresenceResultEntry($presenceResultEntry);
        }
        else
        {
            $presenceResultEntry = $this->updatePresenceResultEntryFields($presenceResultEntry, $statusId, $fixedStatusId);
            $this->presenceRepository->updatePresenceResultEntry($presenceResultEntry);
        }
        return $presenceResultEntry;
    }

    /**
     * @param Presence $presence
     * @param int $periodId
     * @param array $users
     * @param int $statusId
     *
     * @return PresenceResultEntry[]
     */
    public function createOrUpdatePresenceResultEntries(Presence $presence, int $periodId, array $users, int $statusId): array
    {
        $presenceResultEntries = array();
        foreach ($users as $user)
        {
            $presenceResultEntries[] = $this->createOrUpdatePresenceResultEntry($presence, $periodId, $user['id'], $statusId);
        }
        return $presenceResultEntries;
    }

    /**
     * @param int $periodId
     * @param int $userId
     * @return PresenceResultEntry
     */
    protected function createPresenceResultEntry(int $periodId, int $userId): PresenceResultEntry
    {
        $presenceResultEntry = new PresenceResultEntry();
        $presenceResultEntry->setPresencePeriodId($periodId);
        $presenceResultEntry->setUserId($userId);
        return $presenceResultEntry;
    }

    /**
     * @param PresenceResultEntry $presenceResultEntry
     * @param int $statusId
     * @param int $fixedStatusId
     * @return PresenceResultEntry
     */
    protected function updatePresenceResultEntryFields(PresenceResultEntry $presenceResultEntry, int $statusId, int $fixedStatusId): PresenceResultEntry
    {
        $presenceResultEntry->setChoiceId($statusId);
        $presenceResultEntry->setPresenceStatusId($fixedStatusId);
        $presenceResultEntry->setCheckedInDate($fixedStatusId == 3 ? (new DateTime())->getTimestamp() : 0);
        $presenceResultEntry->setCheckedOutDate(0);
        return $presenceResultEntry;
    }

    /**
     * @param int $periodId
     * @param int $userId
     *
     * @return PresenceResultEntry
     */
    public function togglePresenceResultEntryCheckout(int $periodId, int $userId): PresenceResultEntry
    {
        $presenceResultEntry = $this->presenceRepository->getPresenceResultEntry($periodId, $userId);

        if (! $presenceResultEntry instanceof PresenceResultEntry)
        {
            throw new InvalidArgumentException();
        }

        $checkedInDate = $presenceResultEntry->getCheckedInDate();
        $checkedOutDate = $presenceResultEntry->getCheckedOutDate();

        if ($checkedInDate > 0)
        {
            $isCheckedOut = $checkedOutDate > $checkedInDate;
            $presenceResultEntry->setCheckedOutDate($isCheckedOut ? 0 : (new DateTime())->getTimestamp());
            $this->presenceRepository->updatePresenceResultEntry($presenceResultEntry);
        }

        return $presenceResultEntry;
    }

    /**
     * @param array $user
     * @param int $periodId
     *
     * @return array
     */
    protected function completeUserFields(array $user, int $periodId): array
    {
        $userId = (int) $user['id'];
        $user['id'] = $userId;

        if (!isset($user['photo']))
        {
            $user['photo'] = $this->userService->getProfilePhotoUrl($userId);
        }

        $periodStr = 'period#' . $periodId;
        $statusStr = $periodStr . '-status';
        $checkedInStr = $periodStr . '-checked_in_date';
        $checkedOutStr = $periodStr . '-checked_out_date';

        if (!array_key_exists($statusStr, $user))
        {
            $user[$statusStr] = NULL;
        }

        if (array_key_exists($checkedInStr, $user))
        {
            $user[$checkedInStr] = (int) $user[$checkedInStr];
        }

        if (array_key_exists($checkedOutStr, $user))
        {
            $user[$checkedOutStr] = (int) $user[$checkedOutStr];
        }

        return $user;
    }

    /**
     * @param ChamiloRequest $request
     * @param bool $clear
     *
     * @return FilterParameters
     */
    public function createFilterParameters(ChamiloRequest $request, bool $clear = false): FilterParameters
    {
        $fieldMapper = $this->userService->getFieldMapper();
        $filterParameters = $this->filterParametersBuilder->buildFilterParametersFromRequest($request, $fieldMapper);

        if ($clear)
        {
            return $filterParameters->setCount(null)->setOffset(null);
        }
        return $filterParameters;
    }

    /**
     * @param Presence $presence
     * @param ContextIdentifier $contextIdentifier
     *
     * @return array
     */
    public function getDistinctPresenceResultEntryUsers(Presence $presence, ContextIdentifier $contextIdentifier): array
    {
        $users = $this->presenceRepository->getDistinctPresenceResultEntryUsers($presence->getId(), $contextIdentifier);
        return iterator_to_array($users);
    }


    /**
     * @param array $users
     * @param array $registeredUserIds
     *
     * @return array
     */
    public function filterNonRegisteredPresenceResultEntryUsers(array $users, array $registeredUserIds): array
    {
        $nonRegisteredUsers = [];
        foreach ($users as $user)
        {
            if (!in_array($user['user_id'], $registeredUserIds))
            {
                $nonRegisteredUsers[] = $user['user_id'];
            }
        }
        return $nonRegisteredUsers;
    }
}
