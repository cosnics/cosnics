<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Ajax\Manager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Platform\Security\Csrf\CsrfComponentInterface;

/**
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Display\Ajax\Component
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class SavePresenceStatusComponent extends Manager implements CsrfComponentInterface
{
    public function run(): string
    {
        try
        {
            $this->validateEvaluationEntityInput();

            $entityId = $this->getRequest()->getFromPost('entity_id');
            $isPresent = $this->getRequest()->getFromPost('presence_status') == 'true';

            $evaluation = $this->getEvaluation();
            $evaluationId = $evaluation->getId();
            $evaluatorId = $this->getUser()->getId();
            $contextId = $this->getEvaluationServiceBridge()->getContextIdentifier();
            $entityType = $this->getEvaluationServiceBridge()->getCurrentEntityType();

            if ($isPresent)
            {
                $evaluationEntryScore = $this->getEvaluationEntryService()->saveEntityAsPresent($evaluationId, $evaluatorId, $contextId, $entityType, $entityId);
            }
            else
            {
                $evaluationEntryScore = $this->getEvaluationEntryService()->saveEntityAsAbsent($evaluationId, $evaluatorId, $contextId, $entityType, $entityId);
            }

            $result = new JsonAjaxResult(200, ['entity_id' => $entityId, 'score' => $evaluationEntryScore->getScore(), 'presence_status' => $evaluationEntryScore->isAbsent() ? 'absent' : 'present']);
            $result->display();
        } catch (\Exception $ex)
        {
            $result = new JsonAjaxResult();
            $result->set_result_code(500);
            $result->set_result_message($ex->getMessage());
            $result->display();
        }
    }
}