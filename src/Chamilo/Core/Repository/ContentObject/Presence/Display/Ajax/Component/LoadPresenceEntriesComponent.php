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

            $periods = $this->getPresenceResultEntryService()->getResultPeriods($presence, $contextIdentifier, $canEditPresence, true);
            $users = $this->getPresenceResultEntryService()->getUsers($userIds, $periods, $contextIdentifier, $filterParameters);

            $resultData = ['students' => $users, 'periods' => $periods];

            if ($canEditPresence && $this->getRequest()->getFromPostOrUrl('request_count') == 'true')
            {
                $resultData['count'] = count($this->getPresenceServiceBridge()->getTargetUserIds($filterParameters));
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
