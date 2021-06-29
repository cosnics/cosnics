<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Component;

use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Security\Csrf\CsrfComponentInterface;

/**
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Display\Component
 *
 * @author - Stefan Gabriëls - Hogeschool Gent
 */
class SaveScoreComponent extends Manager implements CsrfComponentInterface
{
    public function run()
    {
        $this->ensureEntityIdentifier();

        if (!$this->getRightsService()->canUserEditEvaluation()) {
            throw new NotAllowedException();
        }

        try
        {
            $this->validateEvaluationEntityInput();

            $entityId = $this->getRequest()->query->get('entity_id');
            $action = $this->getRequest()->getFromPost('action');
            $score = (int) $this->getRequest()->getFromPost('score');
            $evaluation = $this->get_root_content_object();
            $evaluationId = $evaluation->getId();
            $evaluatorId = $this->getUser()->getId();
            $contextId = $this->getEvaluationServiceBridge()->getContextIdentifier();
            $entityType = $this->getEvaluationServiceBridge()->getCurrentEntityType();

            switch ($action)
            {
                case 'present':
                    $this->getEvaluationEntryService()->saveEntityAsPresent($evaluationId, $evaluatorId, $contextId, $entityType, $entityId);
                    break;
                case 'absent':
                    $this->getEvaluationEntryService()->saveEntityAsAbsent($evaluationId, $evaluatorId, $contextId, $entityType, $entityId);
                    break;
                default:
                    $this->getEvaluationEntryService()->createOrUpdateEvaluationEntryScoreForEntity($evaluationId, $evaluatorId, $contextId, $entityType, $entityId, $score);
            }

            $message = ($action == 'present' || $action == 'absent') ? 'PresenceStatusComplete' : 'ScoreEntryComplete';
            $success = true;
        } catch (\Exception $ex)
        {
            $action = $this->getRequest()->getFromPost('action');
            $message = ($action == 'present' || $action == 'absent') ? 'PresenceStatusFail' : 'ScoreEntryFail';
            $success = false;
        }

        $this->redirect(
            $this->getTranslator()->trans($message, [], Manager::context()),
            !$success,
            [self::PARAM_ACTION => self::ACTION_ENTRY]
        );
    }
}