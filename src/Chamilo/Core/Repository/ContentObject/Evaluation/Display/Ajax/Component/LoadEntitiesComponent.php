<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Ajax\Manager;
use Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\Evaluation;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Storage\FilterParameters\FieldMapper;
use Chamilo\Core\User\Storage\DataClass\User;

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
            $object = $this->get_root_content_object();

            if (!$object instanceof Evaluation)
            {
                $this->throwUserException('EvaluationNotFound');
            }

            $contextIdentifier = $this->getEvaluationServiceBridge()->getContextIdentifier();
            $userIds = $this->getEvaluationServiceBridge()->getTargetEntityIds();

            $filterParametersBuilder = $this->getFilterParametersBuilder();
            $filterParameters = $filterParametersBuilder->buildFilterParametersFromRequest($this->getRequest(), $this->getFieldMapper());
            $selectedUsers = $this->getEntityService()->getUsersFromIDs($userIds, $filterParameters);

            $resultData = [
                'entity_type' => $this->getEvaluationServiceBridge()->getCurrentEntityType(),
                'context' => $contextIdentifier->getContextClass() . ' - ' . $contextIdentifier->getContextId(),
                'entities' => iterator_to_array($selectedUsers)
            ];

            if ($this->getRequest()->getFromPostOrUrl('request_count') == 'true')
            {
                $resultData['count'] = $this->getEntityService()->countUsersFromIDs($userIds, $filterParameters);
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
     * @return FieldMapper
     */
    protected function getFieldMapper(): FieldMapper
    {
        $class_name = User::class_name();
        $fieldMapper = new FieldMapper();
        $fieldMapper->addFieldMapping('firstname', $class_name, User::PROPERTY_FIRSTNAME);
        $fieldMapper->addFieldMapping('lastname', $class_name, User::PROPERTY_LASTNAME);
        $fieldMapper->addFieldMapping('official_code', $class_name, User::PROPERTY_OFFICIAL_CODE);
        return $fieldMapper;
    }
}
