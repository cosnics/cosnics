<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Manager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 * $Id: assessment_results_deleter.class.php 216 2009-11-13 14:08:06Z kariboe $
 *
 * @package application.lib.weblcms.tool.assessment.component
 */
class ResultsDeleterComponent extends Manager
{

    public function run()
    {
        if (! $this->is_allowed(WeblcmsRights :: DELETE_RIGHT))
        {
            throw new NotAllowedException();
        }

        if (Request :: get(self :: PARAM_USER_ASSESSMENT))
        {
            $uaid = Request :: get(self :: PARAM_USER_ASSESSMENT);
            $track = new \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssessmentAttempt();
            $condition = new EqualityCondition(
                new PropertyConditionVariable(
                    \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssessmentAttempt :: class_name(),
                    \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssessmentAttempt :: PROPERTY_ID),
                new StaticConditionVariable($uaid));
            $items = $track->retrieve_tracker_items($condition);

            if ($items[0] != null)
            {
                $redirect_aid = $items[0]->get_assessment_id();
            }

            $this->delete_user_assessment_results($items[0]);
        }
        elseif (Request :: get(self :: PARAM_ASSESSMENT))
        {
            $aid = Request :: get(self :: PARAM_ASSESSMENT);
            $redirect_aid = $aid;
            $this->delete_assessment_results($aid);
        }

        $params = array(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => self :: ACTION_VIEW_RESULTS);

        if (isset($redirect_aid))
        {
            $params[self :: PARAM_ASSESSMENT] = $redirect_aid;
        }

        $this->redirect(Translation :: get('ResultsDeleted'), false, $params);
    }

    public function delete_user_assessment_results($user_assessment)
    {
        if ($user_assessment != null)
        {
            $track = new \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\QuestionAttempt();
            $condition = new EqualityCondition(
                new PropertyConditionVariable(
                    \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\QuestionAttempt :: class_name(),
                    \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\QuestionAttempt :: PROPERTY_ASSESSMENT_ATTEMPT_ID),
                new StaticConditionVariable($user_assessment->get_id()));
            $items = $track->retrieve_tracker_items($condition);

            foreach ($items as $question_attempt)
            {
                $question_attempt->delete();
            }

            $user_assessment->delete();
        }
    }

    public function delete_assessment_results($aid)
    {
        $track = new \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssessmentAttempt();
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssessmentAttempt :: class_name(),
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssessmentAttempt :: PROPERTY_ASSESSMENT_ID),
            new StaticConditionVariable($aid));
        $items = $track->retrieve_tracker_items($condition);

        foreach ($items as $assessment_attempt)
        {
            $this->delete_user_assessment_results($assessment_attempt);
        }
    }
}
