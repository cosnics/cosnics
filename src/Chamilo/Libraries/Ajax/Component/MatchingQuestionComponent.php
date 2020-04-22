<?php
namespace Chamilo\Libraries\Ajax\Component;

use Chamilo\Libraries\Ajax\Manager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;

/**
 *
 * @package Chamilo\Libraries\Ajax\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class MatchingQuestionComponent extends Manager
{

    /**
     *
     * @see \Chamilo\Libraries\Architecture\Application\Application::run()
     */
    public function run()
    {
        $request = $this->getRequest();
        $value = $request->request->get('value');
        $action = $request->request->get('action');

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