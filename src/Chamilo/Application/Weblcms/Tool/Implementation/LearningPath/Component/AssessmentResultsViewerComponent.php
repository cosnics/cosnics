<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Component;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathChildAttempt;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathQuestionAttempt;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Table\AssessmentResults\AssessmentResultsTable;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * This component renders the assessment attempts from the learning path.
 * Depending on the parameters in the URL, it
 * will show an overview of all the assessment attempts or a detail from one attempt.
 * 
 * @author Bert De Clercq (Hogeschool Gent)
 */
class AssessmentResultsViewerComponent extends Manager implements TableSupport
{
    const PARAM_DELETE_ID = 'delete_id';

    private $assessment;

    /**
     * Launches this component.
     */
    public function run()
    {
        $this->check_view_rights();
        
        $this->modify_last_breadcrumb();
        
        if (Request::get(self::PARAM_DELETE_ID))
        {
            $this->delete_assessment_attempt(Request::get(self::PARAM_DELETE_ID));
        }
        
        if (Request::get(self::PARAM_LEARNING_PATH_ITEM_ATTEMPT_ID))
        {
            return $this->view_single_result();
        }
        else
        {
            return $this->view_assessment_results();
        }
    }

    /**
     * Returns the registered question ids
     * 
     * @return int[] $question_ids
     */
    public function get_registered_question_ids()
    {
        $question_ids = array();
        
        $question_attempts = $this->get_assessment_question_attempts();
        foreach ($question_attempts as $question_attempt)
        {
            $question_ids[] = $question_attempt->get_question_complex_id();
        }
        
        return $question_ids;
    }

    /**
     * Returns the assessment question attempts
     * 
     * @return QuestionAttempt[]
     */
    public function get_assessment_question_attempts()
    {
        if (is_null($this->question_attempts))
        {
            $this->question_attempts = $this->retrieve_question_attempts();
        }
        
        return $this->question_attempts;
    }

