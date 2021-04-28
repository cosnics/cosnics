<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Component;

use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\Platform\Security\Csrf\CsrfComponentInterface;
use spec\Behat\MinkExtension\Listener\SessionsListenerSpec;

/**
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Display\Component
 *
 * @author - Stefan Gabriëls - Hogeschool Gent
 */
class SaveScoreComponent extends Manager implements CsrfComponentInterface
{
    public function run()
    {
        if (!$this->getRightsService()->canUserEditEvaluation()) {
            throw new NotAllowedException();
        }

        try
        {
            $this->validateEvaluationEntityInput();

            $entityId = $this->getRequest()->query->get('entity_id');
            $action = $this->getRequest()->getFromPost('action');
            $score = $this->getRequest()->getFromPost('score');

            $evaluation = $this->get_root_content_object();

            switch ($action)
            {
                case 'present':
                    $this->getEvaluationServiceBridge()->saveEntityAsPresent($evaluation->getId(), $this->getUser()->getId(), $entityId);
                    break;
                case 'absent':
                    $this->getEvaluationServiceBridge()->saveEntityAsAbsent($evaluation->getId(), $this->getUser()->getId(), $entityId);
                    break;
                default:
                    $this->getEvaluationServiceBridge()->saveEntryScoreForEntity($evaluation->getId(), $this->getUser()->getId(), $entityId, $score);
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