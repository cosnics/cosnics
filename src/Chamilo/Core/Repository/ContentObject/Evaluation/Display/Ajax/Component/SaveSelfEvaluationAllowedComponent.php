<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Ajax\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Platform\Security\Csrf\CsrfComponentInterface;

/**
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Display\Ajax\Component
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class SaveSelfEvaluationAllowedComponent extends Manager implements CsrfComponentInterface
{
    function run()
    {
        try
        {
            if (!$this->getRequest()->isMethod('POST'))
            {
                throw new NotAllowedException();
            }
            $selfEvaluationAllowed = $this->getRequest()->getFromPost('self_evaluation_allowed') == 'true';

            $this->getEvaluationServiceBridge()->setSelfEvaluationAllowed($selfEvaluationAllowed);

            $result = new JsonAjaxResult();
            $result->set_result_code(200);
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