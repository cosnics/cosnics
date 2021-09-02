<?php

namespace Chamilo\Core\Repository\ContentObject\Presence\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\Presence\Display\Ajax\Manager;
use Chamilo\Core\Repository\ContentObject\Presence\Storage\DataClass\Presence;
use Chamilo\Libraries\Storage\FilterParameters\FilterParameters;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @package Chamilo\Core\Repository\ContentObject\Presence\Display\Ajax\Component
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class LoadPresenceEntriesComponent extends Manager
{
    /**
     */
    function run()
    {
        try
        {
            $presence = $this->getPresence();

            if (!$presence instanceof Presence)
            {
                $this->throwUserException('PresenceNotFound');
            }

            $userIds = $this->getPresenceServiceBridge()->getTargetUserIds($this->createFilterParameters());
            $contextIdentifier = $this->getPresenceServiceBridge()->getContextIdentifier();

            $filterParameters = $this->createFilterParameters()->setCount(null)->setOffset(null);
            $userService = $this->getUserService();
            $users = $userService->getUsersFromIds($userIds, $contextIdentifier, $filterParameters);

            $presenceService = $this->getPresenceService();
            $periods = $presenceService->getResultPeriodsForPresence($presence->getId(), $contextIdentifier);

            if (count($periods) == 0)
            {
                $period = $presenceService->createPresenceResultPeriod($presence, $contextIdentifier);
                $periods = [['date' => (int) $period->getDate(), 'id' => (int) $period->getId()]];
            }

            foreach ($periods as $period)
            {
                foreach ($users as $index => $user)
                {
                    if (! array_key_exists('period#' . $period['id'] . '-status', $user))
                    {
                        $user['period#' . $period['id'] . '-status'] = NULL;
                        $users[$index] = $user;
                    }
                }
            }

            $resultData = ['students' => $users, 'periods' => $periods, 'last' => (int) end($periods)['id']];

            if ($this->getRequest()->getFromPostOrUrl('request_count') == 'true')
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
     * @return FilterParameters
     */
    protected function createFilterParameters(): FilterParameters
    {
        $userService = $this->getUserService();
        $filterParametersBuilder = $this->getFilterParametersBuilder();
        $fieldMapper = $userService->getFieldMapper();
        return $filterParametersBuilder->buildFilterParametersFromRequest($this->getRequest(), $fieldMapper);
    }
}
