<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\PeerAssessment\Component;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\PeerAssessmentFeedback as PeerAssessmentFeedbackTracker;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\PeerAssessmentScore as PeerAssessmentScoreTracker;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\PeerAssessment\Manager;
use Chamilo\Core\Repository\ContentObject\PeerAssessment\Display\PeerAssessmentDisplaySupport;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Represents the view component for the peer assessment tool.
 *
 * @author Renaat De Muynck
 */
class ComplexDisplayComponent extends Manager implements DelegateComponent, PeerAssessmentDisplaySupport
{
    // TODO optimize database queries
    // TODO cache query results
    function run()
    {
        if (! $this->is_allowed(\Chamilo\Core\Repository\ContentObject\PeerAssessment\Display\Manager::VIEW_RIGHT))
        {
            $this->redirect(
                Translation::get("NotAllowed", null, Utilities::COMMON_LIBRARIES),
                true,
                array(),
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION,
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID));
        }

        // check rights
        if (! $this->is_allowed(WeblcmsRights::VIEW_RIGHT, $this->get_publication()))
        {
            $this->redirect(
                Translation::get("NotAllowed", null, Utilities::COMMON_LIBRARIES),
                true,
                array(),
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION,
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID));
        }

        // launch
        $context = $this->get_root_content_object()->package() . '\Display';
        $factory = new ApplicationFactory(
            $context,
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
        return $factory->run();
    }

    // region settings

    // endregion settings

    // region user
    public function get_all_users($publication_id)
    {
        $users = array();
        $groups = $this->get_groups($publication_id);

        foreach ($groups as $g)
        {
            $users = array_merge($users, $this->get_group_users($g->get_id()));
        }

        return $users;
    }

    // endregion user

    // region group

    // endregion group

    // region attempt

    /**
     * closes each individual status
     *
     * @param int $id
     * @return boolean
     */
    public function close_attempt($id)
    {
        $groups = $this->get_groups($this->get_publication_id());

        foreach ($groups as $group)
        {
            $users = $this->get_group_users($group->get_id());

            foreach ($users as $user)
            {
                if (! $this->close_user_attempt($user->get_id(), $id))
                {
                    // error
                }
            }
        }
        return true;
    }

    public function toggle_attempt_visibility($id)
    {
        $attempt = $this->get_attempt($id);
        $hidden = ! $attempt->get_hidden();
        $attempt->set_hidden($hidden);
        $attempt->save();
        return $hidden;
    }

    // endregion attempt

    // region attempt_status
    private function update_user_attempt_factor($user_id, $attempt_id)
    {
        // get the settings
        // $settings = $this->get_settings($this->get_publication_id());
        // $settings->

        // calculate the factor
        $processor = $this->get_root_content_object()->get_result_processor();
        $processor->retrieve_scores($this, $user_id, $attempt_id);
        $factor = $processor->calculate();

        // update the status of the user
        $status = $this->get_user_attempt_status($user_id, $attempt_id);
        $status->set_factor($factor);
        $status->save();

        return $factor;
    }

    private function update_user_attempt_progress($user_id, $attempt_id)
    {
        // get the number of users in the user's group and the number of indicators
        $group = $this->get_user_group($user_id);

        if ($group)
        {
            $u_count = $this->count_group_users($group->get_id());
            $i_count = $this->count_indicators();

            // get the scores the user has submitted
            $status = $this->get_user_attempt_status($user_id, $attempt_id);
            $tracker = new PeerAssessmentScoreTracker();

            $condition = new AndCondition(
                new EqualityCondition(
                    new PropertyConditionVariable(
                        PeerAssessmentScoreTracker::class_name(),
                        PeerAssessmentScoreTracker::PROPERTY_ATTEMPT_STATUS_ID),
                    new StaticConditionVariable($status->get_id())));

            $items = DataManager::retrieves(
                PeerAssessmentScoreTracker::class_name(),
                new DataClassRetrievesParameters($condition))->as_array();

            // get the non empty values of the scores
            $s_count = array_reduce(
                $items,
                function ($result, $item)
                {
                    // is_numeric() returns 1 or 0 (true or false),
                    // add this to the result and we get the number of non empty values
                    return $result + is_numeric($item->get_score());
                },
                0);

            // calculate the progress percentage
            $progress = ($s_count * 100) / ($u_count * $i_count);

            // update the status of the user
            $status->set_progress($progress);
            $status->save();

            return $progress;
        }
        return false;
    }

    public function close_user_attempt($user_id, $attempt_id)
    {
        $status = $this->get_user_attempt_status($user_id, $attempt_id);
        $status->set_closed(time());
        $status->set_closed_by($this->get_user_id());
        $status->set_modified(time());
        $status->save();

        return $status->get_closed() ? true : false;
    }

    public function open_user_attempt($user_id, $attempt_id)
    {
        $status = $this->get_user_attempt_status($user_id, $attempt_id);
        $status->set_closed(null);
        $status->set_closed_by($this->get_user_id());
        $status->set_modified(time());
        $status->save();

        return $status->get_closed() ? false : true;
    }

    // endregion attempt_status

    // region scores
    public function get_user_scores_received($user_id, $attempt_id)
    {
        $group = $this->get_user_group($user_id);
        $users = $this->get_group_users($group->get_id());
        $indicators = $this->get_indicators();

        $tracker = new PeerAssessmentScoreTracker();

        $scores = array();
        foreach ($users as $u)
        {
            foreach ($indicators as $i)
            {
                $scores[$u->get_id()][$i->get_id()] = null;
            }

            $status = $this->get_user_attempt_status($u->get_id(), $attempt_id);
            $condition = new AndCondition(
                new EqualityCondition(
                    new PropertyConditionVariable(
                        PeerAssessmentScoreTracker::class_name(),
                        PeerAssessmentScoreTracker::PROPERTY_ATTEMPT_STATUS_ID),
                    new StaticConditionVariable($status->get_id())),
                new EqualityCondition(
                    new PropertyConditionVariable(
                        PeerAssessmentScoreTracker::class_name(),
                        PeerAssessmentScoreTracker::PROPERTY_USER_ID),
                    new StaticConditionVariable($user_id)));

            $items = DataManager::retrieves(
                PeerAssessmentScoreTracker::class_name(),
                new DataClassRetrievesParameters($condition))->as_array();

            foreach ($items as $item)
            {
                $scores[$u->get_id()][$item->get_indicator_id()] = $item->get_score();
            }
        }

        return $scores;
    }

    public function get_user_scores_given($user_id, $attempt_id)
    {
        // get the current attempt status and scores from the user
        $status = $this->get_user_attempt_status($user_id, $attempt_id);
        $tracker = new PeerAssessmentScoreTracker();
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                PeerAssessmentScoreTracker::class_name(),
                PeerAssessmentScoreTracker::PROPERTY_ATTEMPT_STATUS_ID),
            new StaticConditionVariable($status->get_id()));

        $items = DataManager::retrieves(
            PeerAssessmentScoreTracker::class_name(),
            new DataClassRetrievesParameters($condition))->as_array();

        // iterate over the results and put them in a two dimensional array
        $scores = array();

        foreach ($items as $item)
        {
            $scores[$item->get_user_id()][$item->get_indicator_id()] = $item->get_score();
        }

        return $scores;
    }

    public function save_scores($user_id, $attempt_id, array $scores)
    {
        // get the current attempt status from the user
        $status = $this->get_user_attempt_status(intval($user_id), intval($attempt_id));
        // save the status with the new time of modification
        $status->set_modified(time());
        if (! $status->save())
            return false;

            // get the scores the user has already filled in
        $tracker = new PeerAssessmentScoreTracker();
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                PeerAssessmentScoreTracker::class_name(),
                PeerAssessmentScoreTracker::PROPERTY_ATTEMPT_STATUS_ID),
            new StaticConditionVariable($status->get_id()));

        $items = DataManager::retrieves(
            PeerAssessmentScoreTracker::class_name(),
            new DataClassRetrievesParameters($condition))->as_array();

        // loop through the existing scores and update/delete them if necessary
        foreach ($items as $item)
        {
            $u = $item->get_user_id();
            $i = $item->get_indicator_id();

            // if there is a new score submitted
            if (isset($scores[$u][$i]))
            {
                // update the score in the database
                $item->set_score($scores[$u][$i]);
                $item->save();
                // remove the score from the list
                unset($scores[$u][$i]);
            }
            else
            {
                $item->delete();
            }
        }
        // loop through the remaining scores and add them to the database
        $parameters = array();
        $parameters[PeerAssessmentScoreTracker::PROPERTY_ATTEMPT_STATUS_ID] = $status->get_id();
        foreach ($scores as $u => $indicators)
        {
            $parameters[PeerAssessmentScoreTracker::PROPERTY_USER_ID] = $u;
            foreach ($indicators as $i => $s)
            {
                $parameters[PeerAssessmentScoreTracker::PROPERTY_INDICATOR_ID] = $i;
                $parameters[PeerAssessmentScoreTracker::PROPERTY_SCORE] = $s;
                // Event :: trigger('peer_assessment_submit_score', 'weblcms', $parameters);

                $scores = new PeerAssessmentScoreTracker();
                $scores->validate_parameters($parameters);

                $scores->save();
            }
        }

        // recalculate the progress for this user
        $this->update_user_attempt_progress($user_id, $attempt_id);

        // recalculate the new factor for all the members of the group
        $group = $this->get_user_group($user_id);
        $users = $this->get_group_users($group->get_id());
        foreach ($users as $u)
        {
            $this->update_user_attempt_factor($u->get_id(), $attempt_id);
        }
        return true;
    }

    // endregion scores

    // region feedback
    public function get_user_feedback_received($user_id, $attempt_id)
    {
        $group = $this->get_user_group($user_id);
        $users = $this->get_group_users($group->get_id());

        $tracker = new PeerAssessmentFeedbackTracker();

        $feedback = array();
        foreach ($users as $u)
        {
            /*
             * foreach ($indicators as $i) { $scores[$u->get_id()][$i->get_id()] = null; }
             */

            $status = $this->get_user_attempt_status($u->get_id(), $attempt_id);
            $condition = new AndCondition(
                new EqualityCondition(
                    new PropertyConditionVariable(
                        PeerAssessmentFeedbackTracker::class_name(),
                        PeerAssessmentFeedbackTracker::PROPERTY_ATTEMPT_STATUS_ID),
                    new StaticConditionVariable($status->get_id())),
                new EqualityCondition(
                    new PropertyConditionVariable(
                        PeerAssessmentFeedbackTracker::class_name(),
                        PeerAssessmentFeedbackTracker::PROPERTY_USER_ID),
                    new StaticConditionVariable($user_id)));

            $items = DataManager::retrieves(
                PeerAssessmentFeedbackTracker::class_name(),
                new DataClassRetrievesParameters($condition))->as_array();

            foreach ($items as $item)
            {
                $feedback[$u->get_id()] = $item->get_feedback();
            }
        }

        return $feedback;
    }

    public function get_user_feedback_given($user_id, $attempt_id)
    {
        // get the current attempt status and scores from the user
        $status = $this->get_user_attempt_status($user_id, $attempt_id);
        $tracker = new PeerAssessmentFeedbackTracker();
        $items = array();
        if ($status->get_id())
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable(
                    PeerAssessmentFeedbackTracker::class_name(),
                    PeerAssessmentFeedbackTracker::PROPERTY_ATTEMPT_STATUS_ID),
                new StaticConditionVariable($status->get_id()));

            $items = DataManager::retrieves(
                PeerAssessmentFeedbackTracker::class_name(),
                new DataClassRetrievesParameters($condition))->as_array();
        }
        // iterate over the results and put them in a two dimensional array
        $feedback = array();
        foreach ($items as $item)
        {
            $feedback[$item->get_user_id()] = $item->get_feedback();
        }
        return $feedback;
    }

    public function save_feedback($user_id, $attempt_id, $feedback)
    {
        // get the current attempt status from the user
        $status = $this->get_user_attempt_status($user_id, $attempt_id);
        // save the status with the new time of modification
        $status->set_modified(time());
        if (! $status->save())
            return false;

            // get the feedback items the user has already filled in
        $tracker = new PeerAssessmentFeedbackTracker();
        $items = array();

        if ($status->get_id())
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable(
                    PeerAssessmentFeedbackTracker::class_name(),
                    PeerAssessmentFeedbackTracker::PROPERTY_ATTEMPT_STATUS_ID),
                new StaticConditionVariable($status->get_id()));

            $items = DataManager::retrieves(
                PeerAssessmentFeedbackTracker::class_name(),
                new DataClassRetrievesParameters($condition))->as_array();
        }

        // loop through the existing feedback and update/delete them if necessary
        foreach ($items as $item)
        {
            $u = $item->get_user_id();

            // if there is a new feedback submitted
            if (isset($feedback[$u]))
            {
                // update the score in the database
                $item->set_feedback($feedback[$u]);
                $item->save();
                // remove the feedback from the list
                unset($feedback[$u]);
            }
            else
            {
                $item->delete();
            }
        }
        // loop through the remaining feedback and add them to the database
        $parameters = array();
        $parameters[PeerAssessmentFeedbackTracker::PROPERTY_ATTEMPT_STATUS_ID] = $status->get_id();
        foreach ($feedback as $u => $f)
        {
            $parameters[PeerAssessmentFeedbackTracker::PROPERTY_USER_ID] = $u;
            $parameters[PeerAssessmentFeedbackTracker::PROPERTY_FEEDBACK] = $f;

            $feedback = new PeerAssessmentFeedbackTracker();

            $feedback->validate_parameters($parameters);
            $feedback->save();
        }
        return true;
    }

    // endregion feedback

    // region indicator

    // endregion indicator
    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        // TODO add breadcrumbs
        // $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_BROWSE)),
        // Translation :: get('PeerAssessmentToolBrowserComponent')));
        return $breadcrumbtrail;
    }

    function get_additional_parameters()
    {
        return array(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);
    }

    function get_available_browser_types()
    {
        /*
         * return array( ContentObjectPublicationListRenderer :: TYPE_TABLE, ContentObjectPublicationListRenderer ::
         * TYPE_LIST );
         */
    }

    // region rights

    /**
     * translates peer assessment right to weblcms right
     *
     * @param integer $right
     * @return boolean the weblcms right
     */
    function is_allowed($right)
    {
        switch ($right)
        {
            case \Chamilo\Core\Repository\ContentObject\PeerAssessment\Display\Manager::EDIT_RIGHT :
                $weblcms_right = WeblcmsRights::EDIT_RIGHT;
                break;
            default :
                $weblcms_right = WeblcmsRights::VIEW_RIGHT;
                break;
        }
        return parent::is_allowed($weblcms_right, $this->get_publication());
    }

    function is_allowed_to_view_content_object()
    {
        return parent::is_allowed(WeblcmsRights::VIEW_RIGHT, $this->get_publication());
    }

    public function is_allowed_to_edit_content_object()
    {
        return parent::is_allowed(WeblcmsRights::EDIT_RIGHT, $this->get_publication()) &&
             $this->get_publication()->get_allow_collaboration();
    }

    function is_allowed_to_add_child()
    {
        return parent::is_allowed(WeblcmsRights::ADD_RIGHT, $this->get_publication());
    }

    function is_allowed_to_delete_child()
    {
        return parent::is_allowed(WeblcmsRights::DELETE_RIGHT, $this->get_publication());
    }

    function is_allowed_to_delete_feedback()
    {
        return parent::is_allowed(WeblcmsRights::DELETE_RIGHT, $this->get_publication());
    }

    function is_allowed_to_edit_feedback()
    {
        return parent::is_allowed(WeblcmsRights::EDIT_RIGHT, $this->get_publication());
    }

    // endregion rights
}