<?php

namespace Chamilo\Core\Repository\ContentObject\Presence\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\Presence\Display\Ajax\Manager;
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
            $filterParameters = $this->createFilterParameters(true);
            $userIds = $this->getTargetUserIds();

            $periods = $this->getPresenceResultPeriodService()->getResultPeriodsForPresence($presence, $contextIdentifier, $canEditPresence);
            $presenceResultEntryService = $this->getPresenceResultEntryService();
            $users = $presenceResultEntryService->getUsers($userIds, $periods, $contextIdentifier, $filterParameters);

            $resultData = ['students' => $users, 'periods' => $periods];

            $requestCount = $this->getRequest()->getFromPostOrUrl('request_count') == 'true';
            $requestNonRegisteredUsers = $this->getRequest()->getFromPostOrUrl('request_non_registered_students') == 'true';

            if ($canEditPresence && ($requestCount || $requestNonRegisteredUsers))
            {
                $allTargetUserIds = $this->getPresenceServiceBridge()->getTargetUserIds($filterParameters);

                if ($requestCount)
                {
                    $resultData['count'] = count($allTargetUserIds);
                }

                if ($requestNonRegisteredUsers)
                {
                    $resultData['non_registered_students'] = false;
                    $distinctUsers = $presenceResultEntryService->getDistinctPresenceResultEntryUsers($presence, $contextIdentifier);
                    $nonRegisteredUserIds = $presenceResultEntryService->filterNonRegisteredPresenceResultEntryUsers($distinctUsers, $allTargetUserIds);
                    $hasNonRegisteredUsers = count($nonRegisteredUserIds) > 0;
                    if ($hasNonRegisteredUsers)
                    {
                        $nonRegisteredUsers = $presenceResultEntryService->getUsers($nonRegisteredUserIds, $periods, $contextIdentifier, new FilterParameters());
                        $resultData['non_registered_students'] = $nonRegisteredUsers;
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
