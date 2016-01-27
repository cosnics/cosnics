<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentSelectQuestion\Ajax;

use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Architecture\JsonAjaxResult;

class AssessmentSelectQuestionComponent extends \Chamilo\Core\Repository\ContentObject\AssessmentSelectQuestion\Ajax\Manager
{

    public function run()
    {
        $value = Request :: post('value');
        $action = Request :: post('action');

        switch ($action)
        {
            case 'skip_option' :
                $_SESSION['select_skip_options'][] = $value;
        }

        JsonAjaxResult :: success();
    }
}