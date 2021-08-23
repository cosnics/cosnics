<?php

namespace Chamilo\Core\Repository\ContentObject\Presence\Display\Ajax\Component;

use Chamilo\Core\Notification\Storage\Entity\Filter;
use Chamilo\Core\Repository\ContentObject\Presence\Display\Ajax\Manager;
//use Chamilo\Core\Repository\ContentObject\Presence\Display\Service\Entity\EvaluationEntityRetrieveProperties;
use Chamilo\Core\Repository\ContentObject\Presence\Storage\DataClass\Presence;
//use Chamilo\Core\Repository\ContentObject\Presence\Storage\DataClass\EvaluationEntryScore;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Storage\FilterParameters\FilterParameters;
use Chamilo\Libraries\Storage\FilterParameters\FilterParametersBuilder;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Symfony\Component\HttpFoundation\JsonResponse;
use DateTime;

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

            $studentIds = $this->getPresenceServiceBridge()->getTargetUserIds();
            $contextIdentifier = $this->getPresenceServiceBridge()->getContextIdentifier();

            $d = new DateTime();

            $filterParameters = $this->createFilterParameters();

            $userService = $this->getUserService();
            $users = $userService->getUsersFromIds($studentIds, $contextIdentifier, $filterParameters);

            $presenceService = $this->getPresenceService();
            $periods = $presenceService->getResultPeriodsForPresence($presence->getId(), $contextIdentifier);

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
                //$resultData['count'] = $entityService->countEntitiesFromIds($entityIds, new FilterParameters());
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
        return new FilterParameters();
        /*$entityService = $this->getEntityServiceByType($this->getEvaluationServiceBridge()->getCurrentEntityType());
        $filterParametersBuilder = $this->getFilterParametersBuilder();
        $fieldMapper = $entityService->getFieldMapper();
        $fieldMapper->addFieldMapping('score', EvaluationEntryScore::class_name(), EvaluationEntryScore::PROPERTY_SCORE);
        $filterParameters = $filterParametersBuilder->buildFilterParametersFromRequest($this->getRequest(), $fieldMapper);
        if ($this->getRequest()->getFromPostOrUrl(FilterParametersBuilder::PARAM_SORT_FIELD) === 'score')
        {
            $sortDirection = strtoupper($this->getRequest()->getFromPostOrUrl(FilterParametersBuilder::PARAM_SORT_DIRECTION));
            $filterParameters->addOrderBy(new OrderBy(
                new PropertyConditionVariable(EvaluationEntryScore::class_name(), EvaluationEntryScore::PROPERTY_IS_ABSENT),
                $sortDirection == FilterParametersBuilder::SORT_ASC ? SORT_DESC : SORT_ASC
            ));
        }
        return $filterParameters;*/
    }
}
