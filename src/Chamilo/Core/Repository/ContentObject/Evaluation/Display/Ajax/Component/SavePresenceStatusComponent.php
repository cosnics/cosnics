<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Ajax\Manager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;

/**
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Display\Ajax\Component
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class SavePresenceStatusComponent extends Manager
{
    public function run(): string
    {
        try
        {
            $this->validateEvaluationEntityInput();

            $entityId = $this->getRequest()->getFromPost('entity_id');
            $isPresent = $this->getRequest()->getFromPost('presence_status') == 'true';

            $evaluation = $this->get_root_content_object();

            if ($isPresent)
            {
                $evaluationEntryScore = $this->getEvaluationServiceBridge()->saveEntityAsPresent($evaluation->getId(), $this->getUser()->getId(), $entityId);
            }
            else
            {
                $evaluationEntryScore = $this->getEvaluationServiceBridge()->saveEntityAsAbsent($evaluation->getId(), $this->getUser()->getId(), $entityId);
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