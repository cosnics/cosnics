<?php

namespace Chamilo\Core\Repository\ContentObject\Presence\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\Presence\Display\Ajax\Manager;
use Chamilo\Core\Repository\ContentObject\Presence\Domain\PresenceResultEntryFilterOptions;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Storage\FilterParameters\FilterParameters;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @package Chamilo\Core\Repository\ContentObject\Presence\Display\Ajax\Component
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class LoadPresenceEntriesComponent extends Manager
{
    function run()
    {
        try
        {
            $presence = $this->getPresence();
            $canEditPresence = $this->canUserEditPresence();
            $contextIdentifier = $this->getPresenceServiceBridge()->getContextIdentifier();

            $periods = $this->getPresenceResultPeriodService()->getResultPeriodsForPresence($presence, $contextIdentifier, $canEditPresence);
            $presenceResultEntryService = $this->getPresenceResultEntryService();

            $statusFilters =  $this->getRequest()->getFromPostOrUrl('status_filters');
            $periodId = $this->getRequest()->getFromPostOrUrl('period_id');
            $withoutStatusSelected = $this->getRequest()->getFromPostOrUrl('without_status') == 'true';

            $useFilters = isset($periodId) && ($withoutStatusSelected || !empty($statusFilters));

            $filterOptions = null;

            if ($useFilters)
            {
                $filterOptions = new PresenceResultEntryFilterOptions();
                $filterOptions->periodId = $periodId;
                if (!empty($statusFilters))
                {
                    $filterOptions->statusFilters = $statusFilters;
                }
                $filterOptions->withoutStatus = $withoutStatusSelected;

                $userIds = $this->getPresenceServiceBridge()->getTargetUserIds($this->createFilterParameters(true));
                $users = $presenceResultEntryService->getUsers($userIds, $periods, $contextIdentifier, $this->createFilterParameters(), $filterOptions);
            }
            else
            {
                $userIds = $this->getTargetUserIds();
                $users = $presenceResultEntryService->getUsers($userIds, $periods, $contextIdentifier, $this->createFilterParameters(true));
            }

            $resultData = ['students' => $users, 'periods' => $periods];

            $requestCount = $this->getRequest()->getFromPostOrUrl('request_count') == 'true';
            $requestNonRegisteredUsers = $this->getRequest()->getFromPostOrUrl('request_non_course_students') == 'true';

            if ($canEditPresence && ($requestCount || $requestNonRegisteredUsers))
            {
                // Note:
                // $allTargetUserIds will only be effectively all users if no global query filter is set.
                // If $requestNonRegisteredUsers is set then the results in $resultData['non_course_students'] will likely be incorrect.
                // However this shouldn't be a problem because $requestNonRegisteredUsers is normally only true the first time around when no global query filter is set.

                $allTargetUserIds = $useFilters ? $userIds : $this->getPresenceServiceBridge()->getTargetUserIds($this->createFilterParameters(true));

                if ($requestCount) // todo: count will be wrong when status filters are set
                {
                    if ($useFilters)
                    {
                        $resultData['count'] = count($presenceResultEntryService->getUsers($allTargetUserIds, $periods, $contextIdentifier, $this->createFilterParameters(true), $filterOptions));
                    }
                    else
                    {
                        $resultData['count'] = count($allTargetUserIds);
                    }
                }

                if ($requestNonRegisteredUsers)
                {
                    $resultData['non_course_students'] = false;
                    $distinctUsers = $presenceResultEntryService->getDistinctPresenceResultEntryUsers($presence, $contextIdentifier);
                    $nonRegisteredUserIds = $presenceResultEntryService->filterNonRegisteredPresenceResultEntryUsers($distinctUsers, $allTargetUserIds);
                    $hasNonRegisteredUsers = count($nonRegisteredUserIds) > 0;
                    if ($hasNonRegisteredUsers)
                    {
                        $nonRegisteredUsers = $presenceResultEntryService->getUsers($nonRegisteredUserIds, $periods, $contextIdentifier, new FilterParameters());
                        $resultData['non_course_students'] = $nonRegisteredUsers;
                    }
                }
            }

            return new JsonResponse($this->serialize($resultData), 200, [], true);
        }
        catch (\Exception $ex)
        {
            return new JsonResponse(['error' => ['code' => 500, 'message' => $ex->getMessage()]], 500);
        }
    }

    /**
     * @return int[]
     * @throws NotAllowedException
     */
    protected function getTargetUserIds(): array
    {
        if ($this->canUserEditPresence())
        {
            return $this->getPresenceServiceBridge()->getTargetUserIds($this->createFilterParameters());
        }

        if ($this->canUserViewPresence())
        {
            return [$this->getUser()->getId()];
        }

        throw new NotAllowedException();
    }

    /**
     * @param bool $clear
     *
     * @return FilterParameters
     */
    protected function createFilterParameters(bool $clear = false): FilterParameters
    {
        return $this->getPresenceResultEntryService()->createFilterParameters($this->getRequest(), $clear);
    }
}
