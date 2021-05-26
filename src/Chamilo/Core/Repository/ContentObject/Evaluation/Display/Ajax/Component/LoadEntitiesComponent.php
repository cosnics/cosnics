<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Ajax\Manager;
use Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\Evaluation;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Storage\FilterParameters\FilterParameters;

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

            $entityService = $this->getEntityServiceByType($this->getEvaluationServiceBridge()->getCurrentEntityType());

            $filterParametersBuilder = $this->getFilterParametersBuilder();
            $filterParameters = $filterParametersBuilder->buildFilterParametersFromRequest($this->getRequest(), $entityService->getFieldMapper());

            $selectedEntities = $entityService->getEntitiesFromIds($entityIds, $contextIdentifier, $filterParameters);

            $entities = array();
            foreach ($selectedEntities as $entity)
            {
                $entity['presence_status'] = 'neutral';

                if ($entity['score_registered'])
                {
                    $entity['presence_status'] = ($entity['is_absent']) ? 'absent' : 'present';
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
}