    /**
     * Retrieves the question attempts for the selected assessment attempt
     * 
     * @return QuestionAttempt[]
     */
    protected function retrieve_question_attempts()
    {
        $question_attempts = array();
        
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                LearningPathQuestionAttempt::class_name(), 
                LearningPathQuestionAttempt::PROPERTY_ITEM_ATTEMPT_ID), 
            new StaticConditionVariable(Request::get(self::PARAM_LEARNING_PATH_ITEM_ATTEMPT_ID)));
        
        $question_attempts_result_set = DataManager::retrieves(
            LearningPathQuestionAttempt::class_name(), 
            new DataClassRetrievesParameters($condition));
        
        while ($question_attempt = $question_attempts_result_set->next_result())
        {
            $question_attempts[$question_attempt->get_question_complex_id()] = $question_attempt;
        }
        
        return $question_attempts;
    }

    /**
     * Checks if the user is allowed to view this page.
     */
    protected function check_view_rights()
    {
        $publication = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
            ContentObjectPublication::class_name(), 
            $this->get_publication_id());
        
        if (! $this->is_allowed(WeblcmsRights::VIEW_RIGHT, $publication) || (! $this->is_allowed(
            WeblcmsRights::EDIT_RIGHT) &&
             Session::get_user_id() != Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_USERS)))
        {
            throw new NotAllowedException();
        }
    }

    /**
     * Returns the publication id from the URL.
     * 
     * @return int The publication id
     */
    public function get_publication_id()
    {
        return Request::get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);
    }

    /**
     * Returns the user id from the URL.
     * 
     * @return int The user id
     */
    public function get_user_id()
    {
        return Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_USERS);
    }

    /**
     * Returns the learning path attempt id from the URL.
     * 
     * @return int The learning path attempt id
     */
    public function get_attempt_id()
    {
        return Request::get(self::PARAM_ATTEMPT_ID);
    }

    /**
     * Returns the complex content object item id from the URL.
     * 
     * @return int The complex content object item id
     */
    public function get_ccoi_id()
    {
        return Request::get(\Chamilo\Core\Repository\Display\Manager::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID);
    }

    /**
     * Returns the assessment id from the URL.
     * 
     * @return int The assessment id
     */
    public function get_assessment_id()
    {
        return Request::get(self::PARAM_ASSESSMENT_ID);
    }

    /**
     * Renders an overview of the assessment attempt in the learning path.
     * On top there's a description of the
     * assessment with some info about the attempts such as the average score of all the attempts.
     */
    public function view_assessment_results()
    {
        $html = array();
        
        $html[] = $this->render_header();
        $html[] = '<div class="panel panel-default">';
        $html[] = '<div class="panel-heading">';
        $html[] = '<h3 class="panel-title"><img src="' .
             Theme::getInstance()->getCommonImagePath('ContentObject/Assessment') . '" />' .
             $this->assessment->get_title() . '</h3>';
        $html[] = '</div>';
        
        $html[] = '<div class="panel-body">';
        $html[] = $this->assessment->get_description();
        $html[] = '</div>';
        $html[] = '</div>';
        
        $html[] = '<div class="panel panel-default">';
        $html[] = '<div class="panel-heading">';
        $html[] = '<h3 class="panel-title">' . Translation::get('Statistics') . '</h3>';
        $html[] = '</div>';
        
        $html[] = '<div class="panel-body">';
        
        $attempts = DataManager::retrieves(
            LearningPathChildAttempt::class_name(),
            new DataClassRetrievesParameters($this->get_table_condition()));
        
        if ($attempts->size() > 0)
        {
            $sum_score = 0;
            
            while ($attempt = $attempts->next_result())
            {
                $sum_score += $attempt->get_score();
            }
            
            $avg = round($sum_score / count($attempts)) . '%';
            $times_taken = $attempts->size();
        }
        else
        {
            $avg = '-';
            $times_taken = '-';
        }
        
        $html[] = Translation::get('AverageScore') . ': ' . $avg;
        $html[] = '<br/>' . Translation::get('TimesTaken') . ': ' . $times_taken;
        $html[] = '</div>';
        $html[] = '</div>';
        
        $table = new AssessmentResultsTable($this);
        
        $html[] = $table->as_html();
        $html[] = $this->render_footer();
        
        return implode(PHP_EOL, $html);
    }

    /**
     * Renders a detailed overview of a single assessment attempt.
     * A course admin can change scores and add feedback.
     */
    public function view_single_result()
    {
        $this->getRequest()->query->set(
            \Chamilo\Core\Repository\Display\Manager::PARAM_ACTION, 
            \Chamilo\Core\Repository\ContentObject\Assessment\Display\Manager::ACTION_VIEW_ASSESSMENT_RESULT);
        
        $this->set_parameter(
            self::PARAM_LEARNING_PATH_ITEM_ATTEMPT_ID, 
            Request::get(self::PARAM_LEARNING_PATH_ITEM_ATTEMPT_ID));
        
        $context = ClassnameUtilities::getInstance()->getNamespaceParent($this->assessment->get_type(), 3) . '\Display';
        $factory = new ApplicationFactory(
            $context, 
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
        return $factory->run();
    }

    /**
     * Retrieves the results for the assessment attempt.
     * 
     * @return array The assessment attempt results
     */
    public function retrieve_assessment_results()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                LearningPathQuestionAttempt::class_name(), 
                LearningPathQuestionAttempt::PROPERTY_ITEM_ATTEMPT_ID), 
            new StaticConditionVariable(Request::get(self::PARAM_LEARNING_PATH_ITEM_ATTEMPT_ID)));
        
        $question_attempts = DataManager::retrieves(
            LearningPathQuestionAttempt::class_name(), 
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

    /**
     * Deletes the assessment attempt with the given id.
     * 
     * @param int $assessment_attempt_id The assessment attempt id
     */
    protected function delete_assessment_attempt($assessment_attempt_id)
    {
        $assessment_attempt = DataManager::retrieve_by_id(LearningPathChildAttempt::class_name(), $assessment_attempt_id);
        
        if ($assessment_attempt instanceof LearningPathChildAttempt)
        {
            $assessment_attempt->delete();
        }
        
        $params = array(
            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_VIEW_ASSESSMENT_RESULTS, 
            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => Request::get(
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID), 
            \Chamilo\Application\Weblcms\Manager::PARAM_USERS => $this->get_user_id(), 
            self::PARAM_ATTEMPT_ID => $this->get_attempt_id(), 
            \Chamilo\Core\Repository\Display\Manager::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_ccoi_id(), 
            self::PARAM_ASSESSMENT_ID => $this->get_assessment_id());
        
        $this->redirect(Translation::get('LpiAttemptDeleted'), false, $params, array());
    }

    /**
     * Display the header and an action bar.
     */
    public function render_header()
    {
        $html = array();
        
        $html[] = parent::render_header();
        
        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();
        
        if ($this->buttonToolbarRenderer)
        {
            $html[] = $this->buttonToolbarRenderer->render();
        }
        
        return implode(PHP_EOL, $html);
    }

    /**
     * Returns an action bar if the user has edit rights.
     * The action bar includes a button to download the assessment
     * attempts documents and a button to delete the assessment attempts.
     * 
     * @return ButtonToolBarRenderer
     */
    public function getButtonToolbarRenderer()
    {
        if (Request::get(self::PARAM_ASSESSMENT_ID) && $this->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            if (! isset($this->buttonToolbarRenderer))
            {
                $buttonToolbar = new ButtonToolBar();
                $commonActions = new ButtonGroup();
                
                $commonActions->addButton(
                    new Button(
                        Translation::get('DownloadDocuments'), 
                        Theme::getInstance()->getCommonImagePath('Action/Save'), 
                        $this->get_url(
                            array(
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_DOWNLOAD_DOCUMENTS, 
                                \Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION => $this->get_publication_id(), 
                                \Chamilo\Application\Weblcms\Manager::PARAM_USERS => $this->get_user_id(), 
                                self::PARAM_ATTEMPT_ID => $this->get_attempt_id(), 
                                \Chamilo\Core\Repository\Display\Manager::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_ccoi_id(), 
                                self::PARAM_ASSESSMENT_ID => $this->get_assessment_id())), 
                        ToolbarItem::DISPLAY_ICON_AND_LABEL));
                
                if (Request::get(self::PARAM_LEARNING_PATH_ITEM_ATTEMPT_ID))
                {
                    $commonActions->addButton(
                        new Button(
                            Translation::get('DeleteResult'), 
                            Theme::getInstance()->getCommonImagePath('Action/Delete'), 
                            $this->get_url(
                                array(
                                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_VIEW_ASSESSMENT_RESULTS, 
                                    \Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION => $this->get_publication_id(), 
                                    \Chamilo\Application\Weblcms\Manager::PARAM_USERS => $this->get_user_id(), 
                                    self::PARAM_ATTEMPT_ID => $this->get_attempt_id(), 
                                    StatisticsViewerComponent::PARAM_ITEM_ID => $this->get_ccoi_id(), 
                                    StatisticsViewerComponent::PARAM_DELETE_ID => Request::get(
                                        self::PARAM_LEARNING_PATH_ITEM_ATTEMPT_ID))), 
                            ToolbarItem::DISPLAY_ICON_AND_LABEL, 
                            true));
                }
                else
                {
                    $commonActions->addButton(
                        new Button(
                            Translation::get('DeleteAllResults'), 
                            Theme::getInstance()->getCommonImagePath('Action/Delete'), 
                            $this->get_url(
                                array(
                                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_VIEW_STATISTICS, 
                                    \Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION => $this->get_publication_id(), 
                                    \Chamilo\Application\Weblcms\Manager::PARAM_USERS => $this->get_user_id(), 
                                    self::PARAM_ATTEMPT_ID => $this->get_attempt_id(), 
                                    StatisticsViewerComponent::PARAM_STAT => StatisticsViewerComponent::ACTION_DELETE_LPI_ATTEMPTS, 
                                    StatisticsViewerComponent::PARAM_ITEM_ID => $this->get_ccoi_id())), 
                            ToolbarItem::DISPLAY_ICON_AND_LABEL, 
                            true));
                }
                
                $buttonToolbar->addButtonGroup($commonActions);
                
                $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
            }
            
            return $this->buttonToolbarRenderer;
        }
    }

    /**
     * Returns true if the user had edit rights and false otherwise.
     * 
     * @return boolean True if the user has edit rights
     */
    public function can_change_answer_data()
    {
        return $this->is_allowed(WeblcmsRights::EDIT_RIGHT);
    }

    /**
     * Updates the question attempts of the assessment.
     * 
     * @param int $question_cid The complex question id
     * @param int $score The score
     * @param string $feedback The feedback
     */
    public function change_answer_data($question_cid, $score, $feedback)
    {
        $conditions = array();
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                LearningPathQuestionAttempt::class_name(), 
                LearningPathQuestionAttempt::PROPERTY_ITEM_ATTEMPT_ID), 
            new StaticConditionVariable(Request::get(self::PARAM_LEARNING_PATH_ITEM_ATTEMPT_ID)));
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                LearningPathQuestionAttempt::class_name(), 
                LearningPathQuestionAttempt::PROPERTY_QUESTION_COMPLEX_ID), 
            new StaticConditionVariable($question_cid));
        
        $condition = new AndCondition($conditions);
        
        $question_attempt = DataManager::retrieve(
            LearningPathQuestionAttempt::class_name(), 
            new DataClassRetrieveParameters($condition));
        
        $question_attempt->set_score($score);
        $question_attempt->set_feedback($feedback);
        $question_attempt->update();
    }

    /**
     * Updates the score of the assessment attempt in this learning path.
     */
    public function change_total_score($score)
    {
        $assessment_attempt = DataManager::retrieve_by_id(
            LearningPathChildAttempt::class_name(),
            Request::get(self::PARAM_LEARNING_PATH_ITEM_ATTEMPT_ID));
        
        $assessment_attempt->set_score($score);
        $assessment_attempt->update();
    }

    /**
     * Returns the root content object.
     * 
     * @return type
     */
    public function get_root_content_object()
    {
        return $this->assessment;
    }

    /**
     * Returns an empty array.
     * 
     * @return array
     */
    public function get_assessment_parameters()
    {
        return array();
    }

    /**
     * Returns the additional parameters.
     * 
     * @return array
     */
    public function get_additional_parameters()
    {
        return array(
            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID, 
            \Chamilo\Application\Weblcms\Manager::PARAM_USERS, 
            self::PARAM_ATTEMPT_ID, 
            \Chamilo\Core\Repository\Display\Manager::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID, 
            self::PARAM_ASSESSMENT_ID);
    }

    /**
     * Updates the last breadcrumb so it displays the name of the assessment.
     * 
     * @return BreadcrumbTrail The modified breadcrumb trail
     */
    protected function modify_last_breadcrumb()
    {
        $breadcrumb_trail = BreadcrumbTrail::getInstance();
        $breadcrumbs = $breadcrumb_trail->get_breadcrumbs();
        
        $breadcrumbs[$breadcrumb_trail->size() - 1] = new Breadcrumb(
            $this->get_url(
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_VIEW_ASSESSMENT_RESULTS, 
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $this->get_publication_id(), 
                    \Chamilo\Application\Weblcms\Manager::PARAM_USERS => $this->get_user_id(), 
                    self::PARAM_ATTEMPT_ID => $this->get_attempt_id(), 
                    \Chamilo\Core\Repository\Display\Manager::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_ccoi_id(), 
                    self::PARAM_ASSESSMENT_ID => $this->get_assessment_id())), 
            $this->assessment->get_title());
        
        $breadcrumb_trail->set_breadcrumbtrail($breadcrumbs);
        
        return $breadcrumb_trail;
    }

    /**
     * Adds additional breadcrumbs to the trail.
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $this->assessment = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
            ContentObject::class_name(), 
            Request::get(self::PARAM_ASSESSMENT_ID));
        
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_BROWSE)), 
                Translation::get('LearningPathToolBrowserComponent')));
        
        $lp = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
            ContentObjectPublication::class_name(), 
            $this->get_publication_id())->get_content_object();
        
        if ($this->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            $breadcrumbtrail->add(
                new Breadcrumb(
                    $this->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_VIEW_STATISTICS, 
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $this->get_publication_id())), 
                    $lp->get_title()));
            
            if ($this->get_user_id())
            {
                $user = \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
                    \Chamilo\Core\User\Storage\DataClass\User::class_name(), 
                    $this->get_user_id());
                
                $breadcrumbtrail->add(
                    new Breadcrumb(
                        $this->get_url(
                            array(
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_VIEW_STATISTICS, 
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $this->get_publication_id(), 
                                self::PARAM_ATTEMPT_ID => $this->get_attempt_id(), 
                                \Chamilo\Application\Weblcms\Manager::PARAM_USERS => $this->get_user_id())), 
                        $user->get_fullname()));
            }
        }
        else
        {
            $breadcrumbtrail->add(
                new Breadcrumb(
                    $this->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_VIEW_STATISTICS, 
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $this->get_publication_id(), 
                            self::PARAM_ATTEMPT_ID => $this->get_attempt_id(), 
                            \Chamilo\Application\Weblcms\Manager::PARAM_USERS => $this->get_user_id())), 
                    $lp->get_title()));
        }
    }

    public function get_table_condition($table_class_name)
    {
        $conditions = array();
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                LearningPathChildAttempt::class_name(),
                LearningPathChildAttempt::PROPERTY_LEARNING_PATH_ITEM_ID),
            new StaticConditionVariable(
                Request::get(\Chamilo\Core\Repository\Display\Manager::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID)));
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                LearningPathChildAttempt::class_name(),
                LearningPathChildAttempt::PROPERTY_LEARNING_PATH_ATTEMPT_ID),
            new StaticConditionVariable(Request::get(self::PARAM_ATTEMPT_ID)));
        
        return new AndCondition($conditions);
    }

    /**
     * Returns the feedback configuration for the assessment
     * 
     * @return AssessmentFeedbackConfiguration
     */
    public function get_assessment_configuration()
    {
        $complex_content_object_item = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
            ComplexContentObjectItem::class_name(), 
            $this->get_ccoi_id());
        
        return $complex_content_object_item->get_ref_object()->get_configuration();
    }
}
