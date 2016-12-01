<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentMatchingQuestion\Ajax\Component;

use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Platform\Session\Request;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\AssessmentMatchingQuestion\Ajax\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class AssessmentMatchingQuestionComponent extends \Chamilo\Core\Repository\ContentObject\AssessmentMatchingQuestion\Ajax\Manager
{

    public function run()
    {
        $value = Request::post('value');
        $action = Request::post('action');
        
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