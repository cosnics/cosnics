<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Ajax\Manager;
use Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\Evaluation;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Display\Ajax\Component
 *
 * @author Sven Vanpoucke - Hogeschool Gent
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
                throw new UserException(
                    $this->getTranslator()->trans('EvaluationNotFound', [], \Chamilo\Core\Repository\ContentObject\Evaluation\Display\Manager::context())
                );
            }

            $contextIdentifier = $this->getEvaluationServiceBridge()->getContextIdentifier();

            $userIds = $this->getEvaluationServiceBridge()->getTargetEntityIds();
            $sortColumn = $this->getRequest()->get('sort_by');
            $sortDesc = $this->getRequest()->get('sort_desc') == 'true';
            $perPage = $this->getRequest()->get('per_page');
            $offset = $perPage * ($this->getRequest()->get('current_page') - 1);

            $sortProperties = $this->getSortProperties();

            $selectedUsers = $this->getEntityService()->getUsersFromIds($userIds, $sortProperties, $sortColumn, $sortDesc, $offset, $perPage);

            $resultData = [
                'entity_type' => $this->getEvaluationServiceBridge()->getCurrentEntityType(),
                'context' => $contextIdentifier->getContextClass() . ' - ' . $contextIdentifier->getContextId(),
                'entities' => iterator_to_array($selectedUsers)
            ];

            if ($this->getRequest()->get('request_count') == 'true')
            {
                $users = $this->getEntityService()->getUsersFromIds($userIds, $sortProperties);
                $resultData['count'] = count($users);
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
     * @return PropertyConditionVariable[]
     */
    protected function getSortProperties(): array
    {
        $sortProperties = [
            'firstname' => new PropertyConditionVariable(User::class_name(), User::PROPERTY_FIRSTNAME),
            'lastname' => new PropertyConditionVariable(User::class_name(), User::PROPERTY_LASTNAME),
            'official_code' => new PropertyConditionVariable(User::class_name(), User::PROPERTY_OFFICIAL_CODE),
        ];
        return $sortProperties;
    }
}
