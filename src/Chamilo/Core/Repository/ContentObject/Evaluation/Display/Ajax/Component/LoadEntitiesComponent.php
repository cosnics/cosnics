<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Ajax\Manager;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service\Entity\EvaluationEntityRetrieveProperties;
use Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\Evaluation;
use Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\EvaluationEntryScore;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Storage\FilterParameters\FilterParameters;
use Chamilo\Libraries\Storage\FilterParameters\FilterParametersBuilder;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Display\Ajax\Component
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class LoadEntitiesComponent extends Manager
{
    /**
     */
    function run()
    {
        try
        {
            $evaluation = $this->get_root_content_object();

            if (!$evaluation instanceof Evaluation)
            {
                $this->throwUserException('EvaluationNotFound');
            }

            $entityIds = $this->getEvaluationServiceBridge()->getTargetEntityIds();
            $contextIdentifier = $this->getEvaluationServiceBridge()->getContextIdentifier();

            $filterParameters = $this->createFilterParameters();
            $entityService = $this->getEntityServiceByType($this->getEvaluationServiceBridge()->getCurrentEntityType());
            $selectedEntities = $entityService->getEntitiesFromIds($entityIds, $contextIdentifier, EvaluationEntityRetrieveProperties::ALL(), $filterParameters);

            $entities = array();
            foreach ($selectedEntities as $entity)
            {
                $entity['presence_status'] = 'neutral';

                if ($entity['score_registered'])
                {
                    $entity['presence_status'] = ($entity['is_absent']) ? 'absent' : 'present';
                }

                if ($this->getEvaluationServiceBridge()->getCurrentEntityType() !== 0)
                {
                    $members = $this->getEvaluationServiceBridge()->getUsersForEntity($entity['id']);
                    $entityMembers = array();
                    foreach ($members as $member)
                    {
                        $entityMembers[] = ['lastname' => $member->get_lastname(), 'firstname' => $member->get_firstname()];
                    }
                    $entity['members'] = $entityMembers;
                }

                if (is_null($entity['score']))
                {
                    $entity['score'] = '';
                }

                $entities[] = $entity;
            }

            $resultData = ['entities' => $entities];

            if ($this->getRequest()->getFromPostOrUrl('request_count') == 'true')
            {
                $resultData['count'] = $entityService->countEntitiesFromIds($entityIds, new FilterParameters());
            }

            $result = new JsonAjaxResult(200, $resultData);
            $result->display();
        }
        catch (\Exception $ex)
        {
            $result = new JsonAjaxResult();
            $result->set_result_code(500);
            $result->set_result_message($ex->getMessage());
            $result->display();
        }
    }

    /**
     * @return FilterParameters
     */
    protected function createFilterParameters(): FilterParameters
    {
        $entityService = $this->getEntityServiceByType($this->getEvaluationServiceBridge()->getCurrentEntityType());
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
        return $filterParameters;
    }
}
