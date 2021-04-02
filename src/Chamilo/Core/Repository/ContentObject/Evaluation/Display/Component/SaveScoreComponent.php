<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Component;

use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Manager;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
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
        try
        {
            $this->validateSaveScoreInput();

            $entityId = $this->getRequest()->query->get('entity_id');
            $score = $this->getRequest()->getFromPost('score');

            $evaluation = $this->get_root_content_object();

            $this->getEvaluationServiceBridge()->saveEntryScoreForEntity($evaluation->getId(), $this->getUser()->getId(), $entityId, $score);

            $message = 'ScoreEntryComplete';
            $success = true;
        } catch (\Exception $ex)
        {
            $message = 'ScoreEntryFail';
            $success = false;
        }

        $this->redirect(
            $this->getTranslator()->trans($message, [], Manager::context()),
            !$success,
            [self::PARAM_ACTION => self::ACTION_ENTRY]
        );
    }
}