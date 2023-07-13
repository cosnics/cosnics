<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentMatrixQuestion\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\AssessmentMatrixQuestion\Ajax\Manager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;

/**
 * @package Chamilo\Core\Repository\ContentObject\AssessmentMatrixQuestion\Ajax\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class AssessmentMatrixQuestionComponent extends Manager
{

    public function run()
    {
        $value = $this->getRequest()->request->get('value');
        $action = $this->getRequest()->request->get('action');

        switch ($action)
        {
            case 'skip_option' :
                $_SESSION['mq_skip_options'][] = $value;
                break;
            case 'skip_match' :
                $_SESSION['mq_skip_matches'][] = $value;
                break;
        }

        JsonAjaxResult::success();
    }
}