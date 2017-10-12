<?php
namespace Chamilo\Libraries\Ajax\Component;

use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Platform\Session\Request;

/**
 *
 * @package Chamilo\Libraries\Ajax\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class OrderingQuestionComponent extends \Chamilo\Libraries\Ajax\Manager
{

    /**
     *
     * @see \Chamilo\Libraries\Architecture\Application\Application::run()
     */
    public function run()
    {
        $value = Request::post('value');
        $action = Request::post('action');

        switch ($action)
        {
            case 'skip_option' :
                $_SESSION['ordering_skip_options'][] = $value;
        }

        JsonAjaxResult::success();
    }
}