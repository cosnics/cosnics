<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentMultipleChoiceQuestion\Ajax;

use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Architecture\JsonAjaxResult;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\AssessmentMultipleChoiceQuestion\Ajax
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class AssessmentMultipleChoiceQuestionComponent extends \Chamilo\Core\Repository\ContentObject\AssessmentMultipleChoiceQuestion\Ajax\Manager
{

    public function run()
    {
        $value = Request :: post('value');
        $action = Request :: post('action');

        switch ($action)
        {
            case 'skip_option' :
                $_SESSION['mc_skip_options'][] = $value;
        }

        JsonAjaxResult :: success();
    }
}