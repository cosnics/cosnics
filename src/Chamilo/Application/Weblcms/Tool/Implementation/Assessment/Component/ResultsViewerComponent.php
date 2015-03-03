<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Component;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssessmentAttempt;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Table\AsessmentAttempt\AssessmentAttemptTable;
use Chamilo\Core\Repository\ContentObject\Assessment\Storage\DataClass\Assessment;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBarRenderer;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Views the results of an assessment
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ResultsViewerComponent extends Manager implements TableSupport
{

    /**
     * The action bar
     *
     * @var ActionBarRenderer
     */
    private $action_bar;

    private $publication;

    /**
     * Runs this component
     *
     * @throws \libraries\architecture\exceptions\NotAllowedException
     */
    public function run()
    {
        $pid = Request :: get(self :: PARAM_ASSESSMENT);

        $this->publication = \Chamilo\Application\Weblcms\Storage\DataManager :: retrieve_by_id(
            ContentObjectPublication :: class_name(),
            $pid);

        $assessment = $this->publication->get_content_object();

        $this->add_assessment_title_breadcrumb($assessment);

        if (! $this->is_allowed(WeblcmsRights :: VIEW_RIGHT, $this->publication))
        {
            throw new NotAllowedException();
        }

        $html = array();

        $html[] = $this->render_header();

        $this->action_bar = $this->get_toolbar();
        if ($this->action_bar)
        {
            $html[] = $this->action_bar->as_html();
        }

        $html[] = '<div class="content_object" style="background-image: url(' .
             Theme :: getInstance()->getImagePath(Assessment :: context(), 'Logo/22') . ');">';
        $html[] = '<div class="title">';
        $html[] = $assessment->get_title();
        $html[] = '</div>';
        $html[] = $assessment->get_description();
        $html[] = '</div>';

        $table = new AssessmentAttemptTable($this);

        $html[] = $table->as_html();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * Returns the action bar
     *
     * @return ActionBarRenderer
     */
    public function get_toolbar()
    {
        if ($this->is_allowed(WeblcmsRights :: EDIT_RIGHT))
        {
            $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
            $action_bar->set_search_url($this->get_url());

            $action_bar->add_common_action(
                new ToolbarItem(
                    Translation :: get('ShowAll', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagePath('action_browser'),
                    $this->get_url(),
                    ToolbarItem :: DISPLAY_ICON_AND_LABEL));

            $aid = Request :: get(self :: PARAM_ASSESSMENT);
            $action_bar->add_common_action(
                new ToolbarItem(
                    Translation :: get('DownloadDocuments'),
                    Theme :: getInstance()->getCommonImagePath('action_download'),
                    $this->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => self :: ACTION_SAVE_DOCUMENTS,
                            self :: PARAM_ASSESSMENT => $aid)),
                    ToolbarItem :: DISPLAY_ICON_AND_LABEL));

            $action_bar->add_common_action(
                new ToolbarItem(
                    Translation :: get('DeleteAllResults'),
                    Theme :: getInstance()->getCommonImagePath('action_delete'),
                    $this->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => self :: ACTION_DELETE_RESULTS,
                            self :: PARAM_ASSESSMENT => $aid)),
                    ToolbarItem :: DISPLAY_ICON_AND_LABEL,
                    true));

            $action_bar->add_common_action(
                new ToolbarItem(
                    Translation :: get('RawExportResults'),
                    Theme :: getInstance()->getCommonImagePath('action_export'),
                    $this->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => self :: ACTION_RAW_EXPORT_RESULTS,
                            self :: PARAM_ASSESSMENT => Request :: get(self :: PARAM_ASSESSMENT))),
                    ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        return $action_bar;
    }

    /**
     * Add a breadcrumb with the title of the assessment
     *
     * @param $assessment Assessment
     */
    protected function add_assessment_title_breadcrumb($assessment)
    {
        $breadcrumb_trail = BreadcrumbTrail :: get_instance();
        $breadcrumbs = $breadcrumb_trail->get_breadcrumbs();

        $breadcrumbs[$breadcrumb_trail->size() - 1] = new Breadcrumb(
            $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_RESULTS)),
            Translation :: get('ViewResultsForAssessment', array('TITLE' => $assessment->get_title())));

        $breadcrumb_trail->set_breadcrumbtrail($breadcrumbs);
    }

    /**
     * Returns the additional parameters needed for registration
     *
     * @return array
     */
    public function get_additional_parameters()
    {
        return array(self :: PARAM_ASSESSMENT);
    }

    /**
     * Returns the condition
     *
     * @param string $table_class_name
     *
     * @return \libraries\storage\Condition
     */
    public function get_table_condition($table_class_name)
    {
        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(AssessmentAttempt :: class_name(), AssessmentAttempt :: PROPERTY_ASSESSMENT_ID),
            new StaticConditionVariable(Request :: get(self :: PARAM_ASSESSMENT)));

        if (! $this->is_allowed(WeblcmsRights :: EDIT_RIGHT))
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(AssessmentAttempt :: class_name(), AssessmentAttempt :: PROPERTY_USER_ID),
                new StaticConditionVariable($this->get_user_id()));
        }

        $action_bar = $this->action_bar;

        if ($this->action_bar)
        {
            $search_properties = array();

            $search_properties[] = new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_FIRSTNAME);
            $search_properties[] = new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_LASTNAME);
            $search_properties[] = new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_OFFICIAL_CODE);

            $search_conditions = $action_bar->get_conditions($search_properties);
            if ($search_conditions)
            {
                $conditions[] = $search_conditions;
            }
        }

        return new AndCondition($conditions);
    }

    public function get_publication()
    {
        return $this->publication;
    }
}
