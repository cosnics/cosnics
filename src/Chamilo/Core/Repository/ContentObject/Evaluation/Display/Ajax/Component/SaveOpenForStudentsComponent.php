<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Ajax\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\JsonAjaxResult;

/**
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Display\Ajax\Component
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class SaveOpenForStudentsComponent extends Manager
{
    function run()
    {
        try
        {
            if (!$this->getRequest()->isMethod('POST'))
            {
                throw new NotAllowedException();
            }
            $openForStudents = $this->getRequest()->getFromPost('open_for_students') == 'true';

            $this->getEvaluationServiceBridge()->setOpenForStudents($openForStudents);

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