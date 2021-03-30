<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Ajax\Manager;
use Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\Evaluation;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\JsonAjaxResult;

/**
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Display\Ajax\Component
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class SaveScoreComponent extends Manager
{
    public function run(): string
    {
        try {
            $req = $this->getRequest();

            if (!$req->isMethod('POST'))
            {
                throw new NotAllowedException();
            }

            $entityId = $req->getFromPost('entity_id');
            $score = $req->getFromPost('score') ?? '';

            if (empty($entityId))
            {
                $this->throwUserException('EntityIdNotProvided');
            }

            $entityType = $this->getEvaluationServiceBridge()->getCurrentEntityType();
            $contextIdentifier = $this->getEvaluationServiceBridge()->getContextIdentifier();
            $userIds = $this->getEvaluationServiceBridge()->getTargetEntityIds();

            if (! in_array($entityId, $userIds))
            {
                $this->throwUserException('EntityNotInList');
            }

            $evaluation = $this->get_root_content_object();

            if (! $evaluation instanceof Evaluation)
            {
                $this->throwUserException('EvaluationNotFound');
            }

            $this->getEntityService()->createOrUpdateEvaluationEntryScoreForEntity($evaluation->getId(), $this->getUser()->getId(), $contextIdentifier, $entityType, $entityId, $score);

            $result = new JsonAjaxResult(200, ['entity_id' => $entityId, 'score' => $score]);
            $result->display();

        } catch (\Exception $ex) {
            $result = new JsonAjaxResult();
            $result->set_result_code(500);
            $result->set_result_message($ex->getMessage());
            $result->display();
        }
    }
}