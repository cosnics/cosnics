<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Ajax\Manager;
use Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\Evaluation;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\Architecture\JsonAjaxResult;

/**
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Display\Ajax\Component
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class SetUseFeedbackComponent extends Manager
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

            $useFeedback = filter_var($this->getRequest()->getFromPost(self::PARAM_USE_FEEDBACK), FILTER_VALIDATE_BOOLEAN);
            $object->setUseFeedback($useFeedback);
            $object->update();
            $result = new JsonAjaxResult(200, [self::PARAM_USE_FEEDBACK => $object->useFeedback()]);
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
}