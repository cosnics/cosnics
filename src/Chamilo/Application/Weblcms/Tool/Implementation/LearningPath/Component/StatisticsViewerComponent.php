<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Component;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template\LearningPathAttemptProgressDetailsTemplate;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template\LearningPathAttemptProgressTemplate;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template\LearningPathAttemptsTemplate;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Storage\DataManager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Tree;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: learning_path_statistics_viewer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * 
 * @package application.lib.weblcms.tool.learning_path.component
 */
class StatisticsViewerComponent extends Manager
{
    const PARAM_STAT = 'stats_action';
    const ACTION_DELETE_LP_ATTEMPT = 'DeleteLpAttempt';
    const ACTION_DELETE_LPI_ATTEMPT = 'DeleteLpiAttempt';
    const ACTION_DELETE_LPI_ATTEMPTS = 'DeleteLpiAttempts';
    const PARAM_ITEM_ID = 'item_id';
    const PARAM_DELETE_ID = 'delete_id';

    private $root_content_object;

    public function run()
    {
        $trail = BreadcrumbTrail::getInstance();
        
        $pid = Request::get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);
        
        if (! $pid)
        {
            throw new \Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException(
                Translation::get('Publication'));
        }
        
        $stats_action = Request::get(self::PARAM_STAT);
        
        switch ($stats_action)
        {
            case self::ACTION_DELETE_LP_ATTEMPT :
                $this->delete_lp_attempt(Request::get(self::PARAM_ATTEMPT_ID));
                
                return;
            case self::ACTION_DELETE_LPI_ATTEMPTS :
                $this->delete_lpi_attempts_from_item(Request::get('item_id'), Request::get(self::PARAM_ATTEMPT_ID));
                
                return;
            case self::ACTION_DELETE_LPI_ATTEMPT :
                $this->delete_lpi_attempt(Request::get('delete_id'));
                
                return;
        }
        
