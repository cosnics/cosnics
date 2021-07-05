<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Ajax\Manager;
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
        try
        {
            $this->validateEvaluationEntityInput();

            $entityId = $this->getRequest()->getFromPost('entity_id');
            $score = (int) $this->getRequest()->getFromPost('score');
            $evaluation = $this->getEvaluation();
            $evaluationId = $evaluation->getId();
            $evaluatorId = $this->getUser()->getId();
            $contextId = $this->getEvaluationServiceBridge()->getContextIdentifier();
            $entityType = $this->getEvaluationServiceBridge()->getCurrentEntityType();

            $evaluationEntryScore = $this->getEvaluationEntryService()->createOrUpdateEvaluationEntryScoreForEntity($evaluationId, $evaluatorId, $contextId, $entityType, $entityId, $score);

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