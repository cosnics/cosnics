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
            $this->validateSaveScoreInput();

            $entityId = $this->getRequest()->getFromPost('entity_id');
            $score = $this->getRequest()->getFromPost('score') ?? '';

            $evaluation = $this->get_root_content_object();

            $this->getEvaluationServiceBridge()->saveEntryScoreForEntity($evaluation->getId(), $this->getUser()->getId(), $entityId, $score);

            $result = new JsonAjaxResult(200, ['entity_id' => $entityId, 'score' => $score]);
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