        $publication = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
            ContentObjectPublication::class_name(), 
            $pid);
        
        $root_object = $publication->get_content_object();
        
        $parameters = array(
            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_VIEW_STATISTICS, 
            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $pid);
        $url = $this->get_url($parameters);
        
        $attempt_id = Request::get(self::PARAM_ATTEMPT_ID);
        
        if ($attempt_id)
        {
            $tracker = $this->retrieve_tracker($attempt_id);
            $attempt_data = $this->retrieve_tracker_items($tracker);
            $menu = $this->get_menu($root_object->get_id(), null, $pid, $attempt_data);
            
            $cid = Request::get('cid');
            if ($cid)
            {
                $parameters['cid'] = $cid;
                $url = $this->get_url($parameters);
                $trail->add(new Breadcrumb($url, Translation::get('ItemDetails')));
            }
            
            $objects = $menu->get_objects();
            $details = Request::get('details');
            
            if ($details)
            {
                $trail->add(
                    new Breadcrumb(
                        $this->get_url($parameters), 
                        Translation::get('AssessmentResult', null, 'application/assessment')));
                
                $this->set_parameter('tool_action', 'stats');
                $this->set_parameter(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID, $pid);
                $this->set_parameter(self::PARAM_ATTEMPT_ID, $attempt_id);
                $this->set_parameter('cid', $cid);
                $this->set_parameter('details', $details);
                $_GET['display_action'] = 'view_result';
                
                $object = $objects[$cid];
                
                $this->root_content_object = $object;
                
                $context = ClassnameUtilities::getInstance()->getNamespaceParent($object->get_type(), 3) . '\Display';
                $factory = new ApplicationFactory(
                    $context, 
                    new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
                
                return $factory->run();
            }
            else
            {
                if ($cid)
                {
                    $factory = new ApplicationFactory(
                        \Chamilo\Core\Reporting\Viewer\Manager::context(), 
                        new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
                    $component = $factory->getComponent();
                    $component->set_template_by_name(LearningPathAttemptProgressDetailsTemplate::class_name());
                    
                    return $component->run();
                }
                else
                {
                    if (! $this->is_allowed(WeblcmsRights::EDIT_RIGHT) &&
                         Session::get_user_id() != Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_USERS))
                    {
                        throw new NotAllowedException();
                    }
                    
                    if (! $this->is_allowed(WeblcmsRights::EDIT_RIGHT))
                    {
                        $breadcrumb_trail = BreadcrumbTrail::getInstance();
                        $breadcrumbs = $breadcrumb_trail->get_breadcrumbs();
                        
                        $params = array();
                        $params[self::PARAM_ATTEMPT_ID] = Request::get(self::PARAM_ATTEMPT_ID);
                        $params[\Chamilo\Application\Weblcms\Manager::PARAM_USERS] = Request::get(
                            \Chamilo\Application\Weblcms\Manager::PARAM_USERS);
                        
                        $breadcrumbs[$breadcrumb_trail->size() - 1] = new Breadcrumb(
                            $this->get_url($params), 
                            $root_object->get_title());
                        
                        $breadcrumb_trail->set_breadcrumbtrail($breadcrumbs);
                    }
                    else
                    {
                        $breadcrumb_trail = BreadcrumbTrail::getInstance();
                        $breadcrumbs = $breadcrumb_trail->get_breadcrumbs();
                        
                        $breadcrumbs[$breadcrumb_trail->size() - 1] = new Breadcrumb(
                            $this->get_url(), 
                            $root_object->get_title());
                        
                        $params = array();
                        $params[self::PARAM_ATTEMPT_ID] = Request::get(self::PARAM_ATTEMPT_ID);
                        $params[\Chamilo\Application\Weblcms\Manager::PARAM_USERS] = Request::get(
                            \Chamilo\Application\Weblcms\Manager::PARAM_USERS);
                        
                        $user_id = Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_USERS);
                        if ($user_id)
                        {
                            $user = \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
                                \Chamilo\Core\User\Storage\DataClass\User::class_name(), 
                                $user_id);
                            
                            $breadcrumbs[] = new Breadcrumb($this->get_url($params), $user->get_fullname());
                        }
                        
                        $breadcrumb_trail->set_breadcrumbtrail($breadcrumbs);
                    }
                    
                    $factory = new ApplicationFactory(
                        \Chamilo\Core\Reporting\Viewer\Manager::context(), 
                        new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
                    $component = $factory->getComponent();
                    $component->set_template_by_name(LearningPathAttemptProgressTemplate::class_name());
                    
                    return $component->run();
                }
            }
        }
        else
        {
            if (! $this->is_allowed(WeblcmsRights::EDIT_RIGHT))
            {
                $this->redirect(
                    Translation::get('NotAllowed', null, Utilities::COMMON_LIBRARIES), 
                    true, 
                    array(), 
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION, 
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID));
            }
            
            $factory = new ApplicationFactory(
                \Chamilo\Core\Reporting\Viewer\Manager::context(), 
                new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
            $component = $factory->getComponent();
            $component->set_template_by_name(LearningPathAttemptsTemplate::class_name());
            
            return $component->run();
        }
    }

    function get_menu($root_object_id, $selected_object_id, $pid, $lpi_tracker_data)
    {
        $url = '?go=courseviewer&course=' . Request::get('course') .
             '&application=weblcms&tool=learning_path&tool_action=view&publication=' . $pid . '&' . self::PARAM_LP_STEP .
             '=%s';
        
        $menu = new Tree($root_object_id, $selected_object_id, $url, $lpi_tracker_data);
        
        return $menu;
    }
    
    // Statistics
    private function retrieve_tracker($attempt_id)
    {
        return DataManager::retrieve_by_id(
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathAttempt::class_name(), 
            $attempt_id);
    }

    private function retrieve_tracker_items($learning_path_attempt)
    {
        $item_attempt_data = array();
        
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\TreeNodeDataAttempt::class_name(),
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\TreeNodeDataAttempt::PROPERTY_LEARNING_PATH_ATTEMPT_ID),
            new StaticConditionVariable($learning_path_attempt->get_id()));
        
        $item_attempts = DataManager::retrieves(
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\TreeNodeDataAttempt::class_name(),
            new DataClassRetrievesParameters($condition));
        
        while ($item_attempt = $item_attempts->next_result())
        {
            $item_attempt_data[$item_attempt->get_learning_path_item_id()][] = $item_attempt;
        }
        
        return $item_attempt_data;
    }
    
    // Deleter functions
    private function delete_lp_attempt($learning_path_attempt_id)
    {
        if (! $this->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            $this->redirect(
                Translation::get('NotAllowed', null, Utilities::COMMON_LIBRARIES), 
                true, 
                array(), 
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION, 
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID));
        }
        
        $attempt = DataManager::retrieve_by_id(
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathAttempt::class_name(), 
            $learning_path_attempt_id);
        $attempt->delete();
        
        $params = array(
            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_VIEW_STATISTICS, 
            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => Request::get(
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID));
        
        $this->redirect(Translation::get('LpAttemptDeleted'), false, $params, array());
    }

    private function delete_lpi_attempt($learning_path_item_attempt_id)
    {
        if (! $this->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            $this->redirect(
                Translation::get('NotAllowed', null, Utilities::COMMON_LIBRARIES), 
                true, 
                array(), 
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION, 
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID));
        }
        
        $item_attempt = DataManager::retrieve_by_id(
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\TreeNodeDataAttempt::class_name(),
            $learning_path_item_attempt_id);
        $item_attempt->delete();
        
        $params = array(
            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_VIEW_STATISTICS, 
            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => Request::get(
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID), 
            self::PARAM_ATTEMPT_ID => Request::get(self::PARAM_ATTEMPT_ID), 
            'cid' => Request::get('cid'));
        
        $this->redirect(Translation::get('LpiAttemptDeleted'), false, $params, array());
    }

    private function delete_lpi_attempts_from_item($item_id, $lp_attempt_id)
    {
        if (! $this->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            $this->redirect(
                Translation::get('NotAllowed', null, Utilities::COMMON_LIBRARIES), 
                true, 
                array(), 
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION, 
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID));
        }
        
        $conditions = array();
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\TreeNodeDataAttempt::class_name(),
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\TreeNodeDataAttempt::PROPERTY_LEARNING_PATH_ITEM_ID),
            new StaticConditionVariable($item_id));
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\TreeNodeDataAttempt::class_name(),
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\TreeNodeDataAttempt::PROPERTY_LEARNING_PATH_ATTEMPT_ID),
            new StaticConditionVariable($lp_attempt_id));
        
        $condition = new AndCondition($conditions);
        
        $item_attempts = DataManager::retrieves(
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\TreeNodeDataAttempt::class_name(),
            new DataClassRetrievesParameters($condition));
        
        while ($item_attempt = $item_attempts->next_result())
        {
            $item_attempt->delete();
        }
        
        $params = array(
            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_VIEW_STATISTICS, 
            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => Request::get(
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID), 
            self::PARAM_ATTEMPT_ID => Request::get(self::PARAM_ATTEMPT_ID), 
            \Chamilo\Application\Weblcms\Manager::PARAM_USERS => Request::get(
                \Chamilo\Application\Weblcms\Manager::PARAM_USERS));
        
        $this->redirect(Translation::get('LpiAttemptsDeleted'), false, $params, array());
    }

    public function can_change_answer_data()
    {
        return true;
    }

    public function retrieve_assessment_results()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathQuestionAttempt::class_name(), 
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathQuestionAttempt::PROPERTY_ITEM_ATTEMPT_ID), 
            new StaticConditionVariable(Request::get('details')));
        
        $question_attempts = DataManager::retrieves(
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathQuestionAttempt::class_name(), 
            new DataClassRetrievesParameters($condition));
        
        $results = array();
        
        while ($question_attempt = $question_attempts->next_result())
        {
            $results[$question_attempt->get_question_complex_id()] = array(
                'answer' => $question_attempt->get_answer(), 
                'feedback' => $question_attempt->get_feedback(), 
                'score' => $question_attempt->get_score(), 
                'hint' => $question_attempt->get_hint());
        }
        
        return $results;
    }

    public function change_answer_data($question_complex_id, $score, $feedback)
    {
        $conditions = array();
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathQuestionAttempt::class_name(), 
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathQuestionAttempt::PROPERTY_ITEM_ATTEMPT_ID), 
            new StaticConditionVariable(Request::get('details')));
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathQuestionAttempt::class_name(), 
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathQuestionAttempt::PROPERTY_QUESTION_COMPLEX_ID), 
            new StaticConditionVariable($question_complex_id));
        
        $condition = new AndCondition($conditions);
        
        $question_attempt = DataManager::retrieve(
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathQuestionAttempt::class_name(), 
            new DataClassRetrieveParameters($condition));
        
        $question_attempt->set_score($score);
        $question_attempt->set_feedback($feedback);
        $question_attempt->update();
    }

    public function change_total_score($total_score)
    {
        $item_attempt = DataManager::retrieve_by_id(
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\TreeNodeDataAttempt::class_name(),
            Request::get('details'));
        $item_attempt->set_score($total_score);
        $item_attempt->update();
    }

    public function get_root_content_object()
    {
        return $this->root_content_object;
    }

    public function get_additional_parameters()
    {
        return array(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);
    }
}
