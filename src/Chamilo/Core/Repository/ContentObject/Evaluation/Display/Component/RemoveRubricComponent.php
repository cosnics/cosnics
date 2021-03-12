<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Component;

use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Cache\ParameterBag;

/**
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Display\Component
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class RemoveRubricComponent extends Manager
{
    /**
     * @return string
     * @throws \Exception
     */
    public function run()
    {
        if (!$this->getEvaluationServiceBridge()->canEditEvaluation() || $this->getRequest()->getMethod() != 'POST')
        {
            throw new NotAllowedException();
        }

        try
        {
            $object = $this->get_root_content_object();
            $object->setRubricId(null);
            $object->update();
            $message = 'RubricRemoved';
        }
        catch (\Exception $ex)
        {
            $success = false;
            $message = 'RubricNotRemoved';
            $this->getExceptionLogger()->logException($ex);
        }

        $this->redirect(
            $this->getTranslator()->trans($message, [], Manager::context()),
            !$success,
            [self::PARAM_ACTION => self::DEFAULT_ACTION]
        );

        return null;
    }
}
