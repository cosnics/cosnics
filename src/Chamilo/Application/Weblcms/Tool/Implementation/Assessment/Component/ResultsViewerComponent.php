<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Component;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssessmentAttempt;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Table\AsessmentAttempt\AssessmentAttemptTable;
use Chamilo\Core\Repository\ContentObject\Assessment\Storage\DataClass\Assessment;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Views the results of an assessment
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ResultsViewerComponent extends Manager implements TableSupport
{

    /**
     *
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    private $publication;

    /**
     * Runs this component
     *
     * @throws \libraries\architecture\exceptions\NotAllowedException
     */
    public function run()
    {
        $pid = Request::get(self::PARAM_ASSESSMENT);

        $this->set_parameter(self::PARAM_ASSESSMENT, $pid);
        $this->publication = DataManager::retrieve_by_id(
            ContentObjectPublication::class_name(), $pid
        );

        $assessment = $this->publication->get_content_object();

        $this->add_assessment_title_breadcrumb($assessment);

        if (!$this->is_allowed(WeblcmsRights::VIEW_RIGHT, $this->publication))
        {
            throw new NotAllowedException();
        }

        $html = array();

        $html[] = $this->render_header();

        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer($assessment);
        if ($this->buttonToolbarRenderer)
        {
            $html[] = $this->buttonToolbarRenderer->render();
        }

        $html[] = '<div class="panel panel-default">';

        $html[] = '<div class="panel-heading">';
        $html[] = '<h3 class="panel-title">';
        $html[] = '<img src="' .
            Theme::getInstance()->getImagePath('Chamilo\Core\Repository\ContentObject\Assessment', 'Logo/16') .
            '" /> ' . $assessment->get_title();
        $html[] = '</h3>';
        $html[] = '</div>';

        $html[] = '<div class="panel-body">';
        $html[] = $assessment->get_description();
        $html[] = '</div>';

        $html[] = '</div>';

        $table = new AssessmentAttemptTable($this);

        $html[] = $table->as_html();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @param BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $this->addBrowserBreadcrumb($breadcrumbtrail);
    }

    /**
     * Add a breadcrumb with the title of the assessment
     *
     * @param $assessment Assessment
     */
    protected function add_assessment_title_breadcrumb($assessment)
    {
        $breadcrumb_trail = BreadcrumbTrail::getInstance();
        $breadcrumbs = $breadcrumb_trail->get_breadcrumbs();

        $breadcrumbs[$breadcrumb_trail->size() - 1] = new Breadcrumb(
            $this->get_url(array(self::PARAM_ACTION => self::ACTION_VIEW_RESULTS)),
            Translation::get('ViewResultsForAssessment', array('TITLE' => $assessment->get_title()))
        );

        $breadcrumb_trail->set_breadcrumbtrail($breadcrumbs);
    }

    /**
     * @param null $assessment
     *
     * @return ButtonToolBarRenderer
     * @throws NotAllowedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    public function getButtonToolbarRenderer($assessment = null)
    {
        if ($this->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            if (!isset($this->buttonToolbarRenderer))
            {
                $aid = Request::get(self::PARAM_ASSESSMENT);

                $buttonToolbar = new ButtonToolBar($this->get_url());
                $commonActions = new ButtonGroup();

                $commonActions->addButton(
                    new Button(
                        Translation::get('ShowAll', null, Utilities::COMMON_LIBRARIES), new FontAwesomeGlyph('folder'),
                        $this->get_url(), ToolbarItem::DISPLAY_ICON_AND_LABEL
                    )
                );

                $commonActions->addButton(
                    new Button(
                        Translation::get('DeleteAllResults'), new FontAwesomeGlyph('times'), $this->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_DELETE_RESULTS,
                            self::PARAM_ASSESSMENT => $aid
                        )
                    ), ToolbarItem::DISPLAY_ICON_AND_LABEL, true
                    )
                );

                if ($assessment instanceof Assessment)
                {
                    $commonActions->addButton(
                        new Button(
                            Translation::get('DownloadDocuments'), new FontAwesomeGlyph('download'), $this->get_url(
                            array(
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_SAVE_DOCUMENTS,
                                self::PARAM_ASSESSMENT => $aid
                            )
                        ), ToolbarItem::DISPLAY_ICON_AND_LABEL
                        )
                    );

                    $commonActions->addButton(
                        new Button(
                            Translation::get('RawExportResults'), new FontAwesomeGlyph('upload'), $this->get_url(
                            array(
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_RAW_EXPORT_RESULTS,
                                self::PARAM_ASSESSMENT => Request::get(self::PARAM_ASSESSMENT)
                            )
                        ), ToolbarItem::DISPLAY_ICON_AND_LABEL
                        )
                    );
                }

                $buttonToolbar->addButtonGroup($commonActions);

                $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
            }
        }

        return $this->buttonToolbarRenderer;
    }

    public function get_publication()
    {
        return $this->publication;
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
            new PropertyConditionVariable(AssessmentAttempt::class_name(), AssessmentAttempt::PROPERTY_ASSESSMENT_ID),
            new StaticConditionVariable(Request::get(self::PARAM_ASSESSMENT))
        );

        if (!$this->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(AssessmentAttempt::class_name(), AssessmentAttempt::PROPERTY_USER_ID),
                new StaticConditionVariable($this->get_user_id())
            );
        }

        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();

        if ($this->buttonToolbarRenderer)
        {
            $search_properties = array();

            $search_properties[] = new PropertyConditionVariable(User::class_name(), User::PROPERTY_FIRSTNAME);
            $search_properties[] = new PropertyConditionVariable(User::class_name(), User::PROPERTY_LASTNAME);
            $search_properties[] = new PropertyConditionVariable(User::class_name(), User::PROPERTY_OFFICIAL_CODE);

            $search_conditions = $this->buttonToolbarRenderer->getConditions($search_properties);
            if ($search_conditions)
            {
                $conditions[] = $search_conditions;
            }
        }

        return new AndCondition($conditions);
    }
}
