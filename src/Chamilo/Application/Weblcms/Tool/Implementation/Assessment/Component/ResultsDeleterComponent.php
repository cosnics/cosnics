<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

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

            $condition = new EqualityCondition(
                new PropertyConditionVariable(
                    \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssessmentAttempt :: class_name(),
                    \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssessmentAttempt :: PROPERTY_ID),
                new StaticConditionVariable($uaid));

            $item = DataManager :: retrieve(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssessmentAttempt :: class_name(),
                new DataClassRetrieveParameters($condition));

            if ($item)
            {
                $redirect_aid = $item->get_assessment_id();
            }

            $this->delete_user_assessment_results($item);
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
            $condition = new EqualityCondition(
                new PropertyConditionVariable(
                    \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\QuestionAttempt :: class_name(),
                    \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\QuestionAttempt :: PROPERTY_ASSESSMENT_ATTEMPT_ID),
                new StaticConditionVariable($user_assessment->get_id()));

            $items = DataManager :: retrieves(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\QuestionAttempt :: class_name(),
                new DataClassRetrievesParameters($condition));

            while ($question_attempt = $items->next_result())
            {
                $question_attempt->delete();
            }

            $user_assessment->delete();
        }
    }

    public function delete_assessment_results($aid)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssessmentAttempt :: class_name(),
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssessmentAttempt :: PROPERTY_ASSESSMENT_ID),
            new StaticConditionVariable($aid));

        $items = DataManager :: retrieves(
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssessmentAttempt :: class_name(),
            new DataClassRetrievesParameters($condition));

        while ($assessment_attempt = $items->next_result())
        {
            $this->delete_user_assessment_results($assessment_attempt);
        }
    }

    /**
     * @param BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $this->addBrowserBreadcrumb($breadcrumbtrail);
    }
}
